<?php

// Dans src/Service/PdfService.php

namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;
use Twig\Environment;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Inline;

class PdfService
{
    private $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function generatePdf(
        string $view,
        string $directory,
        array $data = [],
        string $filename = 'document.pdf',
        string $size = 'A4',
        string $orientation = 'portrait',
        
    ): Response {
        // Configuration des options de Dompdf
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->set('isRemoteEnabled', true);

        // Instanciation de Dompdf avec les options configurées
        $dompdf = new Dompdf($pdfOptions);

        // Rendu du template Twig en HTML avec les données fournies
        $html = $this->twig->render($view, $data);

        // Chargement du HTML dans Dompdf
        $dompdf->loadHtml($html);

        // Configuration du format du papier et de l'orientation
        $dompdf->setPaper($size, $orientation);

        // Rendu du HTML en PDF
        $dompdf->render();

        // Récupération du contenu du PDF généré
        $output = $dompdf->output();

        $response = new Response($output);
        
        $response->headers->set('Content-Type','application/pdf');

        $response->headers->set('Content-Disposition','inline; filename="'.$filename.'"');

        // // Génération d'un nom de fichier unique pour le PDF
        // $filename = $uniqueFilename . uniqid() . '.pdf';

        // Enregistrement du PDF dans le directory
        file_put_contents($directory.$filename, $output);

        // Retourne le nom du fichier PDF généré
        return $response;
    }
}
