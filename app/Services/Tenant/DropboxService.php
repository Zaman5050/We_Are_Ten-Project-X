<?php

namespace App\Services\Tenant;

use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\FilesystemAdapter;
use League\Flysystem\Filesystem;
use Spatie\Dropbox\Client as DropboxClient;
use Spatie\FlysystemDropbox\DropboxAdapter;
use Stevenmaguire\OAuth2\Client\Provider\Dropbox;
use Illuminate\Support\Facades\Log;
use \Illuminate\Support\Facades\Http;
use App\Interfaces\ServiceInterfaces\Tenant\DropboxServiceInterface;
use Exception;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DropboxService implements DropboxServiceInterface
{
    private $dropboxProvider;

    public function __construct(Dropbox $dropboxProvider)
    {
        $this->dropboxProvider = $dropboxProvider;
    }

    public function getDropboxAuthUrl($request)
    {
        $authorizationUrl = $this->dropboxProvider->getAuthorizationUrl(
            [
                'state' => json_encode([
                    'subdomain' => $request->subdomain,
                    'project_uuid' => $request->project_uuid,
                    'uuid' => auth()->user()->uuid,
                ]),
                'token_access_type' => 'offline',
                'scope' => 'files.content.read files.content.write sharing.write',
            ]
        );
        return response()->json(['dropboxAuthUrl' => $authorizationUrl], 200);
    }

    public function getAllFilesFromDropbox($data = '', $retryCount = 0)
    {
        if ((isset($data['name']) && $data['name'] == "Home") || (isset($data['breadcrumbName'])  && $data['breadcrumbName'] == "Home")) {

            $path = '';
        } else {
            $path = $data['path'] ?? '';
        }

        try {
            // Retrieve the access token from the database
            $dropboxAccessToken = auth()->user()->fresh()->dropbox_access_token;
            if (!$dropboxAccessToken) return [];
            // Update configuration
            $dropboxConfig['authorization_token'] = $dropboxAccessToken;
            config(['filesystems.disks.dropbox.authorization_token' => $dropboxAccessToken]);

            // Extend Storage Disk
            Storage::extend('dropbox', function (Application $app, array $dropboxConfig) {
                $adapter = new DropboxAdapter(new DropboxClient(
                    $dropboxConfig['authorization_token']
                ));
                return new FilesystemAdapter(
                    new Filesystem($adapter, $dropboxConfig),
                    $adapter,
                    $dropboxConfig
                );
            });
            // Get disk and files
            $disk = Storage::disk('dropbox');
            if ($path === '') {
                $this->createFolder($this->validatePath($path, request()->project), $dropboxAccessToken);
            }
            $duplicatePath = $this->validatePath($path, request()->project) . ' (1)';
            $foldersAndFilesDuplication = $this->getAllFoldersAndFiles($disk, $duplicatePath);

            // $foldersAndFiles = $this->getAllFoldersAndFiles($disk, $this->validatePath($path, request()->project));

            // dd($foldersAndFilesDuplication,$foldersAndFiles);
            $foldersAndFiles = $this->getSharedFolderContents($disk, $path);
            return $foldersAndFiles;
        } catch (Exception $exception) {
            Log::error('Error in getAllFilesFromDropbox in Project Service class: ' . $exception->getMessage());
            if (strpos($exception->getMessage(), 'expired_access_token') !== false) {
                // Avoid infinite loop by limiting the number of retries
                if ($retryCount < 1) {
                    $this->refreshDropboxAccessToken();
                    header('Location: ' . tenant_route('projects.files', ['project' => request()->project]));
                    exit;
                } else {
                    Log::error('Max retry attempts reached for refreshing Dropbox access token.');
                    throw new Exception('Max retry attempts reached for refreshing Dropbox access token.');
                }
            } else if (strpos($exception->getMessage(), 'invalid_access_token') !== false) {
                $exception = 'invalid access token';
            }
            if(auth()->user()->dropbox_access_token != ''){
                $this->refreshDropboxAccessToken();
                header('Location: ' . tenant_route('projects.files', ['project' => request()->project]));
                exit;
            }
            throw new Exception($exception);
        }
    }
    private function getAllFoldersAndFiles($disk, $path)
    {
        $contentList = [];
        $listContentsResponse = $disk->listContents($path, false); // true for recursive
        foreach ($listContentsResponse as $contentIndex => $content) {
            $contentList[$contentIndex]['type'] = $content['type'];
            $contentList[$contentIndex]['path'] = $content['path'];
            if ($content['type'] == 'file') {
                $contentList[$contentIndex]['fileSize'] = $content['fileSize'];
                $contentList[$contentIndex]['mimeType'] = $content['mimeType'];
                $contentList[$contentIndex]['link'] = $this->getSharedLink($content['path']);
            }
        }
        return $contentList;
    }
    private function getSharedFolderContents($disk, $basePath)
    {
        $contentList = [];
        $basePath = $this->validatePath($basePath, request()->project);

        // Extract parent directory and folder name
        $parentDir = dirname($basePath);
        $folderName = basename($basePath);

        // List all folders in the parent directory
        $listContentsResponse = $disk->listContents($parentDir, false);

        // Match base folder and duplicates like 'folderName (1)', 'folderName (2)', etc.
        foreach ($listContentsResponse as $content) {
            if (
                $content['type'] === 'dir' &&
                (
                    basename($content['path']) === $folderName ||
                    preg_match('/^' . preg_quote($folderName, '/') . ' \(\d+\)$/', basename($content['path']))
                )
            ) {
                // Fetch contents of the matching folder
                $folderContents = $disk->listContents($content['path'], false);

                foreach ($folderContents as $fileOrFolder) {
                    $contentList[] = [
                        'type' => $fileOrFolder['type'],
                        'path' => $fileOrFolder['path'],
                        'fileSize' => $fileOrFolder['type'] === 'file' ? $fileOrFolder['fileSize'] : null,
                        'mimeType' => $fileOrFolder['type'] === 'file' ? $fileOrFolder['mimeType'] : null,
                        'link' => $fileOrFolder['type'] === 'file' ? $this->getSharedLink($fileOrFolder['path']) : null,
                    ];
                }
            }
        }

        return $contentList;
    }


    public function handleDropboxCallback($request)
    {
        DB::beginTransaction();
        try {
            $state = json_decode($request->input('state'));
            $subdomain = $state?->subdomain;
            $projectUuid = $state?->project_uuid;
            $userUUid = $state?->uuid;
            $accessTokenResponse = $this->dropboxProvider->getAccessToken('authorization_code', [
                'code' => $request->input('code'),
            ]);
            $this->addDropboxResponseToUserTable($userUUid, $accessTokenResponse);
        } catch (Exception $e) {
            DB::rollback();
            throw new Exception($e);
        }
        DB::commit();
        return redirect(route('tenant.projects.files', ['subdomain' => $subdomain, 'project' => $projectUuid]));
    }

    private function refreshDropboxAccessToken()
    {
        Log::info('Refreshing Dropbox access token');

        try {
            $dropboxRefreshToken = auth()->user()->dropbox_refresh_token;

            $response = Http::asForm()->post('https://api.dropbox.com/oauth2/token', [
                'grant_type' => 'refresh_token',
                'refresh_token' => $dropboxRefreshToken,
                'client_id' => config('filesystems.disks.dropbox.key'),
                'client_secret' => config('filesystems.disks.dropbox.secret'),
            ]);

            $body = $response->json();

            Log::info(['dropbox_refresh_access_token_response' => $body]);

            if ($response->successful()) {
                $accessToken = $body['access_token'];
                User::where('id', auth()->id())->update([
                    'dropbox_access_token' => $accessToken
                ]);
                Log::info('Updated access token for user: ' . auth()->user()->uuid);
                return $accessToken;
            } else {
                $error = $body['error_description'] ?? 'Unknown error';
                throw new Exception($error);
            }
        } catch (Exception $e) {
            Log::error('Failed to refresh Dropbox access token: ' . $e->getMessage());
            throw $e;
        }
    }


    private function addDropboxResponseToUserTable($userUUid, $accessTokenResponse)
    {
        $accessToken = $accessTokenResponse->getToken();
        $refreshToken = $accessTokenResponse->getRefreshToken();

        User::where('uuid', $userUUid)->update([
            'dropbox_access_token' => $accessToken,
            'dropbox_refresh_token' => $refreshToken,
        ]);
        Log::info([
            'dropbox_access_token_response' => $accessToken,
        ]);
    }

    public function deleteFromDropbox(array $data)
    {
        try {

            $dropboxAccessToken = auth()->user()->fresh()->dropbox_access_token;

            // Update configuration
            $dropboxConfig['authorization_token'] = $dropboxAccessToken;
            config(['filesystems.disks.dropbox.authorization_token' => $dropboxAccessToken]);

            // Extend Storage Disk
            Storage::extend('dropbox', function (Application $app, array $dropboxConfig) {
                $adapter = new DropboxAdapter(new DropboxClient(
                    $dropboxConfig['authorization_token']
                ));
                return new FilesystemAdapter(
                    new Filesystem($adapter, $dropboxConfig),
                    $adapter,
                    $dropboxConfig
                );
            });

            // Get disk and files
            $dropbox = Storage::disk('dropbox');


            if ($dropbox->exists($data['content']['path'])) {
                if ($data['content']['type'] === 'file') $dropbox->delete($data['content']['path']);
                else if ($data['content']['type'] === 'dir') $dropbox->deleteDirectory($data['content']['path']);
            }
        } catch (Exception $e) {
            Log::error('Failed to delete file or folder from Dropbox: ' . $e->getMessage());
            throw $e;
        }
        return response()->json(['message' => 'Content is deleted successfully.'], 200);
    }

    private function getDropboxFileTemporaryUrl($path)
    {
        try {
            $dropboxAccessToken = auth()->user()->dropbox_access_token;
            // Send the request to Dropbox API for listing files in the user's Dropbox
            $url = 'https://api.dropboxapi.com/2/files/get_temporary_link';
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $dropboxAccessToken,
                'Content-Type' => 'application/json',
            ])->post($url, [
                'path' => '/' . $path,
            ]);
            $body = $response->json();
            if ($response->successful()) {
                // Handle the response here (for example, map files to their URLs)
                return $body['link'];
            } else if ($response->failed()) {
                Log::error('Dropbox API error:', [$response->status(), $response->body()]);
                // Handle the error or throw an exception
                throw new Exception($response->body());
            }
        } catch (Exception $e) {
            // Handle exceptions
            Log::error(['Dropbox API error:' => $e->getMessage()]);
            throw $e;
        }
    }

    private function getSharedLink($filePath)
    {
        try {
            $dropboxAccessToken = auth()->user()->dropbox_access_token;

            // Check if the shared link already exists
            $existingLinkResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . $dropboxAccessToken,
                'Content-Type' => 'application/json',
            ])->post('https://api.dropboxapi.com/2/sharing/list_shared_links', [
                'path' => '/' . $filePath,
                'direct_only' => true, // Only list direct shared links for this file
            ]);

            $existingLinks = $existingLinkResponse->json();
            if (!empty($existingLinks['links'])) {
                // Shared link already exists, return the first one
                return str_replace('dl=0', 'raw=1', $existingLinks['links'][0]['url']);
            }

            // If no shared link exists, create a new one
            $createLinkResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . $dropboxAccessToken,
                'Content-Type' => 'application/json',
            ])->post('https://api.dropboxapi.com/2/sharing/create_shared_link_with_settings', [
                'path' => '/' . $filePath,
            ]);
            if ($createLinkResponse->successful()) {
                return str_replace('dl=0', 'raw=1', $createLinkResponse->json()['url']);
            }

            // Handle error if creating a new link fails
            throw new Exception('Failed to create or retrieve shared link.');
        } catch (Exception $e) {
            // Handle any errors
            throw $e;
        }
    }

    private function createFolder($path, $accessToken, $projectName = '')
    {
        Log::info('Creating folder at dropbox');
        try {
            $response = $this->checkIfFolderOrFileExists($path, $accessToken);
            if ($response['status'] == 'exists') return $response;


            $response = Http::withToken($accessToken)
                ->post('https://api.dropboxapi.com/2/files/create_folder_v2', [
                    'autorename' => false,
                    'path' => $path,
                ]);
            $body = $response->json();
            Log::info(['create folder at dropbox response: ' => $body]);
            if ($response->successful()) {
                return [...$body['metadata'], 'status' => 'success'];
            } else {
                $error = $body['error_description'] ?? 'Unknown error';
                throw new Exception($error);
            }
        } catch (Exception $e) {
            Log::error('Failed to create folder at dropbox: ' . $e->getMessage());
            throw $e;
        }
    }

    private function checkIfFolderOrFileExists($path, $accessToken)
    {
        try {
            $checkResponse = Http::withToken($accessToken)
                ->post('https://api.dropboxapi.com/2/files/get_metadata', [
                    'path' => $path
                ]);
            if ($checkResponse->successful()) {
                // Folder exists, return the metadata
                $metadata = $checkResponse->json();
                return ['status' => 'exists', 'type' => $metadata['.tag'], 'metadata' => $metadata];
            } else {
                $error = $checkResponse->json();
                if (isset($error['error_summary']) && $error['error_summary'] === 'expired_access_token/') {
                    $this->refreshDropboxAccessToken();
                    header('Location: ' . tenant_route('projects.files', ['project' => request()->project]));
                } else {
                    return [
                        'status' => 'not-exists',
                        'exception' => $error['error_summary'] ?? "Error: Unable to determine the error."
                    ];
                }
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    private function validatePath($path, $projectUuid)
    {
        $projectName = getProjectName($projectUuid ?? request()->projectUuid);

        $pathN = substr($projectUuid, 0, 6);
        $pathN = '/' . $projectName . '-' . $pathN;
        $path =  $path === '' ?  $pathN :  $path;
        return substr($path, 0, 1) !== '/' ? '/' . $path : $path;
    }

    public function createDropboxFolder(string $projectUuid, array $data)
    {
        try {
            if (empty($data['folderPath']) || empty($data['folder'])) {
                throw new Exception('Folder path or folder name is missing.');
            }
            if (isset($data['breadcrumbName']) && $data['breadcrumbName'] == 'Home') {
                $parentFodler = $this->validatePath('', request()->project);
                $path = $parentFodler . '/' . $data['folder'];
            } else {
                $path = $data['folderPath'] . '/' . $data['folder'];
            }
            $createFolderResponse = $this->createFolder($path, auth()->user()->dropbox_access_token);
            if ($createFolderResponse['status'] === 'exists') {
                return response()->json(['message' => 'Folder already exists.'], 400);
            }
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
        return response()->json(['message' => 'Folder is created successfully.', 'data' => $createFolderResponse], 200);
    }

    public function uploadFiles(string $projectUuid, array $data)
    {
        try {
            $dropboxAccessToken = auth()->user()->fresh()->dropbox_access_token;
            if (!$dropboxAccessToken) return [];
            // Update configuration
            $dropboxConfig['authorization_token'] = $dropboxAccessToken;
            config(['filesystems.disks.dropbox.authorization_token' => $dropboxAccessToken]);

            // Extend Storage Disk
            Storage::extend('dropbox', function (Application $app, array $dropboxConfig) {
                $adapter = new DropboxAdapter(new DropboxClient(
                    $dropboxConfig['authorization_token']
                ));
                return new FilesystemAdapter(
                    new Filesystem($adapter, $dropboxConfig),
                    $adapter,
                    $dropboxConfig
                );
            });
            $files = $data['files'];
            if (!$files || !is_array($files)) {
                return response()->json(['error' => 'No files uploaded'], 400);
            }
            $response = [];
            foreach ($files as $file) {
                if (isset($data['breadcrumbName']) && $data['breadcrumbName'] == 'Home') {
                    $parentFodler = $this->validatePath('', request()->project);
                    $filePath = $parentFodler . '/' . $file->getClientOriginalName();
                } else {
                    $filePath = $data['path'] . '/' . $file->getClientOriginalName();
                }

                $fileContent = file_get_contents($file->getRealPath());
                if ($fileContent === false) {
                    throw new \Exception('Unable to read file content');
                }
                // Upload to Dropbox
                Storage::disk('dropbox')->put($filePath, $fileContent);
                $response[] = ['file' => $file->getClientOriginalName()];
                $content = $this->getAllFilesFromDropbox($data);
            }
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
        return response()->json([
            'message' => count($response) > 1 ? 'Files are' : 'File is' . ' uploaded successfully.',
            'data' => $response,
            'content' => $content,
        ], 200);
    }

    public function disconnectDropbox()
    {
        try {
            User::where('id', auth()->id())->update([
                'dropbox_access_token' => null,
                'dropbox_refresh_token' => null,
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage()
            ], 500);
        }
        return response()->json([
            'message' => 'Dropbox is disconnected successfully.',
        ], 200);
    }
    public function shareDropboxFolder(string $projectUuid, array $data)
    {
        $data = $data['data'] ?? '';
        if (empty($data['selectedParticipants'])) {
            throw new Exception('Selected Participants is missing.');
        }
        // if (isset($data['breadcrumbName']) && $data['breadcrumbName'] == 'Home') {

        $parentFodler = $this->validatePath('', request()->project);
        // }

        $dropboxAccessToken = auth()->user()->dropbox_access_token;
        foreach ($data['selectedParticipants'] as $key => $value) {
            $this->shareAndAddMember($parentFodler, $value['email'], $dropboxAccessToken);
        }

        //     $shareResponse = Http::withHeaders([
        //         'Authorization' => 'Bearer ' . $dropboxAccessToken,
        //         'Content-Type' => 'application/json',
        //     ])->post('https://api.dropboxapi.com/2/sharing/share_folder', [
        //         'path' => $parentFodler,
        //         'acl_update_policy' => 'editors',
        //         'shared_link_policy' => 'anyone',
        //         'force_async' => false,
        //     ]);
        //     if (!$shareResponse->successful()) {
        //         $alreadyShared = $shareResponse->json();
        //         $listFoldersResponse = Http::withHeaders([
        //             'Authorization' => 'Bearer ' . $dropboxAccessToken,
        //             'Content-Type' => 'application/json',
        //         ])->post('https://api.dropboxapi.com/2/sharing/list_folders');

        //         if (!$listFoldersResponse->successful()) {
        //             throw new Exception("Failed to list shared folders: " . $listFoldersResponse->body());
        //         }

        //         $folders = $listFoldersResponse->json()['entries'];
        //         throw new Exception("Failed to share folder: " . $shareResponse->body());
        //     }
        //     dd($shareResponse->json());

        //     $sharedFolderId = $shareResponse->json();
        //     dd($sharedFolderId);
        //     $existingLinkResponse = Http::withHeaders([
        //         'Authorization' => 'Bearer ' . $dropboxAccessToken,
        //         'Content-Type' => 'application/json',
        //     ])->post('https://api.dropboxapi.com/2/sharing/list_folders', [
        //         'limit' => 100,
        //     ]);
        //     dd($existingLinkResponse->json());
        //     // dd($existingLinkResponse->json());
        //     // Check if the shared link already exists
        //     $response = Http::withHeaders([
        //         'Authorization' => 'Bearer ' . $dropboxAccessToken,
        //         'Content-Type' => 'application/json',
        //     ])->post('https://api.dropboxapi.com/2/sharing/list_folders', []);
        //         dd($response);
        //     if (!$response->successful()) {
        //         throw new Exception('Failed to list shared folders: ' . $response->body());
        //     }

        //     $folders = $response->json()['entries'] ?? [];

        // } catch (Exception $e) {
        //     return response()->json([
        //         'message' => $e->getMessage()
        //     ], 500);
        // }
        // return response()->json(['message' => 'Folder is created successfully.', 'data' => $createFolderResponse], 200);
    }
    function shareAndAddMember($path, $email, $accessToken)
    {
        $existingLinkResponse = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type' => 'application/json',
        ])->post('https://api.dropboxapi.com/2/sharing/list_folders', [
            'limit' => 100,
        ]);
        if (!$existingLinkResponse->successful()) {
            throw new Exception("Failed to list shared folders: " . $existingLinkResponse->body());
        }

        $folders = $existingLinkResponse->json()['entries'];
        $sharedFolderId = null;

        // Find if the folder is already shared
        foreach ($folders as $folder) {
            if (isset($folder['path_lower']) && $folder['path_lower'] === strtolower($path)) {

                $sharedFolderId = $folder['shared_folder_id'];
                break;
            }
        }
        if (!$sharedFolderId) {

            $shareResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->post('https://api.dropboxapi.com/2/sharing/share_folder', [
                'path' => $path,
                'acl_update_policy' => 'editors',
                'shared_link_policy' => 'anyone',
                'force_async' => false,
            ]);

            if (!$shareResponse->successful()) {
                throw new Exception("Failed to share folder: " . $shareResponse->body());
            }

            $sharedFolderId = $shareResponse->json()['shared_folder_id'];
        }
        $addMemberResponse = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type' => 'application/json',
        ])->post('https://api.dropboxapi.com/2/sharing/add_folder_member', [
            'shared_folder_id' => $sharedFolderId,
            'quiet' => false,
            'members' => [
                [
                    'member' => [
                        '.tag' => 'email',
                        'email' => $email,
                    ],
                    'access_level' => 'viewer', // Change as needed
                ],
            ],
            'custom_message' => 'You have been granted access to this folder.',
        ]);

        if (!$addMemberResponse->successful()) {
            throw new Exception("Failed to add member: " . $addMemberResponse->body());
        }
        return response()->json([
            'message' => 'User added successfully to the shared folder.',
        ], 200);
    }
}
