<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait FileHandler
{
    protected string $disk = 's3';

    public function storeMediaByHashName($mediaResource, $path = 'media-attachments')
    {
        try {

            return Storage::disk($this->disk)->putFile($path, $mediaResource);

        } catch (\Exception $exception) {
            throw new \Exception('Unable to store media', 400);
        }
    }

    public function storeMedia($media_resource, ?string $path = 'media/', $name)
    {
        try {
            $returnable_path = $path . $name;
            Storage::disk($this->disk)->put($returnable_path, $media_resource, 'public');
            return $returnable_path;

        } catch (\Exception $exception) {
            throw new \Exception('Unable to store media', 400);
        }
    }

    public function deleteMedia($path)
    {
        $is_deleted = Storage::disk($this->disk)->delete($path);

        if (!$is_deleted) {
            return false;
        }

        return true;
    }

    protected function mediaExists($path)
    {
        return Storage::disk($this->disk)->exists($path);
    }

    protected function mediaUrl($path)
    {
        if ($this->mediaExists($path)) {
            $path = Storage::disk($this->disk)->url($path);
        }

        return $path;
    }
}
