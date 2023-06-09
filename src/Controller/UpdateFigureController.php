<?php

namespace App\Controller;

use App\Entity\Illustration;
use App\Entity\Video;
use App\Form\UpdateFigureFormType;
use App\Repository\IllustrationRepository;
use App\Repository\ImageRepository;
use App\Repository\VideoRepository;
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
        $oldImage = $this->entityManager->getRepository(Illustration::class)->findByLink($id);
        $oldVideo = $this->entityManager->getRepository(Video::class)->findByLink($id);
        $form = $this->createForm(UpdateFigureFormType::class, $figure);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $figure = $form->getData();
            $date = new \DateTimeImmutable();
            $date->format("d/m/Y H:i:s");
            $figure->setCreatedAt($date);
            //dd($form->get('illustration')->getData());
            $imageFile = $form->get('illustration')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($imageFile) {
                foreach ($imageFile as $image) {
                    $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                    //dd($originalFilename);
                    // this is needed to safely include the file name as part of the URL
                    $safeFilename = $slugger->slug($originalFilename);
                    //dd($safeFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $image->guessExtension();
                    //dd($newFilename);
                    // Move the file to the directory where brochures are stored
                    try {
                        $image->move(
                            $this->getParameter('image_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }
                    // updates the 'brochureFilename' property to store the PDF file name
                    // instead of its contents
                    // Créer une nouvelle instance d'Illustration
                    $illustration = new Illustration();
                    $illustration->setImageName($newFilename);
                    $illustration->setLink($figure);
                    $illustration->setIsDeleted(false);
                    $this->entityManager->persist($illustration);
                    $this->entityManager->flush();
                }
            }

            $video_url = $figure->getVideo();
            $video_url = $this->video_cleanURL_YT($video_url);
            $figure->setVideo($video_url);


            $video_url = $figure->getVideo();
            $video_url = explode('&', $video_url)[0];
            $new_video = new Video();

            $new_video->setVideoName($video_url);
            $new_video->setLink($figure);
            $new_video->setIsDeleted(0);
            $this->entityManager->persist($new_video);
            $this->entityManager->flush();
            //$figure->setVideo($video_url);

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
            'oldImg' =>$oldImage,
            'oldVideo' => $oldVideo,
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

    /**
     * @Route("/delete-image/{id}", name="delete_image")
     */
    public function deleteImage(Request $request, EntityManagerInterface $entityManager, IllustrationRepository $illustrationRepository, $id)
    {
        $image = $illustrationRepository->find($id);
        if (!$image) {
            throw $this->createNotFoundException('Image not found.');
        }

        // Supprimer l'image de la base de données
        $entityManager->remove($image);
        $entityManager->flush();

        // Supprimer le fichier de votre dossier
        $path = $this->getParameter('image_directory') . '/' . $image->getImageName();
        if (!unlink($path)) {
            dump("An error occurred while deleting the file: " . $path);
        }
        // Rediriger vers la page de mise à jour de la figure
        return $this->redirectToRoute('app_update_figure', ['id' => $image->getLink()->getId()]);
    }

    /**
     * @Route("/delete-video/{id}", name="delete_video")
     */
    public function deleteVideo(Request $request, EntityManagerInterface $entityManager, VideoRepository $videoRepository, $id)
    {
        $video = $videoRepository->find($id);
        if (!$video) {
            throw $this->createNotFoundException('Video not found.');
        }

        // Supprimer la vidéo de la base de données
        $entityManager->remove($video);
        $entityManager->flush();

        // Supprimer le fichier de votre dossier (si applicable)
        // Ajoutez le code ici pour supprimer le fichier de votre dossier

        // Rediriger vers la page de mise à jour de la figure
        return $this->redirectToRoute('app_update_figure', ['id' => $video->getLink()->getId()]);
    }
}