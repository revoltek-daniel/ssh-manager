<?php

namespace App\Controller;

use App\Entity\SshKey;
use App\Entity\User;
use App\Form\SshKeyFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
        $form->add('submit', SubmitType::class, ['label' => 'Absenden']);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $this->getUser();
            $user->addSshKey($key);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Key erfolgreich hinzugefügt');
            return $this->redirectToRoute('profile_index');
        }

        return $this->render(
            'profile/addKey.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/editKey", name="editKey")
     */
    public function editKey(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();
        $keys = $user->getSshKeys();

        foreach ($keys as $key) {
            if ($key->isActive()) {
                break;
            }
        }
        if (isset($key) === false) {
            $this->redirectToRoute('profile_addKey');
        }

        $form = $this->createForm(SshKeyFormType::class, $key);
        $form->add('submit', SubmitType::class, ['label' => 'Absenden']);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->addSshKey($key);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Key erfolgreich geändert');
            return $this->redirectToRoute('profile_index');
        }

        return $this->render(
            'profile/editKey.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }
}
