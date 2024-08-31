<?php

// src/Service/EncryptionService.php
namespace App\Service;

class EncryptionService
{
    private $key ;

    public function __construct(string $key)
    {
        $this->key = $key;
        // '598dbe6e961d056e7bbf2d16596faa35400e4f4dbdc44345f8b75caf95d8744e';
    }
    

    public function encrypt(string $data): string
    {
        return openssl_encrypt($data, 'aes-256-cbc', $this->key, 0, substr($this->key, 0, 16));
    }

    public function decrypt(string $data): string
    {
        return openssl_decrypt($data, 'aes-256-cbc', $this->key, 0, substr($this->key, 0, 16));
    }
}


