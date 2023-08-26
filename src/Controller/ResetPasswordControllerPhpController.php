<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use App\Form\ResetPasswordType;
use App\Entity\ResetPassword;
use App\Classe\Mail;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class ResetPasswordControllerPhpController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }

    #[Route('/mot-de-passe-oublie', name: 'reset_password')]
    public function index(Request $request): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('accueil');
        }

        if($request->get('email')){
            $user = $this->entityManager->getRepository(User::class)->findOneByEmail($request->get('email'));

            if($user){
                //Enregistrer en base la demande de reset password
                $reset_password = new ResetPassword();
                $reset_password->setUser($user);
                $reset_password->setToken(uniqid());
                $reset_password->setCreatedAt(new \DateTimeImmutable());
                $this->entityManager->persist($reset_password);
                $this->entityManager->flush();

                // 2 : Envoyer un email à l'utilisateur avec un lien lui permettant de mettre à jour son mot de passe.
                $url = $this->generateUrl('update_password', [
                    'token' => $reset_password->getToken()
                ]);

                //FAIRE UN FLUSH ET CONFIGURER LES EMAILS
                $content = "Bonjour ".$user->getFirstname()."<br/>Vous avez demandé à réinitialiser votre mot de passe sur le site Snwotricks.<br/><br/>";
                $content .= "Merci de bien vouloir cliquer sur le lien suivant pour <a href='"."http://127.0.0.1:8000".$url."'> mettre à jour votre mot de passe</a>.";

                $mail = new Mail();
                $mail->sendResetPassword($user->getEmail(), $user->getFirstname().' '.$user->getLastname(), 'Réinitialiser votre mot de passe sur Snowtricks', "$content");

                //Faire un addFlash();
            }else{
                //Faire un addFlash();
            }
        }
        return $this->render('reset_password/index.html.twig');
    }

    #[Route('/modifier-mon-mot-de-passe/{token}', name: 'update_password')]

    public function update(Request $request, UserPasswordHasherInterface $hasher, $token): Response
    {
        $reset_password = $this->entityManager->getRepository(ResetPassword::class)->findOneByToken($token);

        if(!$reset_password){
            return $this->redirectToRoute('reset_password');
        }

        //vérifier si le createdAt = now -3h
        $now = new \DateTimeImmutable();
        if($now > $reset_password->getCreatedAt()->modify('+ 3 hour')){
            //Faire une notice addFlash();
            return $this->redirectToRoute('reset_password');
        };

        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $new_pwd = $form->get('new_password')->getData();
            $password = $hasher->hashPassword($reset_password->getUser(), $new_pwd);

            $reset_password->getUser()->setPassword($password);
            $this->entityManager->flush();
            //Faire un addFlash();
            $notification = "Votre mot de passe a bien été mis à jour";
        }else{
            $notification = "Erreur";
        }
        return $this->render('reset_password/update.html.twig', [
            'form' => $form->createView()
        ]);
    }
}