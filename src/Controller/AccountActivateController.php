<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountActivateController extends AbstractController
{
    #[Route('/account/activate', name: 'app_account_activate')]
    public function index(): Response
    {
        return $this->render('account_activate/index.html.twig', [
            'controller_name' => 'AccountActivateController',
        ]);
    }
}
