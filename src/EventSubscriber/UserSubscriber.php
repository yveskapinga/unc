<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Doctrine\ORM\Events;
use App\Service\ReferralService;
use App\Service\NotificationService;
use Doctrine\Common\EventSubscriber;
use App\Service\InterfederationService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class UserSubscriber implements EventSubscriberInterface
{
    private $entityManager;
    private $referralService;
    private $interfederationService;
    private $notificationService;
    private $router;

    public function __construct(
        EntityManagerInterface $entityManager, 
        ReferralService $referralService,
        InterfederationService $interfederationService,
        NotificationService $notificationService,
        UrlGeneratorInterface $router
    ) {
        $this->entityManager = $entityManager;
        $this->referralService = $referralService;
        $this->interfederationService = $interfederationService;
        $this->notificationService = $notificationService;
        $this->router = $router;
    }

    public static function getSubscribedEvents()
    {
        return [
            // InteractiveLoginEvent::class => 'onUserRegister',
            // Events::postPersist,
        ];
    }

    // public function onUserRegister(InteractiveLoginEvent $event)
    // {
    //     $user = $event->getAuthenticationToken()->getUser();

    //     if ($user instanceof User) {
    //         // Génère un code de parrainage unique pour le nouvel utilisateur
    //         $user->setReferralCode($this->referralService->generateReferralLink($user));

    //         // Vérifie le code de parrainage
    //         $referralCode = $user->getReferralCode();
    //         if ($referralCode) {
    //             $referrer = $this->entityManager->getRepository(User::class)->findOneBy(['referralCode' => $referralCode]);
    //             if ($referrer) {
    //                 $referrer->addReferrer($user);
    //                 $user->addReferredBy($referrer);
    //                 $this->entityManager->persist($referrer);
    //             }
    //         }

    //         $this->entityManager->persist($user);
    //         $this->entityManager->flush();
    //     }
    // }

    // public function postPersist(PostPersistEventArgs $args)
    // {
    //     $entity = $args->getObject();

    //     // Vérifie si l'entité est un utilisateur
    //     if (!$entity instanceof User) {
    //         return;
    //     }

    //     $this->handleNewUser($entity);
    // }

    // // Gère la logique pour un nouvel utilisateur
    // private function handleNewUser(User $user)
    // {
    //     $province = $user->getAddress()->getProvince();
    //     $interfederation = $this->interfederationService->getInterfederationByProvince($province);

    //     if ($interfederation) {
    //         $admin = $interfederation->getSif()->getTheUser();
    //         $message = sprintf(
    //             'Une nouvelle demande d\'adhésion a été soumise pour votre interfédération. <a href="%s">Valider la demande</a>',
    //             $this->generateValidationLink($user)
    //         );
    //         $this->notificationService->createNotification($admin, "demande d'adhésion", $message);
    //     }
    // }

    // // Génère le lien de validation pour l'adhésion
    // private function generateValidationLink(User $user): string
    // {
    //     return $this->router->generate('validate_membership', ['userId' => $user->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
    // }
}
