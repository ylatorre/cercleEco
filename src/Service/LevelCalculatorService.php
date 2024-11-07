<?php
// src/Service/LevelCalculatorService.php
namespace App\Service;

class LevelCalculatorService
{
    public function calculerNiveau(int $xp): int
    {
// Formule exponentielle simplifiée pour le calcul du niveau
        return floor(log($xp / 300) / log(1.3)) + 1;
    }

    public function getXpSeuil(int $niveau): int
    {
        // Formule pour calculer l'XP requis pour atteindre le niveau suivant
        return (int) floor(300 * pow(1.3, $niveau ));
    }
}
