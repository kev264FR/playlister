<?php

namespace App\Controller;

use App\Entity\Platform;
use App\Entity\Playlist;
use App\Entity\User;
use App\Form\PlatformType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/admin")
 * @IsGranted("ROLE_ADMIN")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/", name="admin_home")
     */
    public function index(): Response
    {
        return $this->render('admin/index.html.twig');
    }

    /**
     * @Route("/platforms", name="platforms_list")
     */
    public function platformsList(){
        $platforms = $this->getDoctrine()
                        ->getRepository(Platform::class)
                        ->getAll();
                        
        return $this->render("admin/platforms_list.html.twig", [
            "platforms"=>$platforms,
            "finder"=>$this->generateUrl("playlists_admin")
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

            return $this->redirectToRoute("platforms_list");
        }
        return $this->render("admin/platform_form.html.twig", [
            "form"=>$form->createView(),
            "edit"=>$edit,
            "finder"=>$this->generateUrl("playlists_admin")
        ]);
    }

    /**
     * @Route("/platform/delete/{id}", name="platform_delete")
     */
    public function deletePlatform(Platform $platform = null){
        if (!$platform) {
            $this->addFlash("error", "Cette plateforme n'existe pas");
            return $this->redirectToRoute("platforms_list");
        }

        if ($platform->getContents()->count() == 0) {
            $manager = $this->getDoctrine()->getManager();
            $manager->remove($platform);
            $manager->flush();

            return $this->redirectToRoute("platforms_list");
        }

        $this->addFlash("error", "Suppression impossible");
        return $this->redirectToRoute("platforms_list");
    }

    /**
     * @Route("/playlists", name="playlists_admin")
     */
    public function listAllPlaylists(Request $request){
        $search = $request->get("search");
        if ($search) {
            $playlists = $this->getDoctrine()
                            ->getRepository(Playlist::class)
                            ->getAllNamed($search);
        }else{
            $playlists = $this->getDoctrine()
                        ->getRepository(Playlist::class)
                        ->findAll();
        }
        
        return $this->render('playlist/index.html.twig', [
            "playlists"=>$playlists,
            "search"=>$search,
            "finder"=>$this->generateUrl("playlists_admin")
        ]);
    }

    /**
     * @Route("/users", name="users_admin")
     */
    public function usersList(){
        $users = $this->getDoctrine()
                        ->getRepository(User::class)
                        ->findAll();

        return $this->render("admin/user_list.html.twig", [
            "users"=>$users
        ]);
    }

    /**
     * @Route("/toggle/admin/{id}", name="admin_switch")
     */
    public function adminSwitch(User $user = null){
        if (!$user) {
            $this->addFlash("error", "User not found");
            return $this->redirectToRoute("users_admin");
        }
        $manager = $this->getDoctrine()->getManager();

        if (in_array("ROLE_ADMIN", $user->getRoles())) {
            $user->setRoles([]);
        }else{
            $user->setRoles(["ROLE_ADMIN"]);
        }
        $manager->flush();

        return $this->redirectToRoute("public_profile", [
            "id"=>$user->getId()
        ]);
    }

    /**
     * @Route("/toggle/ban/{id}", name="ban_switch")
     */
    public function banSwitch(User $user = null){
        if (!$user) {
            $this->addFlash("error", "User not found");
            return $this->redirectToRoute("users_admin");
        }
        $manager = $this->getDoctrine()->getManager();

        if (in_array("ROLE_BANNED", $user->getRoles())) {
            $user->setRoles([]);
        }else{
            $user->setRoles(["ROLE_BANNED"]);
        }
        $manager->flush();

        return $this->redirectToRoute("public_profile", [
            "id"=>$user->getId()
        ]);
    }
}
