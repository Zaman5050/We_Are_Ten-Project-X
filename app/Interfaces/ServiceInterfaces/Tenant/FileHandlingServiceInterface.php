<?php

namespace App\Interfaces\ServiceInterfaces\Tenant;


interface FileHandlingServiceInterface
{
    public function uploadAttachments(array $data);
    public function deleteAttachment(array $data);
}
