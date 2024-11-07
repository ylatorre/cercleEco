<?php

namespace App\Entity\Application;

use App\Repository\Application\DayQuestUserRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DayQuestUserRepository::class)]
class DayQuestUser
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'dayQuestUsers')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'dayQuestUsers')]
    private ?DayQuest $dayQuest = null;

    #[ORM\Column(nullable: true)]
    private ?int $etat = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getDayQuest(): ?DayQuest
    {
        return $this->dayQuest;
    }

    public function setDayQuest(?DayQuest $dayQuest): static
    {
        $this->dayQuest = $dayQuest;

        return $this;
    }

    public function getEtat(): ?int
    {
        return $this->etat;
    }

    public function setEtat(?int $etat): static
    {
        $this->etat = $etat;

        return $this;
    }

}
