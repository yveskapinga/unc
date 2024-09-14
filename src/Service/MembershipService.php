<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Membership;
use App\Service\InterfederationService;
use App\Service\NotificationService;
use App\Service\SecurityService;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Doctrine\ORM\EntityManagerInterface;



class MembershipService
{

    public function __construct(
        private InterfederationService $interfederationService, 
        private NotificationService $notificationService,
        private SecurityService $securityService,
        private EntityManagerInterface $entityManager,
        private UrlGeneratorInterface $router,
        )
    {
    }
    
        // Approuve l'adhésion d'un utilisateur
    public function approveMembership(Membership $membership)
    {
        $this->entityManager->persist($membership);
        $this->entityManager->flush();
    }
    
        // Obtient l'interfédération d'un utilisateur
    public function getInterfederationByUser(User $user)
    {
        $province = $user->getAddress()->getProvince();
        return $this->interfederationService->getInterfederationByProvince($province);
    }

    // Approuve l'adhésion d'un utilisateur
    // public function approveMembership(User $user)
    // {
    //     $this->securityService->isAdmin();
    //     $province = $user->getAddress()->getProvince();
    //     $interfederation = $this->interfederationService->getInterfederationByProvince($province);

    //     if ($interfederation) {
    //         $membership = new Membership();
    //         $membership->setTheUser($user);
    //         $membership->setInterfederation($interfederation);
    //         // Enregistrez le membership dans la base de données

    //         // Envoyer une notification interne à l'administrateur
    //         $admin = $interfederation->getSif()->getTheUser();
    //         $message = sprintf(
    //             'Une nouvelle demande d\'adhésion a été soumise pour votre interfédération. <a href="%s">Valider la demande</a>',
    //             $this->generateValidationLink($user)
    //         );
    //         $this->notificationService->createNotification($admin, "demande d'adhésion", $message);
    //     }
    // }

    // Génère le lien de validation pour l'adhésion
    private function generateValidationLink(User $user): string
    {
        return $this->router->generate('validate_membership', ['userId' => $user->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
    }
}
