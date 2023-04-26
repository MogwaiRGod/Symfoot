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

    public function __construct()
    {
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
}
