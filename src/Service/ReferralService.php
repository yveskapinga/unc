<?php

namespace App\Service;

use App\Entity\User;
use App\Service\SecurityService;
use App\Repository\MessageRepository;
use App\Repository\MembershipRepository;
use App\Repository\NotificationRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use function PHPUnit\Framework\returnValue;

class ReferralService
{
    public function __construct(
        private UrlGeneratorInterface $router,
        private UserRepository $userRepository,
        private EntityManagerInterface $em,
    )
    {
        
    }

    public function generateReferralLink(User $user) : string{
        $referralCode = rawurlencode($user->getReferralCode());

        return $this->router->generate('app_register', [
            'ref'=>$referralCode
        ], 
        UrlGeneratorInterface::ABSOLUTE_URL
    );
    }

    public function generateReferralCode($length = 15): string
    {
        do {
            $code = substr(str_shuffle(str_repeat($x='&)_-("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
            $existingUser = $this->userRepository->findOneBy(['referralCode' => $code]);
        } while ($existingUser);

        return $code;
    }
}