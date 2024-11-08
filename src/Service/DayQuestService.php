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

