<?php
// src/EventSubscriber/MessageSubscriber.php
// src/EventSubscriber/MessageSubscriber.php
namespace App\EventSubscriber;

use App\Entity\Message;
use App\Service\EncryptionService;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Event\PostLoadEventArgs;
use Doctrine\ORM\Events;

class MessageSubscriber implements EventSubscriber
{
    private $encryptionService;

    public function __construct(EncryptionService $encryptionService)
    {
        $this->encryptionService = $encryptionService;
    }

    public function getSubscribedEvents()
    {
        return [
            Events::prePersist,
            Events::preUpdate,
            Events::postLoad,
        ];
    }

    public function prePersist(PrePersistEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof Message) {
            $entity->setContent($this->encryptionService->encrypt($entity->getContent()));
        }
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof Message) {
            $entity->setContent($this->encryptionService->encrypt($entity->getContent()));
        }
    }

    public function postLoad(PostLoadEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof Message) {
            $entity->setContent($this->encryptionService->decrypt($entity->getContent()));
        }
    }
}
