<?php

namespace App\Services\Tenant;

use App\Interfaces\ServiceInterfaces\Tenant\FileHandlingServiceInterface;
use App\Traits\FileHandler;
use Illuminate\Support\Facades\Storage;
use Exception;

class FileHandlingService implements FileHandlingServiceInterface
{
    use FileHandler;

    public function uploadAttachments(array $data)
    {
        $attachmentResponse = [];
        foreach ($data['attachments'] as $attachment) {

            $path = $this->storeMediaByHashName($attachment, $data['sub_path']);

            $attachmentResponse[] = [
                'original_name' => $attachment->getClientOriginalName(),
                'url' => Storage::url($path), // Save the storage path
                'extension' => $attachment->getClientOriginalExtension(),
                'size' => $attachment->getSize(),
            ];
        }

        return response()->json(['attachments' => $attachmentResponse], 200);
    }

    public function deleteAttachment(array $data)
    {
        $baseUrl = config('filesystems.disks.s3.url');
        // Remove the base URL to get the file path
        $imagePath = str_replace($baseUrl . '/', '', $data['attachment_url']);
        if ($this->mediaExists($imagePath)) {
            if ($this->deleteMedia($imagePath)) {
                return response()->json(['message' => 'File deleted successfully']);
            } else {
                return response()->json(['error' => 'Unable to delete the file'], 500);
            }
        }
        return response()->json(['message' => "File doens't exist successfully"]);
    }
}
