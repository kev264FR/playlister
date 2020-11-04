<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Comment;
use App\Entity\Playlist;
use App\Form\CommentType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * @Route("/interaction")
 * @IsGranted("ROLE_USER")
 */
class InteractionController extends AbstractController
{
    /**
     * @Route("/like/{id}", name="user_like")
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

    /**
     * @Route("/comment/{playlist_id}", name="comment_playlist")
     * @Entity("playlist", options={"id" = "playlist_id"})
     * @Route("/answer/{comment_id}", name="answer_comment")
     * @ParamConverter("parentComment", options={"id" = "comment_id"})
     */
    public function postComment(Request $request, Playlist $playlist = null , Comment $parentComment = null){
        $manager = $this->getDoctrine()->getManager();

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$playlist) {
                $playlist = $parentComment->getPlaylist();
            }
            $comment->setCreatedAt(new \DateTime());
            $comment->setPlaylist($playlist);
            $comment->setUser($this->getUser());

            if ($parentComment) {
                $initialComment = $parentComment;
                while ($initialComment->getAnswerFor() != null) {
                    $initialComment = $initialComment->getAnswerFor();
                }
                $comment->setAnswerFor($initialComment);
            }

            $manager->persist($comment);
            $manager->flush();

            return $this->redirectToRoute("playlist_detail", [
                "id"=>$comment->getPlaylist()->getId()
            ]);

        }   
        return $this->render("interaction/comment_form.html.twig", [
            "form"=>$form->createView()
        ]);
    }
}
