<?php

namespace App\Controller;

use App\Entity\Poll;
use App\Form\PollType;
use App\Repository\PollRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;


#[Route('/poll')]
class PollController extends AbstractController
{
    #[Route('/', name: 'app_poll_index', methods: ['GET'])]
    public function index(PollRepository $pollRepository): Response
    {
        return $this->render('poll/index.html.twig', [
            'polls' => $pollRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_poll_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $poll = new Poll();
        $form = $this->createForm(PollType::class, $poll);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($poll);
            $entityManager->flush();

            return $this->redirectToRoute('app_poll_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('poll/new.html.twig', [
            'poll' => $poll,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_poll_show', methods: ['GET'])]
    public function show(Poll $poll): Response
    {
        return $this->render('poll/show.html.twig', [
            'poll' => $poll,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_poll_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Poll $poll, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PollType::class, $poll);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_poll_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('poll/edit.html.twig', [
            'poll' => $poll,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_poll_delete', methods: ['POST'])]
    public function delete(Request $request, Poll $poll, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$poll->getId(), $request->request->get('_token'))) {
            $entityManager->remove($poll);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_poll_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/vote/{id}', name: 'vote_on_poll')]
    public function voteOnPollAction(Poll $poll, Request $request, UserInterface $user, EntityManagerInterface $em): Response
    {
        $vote = $request->request->get('vote');
        $poll->addVote($user, $vote);
        $em->flush();

        return $this->redirectToRoute('app_poll_show', ['id' => $poll->getId()]);
    }
}
