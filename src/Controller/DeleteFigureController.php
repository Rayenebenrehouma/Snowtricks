<?php

namespace App\Controller;

use App\Entity\Figure;
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
    public function index(Request $request, $id): Response
    {
        $figure = $this->entityManager->getRepository(Figure::class)->find($id);

        if ($figure != null){
            $user = $this->getUser();
            $figureUser = $figure->getUser()->getId();

            if ($user->getRoles()[0] == "ROLE_ADMIN" OR $user->getId() == $figureUser){

                $this->entityManager->remove($figure);
                $this->entityManager->flush();

                $this->addFlash('danger',
                    'Votre Tricks a bien été supprimer !'
                );
                return $this->redirectToRoute('accueil');

            }else{
                return $this->redirectToRoute('404');
            }
        }else{
            $this->addFlash('danger',
            'Tricks inconnue !'
            );
            return $this->redirectToRoute('accueil');
        }
    }
}
