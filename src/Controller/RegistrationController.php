<?php
namespace App\Controller;

// use App\Entity\User;
// use App\Entity\Address;
// use App\Entity\Document;
// use App\Entity\Membership;
// use App\Event\UserRegisteredEvent;
// use App\Form\RegistrationFormType;
// use App\Repository\UserRepository;
// use App\Service\ReferralService;
// use Doctrine\ORM\EntityManagerInterface;
// use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
// use Symfony\Component\Routing\Annotation\Route;
// use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
// use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
// use Symfony\Component\HttpFoundation\Request;
// use Symfony\Component\HttpFoundation\Response;
// use App\Security\Authenticator;
// use App\Service\PdfService;
// use Symfony\Component\EventDispatcher\EventDispatcherInterface;

// class RegistrationController extends AbstractController
// {
//     public function __construct(
//         private ReferralService $referralService,
//         private UserRepository $userRep
//         )
//     {
        
//     }
//     #[Route('/register', name: 'app_register', methods: ['GET', 'POST'])]
//     public function register(
//         Request $request, 
//         UserPasswordHasherInterface $userPasswordHasher, 
//         EventDispatcherInterface $eventDispatcher, 
//         EntityManagerInterface $entityManager,
//         PdfService $pdfService
//     ): Response
//     {
//         $user = new User();
//         $address = new Address();
//         $referralCode = $request->query->get('ref');
//         $referrer = null;
    
//         if ($referralCode) {
//             $referralCode = rawurldecode($referralCode);
//             $referrer = $this->userRep->findOneBy(['referralCode' => $referralCode]);
//             if (!$referrer) {
//                 $this->addFlash('danger', 'Code de parrainage inexistant');
//                 return $this->redirectToRoute('app_register'); 

//             }
//         }
    
//         $userReferralCode = $this->referralService->generateReferralCode();
    
//         $user
//             ->setAddress($address)
//             ->setReferralCode($userReferralCode);
    
//         $form = $this->createForm(RegistrationFormType::class, $user);
//         $form->handleRequest($request);
    
//         if ($form->isSubmitted() && $form->isValid()) {
//             // Set Address and Membership
//             // Set latitude and longitude
//             $data = $form->getData();
//             $latitude = $request->request->get('latitude');
//             $longitude = $request->request->get('longitude');
//             $address->setLatitude($latitude);
//             $address->setLongitude($longitude);
    
//             $referrerId = $request->request->get('referrer');
//             if ($referrerId) {
//                 $referrer = $this->userRep->find($referrerId);
//                 if (!$referrer) {
//                     return null;
//                 }
//                 $user->addReferredBy($referrer);
//             }
    
//             $user
//                 ->setRoles(['ROLE_USER'])
//                 ->setPassword(
//                     $userPasswordHasher->hashPassword(
//                         $user,
//                         $form->get('plainPassword')->getData()
//                     )
//                 );
            
//             $entityManager->persist($user);
//             $entityManager->flush();
    
//             if ($referrer) {
//                 $referrer->addReferrer($user);
//                 $entityManager->persist($referrer);
//                 $entityManager->flush();
//             }

//             $directory = $this->getParameter('documents_directory');
//             $filename = 'fiche_d\'adhésion_'.$user->getName().'-'.$user->getFirstName(). uniqid() .'.pdf';

//             $document = new Document();
//             $document->setTitle('Fiche d\'adhésion');
//             $document->setFile($filename);
//             $document->setCreatedAt(new \DateTime());
//             $document->setAuthor($user);

//             // Enregistrement du document dans la base de données
//             $entityManager->persist($document);
//             $entityManager->flush();

//             // Déclencher l'évènement
//             $event = new UserRegisteredEvent($user);

//             $eventDispatcher->dispatch($event, UserRegisteredEvent::NAME);

//             // Retourne le fichier PDF généré pour téléchargement
            
//             return $pdfService->generatePdf(
//                 'user_crud/fiche.html.twig',
//                 $directory, 
//                 ['user'=>$user],
//                 $filename
//             );
//         }
    
//         return $this->render('registration/register.html.twig', [
//             'registrationForm' => $form->createView(),
//             'referrer' => $referrer
//         ]);
//     }
    

//     #[Route('/privacy_policy', name: 'app_privacy')]
//     public function privacy(){
//         return $this->render('page/privacy.html.twig',[
            
