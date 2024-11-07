<?php

namespace App\Controller\Front;


use App\Repository\Application\DonsRepository;
use App\Entity\Application\Quests;
use App\Repository\Application\etatRepository;
use App\Repository\Application\QuestsRepository;
use App\Repository\Application\DayQuestRepository;
use App\Entity\Application\DayQuest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Application\Etat; 
use App\Service\ChatGPTService;
use App\Form\ChatGPTType;
use App\Repository\Application\UserRepository;
use App\Service\DayQuestService;


class FrontController extends AbstractController
{
    private $chatGPTService;

    public function __construct(private Security $security,ChatGPTService $chatGPTService)
    {
        $this->chatGPTService = $chatGPTService;

    }

    #[Route('/', name: 'app_front')]
    public function index(QuestsRepository $questsRepository): Response
    {
        $quests = $questsRepository->findAll();
        return $this->render('Front/index.html.twig', [
            'quests' => $quests,
        ]);
    }

    #[Route('/profil', name: 'app_profil')]
    public function profil(): Response
    {
        $user = $this->getUser();
        return $this->render('Front/profil.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/front/dons', name: 'app_dons')]
    public function dons(DonsRepository $donsRepository): Response
    {
        $dons = $donsRepository->findAll();
        return $this->render('Front/dons.html.twig', [
            'dons' => $dons,
        ]);
    }

    #[Route('/front/dons/{tokenUser}', name: 'app_dons_Perso')]
    public function donsPerso(Request $request, DonsRepository $donsRepository, UserRepository $userRepository): Response
    {
        $token = $request->get('tokenUser');
        $user = $userRepository->findOneBy(['token' => $token]);
        $dons = $donsRepository->findByuser($user);

        return $this->render('Front/donsPerso.html.twig', [
            'dons' => $dons
        ]);
    }

    #[Route('/front/donsdetail/{token}', name: 'app_don_detail')]
    public function donsDetail(Request $request, DonsRepository $donsRepository): Response
    {
        $token = $request->get('token');
        $don = $donsRepository->findOneBy(['token' => $token]);

        return $this->render('Front/donsDetail.html.twig', [
            'don' => $don
        ]);
    }

    #[Route('/front/quetes', name: 'app_quetes')]
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
    
    /*
    #[Route('/quetes-journalières', name: 'app_day_quests')]
    public function dayQuests(DayQuestRepository $dayQuestRepository): Response
    {
        // Appeler la méthode qui génère les quêtes journalières
        $day_quests = $this->generateDailyQuests($dayQuestRepository);

        // Rendre la vue avec les quêtes générées
        return $this->render('Front/day_quest.html.twig', [
            'day_quests' => $day_quests,
        ]);
    }

    public function generateDailyQuests(DayQuestRepository $dayQuestRepository): array
    {
        // Récupérer toutes les quêtes puis en sélectionner 2 ou 3 aléatoirement
        $allQuests = $dayQuestRepository->findAll();
        $randomKeys = array_rand($allQuests, rand(3, 3));  // Sélectionner 2 ou 3 quêtes au hasard

        if (!is_array($randomKeys)) {
            $randomKeys = [$randomKeys];  // Si un seul élément est retourné, le convertir en tableau
        }

        return array_map(fn($key) => $allQuests[$key], $randomKeys);
    }
    */

    #[Route('/quetes-journalières', name: 'app_day_quests')]
    public function dayQuests(DayQuestService $dayQuestService): Response
    {
        // Récupérer les quêtes journalières depuis le cache
        $day_quests = $dayQuestService->getDailyQuests();

        // Rendre la vue avec les quêtes générées
        return $this->render('Front/day_quest.html.twig', [
            'day_quests' => $day_quests,
        ]);
    }


    #[Route('/chatAi', name: 'app_chatAi')]
    public function chatAi(Request $request): Response
    {
        $form = $this->createForm(ChatGPTType::class);
        $form->handleRequest($request);               
        $responseText = '';          
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $userInput = $data['userInput'];
            $responseText = $this->chatGPTService->generateResponse($userInput);
        }                 
        return $this->render('Front/chatAi.html.twig', [
            'form' => $form->createView(),
            'responseText' => $responseText,

        ]);
    }
}
