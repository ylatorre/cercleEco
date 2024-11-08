<?php

namespace App\Entity\Application;

use App\Repository\Application\QuetesReponsesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuetesReponsesRepository::class)]
class QuetesReponses
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\ManyToOne(inversedBy: 'quetesReponses')]
    private ?Quetes $quete = null;

    #[ORM\Column]
    private ?int $isGoodQuestion = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

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

    public function getQuete(): ?Quetes
    {
        return $this->quete;
    }

    public function setQuete(?Quetes $quete): static
    {
        $this->quete = $quete;

        return $this;
    }

    public function getIsGoodQuestion(): ?int
    {
        return $this->isGoodQuestion;
    }

    public function setIsGoodQuestion(int $isGoodQuestion): static
    {
        $this->isGoodQuestion = $isGoodQuestion;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
