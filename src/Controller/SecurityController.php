<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            $this->addFlash('error', 'Vous ètes déjà connecté en tant que <strong>'.$this->getUser()->getUsername().'</strong>');
            return $this->redirectToRoute('playlists');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        if ($error) {
            if($error->getMessage() == 'invalide') {
                $this->addFlash('error', 'Email ou mot de passe invalide');
            }
            if ($error->getMessage() == 'banned') {
                $this->addFlash('error', '<strong>Votre compte a été banni</strong>');
            }
        }
        

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername
        ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
