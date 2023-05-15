<?php

namespace App\Controller;

use App\Entity\Figure;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FigureDetailsController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }

    #[Route('/details-figure/{id}', name: 'figure_details')]
    public function index($id): Response
    {
        $figure = $this->entityManager->getRepository(Figure::class)->findOneById($id);

        return $this->render('figure_details/index.html.twig', [
            'figure' => $figure
        ]);
    }
}
