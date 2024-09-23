<?php

namespace App\EventListener;

use App\Entity\User;
use App\Entity\Interfederation;
use App\Event\UserRegisteredEvent;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UserRegisteredListener
{
    private NotificationService $notificationService;
    private EntityManagerInterface $entityManager;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(NotificationService $notificationService, EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator)
    {
        $this->notificationService = $notificationService;
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
    }

    public function onUserRegistered(UserRegisteredEvent $event)
    {
        $user = $event->getUser();
        
        $province = $user->getAddress()->getProvince();

        $interfederation = $this->entityManager->getRepository(Interfederation::class)->findOneBy(['designation' => $province]);

        $administrators = $this->entityManager->getRepository(User::class)->findAdministratorsByInterfederation($interfederation);

        $superAdmins = $this->entityManager->getRepository(User::class)->findUsersByRole('ROLE_SUPER_ADMIN');

        $link = $this->urlGenerator->generate('app_membership_new', ['id' => $user->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        $message = 'Un nouvel utilisateur a demandé à rejoindre votre interfédération. <a href="' . $link . '" class = "btn btn-primary">Cliquez ici pour valider</a>';

        // $this->notificationService->notificate($administrators, 'Nouvelle demande d\'adhésion', 'Un nouvel utilisateur a demandé à rejoindre votre interfédération. <a href="' . $link . '">Cliquez ici pour valider</a>.');

        foreach ($administrators as $admin) {
            $this->notificationService->createNotification($admin, 'Nouvelle demande d\'adhésion', $message);
        }

        foreach ($superAdmins as $superAdmin) {
            $this->notificationService->createNotification($superAdmin, 'Nouvelle demande d\'adhésion', $message);
        }
    }
}


