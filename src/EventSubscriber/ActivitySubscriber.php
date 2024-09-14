<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Security;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class ActivitySubscriber implements EventSubscriberInterface
{
    private $security;
    private $entityManager;
    private $requestStack;
    private $cache;

    public function __construct(Security $security, EntityManagerInterface $entityManager, RequestStack $requestStack, CacheInterface $cache)
    {
        $this->security = $security;
        $this->entityManager = $entityManager;
        $this->requestStack = $requestStack;
        $this->cache = $cache;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        if (!$event->isMainRequest()) {
            return;
        }

        /** @var User $user */
        $user = $this->security->getUser();
        if ($user) {
            $session = $this->requestStack->getSession();
            $timezone = $session->get('timezone', 'UTC');
            $dateTime = new \DateTime('now', new \DateTimeZone($timezone));

            $cacheKey = 'user_last_activity_' . $user->getId();
            $lastActivity = $this->cache->get($cacheKey, function (ItemInterface $item) use ($user) {
                $item->expiresAfter(3600); // Cache expiration time
                return $user->getLastActivityAt();
            });

            if (!$lastActivity || ($dateTime->getTimestamp() - $lastActivity->getTimestamp()) > 300) { // 300 seconds = 5 minutes
                $user->setLastActivityAt($dateTime);
                $this->entityManager->persist($user);
                $this->entityManager->flush();
                $this->cache->delete($cacheKey); // Clear the cache
                $this->cache->get($cacheKey, function (ItemInterface $item) use ($dateTime) {
                    $item->expiresAfter(3600); // Cache expiration time
                    return $dateTime;
                });
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            RequestEvent::class => 'onKernelRequest',
        ];
    }
}
