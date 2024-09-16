<?php

/* namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\Authenticator;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, Authenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            )
            ->setJoinedAt(new DateTimeImmutable())
            ;
            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
} */

// src/Controller/RegistrationController.php
namespace App\Controller;

use App\Entity\User;
use App\Entity\Address;
use App\Entity\Membership;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Service\ReferralService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Security\Authenticator;


class RegistrationController extends AbstractController
{
    public function __construct(
        private ReferralService $referralService,
        private UserRepository $userRep
        )
    {
        
    }
    #[Route('/register', name: 'app_register', methods: ['GET', 'POST'])]
    public function register(
        Request $request, 
        UserPasswordHasherInterface $userPasswordHasher, 
        Authenticator $authenticator, 
        EntityManagerInterface $entityManager,
        UserAuthenticatorInterface $userAuthenticator
    ): Response
    {
        $user = new User();
        $address = new Address();
        $referralCode = $request->query->get('ref');
        $referrer = null;
    
        if ($referralCode) {
            $referrer = $this->userRep->findOneBy(['referralCode' => $referralCode]);
            if ($referrer) {
                $user->addReferredBy($referrer); // Stocke temporairement le code de parrainage
            } else {
                $this->addFlash('danger', 'Code de parrainage inexistant');
                return $this->redirectToRoute('app_register');
            }
        }
    
        $userReferralCode = $this->referralService->generateReferralCode();
    
        $user
            ->setAddress($address)
            ->setReferralCode($userReferralCode);
    
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Set Address and Membership
            // Set latitude and longitude
            $latitude = $request->request->get('latitude');
            $longitude = $request->request->get('longitude');
            $address->setLatitude($latitude);
            $address->setLongitude($longitude);
    
            $referrerId = $request->request->get('referrer');
            if ($referrerId) {
                $referrer = $this->userRep->find($referrerId);
                if (!$referrer) {
                    throw new \Exception('Referrer not found');
                }
                $user->addReferredBy($referrer);
            }
    
            $user
                ->setRoles(['ROLE_USER'])
                ->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );
    
            $entityManager->persist($user);
            // $entityManager->flush();
    
            if ($referrer) {
                $referrer->addReferrer($user);
                $entityManager->persist($referrer);
                $entityManager->flush();
            }
    dd($user);
            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }
    
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'referrer' => $referrer
        ]);
    }
    

    #[Route('/privacy_policy', name: 'app_privacy')]
    public function privacy(){
        return $this->render('page/privacy.html.twig',[
            
        ]);
    }

}

