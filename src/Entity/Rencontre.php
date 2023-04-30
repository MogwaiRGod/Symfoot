<?php

namespace App\Entity;

use App\Repository\RencontreRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Equipe;
use App\Entity\Joueur;

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
        $tmpOcc = $this->randMinutesOcc($this->occasions1, $this->occasions2);
        $occEqp1 = $tmpOcc['equipe1'];
        $occEqp2 = $tmpOcc['equipe2'];
        $butsEq1 = 0;
        $butsEq2 = 0;


        // à chaque minute du match
        for ($i=0; $i<90; $i++) {
            // si la minute en court correspond à une occasion de marquer pour l'une ou l'autre des équipes
            if($occEqp1[count($occEqp1)-1] == $i) {
                // si la tentative réussit
                if($this->essayerBut($eq1, $eq2)) {
                    $butsEq1++;
                }
            }
            elseif($occEqp2[count($occEqp2)-1] == $i) {
                if($this->essayerBut($eq2, $eq1)) {
                    $butsEq2++;
                }
            }
        }
        
        return [
            'equipe1' => $butsEq1,
            'equipe2' => $butsEq2
        ];
    }

    // méthode sélectionnant un joueur aléatoire d'une équipe pour tenter de marquer un but
    private function randJoueurTir($equipe) : Joueur
    {
        do {
            $joueur = $equipe->getJoueurs()[array_rand($equipe->getJoueurs())];
        } while ($joueur->getPosition() == 'Goal');

        return $joueur;
    }

    // méthode simulant un essai de tir et retournant s'il est réussi ou non
    private function essayerBut($equipeAttaque, $equipeAdverse) : bool
    {
        $joueur = $this->randJoueurTir($equipeAttaque);
        // p(A)
        $chancesTir = $this->calcChances($joueur);
        // p(B)
        $chancesRepousser = $this->calcChancesRepousser($equipeAdverse);
        // p(A ET B)
        $probas = $chancesTir*$chancesRepousser;

        // simulation de l'événement <=> on tire un numéro au hasard de 0 à 100
        $simulation = mt_rand(0, 100);
        // si ce numéro est compris entre 0 et la probabilité de marquer
        if ($simulation <= $probas) {
            return TRUE;
        }
        else {
            return FALSE;
        }
    }

    // méthode déterminant les chances de repousser un but d'un goal et/ou des défenseurs
    // respectivement selon leurs stats d'arrêt et de dribble
    private function calcChancesRepousser($equipe) : float
    {
        $listeStatsDribble;
        // récupération du goal
        $goal = $equipe->getGoal();
        // récupération de ses stats d'arrêt
        $chancesGoal = $goal->getCaracteristiques()->getArret();

        // récupération des stats de dribble des défenseurs
        foreach($equipe->getDefenseurs() as $defenseur) {
            array_push($listeStatsDribble, $defenseur->getCaracteristiques()->getDribble());
        }
        // on calcule la moyenne de ces stats
        $chancesDefense = array_sum(/* additionne tous les éléments de la liste */$listeStatsDribble)/count($listeStatsDribble);
        return ($chancesDefense*0.33/* pondération de 33% */ + $chancesGoal*0.67/* pondération de 67% */)/2;
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
