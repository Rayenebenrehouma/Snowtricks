<?php

namespace App\Controller;

use App\Entity\Figure;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserFigureController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }

    #[Route('/user/figure', name: 'app_user_figure')]
    public function index(): Response
    {
        $email = "";
        $figures = $this->entityManager->getRepository(Figure::class)->findByEmail($email);

        dd($figures);

        return $this->render('user_figure/index.html.twig');
    }
}
