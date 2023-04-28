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

    public function __construct(string $dt, Equipe $eq1, Equipe $eq2) 
    {
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

    // simule un match ; retourne un tableau associatif avec le score final de chaque équipe
    public function match(Equipe $eq1, Equipe $eq2) : mixed
    {
        // à chaque minute du match
        for ($i=0; $i<90; $i++) {

        }
        return;
    }

    // méthode déterminant des minutes où chaque équipe aura une occasion de marquer, selon le nombre d'occasions 
    // dont elles disposent. Retourne un tableau associatif de deux tableaux, chacun contenant les minutes d'occasion
    private function randMinutesOcc(int $nbOccs1, int $nbOccs2) : mixed
    {
        // tableau de toutes les minutes d'occasions
        $allOccasions = [];
        // tant que ce tableau ne contient pas assez de minutes
        while(count($allOccasions) < $nbOccs1+$nbOccs2) {
            // détermine une minute aléatoire et l'ajoute au tableau
            array_push($allOccasions, mt_rand(0, 89));
            // enlève les doublons
            $allOccasions = array_unique($allOccasions);
        }
        // on découpe le tableau obtenu en 2 -> un pour chaque équipe
        // et on trie les minutes en ordre décroissant
        $minutes1 = arsort(array_slice($allOccasions, 0, $nbOccs1-1));
        $minutes2 = arsort(array_slice($allOccasions, $nbOccs1-1));

        return [
            'equipe1' => $minutes1, 
            'equipe2' => $minutes2
        ];
    }

    // calcule des occasions de tirs alétoires
    private function randOccasion() :int 
    {
        return mt_rand(0, 20);
    }
}
