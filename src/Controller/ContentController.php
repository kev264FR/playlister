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
            if (!$url) {
                return $this->json([
                    'status'=>'error',
                    'data'=>'Formulaire non complet'
                ]);
            }            
            
            $platform = $this->getDoctrine()
                            ->getRepository(Platform::class)
                            ->findWhereUrl($url);

            if ($platform) {

                $videoId = str_replace($platform->getBaseUrl(), '', $url);
                $curl = curl_init(str_replace('_content_', $videoId, $platform->getApi()));
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); //Return data instead printing directly in Browser
                    $result = curl_exec($curl);
                curl_close($curl);
                $result = json_decode($result, true);
                
                switch ($platform->getName()) {
                    case 'youtube':
                            if (array_key_exists(0, $result['items'])) {
                                $title = $result['items'][0]['snippet']['title'];
                            }else $title = null;
                        break;

                    case 'dailymotion':
                            if (array_key_exists('title', $result)) {
                                $title = $result['title'];
                                
                            }else $title = null;
                        break;

                    default:
                            $title = null;
                        break;
                }
                
                
                
                if ($title) {
                    $content->setContentId($videoId);
                    $content->setTitle($title);
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
                        'data'=>'Vidéo non trouvé'
                    ]);
                }
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
        $playlist = $content->getPlaylist();
        if ($playlist->getUser() != $this->getUser()) {
            $this->addFlash('error', 'Vous ne pouvez pas modifier cette playlist');
            return $this->redirectToRoute('playlists');
        }

        
        if ($playlist->getContents()->count() == 1) {
            $playlist->setPublic(false);
        }

        $manager = $this->getDoctrine()->getManager();
        $manager->remove($content);
        $manager->flush();

        return $this->redirectToRoute('playlist_detail', [
            'id'=>$playlist->getId()
        ]);
    }
}