//         ]);
//     }

// }

// Le code que bing m'a donné
namespace App\Controller;

use App\Entity\User;
use App\Entity\Address;
use App\Entity\Document;
use App\Event\UserRegisteredEvent;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Service\PdfService;
use App\Service\ReferralService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationController extends AbstractController
{
    private $referralService;
    private $userRep;

    public function __construct(ReferralService $referralService, UserRepository $userRep)
    {
        $this->referralService = $referralService;
        $this->userRep = $userRep;
    }

    #[Route('/register', name: 'app_register', methods: ['GET', 'POST'])]
    public function register(
        Request $request, 
        UserPasswordHasherInterface $userPasswordHasher, 
        EventDispatcherInterface $eventDispatcher, 
        EntityManagerInterface $entityManager,
        PdfService $pdfService
    ): Response
    {
        $user = new User();
        $address = new Address();
        $referralCode = $request->query->get('ref');
        $referrer = null;

        // Vérification du code de parrainage
        if ($referralCode) {
            $referrer = $this->userRep->findOneBy(['referralCode' => $referralCode]);
            if ($referrer) {
                // Stocke temporairement le code de parrainage
                $user->addReferredBy($referrer);
            } else {
                // Ajoute un message flash et redirige si le code de parrainage est inexistant
                $this->addFlash('danger', 'Code de parrainage inexistant');
                return $this->redirectToRoute('app_register');
            }
        }

        // Génère un code de parrainage pour le nouvel utilisateur
        $userReferralCode = $this->referralService->generateReferralCode();

        // Associe l'adresse et le code de parrainage à l'utilisateur
        $user
            ->setAddress($address)
            ->setReferralCode($userReferralCode);

        // Crée et traite le formulaire d'inscription
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupère les données du formulaire
            $data = $form->getData();
            $latitude = $request->request->get('latitude');
            $longitude = $request->request->get('longitude');

            // Vérification des coordonnées
            if ($latitude && $longitude) {
                $address->setLatitude($latitude);
                $address->setLongitude($longitude);
            }

            // Vérifie et associe le référent si un ID de référent est fourni
            $referrerId = $request->request->get('referrer');
            if ($referrerId) {
                $referrer = $this->userRep->find($referrerId);
                if (!$referrer) {
                    // Ajoute un message flash et redirige si le référent n'est pas trouvé
                    $this->addFlash('danger', 'Référent non trouvé');
                    return $this->redirectToRoute('app_register');
                }
                $user->addReferredBy($referrer);
            }

            // Définit les rôles et le mot de passe de l'utilisateur
            $user
                ->setRoles(['ROLE_USER'])
                ->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );

            // Persiste l'utilisateur dans la base de données
            $entityManager->persist($user);
            $entityManager->flush();

            // Associe l'utilisateur au référent et persiste les modifications
            if ($referrer) {
                $referrer->addReferrer($user);
                $entityManager->persist($referrer);
                $entityManager->flush();
            }

            // Détermine le répertoire et le nom du fichier PDF
            $directory = $this->getParameter('documents_directory');
            $filename = 'fiche-d\'adhésion-'.$user->getName().'-'.$user->getFirstName(). uniqid() .'.pdf';

            // Crée un nouveau document et l'associe à l'utilisateur
            $document = new Document();
            $document->setTitle('Fiche d\'adhésion');
            $document->setFile($filename);
            $document->setCreatedAt(new \DateTime());
            $document->setAuthor($user);

            // Enregistre le document dans la base de données
            $entityManager->persist($document);
            $entityManager->flush();

            // Déclenche l'évènement d'inscription de l'utilisateur
            $event = new UserRegisteredEvent($user);
            $eventDispatcher->dispatch($event, UserRegisteredEvent::NAME);

            // Retourne le fichier PDF généré pour téléchargement
            return $pdfService->generatePdf(
                'user_crud/fiche.html.twig',
                $directory, 
                ['user' => $user],
                $filename
            );
        }

        // Rend la vue du formulaire d'inscription
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'referrer' => $referrer
        ]);
    }

    #[Route('/privacy_policy', name: 'app_privacy')]
    public function privacy(): Response
    {
        // Rend la vue de la politique de confidentialité
        return $this->render('page/privacy.html.twig');
    }
}
