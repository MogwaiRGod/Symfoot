<?php

namespace App\Entity;

use App\Repository\EquipeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EquipeRepository::class)]
class Equipe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $ville = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column]
    private ?float $budget = null;

    #[ORM\Column(nullable: true)]
    private ?float $renommee = null;

    #[ORM\Column(nullable: true)]
    private ?int $points_champ = null;

    #[ORM\OneToMany(mappedBy: 'equipe', targetEntity: Joueur::class)]
    private Collection $joueurs;

    #[ORM\OneToMany(mappedBy: 'equipe', targetEntity: Manager::class)]
    private Collection $staff;

    #[ORM\OneToMany(mappedBy: 'vainqueur', targetEntity: Championnat::class)]
    private Collection $championnats;

    public function __construct($vll, $nm, $bdgt, $rnmm)
    {
        $this->ville = $vll;
        $this->nom = $nm;
        $this->budget = $bdgt;
        $this->renommee = $rnmm;
        $this->joueurs = new ArrayCollection();
        $this->staff = new ArrayCollection();
        $this->championnats = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): self
    {
        $this->ville = $ville;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getBudget(): ?float
    {
        return $this->budget;
    }

    public function setBudget(float $budget): self
    {
        $this->budget = $budget;

        return $this;
    }

    public function getRenommee(): ?float
    {
        return $this->renommee;
    }

    public function setRenommee(?float $renommee): self
    {
        $this->renommee = $renommee;

        return $this;
    }

    public function getPointsChamp(): ?int
    {
        return $this->points_champ;
    }

    public function setPointsChamp(?int $points_champ): self
    {
        $this->points_champ = $points_champ;

        return $this;
    }

    /**
     * @return Collection<int, Joueur>
     */
    public function getJoueurs(): Collection
    {
        return $this->joueurs;
    }

    public function addJoueur(Joueur $joueur): self
    {
        if (!$this->joueurs->contains($joueur)) {
            $this->joueurs->add($joueur);
            $joueur->setEquipe($this);
        }

        return $this;
    }

    public function removeJoueur(Joueur $joueur): self
    {
        if ($this->joueurs->removeElement($joueur)) {
            // set the owning side to null (unless already changed)
            if ($joueur->getEquipe() === $this) {
                $joueur->setEquipe(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Manager>
     */
    public function getStaff(): Collection
    {
        return $this->staff;
    }

    public function addStaff(Manager $staff): self
    {
        if (!$this->staff->contains($staff)) {
            $this->staff->add($staff);
            $staff->setEquipe($this);
        }

        return $this;
    }

    public function removeStaff(Manager $staff): self
    {
        if ($this->staff->removeElement($staff)) {
            // set the owning side to null (unless already changed)
            if ($staff->getEquipe() === $this) {
                $staff->setEquipe(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Championnat>
     */
    public function getChampionnats(): Collection
    {
        return $this->championnats;
    }

    public function addChampionnat(Championnat $championnat): self
    {
        if (!$this->championnats->contains($championnat)) {
            $this->championnats->add($championnat);
            $championnat->setVainqueur($this);
        }

        return $this;
    }

    public function removeChampionnat(Championnat $championnat): self
    {
        if ($this->championnats->removeElement($championnat)) {
            // set the owning side to null (unless already changed)
            if ($championnat->getVainqueur() === $this) {
                $championnat->setVainqueur(null);
            }
        }

        return $this;
    }
}
