<?php

// src/Service/TopicService.php
namespace App\Service;

use App\Repository\TopicRepository;

class TopicService
{
    
    public function __construct(private TopicRepository $topicRepository)
    {
    }

    public function getTopCommentedTopics()
    {
        return $this->topicRepository->findTopCommentedTopics();
    }
}
