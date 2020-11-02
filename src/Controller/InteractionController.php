<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Playlist;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class InteractionController extends AbstractController
{
    /**
     * @Route("/like/{id}", name="user_like")
     * @IsGranted("ROLE_USER")
     */
    public function likePlaylist(Playlist $playlist = null){
        $manager = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        if ($playlist == null) {
            $this->addFlash("error", "Cette playlist n'existe pas");
            return $this->redirectToRoute("playlists");
        }
        if ($this->getUser() == $playlist->getUser()) {
            $this->addFlash("error", "Vous ne pouvez pas liker votre propre playlist");
            return $this->redirectToRoute("playlist_detail", [
                "id"=>$playlist->getId()
            ]);
        }
        if ($user->getLikedPlaylists()->contains($playlist)) {
            $user->removeLikedPlaylist($playlist);
        }else{
            $user->addLikedPlaylist($playlist);
        }

        $manager->persist($user);
        $manager->flush();
        return $this->redirectToRoute("playlist_detail", [
            "id"=>$playlist->getId()
        ]);   
    }

    /**
     * @Route("/follow/{id}", name="user_follow_playlist")
     */
    public function followPlaylist(Playlist $playlist){
        $manager = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        if ($playlist == null) {
            $this->addFlash("error", "Cette playlist n'existe pas");
            return $this->redirectToRoute("playlists");
        }

        if ($this->getUser() == $playlist->getUser()) {
            $this->addFlash("error", "Vous ne pouvez pas follow votre propre playlist");
            return $this->redirectToRoute("playlist_detail", [
                "id"=>$playlist->getId()
            ]);
        }

        if ($user->getFollowedPlaylists()->contains($playlist)) {
            $user->removeFollowedPlaylist($playlist);
        }else{
            $user->addFollowedPlaylist($playlist);
        }

        $manager->persist($user);
        $manager->flush();

        return $this->redirectToRoute("playlist_detail", [
            "id"=>$playlist->getId()
        ]);   
    }
}
