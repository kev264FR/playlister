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
        
        if (!$request->isXmlHttpRequest()) {  // Verification si la requete est bien de l'AJAX
            $this->addFlash('error', 'Action impossible');
            return $this->redirectToRoute("playlists");
        }

        if (!$this->getUser()) { // Verification si un utilisateur est en session
            return $this->json([
                'status'=>'error',
                'data'=>'Vous devez ètre connecté <a href="'.$this->generateUrl('app_login').'">Vers la connexion</a>'
            ]);
        }

        if (!$playlist) { // Verification si la playliste existe
            return $this->json([
                'status'=>'error',
                'data'=>'Playlist non existante'
            ]);
        }

        if (!$this->isGranted('ROLE_ADMIN')) { // Si pas de ROLE_ADMIN 
            if ($this->getUser() != $playlist->getUser()) { // verfication si l'utilisateur est propriétaire de la playlist
                return $this->json([
                    'status'=>'error',
                    'data'=>'Cette playlist n\'est pas a vous'
                ]);
            }
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
                // En fonction de la plateforme on cherche l'ID de la vidéo

                $curl = curl_init(str_replace('_content_', $videoId, $platform->getApi())); // Préparation de l'appel api
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // Les données sont mise en variables et non affiché
                    $result = curl_exec($curl); // Execution de l'appel
                curl_close($curl); // Fermeture de la session cURL ( obsolète avec PHP 8 )
                $result = json_decode($result, true); // Résultat json transformé en tableau associatif ( second param )
                
                switch ($platform->getName()) { // recherche du titre en fonction de la plateforme 
                    case 'youtube':
                            if (array_key_exists(0, $result['items'])) {
                                // Si l'entré items existe dans le tableau on recupère le titre
                                $title = $result['items'][0]['snippet']['title']; 

                            }else $title = null;
                            // Sinon le titre est null
                        break;

                    case 'dailymotion':
                            if (array_key_exists('title', $result)) {
                                $title = $result['title'];
                                
                            }else $title = null;
                        break;

                    default: // Par défaut $title vaut null
                            $title = null;
                        break;
                }
                
                
                if ($title) {
                    foreach ($playlist->getContents() as $oldContent) {
                        if ($oldContent->getTitle() == $title and $oldContent->getContentId() == $videoId) {
                            // Verification si la vidéo n'est pas déjà présente
                            return $this->json([ 
                                'status'=>'error',
                                'data'=>'Cette vidéo est déjà présente'
                            ]);
                        }
                    }

                    $content->setContentId($videoId);
                    $content->setTitle($title);
                    $content->setPlatform($platform);
                    $content->setCreatedAt(new \DateTime());

                    $playlist->addContent($content);
                    $playlist->setLastUpdate($content->getCreatedAt());
                    //  Pour avoir exactement la meme date 

                    $manager->flush();
                    
                    $html = $this->renderView('content/content_part.html.twig', [
                        'playlist'=>$playlist,
                        'ajax'=>true
                    ]);
                    
                    return $this->json([
                        'status'=>'success',
                        'data'=> $html
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
            'status'=>'success-form',
            'data'=> $this->renderView('content/content_form.html.twig', [
                        'form'=>$form->createView(),
                        'playlist'=>$playlist
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
