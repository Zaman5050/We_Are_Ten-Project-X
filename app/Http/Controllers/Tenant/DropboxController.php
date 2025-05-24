<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Interfaces\ServiceInterfaces\Tenant\DropboxServiceInterface;

class DropboxController extends Controller
{

    private $service;

    public function __construct(DropboxServiceInterface $dropboxService)
    {
        $this->service = $dropboxService;
    }

    public function getDropboxAuthUrl(Request $request)
    {
        return $this->service->getDropboxAuthUrl($request);
    }

    public function handleDropboxCallback(Request $request)
    {
        return $this->service->handleDropboxCallback($request);
    }

    public function deleteFromDropbox(Request $request)
    {
        return $this->service->deleteFromDropbox($request->input());
    }

    public function createFolder(Request $request)
    {
        return $this->service->createDropboxFolder($request->project,$request->all());
    }
    public function uploadFiles(Request $request)
    {
        return $this->service->uploadFiles($request->project,$request->all());
    }

    public function disconnectDropbox(Request $request)
    {
        return $this->service->disconnectDropbox();
    }
    public function shareFolder(Request $request)
    {
        return $this->service->shareDropboxFolder($request->project,$request->all());
    }

}
