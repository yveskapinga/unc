<?php
// src/EventListener/MessageEncryptionListener.php
// src/EventListener/MessageEncryptionListener.php
namespace App\EventListener;

use App\Entity\Message;
use App\Service\EncryptionService;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class MessageEncryptionListener
{
    private EncryptionService $encryptionService;

    public function __construct(EncryptionService $encryptionService)
    {
        $this->encryptionService = $encryptionService;
    }

    public function prePersist(Message $message, LifecycleEventArgs $args): void
    {
        $this->encryptContent($message);
    }

    public function preUpdate(Message $message, PreUpdateEventArgs $args): void
    {
        if ($args->hasChangedField('content')) {
            $message->setContent($this->encryptionService->encrypt($args->getNewValue('content')));
        }
    }

    public function postLoad(Message $message, LifecycleEventArgs $args): void
    {
        $message->setContent($this->encryptionService->decrypt($message->getContent()));
    }

    private function encryptContent(Message $message): void
    {
        $message->setContent($this->encryptionService->encrypt($message->getContent()));
    }
}
