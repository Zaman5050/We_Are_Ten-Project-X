<?php

namespace App\Services;

use App\Interfaces\RepositoryInterfaces\ChatRepositoryInterface;
use App\Interfaces\ServiceInterfaces\ChatServiceInterface;

class ChatService implements ChatServiceInterface
{
    protected $repository;

    public function __construct(ChatRepositoryInterface $ChatRepositoryInterface)
    {
        $this->repository = $ChatRepositoryInterface;
    }

    public function findChat($uuid)
    {
        return $this->repository->findChat($uuid);
    }

    public function createProjectChat($project)
    {
        return $this->repository->createProjectChat($project);
    }

    public function addParticipant($request)
    {
        return $this->repository->addParticipant($request);
    }


    public function createProjectGroupChat($data, $project)
    {
        return $this->repository->createProjectGroupChat($data, $project);
    }

    public function getProjectChats($projectsIds)
    {
        return $this->repository->getProjectChats($projectsIds);
    }

    public function sendMessage($request)
    {
        return $this->repository->sendMessage($request);
    }

    public function getMessages($request)
    {
        return $this->repository->getMessages($request);
    }

    public function markSeenMessages($request)
    {
        return $this->repository->markSeenMessages($request);
    }

    public function getCompanyChats($companyId)
    {
        return $this->repository->getCompanyChats($companyId);
    }
    public function createDirectChat($request)
    {
        return $this->repository->createDirectChat($request);
    }
    public function directChats()
    {
        return $this->repository->directChats();
    }
}
