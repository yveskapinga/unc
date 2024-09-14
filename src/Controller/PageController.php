<?php

// src/Controller/MapController.php
namespace App\Controller;

use App\Entity\Post;
use App\Entity\Topic;
use App\Form\PostType;
use App\Entity\Category;
use App\Entity\User;
use App\Form\AnonymousPostType;
use App\Service\SecurityService;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use App\Repository\TopicRepository;
use App\Repository\CategoryRepository;
use App\Service\NotificationService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route('/page')]
class PageController extends AbstractController
{
    public function __construct(
        private TopicRepository $topicRepository,
        private CategoryRepository $categoryRepository,
        private UserRepository $userRepository,
        private SecurityService $securityService,
        private PostRepository $postRepository,
        private NotificationService $notificationService
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
        } else {
            $form = $this->createForm(AnonymousPostType::class);
        }
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $parentId = $request->request->get('parent_id');
            if ($parentId) {
                $parent = $this->postRepository->find($parentId);
                $post->setParent($parent);
            }

            if ($request->get('new')){
               
                $content = $form->get('content')->getData();
                $name = $form->get('name')->getData() ;
                $email = $form->get('email')->getData();
                $prenom = $form->get('firstname')->getData();
                $user = new User();
                $user
                    ->setName($name)
                    ->setEmail($email)
                    ->setFirstName($prenom)
                    ->setNationality('anonymous')
                    ->setPhoneNumber('anonymous')
                    ->setPassword('')
                    ->setUsername($name.'.'.$prenom)
                    ->setJoinedAt(new DateTimeImmutable())
                    ->setNationality('anonymous');

                $post->setContent($content);
                $entityManager->persist($user);
                $entityManager->flush();
                $post->setAuthor($user);
            }

            $entityManager->persist($post);
            $entityManager->flush();

            if ($user) {
                if ($request->isXmlHttpRequest()) {
                    $commentHtml = $this->renderView('post/_post.html.twig', ['post' => $post]);
                    return new JsonResponse(['success' => true, 'commentHtml' => $commentHtml]);
                }
                return $this->redirectToRoute('single-post', ['id' => $topic->getId()]);
            } else {
                // Notify the admin for validation
                $admins = $this->userRepository->findByRole('ROLE_ADMIN');
                $this->notificationService->notifyAdminsOfNewComment($admins, $post);
                $this->addFlash('info', 'Votre commentaire a été envoyé pour validation.');
                return new JsonResponse(['success' => true, 'message' => 'Votre commentaire a été envoyé pour validation.']);
            }
        }

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(['success' => false, 'errors' => (string) $form->getErrors(true, false)]);
        }

        return $this->render('page/single-post.html.twig', [
            'topic' => $topic,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/category', name: 'page_category')]
    public function show(): Response
    {
        return $this->render('page/category.html.twig', [
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

