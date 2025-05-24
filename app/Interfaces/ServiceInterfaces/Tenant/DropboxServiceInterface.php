<?php

namespace App\Interfaces\ServiceInterfaces\Tenant;


interface DropboxServiceInterface
{
    public function getAllFilesFromDropbox($path);
    public function getDropboxAuthUrl($request);
    public function handleDropboxCallback($request);
    public function deleteFromDropbox(array $data);
    public function createDropboxFolder(string $projectUuid, array $data);
    public function uploadFiles(string $projectUuid, array $data);
    public function disconnectDropbox();
    public function shareDropboxFolder(string $projectUuid, array $data);

}
