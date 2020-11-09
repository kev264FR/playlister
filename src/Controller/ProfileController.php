<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordType;
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
            "user"=>$user
        ]);
    }

    /**
     * @Route("/password/change", name="change_password")
     */
    public function changePassword(Request $request, UserPasswordEncoderInterface $encoder){
        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (password_verify($form->get("oldPassword")->getData(), $this->getUser()->getPassword())) {
                $ok = $this->getDoctrine()
                            ->getRepository(User::class)
                            ->upgradePassword($this->getUser(), $encoder->encodePassword($this->getUser(), $form->get("newPassword")->getData() ));
                dump($ok);
                return $this->redirectToRoute("app_logout");
            }else{
                $this->addFlash("error", "Mauvais mot de passe");
                return $this->redirectToRoute("change_password");
            }
        }
        return $this->render("profile/change_password.html.twig", [
            "form"=>$form->createView()
        ]);
    }
}
