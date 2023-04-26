<?php

namespace App\Entity;

use App\Repository\ChampionnatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChampionnatRepository::class)]
class Championnat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToMany(mappedBy: 'championnat', targetEntity: Rencontre::class, orphanRemoval: true)]
    private Collection $calendrier;

    #[ORM\Column(length: 5)]
    private ?string $annee = null;

    #[ORM\ManyToOne(inversedBy: 'championnats')]
    private ?Equipe $vainqueur = null;

    public function __construct($year)
    {
        $this->annee = $year;
        $this->calendrier = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Rencontre>
     */
    public function getCalendrier(): Collection
    {
        return $this->calendrier;
    }

    public function addCalendrier(Rencontre $calendrier): self
    {
        if (!$this->calendrier->contains($calendrier)) {
            $this->calendrier->add($calendrier);
            $calendrier->setChampionnat($this);
        }

        return $this;
    }

    public function removeCalendrier(Rencontre $calendrier): self
    {
        if ($this->calendrier->removeElement($calendrier)) {
            // set the owning side to null (unless already changed)
            if ($calendrier->getChampionnat() === $this) {
                $calendrier->setChampionnat(null);
            }
        }

        return $this;
    }

    public function getAnnee(): ?string
    {
        return $this->annee;
    }

    public function setAnnee(string $annee): self
    {
        $this->annee = $annee;

        return $this;
    }

    public function getVainqueur(): ?Equipe
    {
        return $this->vainqueur;
    }

    public function setVainqueur(?Equipe $vainqueur): self
    {
        $this->vainqueur = $vainqueur;

        return $this;
    }
}
