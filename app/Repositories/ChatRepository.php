<?php

namespace App\Repositories;

use App\Events\Project\ChatMessagePriviateEvent;
use App\Http\Resources\ChatMessageResuouce;
use App\Http\Resources\Tenant\Chat\ChatResource;
use App\Interfaces\RepositoryInterfaces\ChatRepositoryInterface;
use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\ChatMessageStatus;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use App\Models\Project;
use App\Traits\FileHandler;

class ChatRepository implements ChatRepositoryInterface
{
    use FileHandler;
    protected $model;

    public function __construct(Chat $Chat)
    {
        $this->model = $Chat;
    }

    public function findChat($uuid = '')
    {
        if (!$uuid) {
            $uuid = '';
        }

        return $this->model::findOrFailByUuid($uuid);
    }

    public function createProjectChat($project)
    {
        $response = [];

        try {

            $chat = $this->model::create([
                'project_id' => $project->id,
                'title' => $project->name,
            ]);

            $chat->load('project');
            $chat->loadCount('projectChatMembers');

            $response['chat'] = ChatResource::make($chat);
            $response['message'] = 'Project chat created success';

            return response()->json($response, 200);
        } catch (\Throwable $th) {
            $response['chat'] = null;
            $response['message'] = $th->getMessage();
            return response()->json($response, 500);
        }
    }

    public function addParticipant($request)
    {
        $response = [];

        try {

            $participants = $request->participant_uuid;
            $participants = User::whereIn('uuid', $participants)->pluck('id')->toArray();
            $participants[] = auth()->id();

            $chat = $this->model::findOrFailByUuid($request->chatUuid);
            if (is_array($participants)) {
                $chat->projectChatMembers()->sync($participants);
            }

            $chat->load(['project', 'projectChatMembers', 'chatMessages' => fn($q) => $q->with("sender")]);
            $chat->loadCount('projectChatMembers');

            $response['chat'] = ChatResource::make($chat);
            $response['message'] = 'Add participents successfully';
            return response()->json($response, 200);
        } catch (\Throwable $th) {
            $response['chat'] = null;
            $response['message'] = $th->getMessage();
            return response()->json($response, 500);
        }
    }

    public function createProjectGroupChat(array $data, $project)
    {
        $response = [
            'chat' => null,
            'threatIndex' => null,
        ];

        try {

            $chat = $this->model::create([
                'project_id' => $project->id,
                'title' => $project->name,
            ]);

            $participants = $data['participant_uuid'];
            $participants = User::whereIn('uuid', $participants)->pluck('id')->toArray();
            $participants[] = auth()->id();
            if (is_array($participants)) {
                $chat->projectChatMembers()->attach($participants);
            }

            $chat->load(['project', 'projectChatMembers']);
            $chat->loadCount('projectChatMembers');

            $response['chat'] = ChatResource::make($chat);
            $response['message'] = 'Group chat created success';
            $response['threatIndex'] = $data['threatIndex'];

            return response()->json($response, 200);
        } catch (\Throwable $th) {
            $response['message'] = $th->getMessage();
            return response()->json($response, 500);
        }
    }

    public function getProjectChats($projectsIds)
    {
        $user = auth()->user();
        $query = $this->model::query();
        $query->with(['projectChatMembers', 'chatMessages' => fn($q) => $q->with("sender")->withCount('getChatMessagesStatus')]);
        $query->withCount(['projectChatMembers']);

        $query->when($user->hasRole(User::ROLE_DESIGNER), function ($q) use ($user) {
            $q->whereHas('projectChatMembers', fn($uQuery) => $uQuery->where("user_id", $user->id));
            // ->orDoesntHave('projectChatMembers', fn($uQuery2) => $uQuery2->where("user_id", $user->id));
        });

        $query->whereIn('project_id', $projectsIds);
        $chats = $query->get();
        return $chats;
    }

    public function getCompanyChats($companyId)
    {
        $projectsIds = Project::select('id')->where('company_id', $companyId)->get()->pluck('id')->toArray();

        $query = $this->model->whereIn('project_id', $projectsIds);
        $query->with(['project', 'projectChatMembers', 'chatMessages' => fn($q) => $q->with("sender")->withCount('getChatMessagesStatus')]);
        $query->withCount(['projectChatMembers']);
        $chats = $query->get();

        return $chats;
    }

    public function sendMessage($request)
    {

        DB::beginTransaction();

        try {

            $chatUuid = request()->chatUuid;
            $chat = $this->model->findOrFailByUuid($chatUuid);
            $chat->load(['projectChatMembers']);

            $receiverUuids = $chat?->projectChatMembers?->pluck('uuid');

            $chatPayload = [];
            $responseMsg = 'Message sent error';
            $responseStatus = false;

            $message = request()->text;
            $user = auth()->user();

            $attachments = [];
            $chatPayload['sender_id'] = $user->id;

            $receiverIds = $chat?->projectChatMembers?->pluck('id');
            $receiverIds = $receiverIds->filter(fn($id) => $id != $user->id);
            $messageStatusArr = Arr::map($receiverIds->toArray(), function ($item) {
                return [
                    "receiver_id" => $item,
                ];
            });

            $newMessageObject = new ChatMessage;
            $isAttachment = $request->hasFile('attachments');
            if ($isAttachment) {
                if ($message) {
                    $chatPayload['message'] = nl2br(e($message));
                }
                $newMessageObject = $this->sendMedia($request, $chat, $chatPayload, $user, $receiverUuids, $messageStatusArr);
                $message = '';
                $attachments[] = $newMessageObject;
            }

            if ($message) {
                $chatPayload['message'] = nl2br(e($message));
                $newMessageObject = $this->createChatMessage($chat, $chatPayload, $user, $receiverUuids, $messageStatusArr);
            }

            if ($newMessageObject?->uuid) {
                $responseMsg = 'Message sent successfully';
                $responseStatus = true;
            }

            DB::commit();

            $last_message = 'isMedia';
            if (($newMessageObject?->message_type ?? 'text') == 'text') {
                $last_message = Str::limit($message, 60);
            }
            // update chat after send message
            $chat->last_message = $last_message;
            $chat->last_messaged_at = now();
            $chat->save();

            return response()->json([
                "message" => $responseMsg,
                "chat" => ChatResource::make($chat),
                "new_message" => $newMessageObject,
                "attachments" => $attachments,
                "status" => $responseStatus,
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                "message" => $th->getMessage(),
                "status" => false,
            ], 500);
        }
    }

