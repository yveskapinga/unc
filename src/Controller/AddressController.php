<?php

namespace App\Controller;

use App\Entity\Address;
use App\Form\AddressType;
use App\Service\DoctrineService;
use App\Repository\AddressRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/address')]
class AddressController extends AbstractController
{
    public function __construct(
        private AddressRepository $addressRepository,
        private UserRepository $userRepository,
        private DoctrineService $doctrineService
    ){

    }
    #[Route('/', name: 'app_address_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('address/index.html.twig', [
            'addresses' => $this->addressRepository->findAll(),
            'users' => $this->userRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_address_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $address = new Address();
        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->doctrineService->persistEntities(
                $address, 
                null, 
                'L\'adresse a été créée', 
                'success'
            );
            // $entityManager->persist($address);
            // $entityManager->flush();

            return $this->redirectToRoute('app_address_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('address/new.html.twig', [
            'address' => $address,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_address_show', methods: ['GET'])]
    public function show(Address $address): Response
    {
        return $this->render('address/show.html.twig', [
            'address' => $address,
        ]);
    }

    #[Route('/{id?}/edit', name: 'app_address_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Address $address = null): Response
    {
        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->doctrineService->persistEntities(
                $address, 
                null, 
                'L\'adresse a été modifiée', 
                'success'
            );

            // $entityManager->flush();

            return $this->redirectToRoute('app_address_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('address/edit.html.twig', [
            'address' => $address,
            'form' => $form,
        ]);
    }

    #[Route('/{id?}', name: 'app_address_delete', methods: ['POST'])]
    public function delete(Request $request, Address $address = null): Response
    {
        if ($this->isCsrfTokenValid('delete'.$address->getId(), $request->request->get('_token'))) {
            $this->doctrineService->removeEntities(
                $address, 
                'L\'adresse a été modifiée', 
                'success'
            );
            // $entityManager->remove($address);
            // $entityManager->flush();
        }

        return $this->redirectToRoute('app_address_index', [], Response::HTTP_SEE_OTHER);
    }
}
