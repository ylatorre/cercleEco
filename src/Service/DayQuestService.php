<?php

namespace App\Service;

use App\Repository\Application\DayQuestRepository;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Contracts\Cache\CacheInterface;

use Psr\Cache\CacheItemPoolInterface;

/*
class DayQuestService
{
    private DayQuestRepository $dayQuestRepository;

    public function __construct(DayQuestRepository $dayQuestRepository)
    {
        $this->dayQuestRepository = $dayQuestRepository;
    }

    public function generateDailyQuests(): array
    {
        // Récupérer toutes les quêtes puis en sélectionner 2 ou 3 aléatoirement
        $allQuests = $this->dayQuestRepository->findAll();
        $randomKeys = array_rand($allQuests, 3);

        if (!is_array($randomKeys)) {
            $randomKeys = [$randomKeys];
        }

        return array_map(fn($key) => $allQuests[$key], $randomKeys);
    }
}
*/


//Utilisable actuellement avec le changement en vidant le cache

class DayQuestService
{
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
}

