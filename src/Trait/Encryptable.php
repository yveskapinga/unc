<?php

// src/Entity/Traits/Encryptable.php
namespace App\Trait;

use Doctrine\ORM\Mapping as ORM;
use App\Service\EncryptionService;

trait Encryptable
{
    private static ?EncryptionService $encryptionService = null;

    public static function setEncryptionService(EncryptionService $encryptionService): void
    {
        self::$encryptionService = $encryptionService;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function encryptContent(): void
    {
        if (self::$encryptionService && $this->content) {
            $this->content = self::$encryptionService->encrypt($this->content);
        }
    }

    #[ORM\PostLoad]
    public function decryptContent(): void
    {
        if (self::$encryptionService && $this->content) {
            $this->content = self::$encryptionService->decrypt($this->content);
        }
    }
}
