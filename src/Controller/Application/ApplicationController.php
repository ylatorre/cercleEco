<?php

namespace App\Controller\Application;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ApplicationController extends AbstractController
{
    #[Route('/back', name: 'app_back')]
    public function index(): Response
    {
        return $this->render('Application/index.html.twig', [
            'controller_name' => 'BackController',
        ]);
    }
}
