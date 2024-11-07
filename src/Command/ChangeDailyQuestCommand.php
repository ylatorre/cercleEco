<?php

namespace App\Command;

use App\Service\DayQuestService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ChangeDailyQuestCommand extends Command
{
    private DayQuestService $dayQuestService;

    public function __construct(DayQuestService $dayQuestService)
    {
        parent::__construct();
        $this->dayQuestService = $dayQuestService;
    }

    protected static $defaultName = 'app:change-daily-quests';

    protected function configure()
    {
        $this->setDescription('Change les quêtes journalières pour tout le monde');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Mettre à jour les quêtes dans le cache pour 24 heures
        $this->dayQuestService->setDailyQuestsInCache();

        $output->writeln('Les quêtes journalières ont été mises à jour.');

        return Command::SUCCESS;
    }
}