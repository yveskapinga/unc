<?php


// src/EventSubscriber/UserSubscriber.php
namespace App\EventSubscriber;

use App\Entity\User;
use App\Service\ReferralService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class UserSubscriber implements EventSubscriberInterface
{
    private $entityManager;
    private $referralService;

    public function __construct(
        EntityManagerInterface $entityManager, 
        ReferralService $referralService,
        
        )
    {
        $this->entityManager = $entityManager;
        $this->referralService = $referralService;
    }

    public static function getSubscribedEvents()
    {
        return [
            InteractiveLoginEvent::class => 'onUserRegister',
        ];
    }

    public function onUserRegister(InteractiveLoginEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();

        if ($user instanceof User) {
            // Génère un code de parrainage unique pour le nouvel utilisateur
            $user->setReferralCode($this->referralService->generateReferralLink($user));

            // Vérifie le code de parrainage
            $referralCode = $user->getReferralCode();
            if ($referralCode) {
                $referrer = $this->entityManager->getRepository(User::class)->findOneBy(['referralCode' => $referralCode]);
                if ($referrer) {
                    $referrer->addReferrer($user);
                    $user->addReferredBy($referrer);
                    $this->entityManager->persist($referrer);
                }
            }

            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }
    }
}
