<?php

namespace App\Controller;

use App\Entity\Playlist;
use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Form\SecurityType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/profile")
 */
class ProfileController extends AbstractController
{
    /**
     * @Route("/{id}", name="public_profile")
     */
    public function index(User $user = null, Request $request, PaginatorInterface $paginator): Response
    {
        if ($this->getUser() == $user) {
            $query = $this->getDoctrine()
                            ->getRepository(Playlist::class)
                            ->getAllMyPlaylists($this->getUser());

            $myPlaylists = $paginator->paginate(
                $query, /* query NOT result */
                $request->query->getInt('page', 1), /*page number*/
                8 /*limit per page*/
            );
            return $this->render('profile/my_profile.html.twig', [
                'myPlaylists'=>$myPlaylists
            ]);
        }else{
            $query = $this->getDoctrine()
                    ->getRepository(Playlist::class)
                    ->getUsersPublicPlaylists($user);
                            
            $playlists = $paginator->paginate(
                $query, /* query NOT result */
                $request->query->getInt('page', 1), /*page number*/
                8 /*limit per page*/
            );

            return $this->render('profile/public_profile.html.twig', [
                'user'=>$user,
                'playlists'=>$playlists
            ]);
        }
    }

    /**
     * @Route("/password/change", name="change_password")
     * @IsGranted("ROLE_USER")
     */
    public function changePassword(Request $request, UserPasswordEncoderInterface $encoder){
        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            if (password_verify($form->get('oldPassword')->getData(), $this->getUser()->getPassword())) {
                $this->getDoctrine()
                        ->getRepository(User::class)
                        ->upgradePassword($this->getUser(), $encoder->encodePassword($this->getUser(), $form->get('newPassword')->getData() ));

                $this->get('security.token_storage')->setToken(null);
                $this->addFlash('success', 'Votre mot de passe a bien été changé, vous pouvez désormais vous ré-identifier');
                return $this->redirectToRoute('app_login');
            }else{
                $this->addFlash('error', 'Mauvais mot de passe');
                return $this->redirectToRoute('change_password');
            }
        }
        return $this->render('profile/change_password.html.twig', [
            'form'=>$form->createView()
        ]);
    }


    /**
     * @Route("/delete/{id}", name="delete_account")
     * @IsGranted("ROLE_USER")
     */
    public function deleteAccount(Request $request, User $user = null){
        $manager = $this->getDoctrine()->getManager();
        if ($this->isGranted('ROLE_ADMIN')) {
            
            foreach ($user->getFollowedUsers() as $followedUser) {
                $user->removeFollowedUser($followedUser);
            }
            foreach ($user->getFollowers() as $follower) {
                $user->removeFollower($follower);
            }
            foreach ($user->getMyPlaylists() as $myPlaylist) {
                $myPlaylist->setUser(null);
            }
            foreach ($user->getFollowedPlaylists() as $followedPlaylist) {
                $user->removeFollowedPlaylist($followedPlaylist);
            }
            foreach ($user->getLikedPlaylists() as $likedPlaylist) {
                $user->removeLikedPlaylist($likedPlaylist);
            }
            foreach ($user->getComments() as $comment) {
                $comment->setUser(null);
            }
            $manager->remove($user);
            $manager->flush();

            $this->addFlash('success', 'Le compte utilisateur a été supprimé de la base de données');
            return $this->redirectToRoute('users_admin');

        }else {

            $form = $this->createForm(SecurityType::class);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                if (
                    $user != $this->getUser() || $form->get('email')->getData() != $this->getUser()->getEmail() 
                    ||!password_verify($form->get('password')->getData(), $this->getUser()->getPassword())
                ) 
                {
                    $this->addFlash('error', 'Email ou mot de passe invalide');
                    return $this->redirectToRoute('my_profile');
                }
                
                
                $this->get('security.token_storage')->setToken(null);
                foreach ($user->getFollowedUsers() as $followedUser) {
                    $user->removeFollowedUser($followedUser);
                }
                foreach ($user->getFollowers() as $follower) {
                    $user->removeFollower($follower);
                }
                foreach ($user->getMyPlaylists() as $myPlaylist) {
                    if ($myPlaylist->getPublic()) {
                        $myPlaylist->setUser(null);
                    }else {

                        foreach ($myPlaylist->getContents() as $content) {
                            $manager->remove($content);
                        }
                        
                        foreach ($myPlaylist->getLikers() as $liker) {
                            $liker->removeLikedPlaylist($myPlaylist);
                        }
                
                        foreach ($myPlaylist->getFollowers() as $follower) {
                            $follower->removeFollowedPlaylist($myPlaylist);
                        }
                
                        foreach ($myPlaylist->getComments() as $comment) {
                            foreach ($comment->getAnswers() as $answer) {
                                $manager->remove($answer);
                            }
                            $manager->remove($comment);
                        }
                        
                        $manager->remove($myPlaylist);
                    }
                }
                foreach ($user->getFollowedPlaylists() as $followedPlaylist) {
                    $user->removeFollowedPlaylist($followedPlaylist);
                }
                foreach ($user->getLikedPlaylists() as $likedPlaylist) {
                    $user->removeLikedPlaylist($likedPlaylist);
                }
                foreach ($user->getComments() as $comment) {
                    $comment->setUser(null);
                }
                $manager->remove($user);
                $manager->flush();

                $this->addFlash('success','Votre compte a bien été supprimé');
                return $this->redirectToRoute('playlists');
                
            }
            
            return $this->render('security/security_page.html.twig', [
                'form'=>$form->createView()
            ]);
        }
    }
}
