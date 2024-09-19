<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Membership;
use App\Form\MembershipType;
use App\Utils\GlobalVariables;
use App\Entity\Interfederation;
use App\Service\MembershipService;
use App\Repository\MembershipRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/membership')]
class MembershipController extends AbstractController
{
    public function __construct(private MembershipService $membershipService){}
    

    #[Route('/validate/{userId}', name: 'validate_membership')]
    public function validateAction(User $user, Request $request): Response
    {
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        $membership = new Membership();
        $membership->setTheUser($user);
        $membership->setInterfederation($this->membershipService->getInterfederationByUser($user));

        $form = $this->createForm(MembershipType::class, $membership);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->membershipService->approveMembership($membership);

            return $this->redirectToRoute('app_membership_index');
        }

        return $this->render('membership/validate.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
        // Action pour approuver une demande d'adhésion
    #[Route('/approve/{user}', name: 'approve_membership')]
    public function approveAction(User $user): Response
    {
        $membership = new Membership();
        $this->membershipService->approveMembership($membership);
    
        return $this->redirectToRoute('app_membership_index');
    }
    
    #[Route('/', name: 'app_membership_index', methods: ['GET'])]
    public function index(MembershipRepository $membershipRepository): Response
    {
        return $this->render('membership/index.html.twig', [
            'memberships' => $membershipRepository->findAll(),
        ]);
    }

    #[Route('/new/{id?}', name: 'app_membership_new', methods: ['GET', 'POST'])]
    public function new(User $user=null, Request $request, EntityManagerInterface $entityManager): Response
    {
        $membership = new Membership(); 
        $interfederation = $entityManager->getRepository(Interfederation::class)->findOneBy(['designation'=>$user->getAddress()->getProvince()]);
        $membership->setTheUser($user)->setInterfederation($interfederation);
        
        $form = $this->createForm(MembershipType::class, $membership);
        $form->handleRequest($request);
        $fonctionsJson = json_encode(GlobalVariables::$functions);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($membership);
            $entityManager->flush();

            return $this->redirectToRoute('app_membership_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('membership/new.html.twig', [
            'membership' => $membership,
            'form' => $form,
            'fonctionsJson' => $fonctionsJson,
        ]);
    }

    #[Route('/{id}', name: 'app_membership_show', methods: ['GET'])]
    public function show(Membership $membership): Response
    {
        return $this->render('membership/show.html.twig', [
            'membership' => $membership,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_membership_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Membership $membership, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MembershipType::class, $membership);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_membership_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('membership/edit.html.twig', [
            'membership' => $membership,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_membership_delete', methods: ['POST'])]
    public function delete(Request $request, Membership $membership, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$membership->getId(), $request->request->get('_token'))) {
            $entityManager->remove($membership);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_membership_index', [], Response::HTTP_SEE_OTHER);
    }
}
