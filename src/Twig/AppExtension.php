<?php

// src/Twig/AppExtension.php
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Symfony\Component\Security\Core\Security;
use App\Repository\MembershipRepository;

class AppExtension extends AbstractExtension
{
    private $security;
    private $membershipRepository;

    public function __construct(Security $security, MembershipRepository $membershipRepository)
    {
        $this->security = $security;
        $this->membershipRepository = $membershipRepository;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('time_diff', [$this, 'timeDiff']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_user_level', [$this, 'getUserLevel']),
        ];
    }

    public function timeDiff(\DateTimeInterface $dateTime): string
    {
        $now = new \DateTime();
        $interval = $now->diff($dateTime);

        if ($interval->y > 0) {
            return $interval->y . ' an' . ($interval->y > 1 ? 's' : '');
        } elseif ($interval->m > 0) {
            return $interval->m . ' mois';
        } elseif ($interval->d > 0) {
            return $interval->d . ' jour' . ($interval->d > 1 ? 's' : '');
        } elseif ($interval->h > 0) {
            return $interval->h . ' heure' . ($interval->h > 1 ? 's' : '');
        } elseif ($interval->i > 0) {
            return $interval->i . ' minute' . ($interval->i > 1 ? 's' : '');
        } else {
            return $interval->s . ' seconde' . ($interval->s > 1 ? 's' : '');
        }
    }

    public function getUserLevel()
    {
        $user = $this->security->getUser();
        if (!$user) {
            return null;
        }

        $membership = $this->membershipRepository->findOneBy(['theUser' => $user]);
        return $membership ? $membership->getLevel() : 'Militant';
    }
}
