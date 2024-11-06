<?php

namespace App\Controller\Front;


use App\Repository\Application\DonsRepository;
use App\Entity\Application\Quests;
use App\Repository\Application\etatRepository;
use App\Repository\Application\QuestsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Application\Etat; 

class FrontController extends AbstractController
{
    public function __construct(private Security $security)
    {
    }

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

    #[Route('/quetes', name: 'app_quetes')]
    public function quetes(QuestsRepository $questsRepository): Response
    {
        $quests = $questsRepository->findBy([], ['ordre' => 'ASC']); // Tri par 'ordre'

        return $this->render('Front/quetes.html.twig', [
            'quests' => $quests,
        ]);
    }

    #[Route('/quetes/{id}', name: 'app_quetes_show')]
    public function QuetesShow(int $id, QuestsRepository $questsRepository, EtatRepository $etatRepository): Response
    {
        $quest = $questsRepository->find($id);
        if (!$quest) {
            throw $this->createNotFoundException('La quête demandée n\'existe pas.');
        }

        // Récupération de l'utilisateur connecté
        $user = $this->security->getUser();

        // Rechercher l'état de la quête pour cet utilisateur (s'il existe)
        $etat = $etatRepository->findOneBy(['quest' => $quest, 'user' => $user]);

        return $this->render('Front/quetes_show.html.twig', [
            'quest' => $quest,
            'etat' => $etat, // On passe l'état à la vue
        ]);
    }


    #[Route('/quetes/{id}/repondre', name: 'app_quetes_repondre', methods: ['POST'])]
    public function repondre(int $id, Request $request, QuestsRepository $questsRepository, EtatRepository $etatRepository): Response
    {
        $quest = $questsRepository->find($id);
        if (!$quest) {
            throw $this->createNotFoundException('Quête non trouvée.');
        }

        $reponse = $request->request->get('reponse');
        $etat = new Etat();
        $etat->setTitre($quest->getNom());
        $etat->setCode($quest->getToken());
        $etat->setFinish($reponse === $quest->getReponseCorrecte()); // Marque la quête comme terminée si la réponse est correcte

        $etatRepository->save($etat, true);

        return $this->redirectToRoute('app_quetes_show', ['id' => $id]);
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
