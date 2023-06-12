<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Repository\IllustrationRepository;
use App\Repository\VideoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DeleteFigureController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }

    #[Route('/supprimer-une-figure/{id}', name: 'delete_figure')]
    public function index(Request $request, IllustrationRepository $illustrationRepository,VideoRepository $videoRepository, $id): Response
    {
        $figure = $this->entityManager->getRepository(Figure::class)->find($id);

        if ($figure != null){
            $illustrations = $illustrationRepository->findBy(['link' => $figure]);
            foreach ($illustrations as $illustration) {
                $this->entityManager->remove($illustration);
            }
            // Flush les changements dans la base de données
            $this->entityManager->flush();

            $videos = $videoRepository->findBy(['link' => $figure]);
            foreach ($videos as $video) {
                $this->entityManager->remove($video);
            }

            // Flush les changements dans la base de données
            $this->entityManager->flush();


            $this->entityManager->remove($figure);
            $this->entityManager->flush();
            $this->addFlash('danger',
                'Votre Tricks a bien été supprimer !'
            );
            return $this->redirectToRoute('accueil');
        }else{
            $this->addFlash('danger',
            'Tricks inconnue !'
            );
            return $this->redirectToRoute('accueil');
        }
    }
}
