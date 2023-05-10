<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Figure;
use App\Form\AjoutFigureType;
use Symfony\Component\String\Slugger\SluggerInterface;

class AjoutFigureController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }

    #[Route('/ajouter-une-figure', name: 'app_ajout_figure')]
    public function index(Request $request, SluggerInterface $slugger): Response
    {
        $figure = new Figure();
        $form = $this->createForm(AjoutFigureType::class, $figure);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $figure = $form->getData();
            //dd($form->get('illustration')->getData());
            $imageFile = $form->get('illustration')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                //dd($originalFilename);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                //dd($safeFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
                //dd($newFilename);
                // Move the file to the directory where brochures are stored
                try {
                    $imageFile->move(
                        $this->getParameter('image_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $figure->setIllustration($newFilename);
            }

            $video_url = $figure->getVideo();
            $video_url = $this->video_cleanURL_YT($video_url);
            $figure->setVideo($video_url);

            $video_url = $figure->getVideo();
            $video_url = explode('&', $video_url)[0];
            $figure->setVideo($video_url);

            if ($figure->getVideo() == ""){
                $this->addFlash(
                    'danger',
                    'Votre URL Youtube n\'est pas au bon format !'
                );
            }else{
                $this->entityManager->persist($figure);
                $this->entityManager->flush();
                $this->addFlash(
                    'success',
                    'Votre Tricks a bien été ajouté !'
                );
                return $this->redirectToRoute('accueil');
            }



        }
        return $this->render('ajout_figure/index.html.twig', [
            'form' => $form->createView()
        ]);
    }

    public function video_cleanURL_YT(string $video_url)
    {
        if (!empty($video_url)) {
            $url_origin = $video_url;
            $video_url = str_replace('youtu.be/', 'www.youtube.com/embed/', $video_url);
            $video_url = str_replace('www.youtube.com/watch?v=', 'www.youtube.com/embed/', $video_url);
            $str = preg_match('%^((?:https?:)?\/\/)?((?:www|m)\.)?((?:youtube(-nocookie)?\.com|youtu.be))(\/(?:[\w\-]+\?v=|embed\/|v\/)?)([\w\-]+)(\S+)?$%', $video_url, $matches);
            if($str == 0){
                $video_url = "";
            }
        }
        // -----------------
        return $video_url;
    }
}