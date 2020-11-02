<?php

namespace App\Controller;

use App\Entity\Platform;
use App\Form\PlatformType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

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
            "platforms"=>$platforms
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
            "edit"=>$edit
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
}
