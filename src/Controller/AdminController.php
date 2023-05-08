<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Figure;

class AdminController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }

    #[Route('/administration', name: 'admin')]
    public function index(Request $request): Response
    {
        $figure = $this->entityManager->getRepository(Figure::class)->findAll();

        return $this->render('admin/index.html.twig', [
            'figure' => $figure
        ]);
    }
}