<?php

namespace App\Entity\Application;

use App\Repository\Application\etatRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: etatRepository::class)]
class etat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $code = null;

    #[ORM\Column]
    private ?bool $isFinish = null;

    // Dans l'entité Etat
    #[ORM\ManyToOne(targetEntity: Quests::class)]
    private ?Quests $quest = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $user = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function isFinish(): ?bool
    {
        return $this->isFinish;
    }

    public function setFinish(bool $isFinish): static
    {
        $this->isFinish = $isFinish;

        return $this;
    }
}

