<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\SecurityService;
use App\Service\UploaderService;
use App\Service\DoctrineService;
use DateTimeImmutable;

#[Route('/category')]
class CategoryController extends AbstractController
{
    
    public function __construct(
        private EntityManagerInterface $em,
        private UploaderService $uploaderService,
        private SecurityService $securityService,
        private CategoryRepository $categoryRepository,
        private DoctrineService $doctrineService

    ) {
    }
    #[Route('/', name: 'app_category_index', methods: ['GET'])]
    public function index(): Response
    {
        $topCategories = $this->categoryRepository->findTopCategories();
        $mainCategory = array_shift($topCategories); // Récupère la première catégorie
        $firstTwoCategories = array_slice($topCategories, 0, 2); // Récupère les deux suivantes
        $lastTwoCategories = array_slice($topCategories, 2, 2); // Récupère les deux dernières

        return $this->render('category/index.html.twig', [
            'mainCategory' => $mainCategory,
            'firstTwoCategories' => $firstTwoCategories,
            'lastTwoCategories' => $lastTwoCategories,
            'categories' => $this->categoryRepository->findAll(),
        ]);
        // return $this->render('page/category.html.twig', [
        //     'categories' => $this->categoryRepository->findAll(),
        // ]);
    }

    #[Route('/new/{id?}', name: 'app_category_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Category $category = null): Response
    {
        if ($category === null) {
            $category = new Category();
        }
    
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $photo = $form->get('photo')->getData();
            if ($photo) {
                $directory = $this->getParameter('pictures_directory');
                $category->setImage($this->uploaderService->uploadPhoto($photo, $directory));
            }
            $category->setCreatedAt(new \DateTimeImmutable());
            $this->doctrineService->persistEntities(
                $category, 
                null, 
                'La catégorie a été créée', 
                'success'
            );
            // $this->em->persist($category);
            // $this->em->flush();
    
            return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->renderForm('category/new.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }
    

    #[Route('/{id}', name: 'app_category_show', methods: ['GET'])]
    public function show(Category $category): Response
    {
        return $this->render('category/show.html.twig', [
            'category' => $category,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Category $category, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->doctrineService->persistEntities(
                $category, 
                null, 
                'La catégorie a été modifiée', 
                'success'
            );
            // $entityManager->flush();

            return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('category/edit.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_category_delete', methods: ['POST'])]
    public function delete(Request $request, Category $category, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $this->doctrineService->removeEntities(
                $category, 
                'La catégorie a été supprimée', 
                'success'
            );
            // $entityManager->remove($category);
            // $entityManager->flush();
        }

        return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
    }
}
