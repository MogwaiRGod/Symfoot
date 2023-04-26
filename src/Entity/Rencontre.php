<?php

namespace App\Entity;

use App\Repository\RencontreRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RencontreRepository::class)]
class Rencontre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $occasions1 = null;

    #[ORM\Column(nullable: true)]
    private ?int $occasions2 = null;

    #[ORM\Column(length: 50)]
    private ?string $date = null;

    #[ORM\Column(nullable: true)]
    private ?int $buts1 = null;

    #[ORM\Column(nullable: true)]
    private ?int $buts2 = null;

    #[ORM\ManyToOne(inversedBy: 'calendrier')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Championnat $championnat = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Equipe $equipe1 = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Equipe $equipe2 = null;

    public function __construct(string $dt, Equipe $eq1, Equipe $eq2) {
        $this->occasions1 = $this->randOccasion();
        $this->occasions2 = $this->randOccasion();
        $this->date = $dt;
        $this->equipe1 = $eq1;
        $this->equipe2 = $eq2;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOccasions1(): ?int
    {
        return $this->occasions1;
    }

    public function setOccasions1(?int $occasions1): self
    {
        $this->occasions1 = $occasions1;

        return $this;
    }

    public function getOccasions2(): ?int
    {
        return $this->occasions2;
    }

    public function setOccasions2(?int $occasions2): self
    {
        $this->occasions2 = $occasions2;

        return $this;
    }

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function setDate(string $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getButs1(): ?int
    {
        return $this->buts1;
    }

    public function setButs1(?int $buts1): self
    {
        $this->buts1 = $buts1;

        return $this;
    }

    public function getButs2(): ?int
    {
        return $this->buts2;
    }

    public function setButs2(?int $buts2): self
    {
        $this->buts2 = $buts2;

        return $this;
    }

    public function getChampionnat(): ?Championnat
    {
        return $this->championnat;
    }

    public function setChampionnat(?Championnat $championnat): self
    {
        $this->championnat = $championnat;

        return $this;
    }

    public function getEquipe1(): ?Equipe
    {
        return $this->equipe1;
    }

    public function setEquipe1(Equipe $equipe1): self
    {
        $this->equipe1 = $equipe1;

        return $this;
    }

    public function getEquipe2(): ?Equipe
    {
        return $this->equipe2;
    }

    public function setEquipe2(Equipe $equipe2): self
    {
        $this->equipe2 = $equipe2;

        return $this;
    }

    private function randOccasion() {
        return mt_rand(0, 20);
    }
}
