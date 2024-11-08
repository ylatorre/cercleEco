<?php

namespace App\Entity\Application;

use App\Repository\Application\QuetesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuetesRepository::class)]
class Quetes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    private ?int $etat = null;

    #[ORM\Column(length: 255)]
    private ?string $titreQuestion = null;

    /**
     * @var Collection<int, QuetesReponses>
     */
    #[ORM\OneToMany(targetEntity: QuetesReponses::class, mappedBy: 'quete')]
    private Collection $quetesReponses;

    #[ORM\Column]
    private ?int $xp = null;

    #[ORM\Column(length: 255)]
    private ?string $token = null;

    public function __construct()
    {
        $this->quetesReponses = new ArrayCollection();
        $this->token = bin2hex(random_bytes(50));
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

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

    public function getTitreQuestion(): ?string
    {
        return $this->titreQuestion;
    }

    public function setTitreQuestion(string $titreQuestion): static
    {
        $this->titreQuestion = $titreQuestion;

        return $this;
    }

    /**
     * @return Collection<int, QuetesReponses>
     */
    public function getQuetesReponses(): Collection
    {
        return $this->quetesReponses;
    }

    public function addQuetesReponse(QuetesReponses $quetesReponse): static
    {
        if (!$this->quetesReponses->contains($quetesReponse)) {
            $this->quetesReponses->add($quetesReponse);
            $quetesReponse->setQuete($this);
        }

        return $this;
    }

    public function removeQuetesReponse(QuetesReponses $quetesReponse): static
    {
        if ($this->quetesReponses->removeElement($quetesReponse)) {
            // set the owning side to null (unless already changed)
            if ($quetesReponse->getQuete() === $this) {
                $quetesReponse->setQuete(null);
            }
        }

        return $this;
    }

    public function getXp(): ?int
    {
        return $this->xp;
    }

    public function setXp(int $xp): static
    {
        $this->xp = $xp;

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
