<?php

namespace App\Service;

use App\Repository\Application\DayQuestRepository;
use App\Entity\Application\DayQuestUser;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Contracts\Cache\CacheInterface;

use App\Repository\Application\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;


class DayQuestService
{
    /*
    private DayQuestRepository $dayQuestRepository;
    private CacheItemPoolInterface $cache;

    public function __construct(DayQuestRepository $dayQuestRepository, CacheItemPoolInterface $cache)
    {
        $this->dayQuestRepository = $dayQuestRepository;
        $this->cache = $cache;
    }

    public function generateDailyQuests(): array
    {
        // Récupérer toutes les quêtes disponibles
        $allQuests = $this->dayQuestRepository->findAll();
        
        // S'assurer qu'il y a au moins 3 quêtes disponibles
        if (count($allQuests) < 3) {
            throw new \Exception("Il y a moins de 3 quêtes disponibles dans la base de données.");
        }

        // Sélectionner 3 quêtes aléatoires
        $randomKeys = array_rand($allQuests, 3);  // Sélectionner 3 quêtes aléatoirement
        
        // Si un seul élément est sélectionné, le convertir en tableau
        if (!is_array($randomKeys)) {
            $randomKeys = [$randomKeys];
        }

        // Retourner les 3 quêtes sélectionnées
        return array_map(fn($key) => $allQuests[$key], $randomKeys);
    }

    public function setDailyQuestsInCache(): void
    {
        $dailyQuests = $this->generateDailyQuests();

        // Stocker les quêtes générées dans le cache pour 24 heures
        $cacheItem = $this->cache->getItem('daily_quests');
        $cacheItem->set($dailyQuests);
        $cacheItem->expiresAt(new \DateTime('+1 day'));  // Les quêtes expirent après 24 heures
        $this->cache->save($cacheItem);
    }

    public function getDailyQuests(): array
    {
        // Récupérer les quêtes depuis le cache
        $cacheItem = $this->cache->getItem('daily_quests');

        if ($cacheItem->isHit()) {
            return $cacheItem->get();
        }

        // Si le cache est vide ou expiré, les regénérer
        $this->setDailyQuestsInCache();

        // Retourner les nouvelles quêtes
        return $this->getDailyQuests();
    }

    */

    private EntityManagerInterface $entityManager;
    private DayQuestRepository $dayQuestRepository;
    private UserRepository $userRepository;
    private FilesystemAdapter $cache;

    public function __construct(EntityManagerInterface $entityManager, DayQuestRepository $dayQuestRepository, UserRepository $userRepository)
    {
        $this->entityManager = $entityManager;
        $this->dayQuestRepository = $dayQuestRepository;
        $this->userRepository = $userRepository;
        $this->cache = new FilesystemAdapter();
    }
    
    public function setDailyQuestsInCacheAndDatabase(): void
    {
        $dailyQuests = $this->getRandomDailyQuests();

        // Vider et remplir la table de liaison day_quest_user
        $this->clearDayQuestUserTable();
        $this->assignQuestsToAllUsers($dailyQuests);

        // Vider le cache pour forcer le rafraîchissement
        $this->cache->delete('daily_quests');

        // Enregistrer les nouvelles quêtes dans le cache sans durée spécifique
        $this->cache->save($this->cache->getItem('daily_quests')->set($dailyQuests));
    }

    // Nouvelle méthode pour obtenir les quêtes journalières
    public function getDailyQuests(): array
    {
        return $this->cache->get('daily_quests', function () {
            // Si les quêtes ne sont pas encore en cache, les générer
            $dailyQuests = $this->getRandomDailyQuests();
            $this->setDailyQuestsInCacheAndDatabase();
            return $dailyQuests;
        });
    }

    private function getRandomDailyQuests(): array
    {
        $allQuests = $this->dayQuestRepository->findAll();
        $randomKeys = array_rand($allQuests, 3);

        if (!is_array($randomKeys)) {
            $randomKeys = [$randomKeys];
        }

        return array_map(fn($key) => $allQuests[$key], $randomKeys);
    }

    private function clearDayQuestUserTable(): void
    {
        $connection = $this->entityManager->getConnection();
        $sql = 'DELETE FROM day_quest_user';
        $connection->executeStatement($sql);
    }

    private function assignQuestsToAllUsers(array $dailyQuests): void
    {
        $users = $this->userRepository->findAll();

        foreach ($users as $user) {
            foreach ($dailyQuests as $quest) {
                $dayQuestUser = new DayQuestUser();
                $dayQuestUser->setUser($user);
                $dayQuestUser->setDayQuest($quest);
                $dayQuestUser->setEtat(0); // 0 pour "non terminé"

                $this->entityManager->persist($dayQuestUser);
            }
        }

        $this->entityManager->flush();
    }
}

