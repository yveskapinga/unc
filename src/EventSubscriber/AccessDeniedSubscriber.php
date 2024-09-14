<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class AccessDeniedSubscriber implements EventSubscriberInterface
{
    private $security;
    private $requestStack;

    public function __construct(Security $security, RequestStack $requestStack)
    {
        $this->security = $security;
        $this->requestStack = $requestStack;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $this->requestStack->getCurrentRequest();
        $route = $request->attributes->get('_route');

        // Vérifiez si l'utilisateur est authentifié
        if (!$this->security->getUser()) {
            return;
        }

        // Vérifiez les rôles nécessaires pour accéder à la route
        if (preg_match('/^admin\//', $route) && !$this->security->isGranted('ROLE_ADMIN')) {
            $this->setSession('error', 'Vous n\'avez pas les droits nécessaires pour accéder à cette page.', $event);
            
        }

        if (preg_match('/^super_admin\//', $route) && !$this->security->isGranted('ROLE_SUPER_ADMIN')) {
            $this->setSession('error', 'Vous n\'avez pas les droits nécessaires pour accéder à cette page.', $event);
        }

        if (preg_match('/^moderator\//', $route) && !$this->security->isGranted('ROLE_MODERATOR')) {
            $this->setSession('error', 'Vous n\'avez pas les droits nécessaires pour accéder à cette page.', $event);
        }

        if (preg_match('/^user\//', $route) && !$this->security->isGranted('ROLE_USER')) {
            $this->setSession('error', 'Vous devez être connecté pour accéder à cette page.', $event);
        }
    }

    private function setSession(string $type, string $message, RequestEvent $event)
    {
        $request = $this->requestStack->getCurrentRequest();
        $session = $this->requestStack->getSession();
        if ($session) {
            $flashBag = $session->getBag('flashes');
            $flashBag->add($type, $message);
        }
        $event->setResponse(new RedirectResponse($request->headers->get('referer')));
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }
}
