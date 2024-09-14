<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\Interfederation;
use App\Form\InterfederationType;
use App\Repository\InterfederationRepository;
use App\Service\SecurityService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/interfederation')]
class InterfederationController extends AbstractController
{
    public function __construct(
        private SecurityService $securityService
    ){

    }

    #[Route('/', name: 'app_interfederation_index', methods: ['GET'])]
    public function index(InterfederationRepository $interfederationRepository): Response
    {
        return $this->render('interfederation/index.html.twig', [
            'interfederations' => $interfederationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_interfederation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $address = new Address;
        $interfederation = new Interfederation();
        $interfederation->setSiege($address);

        $form = $this->createForm(InterfederationType::class, $interfederation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($interfederation);
            $entityManager->flush();

            return $this->redirectToRoute('app_interfederation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('interfederation/new.html.twig', [
            'interfederation' => $interfederation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_interfederation_show', methods: ['GET'])]
    public function show(Interfederation $interfederation): Response
    {
        return $this->render('interfederation/show.html.twig', [
            'interfederation' => $interfederation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_interfederation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Interfederation $interfederation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(InterfederationType::class, $interfederation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_interfederation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('interfederation/edit.html.twig', [
            'interfederation' => $interfederation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_interfederation_delete', methods: ['POST'])]
    public function delete(Request $request, Interfederation $interfederation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$interfederation->getId(), $request->request->get('_token'))) {
            $entityManager->remove($interfederation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_interfederation_index', [], Response::HTTP_SEE_OTHER);
    }
}
