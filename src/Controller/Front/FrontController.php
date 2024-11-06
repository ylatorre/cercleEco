<?php

namespace App\Controller\Front;

use App\Entity\Application\Quests;
use App\Repository\Application\QuestsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/front')]
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

    #[Route('/quetes', name: 'app_quetes')]
    public function quetes(QuestsRepository $questsRepository): Response
    {
        $quests = $questsRepository->findBy([], ['ordre' => 'ASC']); // Tri par 'ordre'

        return $this->render('Front/quetes.html.twig', [
            'quests' => $quests,
        ]);
    }

    #[Route('/quetes/{id}', name: 'app_quetes_show')]
    public function QuetesShow(int $id, QuestsRepository $questsRepository): Response
    {
        $quest = $questsRepository->find($id);
        if (!$quest) {
            throw $this->createNotFoundException('La quête demandée n\'existe pas.');
        }
        return $this->render('Front/quetes_show.html.twig', [
            'quest' => $quest,
        ]);
    }
    #[Route('/actualités', name: 'app_actualites')]
    public function actualites(): Response
    {
        return $this->render('Front/actualites.html.twig', [

        ]);
    }

    #[Route('/quetes-journalières', name: 'app_day_quests')]
    public function day_quests(): Response
    {
        return $this->render('Front/day_quest.html.twig', [

        ]);
    }

    #[Route('/chatAi', name: 'app_chatAi')]
    public function chatAi(): Response
    {
        return $this->render('Front/chatAi.html.twig', [

        ]);
    }
}
