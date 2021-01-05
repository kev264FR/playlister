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
 */
class InteractionController extends AbstractController
{
    /**
     * @Route("/playlist/like/{id}", name="user_like")
     */
    public function likePlaylist(Request $request, Playlist $playlist = null){
        $manager = $this->getDoctrine()->getManager();

        if (!$request->isXmlHttpRequest()) {
            $this->addFlash('error', 'Action impossible');
            return $this->redirectToRoute("playlists");
        }
        
        if ($this->getUser()) {
            $user = $this->getUser();
        }else{
            return $this->json([
                "status"=>'error',
                "data"=>'Vous devez ètre connecté <a href="'.$this->generateUrl('app_login').'">Vers la connexion</a>'
            ]);
        }

        if ($playlist == null || !$playlist->getPublic()) {
            return $this->json([
                "status"=>'error',
                "data"=>'playlist privée ou non existente'
            ]);
        }

        if ($this->getUser() == $playlist->getUser()) {
            return $this->json([
                "status"=>'error',
                "data"=>'Vous ne pouvez pas liker votre propre playlist'
            ]);
        }

        if ($user->getLikedPlaylists()->contains($playlist)) {
            $user->removeLikedPlaylist($playlist);
            $status = [
                "status"=>'success',
                "data"=>'dislike'
            ];
        }else{
            $user->addLikedPlaylist($playlist);
            $status = [
                "status"=>'success',
                "data"=>'like'
            ];
        }
        
        $manager->persist($user);
        $manager->flush();

        return $this->json($status);
        
    }

    /**
     * @Route("/playlist/follow/{id}", name="user_follow_playlist")
     */
    public function followPlaylist(Request $request, Playlist $playlist = null){
        $manager = $this->getDoctrine()->getManager();

        if (!$request->isXmlHttpRequest()) {
            $this->addFlash('error', 'Action impossible');
            return $this->redirectToRoute("playlists");
        }
        
        if ($this->getUser()) {
            $user = $this->getUser();
        }else{
            return $this->json([
                "status"=>'error',
                "data"=>'Vous devez ètre connecté <a href="'.$this->generateUrl('app_login').'">Vers la connexion</a>'
            ]);
        }
        
        if ($playlist == null || !$playlist->getPublic()) {
            return $this->json([
                "status"=>'error',
                "data"=>'playlist privée ou non existante'
            ]);
        }

        if ($user == $playlist->getUser()) {
            return $this->json([
                "status"=>'error',
                "data"=>'Vous ne pouvez pas follow votre propre playlist'
            ]);
        }

        if ($user->getFollowedPlaylists()->contains($playlist)) {
            $user->removeFollowedPlaylist($playlist);
            $status = [
                "status"=>'success',
                "data"=>'unfollow',
                "id"=>$playlist->getId()
            ];
        }else{
            $user->addFollowedPlaylist($playlist);
            $status = [
                "status"=>'success',
                "data"=>'follow',
                "id"=>$playlist->getId()
            ];
        }

        $manager->persist($user);
        $manager->flush();

        return $this->json($status);
    }

    /**
     * @Route("/user/follow/{id}", name="user_follow_user")
     */
    public function followUser(Request $request, User $target = null){
        $manager = $this->getDoctrine()->getManager();

        if (!$request->isXmlHttpRequest()) {
            $this->addFlash('error', 'Action impossible');
            return $this->redirectToRoute("playlists");
        }
        
        if ($this->getUser()) {
            $user = $this->getUser();
        }else{
            return $this->json([
                "status"=>'error',
                "data"=>'Vous devez ètre connecté <a href="'.$this->generateUrl('app_login').'">Vers la connexion</a>'
            ]);
        }
        
        if ($user == $target) {
            return $this->json([
                "status"=>'error',
                "data"=>'Vous ne pouvez pas vous follow'
            ]);
        }

        if ($user->getFollowedUsers()->contains($target)) {
            $user->removeFollowedUser($target);
            $status = [
                "status"=>'success',
                "data"=>'unfollow',
                "id"=>$target->getId()
            ];
        }else{
            $user->addFollowedUser($target);
            $status = [
                "status"=>'success',
                "data"=>'follow',
                "id"=>$target->getId()
            ];
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

        if (!$request->isXmlHttpRequest()) {
            $this->addFlash('error', 'Action impossible');
            return $this->redirectToRoute("playlists");
        }
        
        if (!$playlist && !$parentComment) {
            return $this->json([
                "status"=>'error',
                "data"=>'Erreur'
            ]);
        }

        if (!$this->getUser()) {
            return $this->json([
                "status"=>'error',
                "data"=>'Vous devez ètre connecté <a href="'.$this->generateUrl('app_login').'">Vers la connexion</a>'
            ]);
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
                return $this->json([
                    "status"=>'error',
                    "data"=>'playlist privée'
                ]);
            }

            $manager->persist($comment);
            $manager->flush();

            return $this->json([
                "status"=>'success',
                "data"=>$this->renderView('interaction/playlist_comment_part.html.twig', [
                    'playlist'=>$playlist,
                    'parentComment'=>$parentComment
                ])
            ]);

        }
        
        return $this->json([
            'status'=>'success-form',
            'data'=>$this->renderView('interaction/comment_form.html.twig', [
                'form'=>$form->createView()
            ])
        ]);
    }

    /**
     * @Route("/comment/delete/{id}", name="comment_delete")
     * @IsGranted("ROLE_ADMIN")
     */
    public function deleteComment(Comment $comment = null){
        if (!$comment) {
            $this->addFlash('error', 'Ce commentaire n\'existe pas');
            return $this->redirectToRoute('playlists');
        }
        $manager = $this->getDoctrine()->getManager();

        if ($comment->getAnswers()->count() > 0) {
            foreach ($comment->getAnswers() as $answer) {
                $manager->remove($answer);
            }
        }
        $manager->remove($comment);
        $manager->flush();

        return $this->redirectToRoute('playlist_detail', [
            'id'=>$comment->getPlaylist()->getId()
        ]);
    }
}
