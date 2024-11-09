<?php

namespace App\Controller\Front;


use App\Repository\Application\DonsRepository;
use App\Service\LevelCalculatorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route; 
use App\Service\ChatGPTService;
use App\Form\ChatGPTType;
use App\Repository\Application\QuetesRepository;
use App\Repository\Application\UserRepository;
use App\Repository\Application\DayQuestUserRepository;
use App\Entity\Application\DayQuestUser;
use App\Entity\Application\Quetes;
use App\Entity\Application\QuetesReponses;
use App\Entity\Application\User;
use App\Form\Front\UserTypeUser;

class FrontController extends AbstractController
{
    private $chatGPTService;
    private $levelCalculator;

    public function __construct(private Security $security,ChatGPTService $chatGPTService, LevelCalculatorService $levelCalculator, EntityManagerInterface $entityManager)
    {
        $this->chatGPTService = $chatGPTService;
        $this->levelCalculator = $levelCalculator;
    }

    #[Route('/', name: 'app_front')]
    public function index(QuetesRepository $quetesRepository): Response
    {
        $quests = $quetesRepository->findAll();
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

    #[Route('/front/dons/cloture/{token}', name: 'app_dons_perso_cloture')]
    public function donsCloture(Request $request, DonsRepository $donsRepository, UserRepository $userRepository): Response
    {
        $token = $request->get('token');
        $user = $userRepository->findOneBy(['token' => $token]);
        $dons = $donsRepository->findBy(['user' => $user, 'etat'=>2]); // Assuming 'etat' 2 means closed
        return $this->render('Front/donsCloture.html.twig', [
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

    #[Route('/front/donsdetail/acquisition/{token}', name: 'app_don_detail_acquisition')]
    public function donsDetailAcquisition(Request $request, DonsRepository $donsRepository): Response
    {
        $token = $request->get('token');
        $don = $donsRepository->findOneBy(['token' => $token]);

        return $this->render('Front/acquisition.html.twig', [
            'don' => $don
        ]);
    }

    #[Route('/front/donsdetail/changeStatus/{token}', name: 'app_don_detail_change_status')]
    public function changeStatusDons(Request $request, DonsRepository $donsRepository, EntityManagerInterface $entityManager): Response
    {
        $token = $request->get('token');
        $params = $request->request->all();
        $userWhoReserve = $entityManager->getRepository(User::class)->findOneByToken(['token' => $params['user'][0]]);
        $don = $donsRepository->findOneBy(['token' => $token]);
        $don->setEtat(1);
        $don->setAcheteur($userWhoReserve);

        $entityManager->persist($don);
        $entityManager->flush();

        return $this->redirectToRoute('app_dons');

        // return $this->render('Front/donsDetail.html.twig', [
        //     'don' => $don
        // ]);
    }

    #[Route('/front/donsdetail/cloturer/{token}', name: 'app_don_detail_cloturer')]
    public function cloturerAcquisition(Request $request, DonsRepository $donsRepository, EntityManagerInterface $entityManager): Response
    {
        $token = $request->get('token');
        $don = $donsRepository->findOneBy(['token' => $token]);

        if (!$don) {
            throw $this->createNotFoundException('Don non trouvé.');
        }

        $don->setEtat(2); // Marquer comme clôturé
        $entityManager->persist($don);
        $entityManager->flush();

        return $this->redirectToRoute('app_dons');
    }

    #[Route('/front/donsdetail/annuler/{token}', name: 'app_don_detail_annuler')]
    public function annulerAcquisition(Request $request, DonsRepository $donsRepository, EntityManagerInterface $entityManager): Response
    {
        $token = $request->get('token');
        $don = $donsRepository->findOneBy(['token' => $token]);

        if (!$don) {
            throw $this->createNotFoundException('Don non trouvé.');
        }

        $don->setEtat(0); // Réinitialiser l'état
        $don->setAcheteur(null); // Supprimer l'acheteur
        $entityManager->persist($don);
        $entityManager->flush();

        return $this->redirectToRoute('app_dons');
    }

    #[Route('/front/donsdetail/reopen/{token}', name: 'app_don_detail_reopen')]
    public function reopenAcquisition(Request $request, DonsRepository $donsRepository, EntityManagerInterface $entityManager): Response
    {
        $token = $request->get('token');
        $don = $donsRepository->findOneBy(['token' => $token]);

        if (!$don) {
            throw $this->createNotFoundException('Don non trouvé.');
        }

        $don->setEtat(0); // Réinitialiser l'état à ouvert
        $don->setAcheteur(null);
        $entityManager->persist($don);
        $entityManager->flush();

        return $this->redirectToRoute('app_dons');
    }

    #[Route('/front/mes-reservations/{token}', name: 'app_mes_reservations')]
    public function mesReservations(DonsRepository $donsRepository): Response
    {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour voir vos réservations.');
        }

        $reservations = $donsRepository->findBy(['acheteur' => $user]);

        return $this->render('Front/mesReservations.html.twig', [
            'reservations' => $reservations,
        ]);
    }

    #[Route('/front/quetes', name: 'app_quetes')]
    public function quetes(QuetesRepository $quetesRepository): Response
    {
        $quests = $quetesRepository->findAll(['ordre' => 'ASC']);

        return $this->render('Front/quetes/quetes.html.twig', [
            'quests' => $quests,
        ]);
    }

    #[Route('/front/{id}', name: 'app_front_quetes_play', methods: ['GET'])]
    public function show(Quetes $quete, EntityManagerInterface $entityManager): Response
    {
        $checkbox = false;
        $counter = 0;
        $quetesReponses = $entityManager->getRepository(QuetesReponses::class)->findByQuete($quete);
        foreach($quetesReponses as $reponse){
            if($reponse->getIsGoodQuestion() == 1){
                $counter+=1;
            }
        }
        if($counter > 1){
            $checkbox = true;
        }
        return $this->render('Front/quetes/queteQuizz.html.twig', [
            'quete' => $quete,
            'reponses' => $quetesReponses,
            'checkbox' => $checkbox,
            'counter' => $counter
        ]);
    }

    #[Route('/front/reponse/{id}', name: 'app_front_quetes_reponse', methods: ['GET', 'POST'])]
    public function reponse(Request $request, EntityManagerInterface $entityManager): Response
    {
        $params = $request->request->all();
        $user = $this->getUser();
        if(array_key_exists('checkbox', $params) && $params['checkbox'][0] == 'true' && array_key_exists('counter', $params)){
            $counterReponse = 0;
            $reponseCoche = 0;
            foreach($params['reponses'] as $r){
                $reponseCoche++;
                $reponse = $entityManager->getRepository(QuetesReponses::class)->findById($r);
                if($reponse[0]->getIsGoodQuestion() == 1){
                    $counterReponse++;
                }
            }
            $quete = $reponse[0]->getQuete();
            if($counterReponse == $params['counter'][0] && $reponseCoche == $params['counter'][0]){
                $quete->setEtat(1);
                $user->setXpTotal($user->getXpTotal() + $quete->getXp());
            }else{
                $quete->setEtat(2);
            }
        }else{
            $reponse = $entityManager->getRepository(QuetesReponses::class)->findOneById($params['reponse']);
            $quete = $reponse->getQuete();
            if($reponse->getIsGoodQuestion() == 1){
                $quete->setEtat(1);
                $user->setXpTotal($user->getXpTotal() + $quete->getXp());
            }else{
                $quete->setEtat(2);
            }
        }
        $entityManager->persist($quete);
        $entityManager->persist($user);
        $entityManager->flush();
        return $this->redirectToRoute('app_quetes');
        // return $this->render('Front/quetes/queteQuizz.html.twig', [

        // ]);
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

    #[Route('/{id}/edit', name: 'app_front_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserTypeUser::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_profil', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Front/modifProfil/modifProfil.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
}
