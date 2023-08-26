<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\Figure;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccueilController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'accueil')]

    public function index(): Response
    {
        $figure = $this->entityManager->getRepository(Figure::class)->findAll();
        //dd($figure);
        return $this->render('accueil/index.html.twig',[
            'figure' => $figure
        ]);
    }
}
