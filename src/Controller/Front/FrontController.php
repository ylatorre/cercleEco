<?php

namespace App\Controller\Front;

use App\Repository\Application\DonsRepository;
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

    #[Route('/front/dons', name: 'app_dons')]
    public function dons(DonsRepository $donsRepository): Response
    {
        $dons = $donsRepository->findAll();

        return $this->render('Front/dons.html.twig', [
            'dons' => $dons
        ]);
    }

    #[Route('/front/quetes', name: 'app_quetes')]
    public function quetes(): Response
    {
        return $this->render('Front/quetes.html.twig', [

        ]);
    }

    #[Route('/front/actualités', name: 'app_actualites')]
    public function actualites(): Response
    {
        return $this->render('Front/actualites.html.twig', [

        ]);
    }

    #[Route('/front/quetes-journalières', name: 'app_day_quests')]
    public function day_quests(): Response
    {
        return $this->render('Front/day_quest.html.twig', [

        ]);
    }

    #[Route('/front/chatAi', name: 'app_chatAi')]
    public function chatAi(): Response
    {
        return $this->render('Front/chatAi.html.twig', [

        ]);
    }
}
