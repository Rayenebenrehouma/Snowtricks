<?php

namespace App\Controller;

use App\Entity\Illustration;
use App\Entity\Video;
use App\Form\IllustrationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Figure;
use App\Form\AjoutFigureType;
use Symfony\Component\Security\Core\Security;
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
        $illustration = new Illustration();
        $form = $this->createForm(AjoutFigureType::class, $figure);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $user = $this->getUser();

            $figures = $form->getData();
            //On récupère les images transmises
            $imageFile = $form->get('illustrations')->getData();
            //On boucle si il y en a plusieurs
            if ($imageFile) {
                foreach ($imageFile as $image) {
                    //on génère pour chacune un nouveau nom de fichier
                    $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                    //dd($originalFilename);
                    // this is needed to safely include the file name as part of the URL
                    $safeFilename = $slugger->slug($originalFilename);
                    //dd($safeFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$image->guessExtension();
                    //dd($newFilename);
                    // Move the file to the directory where brochures are stored
                    try {
                        $image->move(
                            $this->getParameter('image_directory'),
                            $newFilename
                        );
                        $illustration = new Illustration();
                        $illustration->setLink($figure);
                        $illustration->setImageName($newFilename);
                        $illustration->setIsDeleted(0);
                        $figure->addIllustration($illustration);

                        $this->entityManager->persist($illustration);
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }
                    // updates the 'brochureFilename' property to store the PDF file name
                    // instead of its contents
                }
            }
            //Instancier la nouvelle video :
            $video = new Video();
            //Rendre le lien potable:
            $video_url = $figure->getVideo();
            $video_url = $this->video_cleanURL_YT($video_url);
            $figure->setVideo($video_url);

            $video_url = explode('&', $video_url)[0];
            $figure->setVideo($video_url);
            //Lui attribuer les valeurs
            $video->setLink($figure);
            $video->setVideoName($video_url);
            $video->setIsDeleted(0);
            $figure->addVideoId($video);



            if ($figure->getVideo() == ""){
                $this->addFlash(
                    'danger',
                    'Votre URL Youtube n\'est pas au bon format !'
                );
            }
            $date = new \DateTimeImmutable();
            $date->modify("+2 hour");
            $date->format("d/m/Y H:i:s");
            $figure->setCreatedAt($date);
            $figure->setUser($user);

            $this->entityManager->persist($figure);
            $this->entityManager->flush();
            $this->addFlash(
                'success',
                'Votre Tricks a bien été ajouté !'
            );
            return $this->redirectToRoute('accueil');
        }


        return $this->render('ajout_figure/index.html.twig', [
            'form' => $form->createView(),
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