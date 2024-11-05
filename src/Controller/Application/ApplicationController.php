<?php

namespace App\Controller\Application;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ApplicationController extends AbstractController
{
    #[Route('/', name: 'app_back')]
    public function index(): Response
    {
        $user = $this->getUser();
        return $this->render('Application/index.html.twig', [
            'user' => $user,
        ]);
    }
}
