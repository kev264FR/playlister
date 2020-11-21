<?php

namespace App\Controller;

use App\Entity\Content;
use App\Entity\Platform;
use App\Entity\Playlist;
use App\Form\ContentType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/content")
 */
class ContentController extends AbstractController
{
    /**
     * @Route("/add/{id}", name="content_add")
     */
    public function contentForm(Request $request, Playlist $playlist = null): Response
    {
        if (!$this->getUser()) {
            return $this->json([
                'status'=>'error',
                'data'=>'Vous devez ètre connecté'
            ]);
        }

        if (!$playlist) {
            return $this->json([
                'status'=>'error',
                'data'=>'Playlist non existante'
            ]);
        }

        if ($this->getUser() != $playlist->getUser()) {
            return $this->json([
                'status'=>'error',
                'data'=>'Cette playliste n\'est pas a vous'
            ]);
        }

        $manager = $this->getDoctrine()->getManager();

        $content = new Content();
        $form = $this->createForm(ContentType::class, $content);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $url = $form->get('url')->getData();
            if (!$content->getTitle() || !$url) {
                return $this->json([
                    'status'=>'error',
                    'data'=>'Formulaire non complet'
                ]);
            }
            
            $platform = $this->getDoctrine()
                            ->getRepository(Platform::class)
                            ->findWhereUrl($url);

            if ($platform) {
                $content->setContentId(str_replace($platform->getBaseUrl(), '', $url));
                $content->setPlatform($platform);
                $content->setPlaylist($playlist);
                $content->setCreatedAt(new \DateTime());
                $playlist->setLastUpdate($content->getCreatedAt());

                $manager->persist($content);
                $manager->flush();

                return $this->json([
                    'status'=>'success',
                    'data'=> $this->renderView('content/content_part.html.twig', [
                            'playlist'=>$playlist
                    ])
                ]);

            }else{
                return $this->json([
                    'status'=>'error',
                    'data'=>'Plateforme non prise en charge'
                ]);
            }
            
        }

        return $this->json([
            'status'=>'success',
            'data'=> $this->renderView('content/content_form.html.twig', [
                        'form'=>$form->createView()
            ])
        ]);
        // return $this->render('content/content_form.html.twig', [
        //     'form'=>$form->createView()
        // ]);
    }

    /**
     * @Route("/delete/{id}", name="delete_content")
     * @IsGranted("ROLE_USER")
     */
    public function removeContent(Content $content = null){
        if ($content == null) {
            $this->addFlash('error', 'Contenu non trouvé');
            return $this->redirectToRoute('playlists');
        }
        if ($content->getPlaylist()->getUser() != $this->getUser()) {
            $this->addFlash('error', 'Vous ne pouvez pas modifier cette playlist');
            return $this->redirectToRoute('playlists');
        }

        $manager = $this->getDoctrine()->getManager();
        $manager->remove($content);
        $manager->flush();

        return $this->redirectToRoute('playlist_detail', [
            'id'=>$content->getPlaylist()->getId()
        ]);
    }
}
