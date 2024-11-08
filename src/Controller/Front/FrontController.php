<?php

namespace App\Controller\Front;


use App\Entity\Application\User;
use App\Repository\Application\DonsRepository;
use App\Entity\Application\Quests;
use App\Repository\Application\etatRepository;
use App\Repository\Application\QuestsRepository;
use App\Repository\Application\DayQuestRepository;
use App\Entity\Application\DayQuest;
use App\Service\LevelCalculatorService;
use Doctrine\ORM\EntityManagerInterface;
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
use App\Repository\Application\DayQuestUserRepository;
use App\Entity\Application\DayQuestUser;

class FrontController extends AbstractController
{
    private $chatGPTService;
    private $levelCalculator;

    public function __construct(private Security $security,ChatGPTService $chatGPTService, LevelCalculatorService $levelCalculator, EntityManagerInterface $entityManager)
    {
        $this->chatGPTService = $chatGPTService;
        $this->levelCalculator = $levelCalculator;
        $this->entityManager = $entityManager;
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
    
    #[Route('/quetes-journalières', name: 'app_day_quests')]
    public function dayQuests(DayQuestService $dayQuestService, DayQuestUserRepository $dayQuestUserRepository): Response
    {
        // Récupérer les quêtes journalières depuis le cache
        $day_quests = $dayQuestService->getDailyQuests();

        // Récupérer l'utilisateur actuel
        $user = $this->getUser();

        // Vérifier si chaque quête est complétée par l'utilisateur
        foreach ($day_quests as $quest) {
            $dayQuestUser = $dayQuestUserRepository->findOneBy([
                'user' => $user,
                'dayQuest' => $quest,
            ]);

            // Ajouter un champ 'isCompleted' à chaque quête pour vérifier son statut
            $quest->isCompleted = $dayQuestUser ? $dayQuestUser->getEtat() === 1 : false;
        }

        // Rendre la vue avec les quêtes générées
        return $this->render('Front/day_quest.html.twig', [
            'day_quests' => $day_quests,
        ]);
    }

    #[Route('/day-quest/{id}/complete', name: 'app_complete_quest', methods: ['POST'])]
    public function completeQuest(
        int $id,
        DayQuestRepository $dayQuestRepository,
        DayQuestUserRepository $dayQuestUserRepository,
        EntityManagerInterface $entityManager
    ): RedirectResponse {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour terminer une quête.');
        }

        $dayQuest = $dayQuestRepository->find($id);
        if (!$dayQuest) {
            throw $this->createNotFoundException('Quête introuvable.');
        }

        $dayQuestUser = $dayQuestUserRepository->findOneBy(['user' => $user, 'dayQuest' => $dayQuest]);
        if (!$dayQuestUser) {
            $dayQuestUser = new DayQuestUser();
            $dayQuestUser->setUser($user);
            $dayQuestUser->setDayQuest($dayQuest);
        }

        $dayQuestUser->setEtat(1); // Marquer comme terminée
        $entityManager->persist($dayQuestUser);
        $entityManager->flush();

        $this->addFlash('success', 'La quête a été marquée comme terminée.');
        return $this->redirectToRoute('app_day_quests');
    }


    #[Route('/marquer-quete/{questId}/terminee', name: 'app_day_quest_complete', methods: ['GET', 'POST'])]
    public function dayQuestCompleted(
        $questId,
        DayQuestRepository $dayQuestRepository,
        DayQuestUserRepository $dayQuestUserRepository,
        Security $security,
        EntityManagerInterface $entityManager,
        Request $request
    ): Response {
        $user = $this->getUser();
        $quest = $dayQuestRepository->find($questId);
        
        if (!$quest) {
            throw $this->createNotFoundException('La quête n\'existe pas.');
        }

        // Vérifier si l'utilisateur a déjà effectué cette quête
        $dayQuestUser = $dayQuestUserRepository->findOneBy([
            'user' => $user,
            'dayQuest' => $quest,
        ]);

        if (!$dayQuestUser) {
            throw $this->createNotFoundException('Cette quête n\'a pas été assignée à cet utilisateur.');
        }

        // Marquer la quête comme terminée (status = 1)
        $dayQuestUser->setEtat(1);

        $this->ajouterXP($quest->getXp(), $security, $entityManager);


        // Sauvegarder les modifications
        $entityManager->persist($dayQuestUser);
        $entityManager->flush();

        // Message flash pour indiquer que la quête a été complétée
        $this->addFlash('success', 'La quête a été complétée avec succès !');

        // Rediriger vers la page des quêtes ou une autre page
        return $this->redirectToRoute('app_day_quests'); // Ou toute autre page de votre choix
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

//Gestion xp

    #[Route('/quete/ajouter-xp/{amount}', name: 'ajouter_xp')]

    public function ajouterXP(int $amount, Security $security, EntityManagerInterface $entityManager): Response
    {
        // Récupère l'utilisateur connecté
        $user = $security->getUser();
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        // Ajoute l'XP au total actuel de l'utilisateur
        $user->setXpTotal( $user->getXpTotal() + $amount);

        // Sauvegarde la mise à jour dans la base de données
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('app_quetes');
    }

//    #[Route('/levelUser', name: 'app_application_user_show_level', methods: ['GET'])]
//    public function getLevel(): Response
//    {
//        $user = $this->getUser();
//        $niveau = $this->levelCalculator->calculerNiveau($user->getXpTotal());
//
//        return $this->render('Components/xpcalcul.html.twig', [
//            'niveau' => $niveau,
//        ]);
//    }
    #[Route('/levelUser', name: 'app_application_user_show_level', methods: ['GET'])]
    public function getLevel(): Response
    {
        $user = $this->getUser();
        $niveau = $this->levelCalculator->calculerNiveau($user->getXpTotal());
        $xpTotal = $user->getXpTotal();
        $xpSeuil = $this->levelCalculator->getXpSeuil($niveau); // méthode pour obtenir l'XP nécessaire pour atteindre le niveau suivant

        return $this->json([
            'niveau' => $niveau,
            'xpTotal' => $xpTotal,
            'xpSeuil' => $xpSeuil,
        ]);
    }
    #[Route('/leaderboard', name: 'app_application_user_leaderboard', methods: ['GET'])]
    public function leaderboard(UserRepository $userRepository): Response
    {
        // Récupérer les 50 meilleurs utilisateurs triés par XP
        $topUsers = $userRepository->findTopUsersByXp(50);

        return $this->render('Front/leaderboard.html.twig', [
            'topUsers' => $topUsers,
        ]);
    }
}
