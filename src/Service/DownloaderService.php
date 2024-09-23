<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloaderService
{
    public function downloadFile(string $filePath, string $fileName): StreamedResponse
    {
        $response = new StreamedResponse(function() use ($filePath) {
            $outputStream = fopen('php://output', 'wb');
            $fileStream = fopen($filePath, 'rb');
            stream_copy_to_stream($fileStream, $outputStream);
        });

        $response->headers->set('Content-Type', 'application/octet-stream');
        $response->headers->set('Content-Disposition', 'attachment; filename="'.$fileName.'"');

        return $response;
    }
}
