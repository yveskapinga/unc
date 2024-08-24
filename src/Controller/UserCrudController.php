<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Entity\Address;
use App\Form\AddressType;
use App\Form\UserRoleType;
use App\Service\GeocoderService;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route('/user/crud')]
class UserCrudController extends AbstractController
{
    #[Route('/', name: 'app_user_crud_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user_crud/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_user_crud_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $address = new Address();
        $user->setAddress($address);
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $address = "Champs-Élysées, Paris, France";
            // $coordinates = $geocoderService->geocodeAddress($address);
            // dd($coordinates->getLatitude());
            // // Définir les coordonnées dans l'entité Address
            // $user->getAddress()->setLatitude($coordinates->getLatitude());
            // $user->getAddress()->setLongitude($coordinates->getLongitude());
            // dd($user);
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_crud_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user_crud/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_crud_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user_crud/profile.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_crud_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $address = new Address;
        $user->setAddress($address);
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Profil mis à jour avec succès');
            return $this->redirectToRoute('app_user_crud_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user_crud/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_crud_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_crud_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/voir', name: 'app_user_crud_profile', methods: ['GET','POST'])]
    public function voir(): Response
    {
        // $user = $this->getUser();
        // dd($user);
        return $this->render('user_crud/profile.html.twig', [
            // 'user' => $user,
        ]);
    }

    #[Route('/assign-roles', name: 'assign_roles')]
    public function assignRoles(Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserRoleType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $users = $data['users'];
            $roles = $data['roles'];

            foreach ($users as $user) {
                $user->setRoles($roles);
                $entityManager->persist($user);
            }

            $entityManager->flush();

            $this->addFlash('success', 'Roles assigned successfully!');
            return $this->redirectToRoute('assign_roles');
        }

        return $this->render('user/assign_roles.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
