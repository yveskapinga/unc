<?php

// src/Controller/MapController.php
namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MapController extends AbstractController
{
    #[Route('/map', name: 'map')]
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAllWithCoordinates();

        return $this->render('map/index.html.twig', [
            'users' => $users,
        ]);
    }
}

