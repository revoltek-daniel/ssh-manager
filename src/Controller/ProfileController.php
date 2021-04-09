<?php

namespace App\Controller;

use App\Entity\SshKey;
use App\Entity\User;
use App\Form\SshKeyFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/profile", name="profile_")
 */
class ProfileController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        return $this->render(
            'profile/index.html.twig',
            [
            ]
        );
    }

    /**
     * @Route("/addKey", name="addKey")
     */
    public function addKey(Request $request): Response
    {
        $key = new SshKey();

        $form = $this->createForm(SshKeyFormType::class, $key);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $this->getUser();
            $user->addSshKey($key);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('profile_addKey_success');
        }

        return $this->render(
            'profile/addKey.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/addKey/success", name="addKey_success")
     */
    public function addKeySuccess(): Response
    {
        return $this->render('profile/addKeySuccess.html.twig');
    }

    /**
     * @Route("/editKey", name="editKey")
     */
    public function editKey()
    {
 //       $key =

        return $this->render(
            'profile/editKey.html.twig',
            [
            ]
        );
    }
}
