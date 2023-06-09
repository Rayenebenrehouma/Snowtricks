<?php

namespace App\Controller;

use App\Entity\Illustration;
use App\Entity\Video;
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

        $figures = $this->entityManager->getRepository(Figure::class)->findAll();
        foreach ($figures as $figureData){
            $figure = new Figure();
            $figure->setId($figureData->getId());
            $figure->setNom($figureData->getNom());
            $figure->setDescription($figureData->getDescription());
            $figure->setGroupe($figureData->getGroupe());
            $figure->setVideo($figureData->getVideo());

            $illustrations = $this->entityManager->getRepository(Illustration::class)->findByLink($figure->getId());
            foreach ($illustrations as $illustration){
                $figure->addIllustration($illustration);
            }


            $videos = $this->entityManager->getRepository(Video::class)->findByLink($figure->getId());
            foreach ($videos as $video) {
                $figure->addVideoId($video);
            }
            $figureList[] = $figure;
        }

        return $this->render('admin/index.html.twig', [
            'figures' => $figureList
        ]);
    }
}