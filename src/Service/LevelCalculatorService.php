<?php
// src/Service/LevelCalculatorService.php
namespace App\Service;

class LevelCalculatorService
{
    public function calculerNiveau(int $xp): int
    {
// Formule exponentielle simplifiée pour le calcul du niveau
        return floor(log($xp / 300) / log(1.1)) + 1;
    }
}
