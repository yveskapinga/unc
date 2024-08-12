<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Event;
use App\Form\EventType;
use App\Entity\Notification;
use App\Form\EventInvitationType;
use App\Form\InvitationType;
use App\Form\UserType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route('/event')]
class EventController extends AbstractController
{
    #[Route('/', name: 'app_event_index', methods: ['GET'])]
    public function index(EventRepository $eventRepository): Response
    {
        return $this->render('event/index.html.twig', [
            'events' => $eventRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_event_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($event);
            $entityManager->flush();

            return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('event/new.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_event_show', methods: ['GET'])]
    public function show(Event $event): Response
    {
        return $this->render('event/show.html.twig', [
            'event' => $event,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_event_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('event/edit.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_event_delete', methods: ['POST'])]
    public function delete(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$event->getId(), $request->request->get('_token'))) {
            $entityManager->remove($event);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
    }
    /**
     * Cette méthode permet à un utilisateur de s'inscrire à un évènement
     * Il faut prévoir que sa demande d'inscription soit acceptée
     */
    #[Route('/register/{id}', name: 'app_register_for_event')]
    public function registerForEventAction(Event $event, UserInterface $user, EntityManagerInterface $em): Response
    {
        $event->addParticipant($user);
        $notification = new Notification();
        $notification->setTheUser($user);
        $notification->setType('Invitation');
        $notification->setContent($user->getUsername() . " a souscrit à l'évènement : "   . $event->getTitle());
        $notification->setCreatedAt(new \DateTime());
        $em->persist($notification);
        $em->persist($event);
        $em->flush();

        return $this->redirectToRoute('app_event_show', ['id' => $event->getId()]);
    }

    #[Route('/unregister/{id}', name: 'app_unregister_for_event')]
    public function unregisterForEventAction(Event $event, UserInterface $user, EntityManagerInterface $em): Response
    {
        $event->removeParticipant($user);
        $notification = new Notification();
        $notification->setTheUser($user);
        $notification->setType('Désinscription');
        $notification->setContent($user->getUsername() . " s'est soustrait à l'évènement : "   . $event->getTitle());
        $notification->setCreatedAt(new \DateTime());
        $em->persist($notification);
        $em->persist($event);
        $em->flush();

        return $this->redirectToRoute('app_event_show', ['id' => $event->getId()]);
    }

    /**
     * Cette méthode permet à un organisateur d'inviter des membres à un évènement
     */
    #[Route('/event/{id}/invite', name: 'event_invite')]
    public function invite(Event $event, Request $request, EntityManagerInterface $em): Response
    {
        $participant = new User;
        $form = $this->createForm(EventInvitationType::class, $participant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $participant = $form->get('email')->getData();
            foreach ($participant as $user) {
                $event->addParticipant($user);
                $notification = new Notification();
                $notification->setTheUser($user);
                $notification->setType('Invitation');
                $notification->setContent('Vous avez été invité à l\'événement : ' . $event->getTitle());
                $notification->setCreatedAt(new \DateTime());
                $em->persist($notification);
                //dd($notification);
            }
            $em->flush();

            $this->addFlash('success', 'Invitations envoyées avec succès!');
            return $this->redirectToRoute('app_event_show', ['id' => $event->getId()]);
        }

        return $this->render('event/invite.html.twig', [
            'form' => $form->createView(),
            'event' => $event,
        ]);
    }
}
