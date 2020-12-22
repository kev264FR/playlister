<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Playlist;
use App\Form\CommentType;
use App\Form\PlaylistType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

class PlaylistController extends AbstractController
{
    /**
     * @Route("/", name="playlists")
     */
    public function index(Request $request, PaginatorInterface $paginator): Response
    {

        $mostLiked = null;
        $mostFollowed = null;

        $search = $request->get('search');
        if ($search) {
            $query = $this->getDoctrine()
                            ->getRepository(Playlist::class)
                            ->getAllPublicNamed($search);
            $playlists = $paginator->paginate(
                $query, /* query NOT result */
                $request->query->getInt('page', 1), /*page number*/
                8 /*limit per page*/
            );
                        
        }else{
            $query = $this->getDoctrine()
                        ->getRepository(Playlist::class)
                        ->getAllPublic();
            $playlists = $paginator->paginate(
                $query, /* query NOT result */
                $request->query->getInt('page', 1), /*page number*/
                9 /*limit per page*/
            );

           $mostLiked = $this->getDoctrine()
                        ->getRepository(Playlist::class)
                        ->getMostLikedPublic()[0];

            $mostFollowed = $this->getDoctrine()
                            ->getRepository(Playlist::class)
                            ->getMostFollowedPublic()[0]; 

        }

        return $this->render('playlist/index.html.twig', [
            'playlists'=>$playlists,
            'search'=>$search,
            'mostLiked'=>$mostLiked,
            'mostFollowed'=>$mostFollowed
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

            $this->addFlash('success', 'Enregistrement réussi');
            return $this->redirectToRoute('playlist_detail', [
                'id'=>$playlist->getId()
            ]);
        }

        return $this->render('playlist/playlist_form.html.twig', [
            'form'=>$form->createView(),
            'edit'=>$edit,
            'playlist'=>$playlist
        ]);
    }

    /**
     * @Route("/playlist/delete/{id}", name="playlist_delete")
     */
    public function deletePlaylist(Request $request, Playlist $playlist = null){
        $manager = $this->getDoctrine()->getManager();

        if ($request->headers->get('referer')) {
            $redirect = $request->headers->get('referer');
        }else{
            $redirect = $this->generateUrl('playlists');
        }

        if (!$playlist) {
            $this->addFlash('error', 'Cette playliste n\'existe pas');
            return $this->redirect($redirect);
        }
        if (!$this->isGranted('ROLE_ADMIN')) {
            if ($this->getUser() != $playlist->getUser()) {
                $this->addFlash('error', 'Suppression impossible, c\'ette playliste ne vous appartient pas');
                return $this->redirect($redirect);
            }
        }
        
        if ($playlist->getContents()->count() != 0) {
            $this->addFlash('error', 'Suppression impossible, videz la playliste avant de la supprimer');
            return $this->redirect($redirect);
        }

        foreach ($playlist->getLikers() as $liker) {
            $liker->removeLikedPlaylist($playlist);
        }

        foreach ($playlist->getFollowers() as $follower) {
            $follower->removeFollowedPlaylist($playlist);
        }

        foreach ($playlist->getComments() as $comment) {
            foreach ($comment->getAnswers() as $answer) {
                $manager->remove($answer);
            }
            $manager->remove($comment);
        }
        
        
        $manager->remove($playlist);
        $manager->flush();

        $this->addFlash('success', 'La playlist <strong>'.$playlist->getTitle().'</strong> a été supprimé');
        return $this->redirectToRoute('playlists');

    }

    /**
     * @Route("/playlist/{id}", name="playlist_detail")
     */
    public function detailPlaylist(Playlist $playlist = null){

        if (!$playlist) {
            $this->addFlash('error', 'Cette playlist est privée ou n\'existe pas');
            return $this->redirectToRoute('playlists');
        }

        if (!$this->isGranted('ROLE_ADMIN')) {
            if (!$playlist->getPublic()) {
                if ($playlist->getUser() != $this->getUser()) {
                    $this->addFlash('error', 'Cette playlist est privée ou n\'existe pas');
                    return $this->redirectToRoute('playlists');
                }
            }
        }
        
        $comments = $this->getDoctrine()
                            ->getRepository(Comment::class)
                            ->getAllForPlaylist($playlist->getId());
        
        return $this->render('playlist/playlist_detail.html.twig', [
                'playlist'=>$playlist,
                'comments'=>$comments
        ]);
        
    }

    /**
     * @Route("/playlist/public/{id}", name="playlist_make_public")
     * @Route("/playlist/private/{id}", name="playlist_make_private")
     */
    public function switchPublicPrivate(Playlist $playlist = null){
        $manager = $this->getDoctrine()->getManager();
        if ($playlist == null) {
            $this->addFlash('error', 'Cette playlist n\'existe pas');
            return $this->redirectToRoute('playlists');
        }
        if ($playlist->getContents()->count() == 0) {
            $this->addFlash('error', 'Cette playlist est vide, vous ne pouvez pas changer sa visibilité');
            return $this->redirectToRoute('playlist_detail', ['id'=>$playlist->getId()]);
        }
        if ($playlist->getPublic()) {
            $playlist->setPublic(false);
            $this->addFlash('success', 'La playlist <strong>'.$playlist->getTitle().'</strong> a été mise en privée');
        }else{
            $playlist->setPublic(true);
            $this->addFlash('success', 'La playlist <strong>'.$playlist->getTitle().'</strong> a été mise en public');
        }

        $manager->flush();
        return $this->redirectToRoute('playlist_detail', [
            'id'=>$playlist->getId()
        ]);
    }
}
