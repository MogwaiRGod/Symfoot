<?php

namespace App\Entity;

use App\Repository\CaracteristiqueRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CaracteristiqueRepository::class)]
class Caracteristique
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?float $renommee = null;

    #[ORM\Column(nullable: true)]
    private ?float $vitesse = null;

    #[ORM\Column(nullable: true)]
    private ?float $dribble = null;

    #[ORM\Column(nullable: true)]
    private ?float $tir = null;

    #[ORM\Column]
    private ?float $salaire = null;

    #[ORM\Column(nullable: true)]
    private ?float $arret = null;

    #[ORM\OneToOne(mappedBy: 'caracteristiques', cascade: ['persist', 'remove'])]
    private ?Joueur $joueur = null;

    public function __construct($rnm, $vts, $tr, $slr, $drbbl) {
        $this->renommee = $rnm;
        $this->vitesse = $vts;
        $this->tir = $tr;
        $this->salaire = $tr;
        $this->dribble = $drbbl;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getVitesse(): ?float
    {
        return $this->vitesse;
    }

    public function setVitesse(?float $vitesse): self
    {
        $this->vitesse = $vitesse;

        return $this;
    }

    public function getDribble(): ?float
    {
        return $this->dribble;
    }

    public function setDribble(?float $dribble): self
    {
        $this->dribble = $dribble;

        return $this;
    }

    public function getTir(): ?float
    {
        return $this->tir;
    }

    public function setTir(?float $tir): self
    {
        $this->tir = $tir;

        return $this;
    }

    public function getSalaire(): ?float
    {
        return $this->salaire;
    }

    public function setSalaire(float $salaire): self
    {
        $this->salaire = $salaire;

        return $this;
    }

    public function getArret(): ?float
    {
        return $this->arret;
    }

    public function setArret(?float $arret): self
    {
        $this->arret = $arret;

        return $this;
    }

    public function getJoueur(): ?Joueur
    {
        return $this->joueur;
    }

    public function setJoueur(Joueur $joueur): self
    {
        // set the owning side of the relation if necessary
        if ($joueur->getCaracteristiques() !== $this) {
            $joueur->setCaracteristiques($this);
        }

        $this->joueur = $joueur;

        return $this;
    }
}
