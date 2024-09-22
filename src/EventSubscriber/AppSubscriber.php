<?php
// src/EventSubscriber/AppSubscriber.php
namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use App\Service\EncryptionService;
use Symfony\Component\HttpFoundation\Response;

class AppSubscriber implements EventSubscriberInterface
{
    private $encryptionService;

    public function __construct(EncryptionService $encryptionService)
    {
        $this->encryptionService = $encryptionService;
    }

    public static function getSubscribedEvents()
    {
        return [
            // RequestEvent::class => 'onKernelRequest',
            // ResponseEvent::class => 'onKernelResponse',
            // ExceptionEvent::class => 'onKernelException',
        ];
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        $encryptedUrl = $request->getRequestUri();
        $decryptedUrl = $this->encryptionService->decrypt($encryptedUrl);
        $request->server->set('REQUEST_URI', $decryptedUrl);
    }

    public function onKernelResponse(ResponseEvent $event)
    { 
        $response = $event->getResponse();
        $content = $response->getContent();
        $encryptedContent = $this->encryptionService->encrypt($content);dd($encryptedContent);
        $response->setContent($encryptedContent);
    }

    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        $response = new RedirectResponse('/page/error');

        if ($exception instanceof HttpExceptionInterface) {
            $response->setStatusCode($exception->getStatusCode());
        } else {
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $event->setResponse($response);
    }
}
