<?php

// src/Controller/MapController.php
namespace App\Controller;

use App\Entity\Post;
use App\Entity\Topic;
use App\Form\PostType;
use App\Entity\Category;
use App\Form\AnonymousPostType;
use App\Service\SecurityService;
use App\Repository\UserRepository;
use App\Repository\TopicRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route('/page')]
class PageController extends AbstractController
{
    public function __construct(
        private TopicRepository $topicRepository,
        private CategoryRepository $categoryRepository,
        private UserRepository $userRepository,
        private SecurityService $securityService
        )
    {
        
    }
    
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAllWithCoordinates();

        return $this->render('map/index.html.twig', [
            'users' => $users,
        ]);
    }

    
    #[Route('/{id}/single-post', name:'single-post')]
    public function singlePost(Topic $topic, Request $request, EntityManagerInterface $entityManager) : Response
    {
        $post = new Post();
        $post->setTopic($topic);
        $user = $this->securityService->getConnectedUser();
        if ($user) {
            $post->setAuthor($user);
            $post->setIsValidated(true);
            $form = $this->createForm(PostType::class, $post);
        }else{
            $form=$this->createForm(AnonymousPostType::class); 
        }
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //$entityManager->flush();

            return $this->redirectToRoute('single-post', [
                'id'=>$topic->getId()
            ], Response::HTTP_SEE_OTHER);
        }
        // $bestAuthorData = $this->topicRepository->findBestAuthor();
        // if ($bestAuthorData) {
        //     // Récupérer l'utilisateur à partir de l'ID
        //     $user = $this->userRepository->find($bestAuthorData['id']) ? $this->userRepository->find($bestAuthorData['id']) : null;
        //     $articleCount = $bestAuthorData['articleCount'] ? $bestAuthorData['articleCount'] : null;
        //     $commentCount = $bestAuthorData['commentCount'] ? $bestAuthorData['commentCount'] : null;
        // }
        
        return $this->render('page/single-post.html.twig',[
            'topic'=>$topic,
            'form'=>$form->createView(),
            // 'topics'=>$this->topicRepository->findAll(),
            // 'categories'=>$this->categoryRepository->findAll(),
            // 'user' => $user,
            // 'articleCount' => $articleCount,
            // 'commentCount' => $commentCount,
        ]);
    }

    #[Route('/topic', name:'page_topic')]
    public function topic() : Response
    {
        // $bestAuthorData = $this->topicRepository->findBestAuthor();
        // if ($bestAuthorData) {
        //     // Récupérer l'utilisateur à partir de l'ID
        //     $user = $this->userRepository->find($bestAuthorData['id']) ? $this->userRepository->find($bestAuthorData['id']) : null;
        //     $articleCount = $bestAuthorData['articleCount'] ? $bestAuthorData['articleCount'] : null;
        //     $commentCount = $bestAuthorData['commentCount'] ? $bestAuthorData['commentCount'] : null;
        // }
        return $this->render('page/topic.html.twig',[
            // 'topics'=>$this->topicRepository->findAll(),
            // 'categories'=>$this->categoryRepository->findAll(),
            // 'user' => $user,
            // 'articleCount' => $articleCount,
            // 'commentCount' => $commentCount,
        ]);
        
    }
    #[Route('/about', name:'about')]
    public function about() : Response
    {
        return $this->render('page/about.html.twig',[
            
        ]);
    }

    #[Route('/contact', name:'contact')]
    public function contact() : Response
    {
        return $this->render('page/contact.html.twig',[
            
        ]);
    }

    #[Route('/starter-page', name:'starter-page')]
    public function starterPage() : Response
    {
        return $this->render('page/starter-page.html.twig',[
            
        ]);
    }
}

