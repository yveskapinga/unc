<?php

// src/Controller/HomeController.php
namespace App\Controller;

use App\Repository\EventRepository;
use App\Repository\TopicRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(EventRepository $eventRepository, TopicRepository $topicRepository): Response
    {
        $events = $eventRepository->findAll();
        $topics = $topicRepository->findAll();

        return $this->render('home/index.html.twig', [
            'events' => $events,
            'topics' => $topics,
        ]);
    }

    #[Route('/index', name: 'app_index')]
    public function app(EventRepository $eventRepository, TopicRepository $topicRepository): Response
    {
        $events = $eventRepository->findAll();
        $topics = $topicRepository->findAll();

        return $this->render('index.html.twig', [
            'events' => $events,
            'topics' => $topics,
        ]);
    }
}

