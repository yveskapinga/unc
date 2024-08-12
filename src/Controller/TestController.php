<?php
// src/Controller/TestController.php
namespace App\Controller;

use App\Entity\Topic;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use App\Repository\TopicRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TestController extends AbstractController
{
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    #[Route('/test/geocode', name: 'location')]
    public function geocode(): Response
    {
        $address = '672 Arpenteur Mutombo, Kolwezi, Lualaba, république démocratique du congo';
        $response = $this->client->request('GET', 'https://nominatim.openstreetmap.org/search', [
            'query' => [
                'q' => $address,
                'format' => 'json',
            ],
        ]);

        $data = $response->toArray();

        if (!empty($data)) {
            $location = $data[0];
            $latitude = $location['lat'];
            $longitude = $location['lon'];

            return new Response(sprintf(
                'Adresse: %s<br>Latitude: %s<br>Longitude: %s',
                $address,
                $latitude,
                $longitude
            ));
        }

        return new Response('Adresse non trouvée.');
    }
    
    #[Route('/test/get-location', name: 'test_geocode')]
    public function getLocation(): Response
    {
        return $this->render('get_location.html.twig');
    }

    #[Route('/test/save-location', name: 'test_save_location', methods: ['POST'])]
    public function saveLocation(Request $request, UserInterface $user): Response
    {
        $data = json_decode($request->getContent(), true);
        $latitude = $data['latitude'];
        $longitude = $data['longitude'];
        
        return new Response(sprintf(
        'Adresse: %s<br>Latitude: %s<br>Longitude: %s',
            //$address,
            $latitude,
            $longitude
            ));
    }

    #[Route('/test/edit', name: 'test_edit')]
    public function action(Request $request, UserInterface $user, TopicRepository $repo, EntityManagerInterface $em): Response
    {
        $topics = $repo->find(3);
//        foreach ($topics as $u){
            if (!$topics->getAuthor()){
                $topics->setAuthor($user);
                $em->persist($topics);
            }
//        }
        $em->flush();
        return $this->redirectToRoute('app_topic_index', [], Response::HTTP_SEE_OTHER);

        $data = json_decode($request->getContent(), true);
        $latitude = $data['latitude'];
        $longitude = $data['longitude'];
        
        return new Response(sprintf(
        'Adresse: %s<br>Latitude: %s<br>Longitude: %s',
            //$address,
            $latitude,
            $longitude
            ));
    }
}
