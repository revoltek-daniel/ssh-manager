<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\SshAuthService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{

    /**
     * @Route("/test/{id}")
     */
    public function index(User $user, SshAuthService $sshAuthService)
    {
        $sshAuthService->remove($user);
      //  $sshAuthService->create($user);
    }
}