<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Membership;
use App\Form\MembershipType;
use App\Utils\GlobalVariables;
use App\Entity\Interfederation;
use App\Service\MembershipService;
use App\Repository\MembershipRepository;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/membership')]
class MembershipController extends AbstractController
{
    public function __construct(
        private MembershipService $membershipService,
        private EntityManagerInterface $em,
        private NotificationService $notificationService
        ){}
    

    #[Route('/new/{id?}', name: 'app_membership_new')]
    public function validateAction(User $user, Request $request): Response
    {
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }
        if ($user->getMembership()){
            return new Response('la demande a déjà été validée');
        }

        $membership = new Membership();
        $membership->setTheUser($user);
        $interfederation = $this->em->getRepository(Interfederation::class)->findOneBy(['designation'=>$user->getAddress()->getProvince()]);

        $membership->setInterfederation($interfederation);

        $form = $this->createForm(MembershipType::class, $membership);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $membership->setTheUser($user);
            $membership->setInterfederation($interfederation);
            $user->setIsActive(true);
            $this->em->persist($membership);
            $this->em->persist($user);
            $this->em->flush();

            $this->notificationService
            ->createNotification(
                $user, 
                'info',
                "Votre demande d'adhésion a été acceptée"
            );

            $this->addFlash('success','La demande a été approuvée avec succès');

            return $this->redirectToRoute('app_notification_index');
        }

        return $this->render('membership/validate.html.twig', [
            'form' => $form->createView(),
            'user' => $user
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

    #[Route('/validate/{id?}', name: 'validate_membership', methods: ['GET', 'POST'])]
    public function new(User $user=null, Request $request, EntityManagerInterface $entityManager): Response
    {
        $membership = new Membership();
        if (!is_null($user)) {
            $interfederation = $entityManager->getRepository(Interfederation::class)->findOneBy(['designation'=>$user->getAddress()->getProvince()]);
            $membership->setTheUser($user)->setInterfederation($interfederation); 
            $new = true;           
        }
        
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
