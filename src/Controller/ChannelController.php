<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Channel;
use App\Form\ChannelType;
use App\Repository\ChannelRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/channel')]
class ChannelController extends AbstractController
{
    #[Route('/', name: 'app_channel_index', methods: ['GET'])]
    public function index(ChannelRepository $channelRepository): Response
    {
        return $this->render('channel/index.html.twig', [
            'channels' => $channelRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_channel_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $channel = new Channel();
        $form = $this->createForm(ChannelType::class, $channel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($channel);
            $entityManager->flush();

            return $this->redirectToRoute('app_channel_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('channel/new.html.twig', [
            'channel' => $channel,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_channel_show', methods: ['GET'])]
    public function show(Channel $channel): Response
    {
        return $this->render('channel/show.html.twig', [
            'channel' => $channel,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_channel_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Channel $channel, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ChannelType::class, $channel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_channel_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('channel/edit.html.twig', [
            'channel' => $channel,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_channel_delete', methods: ['POST'])]
    public function delete(Request $request, Channel $channel, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$channel->getId(), $request->request->get('_token'))) {
            $entityManager->remove($channel);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_channel_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/invite/{id}', name: 'invite_user_to_channel')]
    public function inviteUserToChannelAction(Channel $channel, Request $request, EntityManagerInterface $em): Response
    {
        //$channel = $em->getRepository(Channel::class)->find($channelId);
        $userId = $request->request->get('user_id');
        $user = $em->getRepository(User::class)->find($userId);

        if ($channel && $user) {
            $channel->addChannelUser($user);
            $em->flush();

            $this->addFlash('success', 'Utilisateur invité avec succès');
        } else {
            $this->addFlash('error', 'Erreur lors de l\'invitation de l\'utilisateur');
        }

        return $this->redirectToRoute('app_channel_show', ['id' => $channel->getId()]);
    }
}
