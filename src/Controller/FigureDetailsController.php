<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\Figure;
use App\Entity\Illustration;
use App\Entity\User;
use App\Form\CommentaireType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FigureDetailsController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }

    #[Route('/details-figure/{id}', name: 'figure_details')]
    public function index(Request $request,$id): Response
    {
        $user = $this->getUser();
        $figure = $this->entityManager->getRepository(Figure::class)->findOneById($id);
        $illustrations = $this->entityManager->getRepository(Illustration::class)->findByLink($id);

        $newFigure = new Figure();
        foreach ($illustrations as $illustration){
            $newFigure->addIllustration($illustration);
        }

        $commentary_list = $this->entityManager->getRepository(Commentaire::class)->findByFigure($id);
        $commentary = new Commentaire();
        $form = $this->createForm(CommentaireType::class, $commentary);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $commentary = $form->getData();
            $date = new \DateTimeImmutable();
            $date->modify("+2 hour");
            $date->format("d/m/Y H:i:s");
            $commentary->setCreatedAt($date);
            $commentary->setFigure($figure);
            $commentary->setUser($user);
            $commentary->setCommentaireFirstname($user->getFirstname());

            $this->entityManager->persist($commentary);
            $this->entityManager->flush();


            return $this->redirectToRoute('accueil');
        }

        return $this->render('figure_details/index.html.twig', [
            'figure' => $figure,
            'illustration' => $newFigure,
            'commentaire' => $commentary_list,
            'form' => $form->createView()
        ]);
    }
}