    private function sendMedia($request, $chat, $chatPayload = [], $user, $receiverUuids, $messageStatusArr)
    {
        $newChatPayload = $chatPayload;
        $newChatPayload['message_type'] = 'image';

        foreach ($request->file('attachments') as $image) {
            $newChatPayload['attachment'] = $this->storeMediaByHashName($image, 'chats/' . $chat->uuid);
            $newMessageObject = $this->createChatMessage($chat, $newChatPayload, $user, $receiverUuids, $messageStatusArr);
            $newChatPayload['message'] = '';
        }

        return $newMessageObject;
    }

    private function createChatMessage($chat, $chatPayload, $user, $receiverUuids, $messageStatusArr)
    {

        // save the message in the database
        $newMessage = $chat->chatMessages()->create($chatPayload);
        $newMessage->load(['sender'])->loadCount(['getChatMessagesStatus']);
        $newMessageObject = ChatMessageResuouce::make($newMessage);
        $chat->unsetRelation('chatMessages');
        $newChat = ChatResource::make($chat);

        // sned chat message real time
        broadcast(new ChatMessagePriviateEvent($newMessageObject, $newChat, $user->uuid, $receiverUuids))->toOthers();

        if ($newMessage->id) {
            $newMessage->getChatMessagesStatus()->createMany($messageStatusArr);
        }
        return $newMessageObject;
    }

    public function getMessages($request)
    {
        DB::beginTransaction();

        try {

            $lastMessageDate = $request->query('last_date', '');

            $chat = $this->model::findOrFailByUuid($request->chatUuid);
            $chat->load([
                'chatMessages' => function ($query) use ($lastMessageDate) {
                    // $query->when($lastMessageDate, function ($q) use ($lastMessageDate) {
                    //     $q->where('created_at', '>=', $lastMessageDate);
                    // })
                    $query->with(['sender'])
                        ->withCount(['getChatMessagesStatus']);
                }
            ]);

            $chatMessages = ChatMessageResuouce::collection($chat->chatMessages)->response()->getData(true);

            DB::commit();

            return response()->json([
                "unseen_messages_count" => $chat->unseen_messages_count,
                "chat_messages" => $chatMessages,
                "message" => "Messages successfully fetched",
                "status" => true,
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                "message" => $th->getMessage(),
                "status" => false,
            ], 500);
        }
    }


    public function markSeenMessages($request)
    {

        DB::beginTransaction();
        try {

            $chatUuid = $request->chatUuid;
            $chat = $this->model->findOrFailByUuid($chatUuid);

            $getChatMessagesStatus = ChatMessage::with('getChatMessagesStatus')->where('chat_id', $chat->id)
                ->whereHas('getChatMessagesStatus', function ($query) {
                    $query->where('seen', 0);
                })->get()->pluck('getChatMessagesStatus')->toArray();

            $chatMessageStatus = false;
            if (count($getChatMessagesStatus) > 0) {
                $chatMessagesStatusIds = Arr::pluck(Arr::collapse($getChatMessagesStatus), 'id');
                $chatMessageStatus = ChatMessageStatus::whereIn('id', $chatMessagesStatusIds)->forceDelete();
            }

            DB::commit();

            return response()->json([
                "message" => "Messages seen successfully",
                "status" => $chatMessageStatus,
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                "message" => $th->getMessage(),
                "file" => $th->getFile(),
                "line" => $th->getLine(),
                "status" => false,
            ], 500);
        }
    }
    public function createDirectChat($request)
    {
        $response = [];
        try {
            $participants = $request->participant_uuid;
            $participants = User::whereIn('uuid', $participants)->pluck('id')->toArray();
            $participants[] = auth()->id(); // Add the current user to the participants list

            $chat = $this->model::create([
                'project_id' => null, // No project associated
                'title' => 'Direct Chat',
            ]);

            if (is_array($participants)) {
                $chat->projectChatMembers()->attach($participants);
            }

            $chat->load(['projectChatMembers']);
            $chat->loadCount('projectChatMembers');
            $response['chat'] = ChatResource::make($chat);
            $response['message'] = 'Direct chat created successfully';

            return response()->json($response, 200);
        } catch (\Throwable $th) {
            $response['message'] = $th->getMessage();
            return response()->json($response, 500);
        }
    }
    public function directChats()
    {
        $directChatsQuery = $this->model::where(function ($query) {
            $query->whereNull('project_id')
                ->orWhere('project_id', 0);
        })
            ->whereHas('projectChatMembers', function ($q) {
                $q->where('user_id', auth()->id());
            })
            ->with([
                'projectChatMembers',
                'chatMessages' => fn($q) => $q->with('sender')->withCount('getChatMessagesStatus'),
            ])
            ->withCount('projectChatMembers');
        return $directChatsQuery->get();
    }
}
