<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Topic;
use App\Form\AnonymousPostType;
use App\Form\PostType;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use App\Service\SecurityService;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse; // Ajout de l'importation nécessaire


#[Route('/post')]
class PostController extends AbstractController
{
    public function __construct(
        private SecurityService $securityService,
        private NotificationService $notificationService,
        private PostRepository $postRepository,
        private UserRepository $userRepository
        ){

    }
    #[Route('/', name: 'app_post_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('post/index.html.twig', [
            'posts' => $this->postRepository->findAll(),
        ]);
    }

/*     #[Route('/topic/{id}/post/new', name: 'app_post_new', methods: ['GET', 'POST'])]
    public function new(Topic $topic, Request $request, EntityManagerInterface $entityManager): Response
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
            $parentId = $request->request->get('parent_id');
            if ($parentId) {
                $parent = $this->postRepository->find($parentId);
                $post->setParent($parent);
            }
            
            $entityManager->persist($post);
            $entityManager->flush();

            
            if ($user) {
                if ($request->isXmlHttpRequest()) {
                    $commentHtml = $this->renderView('post/_post.html.twig', ['post' => $post]);
                    return new JsonResponse(['success' => true, 'commentHtml' => $commentHtml]);
                }

                return $this->redirectToRoute('app_topic_show', ['id' => $topic->getId()]);
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

        return $this->render('post/new.html.twig', [
            'form' => $form->createView(),
        ]);
    } */

    /**
     * Code ajouté le 04 sept 2024 à 00h49 à KCC, qui remplace le code en commentaire
     */
    #[Route('/topic/{id}/post/new', name: 'app_post_new', methods: ['GET', 'POST'])]
    public function new(Topic $topic, Request $request, EntityManagerInterface $entityManager): Response
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
            $entityManager->persist($post);
            $entityManager->flush();
            if ($user) {
                if ($request->isXmlHttpRequest()) {
                    $commentHtml = $this->renderView('post/_post.html.twig', ['post' => $post]);
                    return new JsonResponse(['success' => true, 'commentHtml' => $commentHtml]);
                }
                return $this->redirectToRoute('app_topic_show', ['id' => $topic->getId()]);
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
        return $this->render('post/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/posts/{id}/validate', name: 'post_validate', methods: ['POST'])]
    public function validate(Post $post, EntityManagerInterface $entityManager): Response
    {
        $post->setIsValidated(true);
        $entityManager->flush();

        $this->addFlash('success', 'Le commentaire a été validé avec succès.');

        return $this->redirectToRoute('app_user_crud_index'); // Remplacez par la route appropriée
    }

    #[Route('/{id}', name: 'app_post_show', methods: ['GET'])]
    public function show(Post $post): Response
    {
        return $this->render('post/show.html.twig', [
            'post' => $post,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_post_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('post/edit.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_post_delete', methods: ['POST'])]
    public function delete(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->request->get('_token'))) {
            $entityManager->remove($post);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/posts/search', name: 'search_posts')]
    public function searchPostsAction(Request $request, PostRepository $postRepository): Response
    {
        $query = $request->query->get('q');
        $posts = $postRepository->searchPosts($query);

        return $this->render('post/search.html.twig', [
            'posts' => $posts,
        ]);
    }
}
