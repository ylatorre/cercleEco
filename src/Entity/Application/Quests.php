<?php

namespace App\Entity\Application;

use App\Repository\Application\QuestsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuestsRepository::class)]
class Quests
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $CreatedAt = null;

    #[ORM\Column(length: 255)]
    private ?string $level = null;

    #[ORM\Column(length: 255)]
    private ?string $contenu = null;

    #[ORM\Column(length: 255)]
    private ?string $question = null;

    #[ORM\Column(length: 255)]
    private ?string $reponseA = null;

    #[ORM\Column(length: 255)]
    private ?string $reponseB = null;

    #[ORM\Column(length: 255)]
    private ?string $reponseC = null;

    #[ORM\Column(length: 255)]
    private ?string $reponseD = null;

    #[ORM\Column(length: 255)]
    private ?string $reponseCorrecte = null;

    #[ORM\Column]
    private ?int $ordre = null;

    #[ORM\Column]
    private ?float $xp_give = null;

    #[ORM\Column(length: 255)]
    private ?string $token = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->token = bin2hex(random_bytes(50));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->CreatedAt;
    }

    public function setCreatedAt(\DateTimeImmutable $CreatedAt): static
    {
        $this->CreatedAt = $CreatedAt;

        return $this;
    }

    public function getLevel(): ?string
    {
        return $this->level;
    }

    public function setLevel(string $level): static
    {
        $this->level = $level;

        return $this;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): static
    {
        $this->contenu = $contenu;

        return $this;
    }

    public function getQuestion(): ?string
    {
        return $this->question;
    }

    public function setQuestion(string $question): static
    {
        $this->question = $question;

        return $this;
    }

    public function getReponseA(): ?string
    {
        return $this->reponseA;
    }

    public function setReponseA(string $reponseA): static
    {
        $this->reponseA = $reponseA;

        return $this;
    }

    public function getReponseB(): ?string
    {
        return $this->reponseB;
    }

    public function setReponseB(string $reponseB): static
    {
        $this->reponseB = $reponseB;

        return $this;
    }

    public function getReponseC(): ?string
    {
        return $this->reponseC;
    }

    public function setReponseC(string $reponseC): static
    {
        $this->reponseC = $reponseC;

        return $this;
    }

    public function getReponseD(): ?string
    {
        return $this->reponseD;
    }

    public function setReponseD(string $reponseD): static
    {
        $this->reponseD = $reponseD;

        return $this;
    }

    public function getReponseCorrecte(): ?string
    {
        return $this->reponseCorrecte;
    }

    public function setReponseCorrecte(string $reponseCorrecte): static
    {
        $this->reponseCorrecte = $reponseCorrecte;

        return $this;
    }

    public function getOrdre(): ?int
    {
        return $this->ordre;
    }

    public function setOrdre(int $ordre): static
    {
        $this->ordre = $ordre;

        return $this;
    }

    public function getXpGive(): ?int
    {
        return $this->xp_give;
    }

    public function setXpGive(int $xp_give): static
    {
        $this->xp_give = $xp_give;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): static
    {
        $this->token = $token;

        return $this;
    }
}
