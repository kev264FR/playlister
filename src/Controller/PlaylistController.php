<?php

namespace App\Controller;

use App\Entity\Playlist;
use App\Form\PlaylistType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlaylistController extends AbstractController
{
    /**
     * @Route("/", name="playlists")
     */
    public function index(Request $request): Response
    {
        $search = $request->get("search");
        if ($search) {
            $playlists = $this->getDoctrine()
                            ->getRepository(Playlist::class)
                            ->getAllPublicNamed($search);
        }else{
            $playlists = $this->getDoctrine()
                        ->getRepository(Playlist::class)
                        ->findAll();
                        // ->getAllPublic();
        }
        
        return $this->render('playlist/index.html.twig', [
            "playlists"=>$playlists,
            "search"=>$search
        ]);
    }

    /**
     * @Route("/playlist/new", name="playlist_new")
     * @Route("/playlist/edit/{id}", name="playlist_edit")
     */
    public function playlistForm(Request $request, Playlist $playlist = null){
        $manager = $this->getDoctrine()->getManager();
        $edit = false;

        if ($playlist != null) {
            $edit = true;
        }else{
            $playlist = new Playlist();
        }

        $form = $this->createForm(PlaylistType::class, $playlist);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($edit) {
                $playlist->setLastUpdate(new \DateTime());
            }else{
                $playlist->setUser($this->getUser());
                $playlist->setCreatedAt(new \DateTime());
                $playlist->setPublic(false);
            }
            
            $manager->persist($playlist);
            $manager->flush();

            return $this->redirectToRoute("playlists");
        }

        return $this->render("playlist/playlist_form.html.twig", [
            "form"=>$form->createView(),
            "edit"=>$edit,
        ]);
    }

    /**
     * @Route("/playlist/delete/{id}", name="playlist_delete")
     */
    public function deletePlaylist(Playlist $playlist = null){
        $manager = $this->getDoctrine()->getManager();
        if ($playlist->getContents()->count() == 0) {
            $manager->remove($playlist);
            $manager->flush();

            $this->addFlash("success", "La playlist <strong>".$playlist->getTitle()."</strong> a été supprimé");
            return $this->redirectToRoute("playlists");
        }

        $this->addFlash("error", "Suppression impossible");
        return $this->redirectToRoute("playlists");
    }

    /**
     * @Route("/playlist/{id}", name="playlist_detail")
     */
    public function detailPlaylist(Playlist $playlist = null){

        if (!$playlist || !$playlist->getPublic()) {
            $this->addFlash("error", "Cette playlist est privée ou n'existe pas");
            return $this->redirectToRoute("playlists");
        }
        return $this->render("playlist/playlist_detail.html.twig", [
            "playlist"=>$playlist,
        ]);
    }
}
