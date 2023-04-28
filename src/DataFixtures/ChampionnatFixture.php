<?php

namespace App\DataFixtures;

use App\Entity\Equipe;
use App\Entity\Joueur;
use App\Entity\Manager;
use App\Entity\Rencontre;
use App\Entity\Championnat;
use App\Entity\Caracteristique;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Repository\ChampionnatRepository;
use Faker\Factory;
use Doctrine\ORM\EntityManager;
use App\Repository;
use Doctrine\Persistence\ManagerRegistry; 


class ChampionnatFixture extends Fixture
{
    private $faker;

    public function __construct() {
        $this->faker = Factory::create();
    }

    // méthode générant et faisant persister 30 championnats aléatoires
    public function load(ObjectManager $objManager): void
    {
        // tableau de tous les championnats générés
        $championnats = [];
        // calendrier d'un championnat
        $calendrier = [];
        // génération d'équipes aléatoires
        $allEquipes = [];
        for ($i=0; $i<100; $i++) {
            array_push($allEquipes, $this->randEquipe($objManager));
            // et génération de joueurs aléatoires n'appartenant à aucune équipe
            $this->randJoueur($this->faker->randomElement(['Goal', 'Avant', 'Arrière', 'Centre']), $objManager);

        }

        // génération des 30 championnats
        for ($i = 0; $i < 30; $i++) {
            $annee = 1980 + $i . "";
            // instanciation du championnat
            $champ = new Championnat($annee);
            // sélection de 16 équipes aléatoires
            $equipesSelectionnees = $this->faker->randomElements($allEquipes, 16);
            // simulation du championnat
            $champ = $this->randChampionnat($champ, $annee, $equipesSelectionnees, $calendrier, $objManager);
            // on ajoute le championnat à chaque équipe
            foreach($equipesSelectionnees as $equipe) {
                // $equipe->addChampionnat($champ);
            }
            array_push($championnats, $champ);
            // remise à 0 du calendrier
            $calendrier = [];

        }
        // on fait persister toutes les équipes
        foreach($allEquipes as $equipe) {
            $objManager->persist($equipe);
        }
        // on fait persister tous les championnats
        foreach($championnats as $championnat) {
            $objManager->persist($championnat);
        }

        $objManager->flush();
    }

    // méthode mettant à jour la renommée et le budget de l'équipe entrée en argument
    // ainsi que la renommée de tous ses jours
    private function updateEquipe($equipe, $bonus, $buts) : Equipe
    {
        // màj de l'équipe
        if (($newRenommee = $equipe->getRenommee()*$bonus) > 100 ) {
            $newRenommee = 100;
        }
        $equipe->setBudget($equipe->getBudget()*$bonus)
                ->setRenommee($newRenommee)
                ->setPointsChamp($equipe->getPointsChamp() + $buts)
        ;
        // màj de ses joueurs
        foreach($equipe as $joueur) {
            if (($newRenommee = $joueur->getCaracteristiques()->getRenommee()*$bonus) > 100) {
                $newRenommee = 100;
            }
            $joueur->getCaracteristiques()->setRenommee($newRenommee);
        }
        
        return $equipe;
    }

    // méthode simulant le déroulé complet d'un championnat
    private function randChampionnat($champ, $annee, $equipes, $calendrier, $em) : Championnat
    {
        $allEquipes = $equipes;
        // organisation du tournoi
        // tant qu'il y a plus d'une équipe en lice
        while (count($equipes)>1) {
            for ($i=0; $i<8; $i++) {
                if (count($equipes)<=1) {
                    break;
                }
                // on sélectionne deux équipes aléatoires qui vont s'affronter
                $randKeys = array_rand($equipes, 2);
                $tmpEquipes[0] = $equipes[$randKeys[0]];
                $tmpEquipes[1] = $equipes[$randKeys[1]];
                
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
                    $rencontre = $this->match($rencontre);
                    
                    // en cas d'égalité : pas de màj
                    if  ($rencontre->getButs1() !== $rencontre->getButs2()) {
                        // màj des victoires
                        if ($rencontre->getButs1() > $rencontre->getButs2()) {
                            $victoires['equipe1']++;
                            // calcul des bonus/malus
                            $bonus1 = $this->faker->randomFloat(1, 1.3, 1.5);
                            $bonus2 = $this->faker->randomFloat(1, 0.9, 0.7);

                        }
                        else {
                            $victoires['equipe2']++;
                            $bonus2 = $this->faker->randomFloat(1, 1.2, 1.5);
                            $bonus1 = $this->faker->randomFloat(1, 0.8, 0.5);
                        }
                        // màj des renommée/budget
                        $updEquipe1 = $this->updateEquipe($tmpEquipes[0], $bonus1, $rencontre->getButs1());
                        $updEquipe2 = $this->updateEquipe($tmpEquipes[1], $bonus2, $rencontre->getButs2());
                        $allEquipes[array_search($tmpEquipes[0], $equipes)] = $updEquipe1;
                        $allEquipes[array_search($tmpEquipes[1], $equipes)] = $updEquipe2;
                        $equipes[array_search($tmpEquipes[0], $equipes)] = $updEquipe1;
                        $equipes[array_search($tmpEquipes[1], $equipes)] = $updEquipe2;
                    }

                    // on enregistre le match dans le championnat
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

                // remise à zéro des compteurs
                $victoires = [
                    'equipe1' => 0,
                    'equipe2' => 0 
                ];
            }
        }
        // on détermine le vainqueur du tournoi (<=> l'équipe restante)
        foreach($equipes as $vainqueur) {
            $champ->setVainqueur($vainqueur);
        }
    
        return $champ;
    }   

