<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Figure;
use App\Form\AjoutFigureType;

class AjoutFigureController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }

    #[Route('/ajouter-une-figure', name: 'app_ajout_figure')]
    public function index(Request $request): Response
    {
        $figure = new Figure();
        $form = $this->createForm(AjoutFigureType::class, $figure);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $figure = $form->getData();


            $video_url = $figure->getVideo();
            $video_url = $this->video_cleanURL_YT($video_url);
            $figure->setVideo($video_url);

            $str = $figure->getVideo();
            $str = explode('&', $str)[0];
            $figure->setVideo($str);

// $str vaut maintenant "visible"
            //dd($figure->getVideo());

            $this->entityManager->persist($figure);
            $this->entityManager->flush();

            return $this->redirectToRoute('accueil');
        }
        return $this->render('ajout_figure/index.html.twig', [
            'form' => $form->createView()
        ]);
    }

    public function video_cleanURL_YT(string $video_url)
    {
        if (!empty($video_url)) {
            $video_url = str_replace('youtu.be/', 'www.youtube.com/embed/', $video_url);
            $video_url = str_replace('www.youtube.com/watch?v=', 'www.youtube.com/embed/', $video_url);
        }
        // -----------------
        return $video_url;
    }
}