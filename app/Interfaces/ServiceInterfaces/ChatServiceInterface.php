<?php

namespace App\Interfaces\ServiceInterfaces;


interface ChatServiceInterface
{
    public function findChat($uuid);
    public function getProjectChats($projectsIds);
    public function createProjectGroupChat(array $data, $project);
    public function createProjectChat($project);
    public function addParticipant($request);
    public function sendMessage($request);
    public function getMessages($request);
    public function markSeenMessages($request);
    public function getCompanyChats($companyId);
    public function createDirectChat($request);
    public function directChats();
}
