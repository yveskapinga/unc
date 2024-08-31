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
use App\Service\DoctrineService;
use App\Service\NotificationService;
use App\Service\SecurityService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Security;

#[Route('/event')]
class EventController extends AbstractController
{
    public function __construct(
        private EventRepository $eventRepo,
        private SecurityService $securityService,
        private NotificationService $notificationService,
        private EntityManagerInterface $em,
        private DoctrineService $doctrineService,

    )
    {
        
    }
    #[Route('/', name: 'app_event_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('event/index.html.twig', [
            'events' => $this->eventRepo->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_event_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->doctrineService->persistEntities($event, null, 'votre évènement a été créé avec succès','success');
            // $this->em->persist($event);
            // $this->em->flush();

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
    public function edit(Request $request, Event $event): Response
    {
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('event/edit.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_event_delete', methods: ['POST'])]
    public function delete(Request $request, Event $event): Response
    {
        if ($this->isCsrfTokenValid('delete'.$event->getId(), $request->request->get('_token'))) {
            $this->em->remove($event);
            $this->em->flush();
        }

        return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
    }
    /**
     * Cette méthode permet à un utilisateur de s'inscrire à un évènement
     * Il faut prévoir que sa demande d'inscription soit acceptée
     */
    #[Route('/register/{id}', name: 'app_register_for_event')]
    public function registerForEventAction(Event $event): Response
    {
        $user = $this->securityService->getConnectedUser();
        $event->addParticipant($user);
        $message = $user->getUsername() . " a souscrit à l'évènement : "   . $event->getTitle();
        $this->notificationService->createNotification($user, 'Invitation', $message);
        $this->doctrineService->persistEntities($event,null,$message,'inscription');
        return $this->redirectToRoute('app_event_show', ['id' => $event->getId()]);
    }

    #[Route('/unregister/{id}', name: 'app_unregister_for_event')]
    public function unregisterForEventAction(Event $event, UserInterface $user, EntityManagerInterface $em): Response
    {
        $user = $this->securityService->getConnectedUser();
        $event->removeParticipant($user);
        $message = $user->getUsername() . " s'est soustrait à l'évènement : "   . $event->getTitle();
        $this->notificationService->createNotification($user, 'Désinscription', $message);
        $this->doctrineService->persistEntities($event,null,$message,'Desinscription');


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

    #[Route('/events/organized', name: 'app_event_organized')]
    public function organizedEvents(EventRepository $eventRepository, Security $security): Response
    {
        $user = $security->getUser();
        $organizedEvents = $eventRepository->findBy(['organizer' => $user]);

        return $this->render('event/organized.html.twig', [
            'organizedEvents' => $organizedEvents,
        ]);
    }

    #[Route('/events/participated', name: 'app_event_participated')]
    public function participatedEvents(EventRepository $eventRepository, Security $security): Response
    {
        $user = $security->getUser();
        $participatedEvents = $eventRepository->findByParticipant($user);

        return $this->render('event/participated.html.twig', [
            'participatedEvents' => $participatedEvents,
        ]);
    }
}
