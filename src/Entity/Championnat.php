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

    // méthode générant une date de match VALIDE selon un calendrier
    private function randDate(string $annee) : string 
    {
        do {
            $date = $annee . substr($this->faker->date(), 4);
        } while(!$this->checkDates($this->calendrier, $date));

        return $date;
    }

    // méthode simulant le déroulé d'un championnat (= une saison)
    public function saison($listeEquipes) {
        // organisation du tournoi
        // tant qu'il y a plus d'une équipe en lice
        while (count($listeEquipes)>1) {
            for ($i=0; $i<8; $i++) {
                if (count($listeEquipes) <= 1) {
                    break;
                }
                // on sélectionne deux équipes aléatoires qui vont s'affronter
                $randKeys = array_rand($listeEquipes, 2);
                $tmpEquipes[0] = $listeEquipes[$randKeys[0]];
                $tmpEquipes[1] = $listeEquipes[$randKeys[1]];
                
                // compteur de victoires pour chacune des équipes
                $victoires = [
                    'equipe1' => 0,
                    'equipe2' => 0 
                ];

                // organisation de 2 matches entre les 2 équipes
                for ($i = 0; $i<2; $i++) {
                    // choix d'une date (disponible) pour le match
                    $date = $this->randDate($annee, $calendrier);
                    // on l'ajoute au calendrier
                    array_push($calendrier, $date);
    
                    // organisation du match
                    $rencontre = $this->randRencontre($date, $tmpEquipes[0], $tmpEquipes[1]);
                    // déroulé du match
                    $scores = $rencontre->match();
                    
                    // en cas d'égalité : pas de màj
                    if  ($score['equipe1'] !== $score['equipe2']) {
                        // màj des victoires
                        if ($score['equipe1'] > $score['equipe2']) {
                            $victoires['equipe1']++;
                            // calcul des bonus/malus
                            $bonus1 = mt_rand(130, 159)/100;
                            $bonus2 = mt_rand(70, 99)/100;

                        }
                        else {
                            $victoires['equipe2']++;
                            $bonus2 = mt_rand(130, 159)/100;
                            $bonus1 = mt_rand(70, 99)/100;
                        }

                        // màj des renommée/budget
                        $updEquipe1 = $this->updateEquipe($tmpEquipes[0], $bonus1, $rencontre->getButs1());
                        $updEquipe2 = $this->updateEquipe($tmpEquipes[1], $bonus2, $rencontre->getButs2());
                        $allEquipes[array_search($tmpEquipes[0], $equipes)] = $updEquipe1;
                        $allEquipes[array_search($tmpEquipes[1], $equipes)] = $updEquipe2;
                        $equipes[array_search($tmpEquipes[0], $equipes)] = $updEquipe1;
                        $equipes[array_search($tmpEquipes[1], $equipes)] = $updEquipe2;
                    }

                    // on enregistre le championnat dans le match
                    $rencontre->setChampionnat($champ);

                    // persistance de la rencontre
                    $em->persist($rencontre);

                    // on l'ajoute au calendrier du championnat
                    $champ->addCalendrier($rencontre);

                    unset($rencontre);
                }
                // on détermine l'équipe gagnante <=> on élimine la perdante
                if ($victoires['equipe1'] > $victoires['equipe2']) {
                    unset($equipes[array_search($tmpEquipes[1], $equipes)]);
                }
                elseif ($victoires['equipe1'] < $victoires['equipe2']) {
                    unset($equipes[array_search($tmpEquipes[0], $equipes)]);
                }
            }
        }
        // on détermine le vainqueur du tournoi (<=> l'équipe restante)
        foreach($equipes as $vainqueur) {
            $champ->setVainqueur($vainqueur);
        }
    
        return $champ;
    }
}