    // méthode simulant un match
    private function match($rencontre) : Rencontre
    {
        // on calcule des buts aléatoires
        $rencontre->setButs1(mt_rand(0,7));
        $rencontre->setButs2(mt_rand(0,7));

        return $rencontre;
    }

    private function randRencontre(string $date, Equipe $eqp1, Equipe $eqp2) : Rencontre
    {
        return new Rencontre (
            /* date */
            $date,
            /* équipe 1 */
            $eqp1,
            /* équipe 2 */
            $eqp2
        );
    }

    // méthode générant une date de match VALIDE selon un calendrier
    private function randDate(string $annee, $calendrier) : string 
    {
        do {
            $date = $annee . substr($this->faker->date(), 4);
        } while(!$this->checkDates($calendrier, $date));

        return $date;
    }

    // méthode vérifiant qu'une date n'est pas déjà prise dans le calendrier du championnat
    private function checkDates($calendrier, $date) : bool 
    {
        if (array_search($date, $calendrier) != FALSE) {
            return FALSE;
        }
        else {
            return TRUE;
        }
    }
 
     // méthode randomisant des caractéristiques de joueur
     private function randCaracts() : Caracteristique {
         return new Caracteristique(
             /* renommée (%) */
             $this->faker->randomFloat(2, 0, 100),
             /* vitesse (notation en %) */
             $this->faker->randomFloat(2, 0, 100),
             /* tir (notation en %) */
             $this->faker->randomFloat(2, 0, 100),
             /* salaire annuel */
             $this->faker->randomFloat(2, 10000, 182000000),
             /* dribble (notation en %) */
             $this->faker->randomFloat(2, 0, 100),
             /* renommée (notation en %) */
             $this->faker->randomFloat(2, 0, 100)
         );
     }
 
    // méthode randomisant un joueur selon sa position sur le terrain
    private function randJoueur( $position, $em) : Joueur {
        // randomisation de caracteristiques
        $cars = $this->randCaracts();

        // randomisation d'un joueur est goal
        $joueur = new Joueur(
            $position,
            /* prénom */
            $this->faker->firstName(),
            /* nom */
            $this->faker->lastName(),
            /* statut */
            $this->faker->randomElement($status = ['Principal-e', 'Réserve', 'Remplaçant-e'])
        );

        // s'il s'agit d'un gardien de but
        if ($position == 'Goal') {
            // on lui ajoute une caractéristique arrêt (notation en %)
            $cars->setArret($this->faker->randomFloat(2, 0, 100));
        }

        // on assigne les caractéristiques obtenues au joueur
        $joueur->setCaracteristiques($cars);

        // persistance du joueur
        $em->persist($joueur);

        return $joueur;
    }
 
     // méthode générant un joueur aléatoire selon sa position sur le terrain
     private function addRandJoueur(string $position, $em) : Joueur {
         $joueur = $this->randJoueur($position, $em);
 
         return $joueur;
     }
 
     private function randEquipe($em) : Equipe {
        $positions = ['Avant', 'Centre', 'Arrière', 'Goal'];

        // nom de la ville
        $city = $this->faker->city();

        // instanciation d'une équipe aléatoire
        $equipe = new Equipe (
            /* ville */
            $this->faker->city(),
            /* nom de l'équipe */
            $this->faker->randomElement($prefixe = ['O', 'FC', 'SC', 'AJ', 'Stade']) . " ". $city,
            /* budget */
            $this->faker->randomFloat(2, 60000, 5555000000),
            /* renommée */
            $this->faker->randomFloat(2, 0, 100),
            $this->faker->paragraph()
        );

        // composition de l'équipe
        foreach($positions as $position) {
            // s'il s'agit du goal
            if ($position == 'Goal') {
                // on n'en ajoute qu'un
                $equipe->addJoueur($this->addRandJoueur($position, $em));
                break;
            }
            // sinon, on ajoute 3 joueurs de chaque position
            for ($i=0; $i<3; $i++) {
                $equipe->addJoueur($this->addRandJoueur($position, $em));
            }
        } 

        // génération d'un staff
        $staff = $this->randStaff();
        foreach ($staff as $manager) {
            // ajout de chaque membre du staff à l'équipe
            $equipe->addStaff($manager);
            // persistance de chaque manager
            $em->persist($manager);
        }

        return $equipe;
    }
 
     // méthode randomisant un manager selon son poste
     private function randManager(string $poste) : Manager {
         return new Manager(
             /* prénom */
             $this->faker->firstName(),
             /* nom */
             $this->faker->lastName(),
             /* salaire annuel */
             $this->faker->randomFloat(2, 18000, 50000),
             $poste
         );
     }
 
     // méthode randomisant un staff complet et le retournant
     private function randStaff() {
         $staff = [];
         $postes = [
             'Entraîneur',
             'Entraîneur suppléant',
             'Directeur général',
             'Directeur sportif',
             'Directeur des médias',
         ];
 
         // randomisation/ajout des managers au staff
         foreach ($postes as $poste) {
             // randomisation de 1 à 5 entraîneurs (nombre aléatoire)
             if ($poste == 'Entraîneur') {
                 for ($i=0; $i<mt_rand(1, 5); $i++) {
                     array_push($staff, $this->randManager($poste));
                 }
             }
             array_push($staff, $this->randManager($poste));
         }
         
         return $staff;
     }
}
