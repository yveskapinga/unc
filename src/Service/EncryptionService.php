<?php

// // src/Service/EncryptionService.php
// namespace App\Service;

// class EncryptionService
// {
//     private $key ;

//     public function __construct(string $key)
//     {
//         $this->key = $key;
//     }
    

//     public function encrypt(string $data): string
//     {
//         return openssl_encrypt($data, 'aes-256-cbc', $this->key, 0, substr($this->key, 0, 16));
//     }

//     public function decrypt(string $data): string
//     {
//         return openssl_decrypt($data, 'aes-256-cbc', $this->key, 0, substr($this->key, 0, 16));
//     }
// }

// src/Service/EncryptionService.php
// src/Service/EncryptionService.php
namespace App\Service;

class EncryptionService
{
    private $key;
    private $salt;

    public function __construct(string $key, string $salt)
    {
        $this->key = $key;
        $this->salt = $salt;
    }

        public function encrypt(string $data): string
    {
        return openssl_encrypt($data, 'aes-256-cbc', $this->key, 0, substr($this->key, 0, 16));
    }

    public function decrypt(string $data): string
    {
        return openssl_decrypt($data, 'aes-256-cbc', $this->key, 0, substr($this->key, 0, 16));
    }
// }

    // public function encrypt(string $data): string
    // {
    //     $hash = hash('sha256', $data);
    //     $data = $this->salt . $data;
    //     $encryptedData = openssl_encrypt($data, 'aes-256-cbc', $this->key, 0, substr($this->key, 0, 16));
    //     return base64_encode($encryptedData . '::' . $hash);
    // }

    // public function decrypt(string $data): string
    // {
    //     list($encryptedData, $hash) = explode('::', base64_decode($data), 2);
    //     $decryptedData = openssl_decrypt($encryptedData, 'aes-256-cbc', $this->key, 0, substr($this->key, 0, 16));
    //     $originalData = substr($decryptedData, strlen($this->salt));

    //     if (hash('sha256', $originalData) !== $hash) {
    //         throw new \Exception('Data integrity check failed');
    //     }

    //     return $originalData;
    // }
}


