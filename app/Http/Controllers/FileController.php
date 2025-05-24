<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Interfaces\ServiceInterfaces\Tenant\FileHandlingServiceInterface;

class FileController extends Controller
{

    protected $fileHandlingService;

    public function __construct(
        FileHandlingServiceInterface $fileHandlingService
    ) {
        $this->fileHandlingService = $fileHandlingService;
    }

    public function upload(Request $request)
    {
        return $this->fileHandlingService->uploadAttachments($request->all());
    }

    public function delete(Request $request)
    {
        return $this->fileHandlingService->deleteAttachment($request->all());
    }
}
