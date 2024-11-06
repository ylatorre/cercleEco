<?php

namespace App\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FrontController extends AbstractController
{
    #[Route('/', name: 'app_front')]
    public function index(): Response
    {
        return $this->render('Front/index.html.twig', [

        ]);
    }

    #[Route('/dons', name: 'app_dons')]
    public function dons(): Response
    {
        return $this->render('Front/dons.html.twig', [

        ]);
    }
}
