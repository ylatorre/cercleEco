<?php

namespace App\Entity\Application;

use App\Repository\Application\DayQuestRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DayQuestRepository::class)]
class DayQuest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?int $xp = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_de_creation = null;

    /**
     * @var Collection<int, DayQuestUser>
     */
    #[ORM\OneToMany(targetEntity: DayQuestUser::class, mappedBy: 'dayQuest')]
    private Collection $dayQuestUsers;

    public function __construct()
    {
        $this->date_de_creation = new \DateTimeImmutable();
        $this->dayQuestUsers = new ArrayCollection();
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

    public function getXp(): ?int
    {
        return $this->xp;
    }

    public function setXp(int $xp): static
    {
        $this->xp = $xp;

        return $this;
    }

    public function getDateDeCreation(): ?\DateTimeInterface
    {
        return $this->date_de_creation;
    }

    public function setDateDeCreation(\DateTimeInterface $date_de_creation): static
    {
        $this->date_de_creation = $date_de_creation;

        return $this;
    }

    /**
     * @return Collection<int, DayQuestUser>
     */
    public function getDayQuestUsers(): Collection
    {
        return $this->dayQuestUsers;
    }

    public function addDayQuestUser(DayQuestUser $dayQuestUser): static
    {
        if (!$this->dayQuestUsers->contains($dayQuestUser)) {
            $this->dayQuestUsers->add($dayQuestUser);
            $dayQuestUser->setDayQuest($this);
        }

        return $this;
    }

    public function removeDayQuestUser(DayQuestUser $dayQuestUser): static
    {
        if ($this->dayQuestUsers->removeElement($dayQuestUser)) {
            // set the owning side to null (unless already changed)
            if ($dayQuestUser->getDayQuest() === $this) {
                $dayQuestUser->setDayQuest(null);
            }
        }

        return $this;
    }
}
