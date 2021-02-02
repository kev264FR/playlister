<?php

namespace App\Controller;

use App\Entity\Platform;
use App\Entity\Playlist;
use App\Entity\User;
use App\Form\PlatformType;
use App\Form\UserType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/admin")
 * @IsGranted("ROLE_ADMIN")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/", name="admin_home")
     */
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $query = $this->getDoctrine()
                        ->getRepository(Playlist::class)
                        ->getAll();

        $playlists = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            8 /*limit per page*/
        );

        return $this->render('admin/index.html.twig', [
            'playlists'=>$playlists
        ]);
    }

    /**
     * @Route("/platforms", name="platforms_list")
     */
    public function platformsList(){
        $platforms = $this->getDoctrine()
                        ->getRepository(Platform::class)
                        ->getAll();
                        
        return $this->render('admin/platforms_list.html.twig', [
            'platforms'=>$platforms,
        ]);
    }

    /**
     * @Route("/platform/new", name="platform_add")
     * @Route("/platform/edit/{id}", name="platform_edit")
     */
    public function platformForm(Request $request, Platform $platform = null){
        if ($platform) {
            $edit = true;
        }else{
            $platform = new Platform();
            $edit = false;
        }
        $form = $this->createForm(PlatformType::class, $platform);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($platform);
            $manager->flush();

            return $this->redirectToRoute('platforms_list');
        }
        return $this->render('admin/platform_form.html.twig', [
            'form'=>$form->createView(),
            'edit'=>$edit,
            'platform'=>$platform
        ]);
    }

    /**
     * @Route("/platform/delete/{id}", name="platform_delete")
     */
    public function deletePlatform(Platform $platform = null){
        if (!$platform) {
            $this->addFlash('error', 'Cette plateforme n\'existe pas');
            return $this->redirectToRoute('platforms_list');
        }

        if ($platform->getContents()->count() == 0) {
            $manager = $this->getDoctrine()->getManager();
            $manager->remove($platform);
            $manager->flush();

            return $this->redirectToRoute('platforms_list');
        }

        $this->addFlash('error', 'Suppression impossible');
        return $this->redirectToRoute('platforms_list');
    }

    /**
     * @Route("/users", name="users_admin")
     */
    public function usersList(){
        $users = $this->getDoctrine()
                        ->getRepository(User::class)
                        ->findAll();

        return $this->render('admin/user_list.html.twig', [
            'users'=>$users
        ]);
    }

    /**
     * @Route("/toggle/ban/{id}", name="ban_switch")
     */
    public function banSwitch(Request $request, User $user = null){
        if (!$user) {
            $this->addFlash('error', 'Utilisateur non-existant');
            return $this->redirectToRoute('users_admin');
        }

        if ($request->headers->get('referer')) {
            $redirect = $request->headers->get('referer');
        }else{
            $redirect = $this->generateUrl('public_profile', [
                'id'=>$user->getId()
            ]);
        }

        $manager = $this->getDoctrine()->getManager();

        if (in_array('ROLE_BANNED', $user->getRoles())) {
            $user->setRoles([]);
        }else{
            $user->setRoles(['ROLE_BANNED']);
        }
        $manager->flush();

        return $this->redirect($redirect);
    }


    /**
     * @Route("/user/new", name="user_add")
     */
    public function addUser(Request $request, UserPasswordEncoderInterface $encoder){
        $manager = $this->getDoctrine()->getManager();

        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('admin')->getData()) {
                $user->setRoles(["ROLE_ADMIN"]);
            }

            $this->getDoctrine()
                ->getRepository(User::class)
                ->upgradePassword($user, $encoder->encodePassword($user, $form->get('password')->getData()));

            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute('users_admin');
        }

        return $this->render('admin/user_form.html.twig', [
            'form'=>$form->createView()
        ]);
    }

}
