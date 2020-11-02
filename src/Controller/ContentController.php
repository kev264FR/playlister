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
 * @IsGranted("ROLE_USER")
 */
class ContentController extends AbstractController
{
    /**
     * @Route("/add/{id}", name="content_add")
     */
    public function contentForm(Request $request, Playlist $playlist): Response
    {
        $manager = $this->getDoctrine()->getManager();

        $content = new Content();
        $form = $this->createForm(ContentType::class, $content);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $url = $form->get("url")->getData();
            $platforms = $this->getDoctrine()
                            ->getRepository(Platform::class)
                            ->findAll();
            foreach ($platforms as $platform) {
                if (strpos($url, $platform->getBaseUrl()) !== false) {
                    $content->setContentId(str_replace($platform->getBaseUrl(), "", $url));
                    $content->setPlatform($platform);
                    $content->setPlaylist($playlist);
                    $content->setCreatedAt(new \DateTime());
                    $playlist->setLastUpdate($content->getCreatedAt());

                    $manager->persist($content);
                    $manager->flush();

                    return $this->redirectToRoute("playlist_detail", [
                        "id"=>$playlist->getId()
                    ]);
                }
            }
            
        }
        return $this->render('content/content_form.html.twig', [
            "form"=>$form->createView()
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete_content")
     */
    public function removeContent(Content $content = null){
        if ($content == null) {
            $this->addFlash("error", "Contenu non trouvé");
            return $this->redirectToRoute("playlists");
        }
        if ($content->getPlaylist()->getUser() != $this->getUser()) {
            $this->addFlash("error", "Vous ne pouvez pas modifier cette playlist");
            return $this->redirectToRoute("playlists");
        }

        $manager = $this->getDoctrine()->getManager();
        $manager->remove($content);
        $manager->flush();

        return $this->redirectToRoute("playlist_detail", [
            "id"=>$content->getPlaylist()->getId()
        ]);
    }
}
