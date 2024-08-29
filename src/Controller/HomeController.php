<?php

// src/Controller/HomeController.php
namespace App\Controller;

use App\Repository\EventRepository;
use App\Repository\TopicRepository;
use App\Repository\MembershipRepository;
use App\Repository\NotificationRepository;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


class HomeController extends AbstractController
{
        public function __construct(
        private Security $security,
        private EventRepository $eventRepo,
        private TopicRepository $topicRepo,
        private NotificationRepository $notificationRepo,
        private MembershipRepository $memberRepo,

    ){
    }
    #[Route('/', name: 'app_home')]
    public function index(EventRepository $eventRepository, TopicRepository $topicRepository): Response
    {
        $events = $eventRepository->findAll();
        $topics = $topicRepository->findAll();

        return $this->render('page/index.html.twig', [
            'events' => $events,
            'topics' => $topics,
        ]);
    }

    


}

