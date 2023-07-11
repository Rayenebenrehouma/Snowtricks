<?php

namespace App\Controller;

use App\Entity\Figure;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FigureDetailIdController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }

    #[Route('/figure-detail/{id}', name: 'app_figure_detail_id')]
    public function index($id): Response
    {
        $user = $this->getUser()->getId();
        if($id = $user){
            $figures = $this->entityManager->getRepository(Figure::class)->findByUser($id);

            return $this->render('figure_detail_id/index.html.twig',[
                'figure' => $figures
            ]);
        }else{
            dd("false");
        }

        return $this->render('figure_detail_id/index.html.twig');
    }
}
