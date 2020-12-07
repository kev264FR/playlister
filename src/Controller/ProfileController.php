<?php

namespace App\Controller;

use App\Entity\Playlist;
use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Form\SecurityType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/profile")
 * @IsGranted("ROLE_USER")
 */
class ProfileController extends AbstractController
{
    /**
     * @Route("/{id}", name="public_profile")
     * @Route("/", name="my_profile")
     */
    public function index(User $user = null): Response
    {
        if (!$user) {
            return $this->render('profile/my_profile.html.twig');
        }

        return $this->render('profile/public_profile.html.twig', [
            'user'=>$user
        ]);
    }

    /**
     * @Route("/password/change", name="change_password")
     */
    public function changePassword(Request $request, UserPasswordEncoderInterface $encoder){
        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (password_verify($form->get('oldPassword')->getData(), $this->getUser()->getPassword())) {
                $this->getDoctrine()
                        ->getRepository(User::class)
                        ->upgradePassword($this->getUser(), $encoder->encodePassword($this->getUser(), $form->get('newPassword')->getData() ));

                return $this->redirectToRoute('app_logout');
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
