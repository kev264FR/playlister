<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Comment;
use App\Entity\Playlist;
use App\Form\CommentType;
use Symfony\Component\HttpFoundation\Request;
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
     * @Route("/playlist/like/{id}", name="user_like")
     */
    public function likePlaylist(Playlist $playlist = null){
        $manager = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $status = null;

        if ($playlist == null || !$playlist->getPublic()) {
            return $this->json($status);
        }

        if ($this->getUser() == $playlist->getUser()) {
            return $this->json($status);
        }

        
        if ($user->getLikedPlaylists()->contains($playlist)) {
            $user->removeLikedPlaylist($playlist);
            $status = false;
        }else{
            $user->addLikedPlaylist($playlist);
            $status = true;
        }

        $manager->persist($user);
        $manager->flush();

        // return $this->redirect($referer);
        return $this->json($status);
        
    }

    /**
     * @Route("/playlist/follow/{id}", name="user_follow_playlist")
     */
    public function followPlaylist(Playlist $playlist = null){
        $manager = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $status = null;
        
        if ($playlist == null || !$playlist->getPublic()) {
            return $this->json($status);
        }

        if ($this->getUser() == $playlist->getUser()) {
            return $this->json($status);
        }

        if ($user->getFollowedPlaylists()->contains($playlist)) {
            $user->removeFollowedPlaylist($playlist);
            $status = false;
        }else{
            $user->addFollowedPlaylist($playlist);
            $status = true;
        }

        $manager->persist($user);
        $manager->flush();

        return $this->json($status);
    }

    /**
     * @Route("/comment/{playlist_id}", name="comment_playlist")
     * @Entity("playlist", options={"id" = "playlist_id"})
     * @Route("/answer/{comment_id}", name="answer_comment")
     * @ParamConverter("parentComment", options={"id" = "comment_id"})
     */
    public function postComment(Request $request, Playlist $playlist = null , Comment $parentComment = null){
        
        if (!$playlist && !$parentComment) {
            $this->addFlash("error", "Erreur");
            return $this->redirectToRoute("playlists");
        }
        
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->getDoctrine()->getManager();
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

            if (!$playlist->getPublic()) {
                $this->addFlash("error", "Cette playliste est privée, les commentaires sont désactivés");
                return $this->redirectToRoute("playlist_detail", [
                    "id"=>$playlist->getId()
                ]);
            }

            $manager->persist($comment);
            $manager->flush();

            $data = $this->renderView("interaction/playlist_comment_part.html.twig", [
                "playlist"=>$playlist
            ]);
            return $this->json($data);

        }
        $data = $this->renderView("interaction/comment_form.html.twig", [
            "form"=>$form->createView()
        ]); 
        return $this->json($data);
    }

    /**
     * @Route("/comment/delete/{id}", name="comment_delete")
     * @IsGranted("ROLE_ADMIN")
     */
    public function deleteComment(Comment $comment = null){
        if (!$comment) {
            $this->addFlash("error", "Ce commentaire n'existe pas");
            return $this->redirectToRoute("playlists");
        }
        $manager = $this->getDoctrine()->getManager();

        if ($comment->getAnswers()->count() > 0) {
            foreach ($comment->getAnswers() as $answer) {
                $manager->remove($answer);
            }
        }
        $manager->remove($comment);
        $manager->flush();

        return $this->redirectToRoute("playlist_detail", [
                    "id"=>$comment->getPlaylist()->getId()
        ]);
    }
}
