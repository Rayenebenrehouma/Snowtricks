<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Classe\Mail;
use App\Entity\AccountActivate;
use App\Repository\UserRepository;

class RegisterController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }

    #[Route('/inscription', name: 'register')]
    public function index(Request $request, UserPasswordHasherInterface $hasher): Response
    {
        $notification = null;
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $user = $form->getData();
            $search_email = $this->entityManager->getRepository(User::class)->findOneByEmail($user->getEmail());
            //Si l'email n'existe pas en bdd
            if(!$search_email) {
                //Ici on hash le password pour le rendre cryptée
                $password = $hasher->hashPassword($user, $user->getPassword());

                $user->setPassword($password);
                //Et on envoie en bdd
                $roles = $user->getRoles();
                $user->setRoles($roles);

                $this->entityManager->persist($user);
                $this->entityManager->flush();

                //On crée notre accountactivate pour envoyer notre email avec le token
                $account_activate = new AccountActivate();
                $account_activate->setUser($user);
                $account_activate->setToken(uniqid());
                $account_activate->setCreatedAt(new \DateTimeImmutable());
                $this->entityManager->persist($account_activate);
                $this->entityManager->flush();
                $token = $account_activate->getToken();
                $firstname = $user->getFirstname();
                $mail = new Mail();
                $content = "Bonjour et bienvenue chez Snowtricks"." Il ne vous reste plus qu'une étape pour profiter pleinement de toutes fonctionnalités de votre compte, n'attendez plus !";

                $mail->send(
                    "snowtricksprojet@gmail.com",
                    "Rayen",
                    "Activation de votre compte Snowtricks",
                    "Bonjour et bienvenu chez Snowtricks, il ne vous reste plus qu'un étape pour profiter pleinement de toutes les fonctionnalité de votre compte . N'hésitez plus =>",
                    "$token"
                );

                $notification = "Votre inscription c'est correctement déroulée. Veuillez activer votre compte ";

                //return $this->redirectToRoute('accueil');
            }else{
                $notification = "L'email que vous avez renseigné existe déjà";
            }

        }

        return $this->render('register/index.html.twig', [
            'form' => $form->createView(),
            'notification' => $notification
        ]);
        return $this->redirectToRoute('accueil');
    }

    #[Route('/verif/{token}', name: 'verif_user')]
    public function verifyUser(Request $request, $token, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        $account_activate = $this->entityManager->getRepository(AccountActivate::class)->findOneByToken($token);

        //vérifier si le createdAt = now -3h
        $now = new \DateTimeImmutable();
        if ($now > $account_activate->getCreatedAt()->modify('+ 3 hour')) {
            //Si il expire recréer un token et envoyé un nouvelle email
            return $this->redirectToRoute('resend_verif');
        } else {
            $user_id = ($account_activate->getUser()->getId());
            $original_account = $this->entityManager->getRepository(User::class)->findOneById($user_id);
            $original_account->setIsVerified(1);
            $this->entityManager->flush();
            return $this->redirectToRoute('accueil');
        }
    }

    #[Route('/renvoie-verif/{token}', name: 'resend_verif')]
    public function resendVerif(UserRepository $userRepository, $token): Response
    {
        $user = $this->getUser();
        $token = uniqid();


        $account_activate = $this->entityManager->getRepository(AccountActivate::class)->findOneByUser($user);
        $account_activate->setToken($token);
        $account_activate->setCreatedAt(new \DateTimeImmutable());
        $this->entityManager->persist($account_activate);
        $this->entityManager->flush();

        $token = $account_activate->getToken();


        $mail = new Mail();
        $content = "Bonjour et bienvenue chez Snowtricks"." Il ne vous reste plus qu'une étape pour profiter pleinement de toutes fonctionnalités de votre compte, n'attendez plus !";

        $mail->send(
            "snowtricksprojet@gmail.com",
            "Rayen",
            "Activation de votre compte Snowtricks",
            "Bonjour et bienvenu chez Snowtricks, il ne vous reste plus qu'un étape pour profiter pleinement de toutes les fonctionnalité de votre compte . N'hésitez plus =>",
            "$token"
        );

        $notification = "Votre inscription c'est correctement déroulée. Veuillez activer votre compte ";

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        if ($user->getIsVerified()) {
            return $this->redirectToRoute('accueil');
        }

        return $this->redirectToRoute('accueil');
    }
}
