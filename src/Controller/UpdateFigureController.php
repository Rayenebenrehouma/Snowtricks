<?php

namespace App\Controller;

use App\Form\UpdateFigureFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Figure;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

class UpdateFigureController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }

    #[Route('/update-une-figure/{id}', name: 'app_update_figure')]
    public function edit(Request $request, SluggerInterface $slugger, $id): Response
    {
        $figure = $this->entityManager->getRepository(Figure::class)->find($id);
        $form = $this->createForm(UpdateFigureFormType::class, $figure);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $figure = $form->getData();
            $date = new \DateTimeImmutable();
            $date->format("d/m/Y H:i:s");
            $figure->setCreatedAt($date);
            //dd($form->get('illustration')->getData());
            $imageFile = $form->get('illustration')->getData();
            $oldImage = $figure->getIllustration();

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
                    $path = $this->getParameter('image_directory')."/".$oldImage;
                    if (!unlink($path)){
                        echo "You have an error !";
                    }
                    /* A REVOIR COMMENT CETTE FONCTION MARCHE
                    $fileSystem = new Filesystem();
                    $fileSystem->symlink();
                    $fileSystem->exists('Snowtricks/public/assets/image_directory');
                    $verify = $fileSystem->remove($this->getParameter('public_directory').'/assets/image_directory/'.$oldImage);
                    dd($verify);
                    $fileSystem->remove($oldImage);
                    */
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
                    'Votre Tricks a bien été Mis à jour !'
                );
                return $this->redirectToRoute('accueil');
            }
        }

        return $this->render('update_figure/index.html.twig', [
            'figure' => $figure,
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
