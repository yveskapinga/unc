<?php

// src/Service/TopicService.php
namespace App\Service;

use App\Repository\TopicRepository;
use App\Repository\UserRepository;


class TopicService
{
    
    public function __construct(
        private TopicRepository $topicRepository,
        private UserRepository $userRepository 
        )
    {
    }

    public function getTopCommentedTopics()
    {
        return $this->topicRepository->findTopCommentedTopics();
    }

    public function getBestAuthorData()
    {
        $bestAuthorData = $this->topicRepository->findBestAuthor();
            // Récupérer l'utilisateur à partir de l'ID
            $bestAuthorData = $this->topicRepository->findBestAuthor();
            $user = $this->userRepository->find($bestAuthorData['id']) ?? null;
            $articleCount = $bestAuthorData['articleCount'] ?? null;
            $commentCount = $bestAuthorData['commentCount'] ?? null;
        
        return [
            'topics'=>$this->topicRepository->findAll(),
            'bestAuthor' => $user,
            'articleCount' => $articleCount,
            'commentCount' => $commentCount,
        ];
    }
}
