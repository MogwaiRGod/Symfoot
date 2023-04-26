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

    // méthode générant 70 championnats aléatoires
    public function load(ObjectManager $objManager): void
    {
        // création du calendrier du championnat
        $calendrier = [];
        // inscription des équipes participant au tournoi
        $equipes = [];

        // génération des 30 championnats
        for ($i = 0; $i < 30; $i++) {
            $annee = 1980 + $i . "";
            // $manager->persist(new Championnat($annee));
        }

        $manager->flush();
        return;
    }

    private function randChampionnat(EntityManager $em, $annee) : randChampionnat
    {
        // instanciation de championnat
        $champ = new Championnat($annee);



        // organisation du tournoi (16)

            // chaque équipe va s'affronter 2 fois
            $equipe1 = $this->randEquipe($em);
            $equipe2 = $this->randEquipe($em);

            // ajout des équipes au championnat
            array_push($equipes, $equipe1, $equipe2);

            // persistance des 2 équipes
            $em->persist($equipe1)
                ->persist($equipe2)
            ;

            // organisation de 2 matches entre les 2 équipes
            for ($i = 0; $i <2; $i++) {
                $date = randDate($annee, $calendrier);
                array_push($calendrier, $date);
                $rencontre = $this->randRencontre($date, $eqp1, $eqp2);

                // on calcule des buts aléatoires
                $rencontre->setButs1(mt_rand(0,7));
                $rencontre->setButs2(mt_rand(0,7));

                // on désigne le vainqueur (s'il y en a un) <=> on élimine le perdant
                if ($rencontre->getButs1() > $rencontre->getButs2()) {
                    unset($equipes[array_search($equipe2, $equipes)]);
                }
                elseif ($rencontre->getButs1() < $rencontre->getButs2()) {
                    unset($equipes[array_search($equipe1, $equipes)]);
                }
                // en cas d'égalité, les 2 équipes restent en lice

                // on enregistre le match dans le championnat
                $rencontre->setChampionnat($champ):

                // persistance de la rencontre
                $em->persist($rencontre);
            }
    }

    private function match() {
        
    }

    private function randRencontre(string $date, Equipe $eqp1, Equipe $eqp2) : Equipe
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
        } while(!checkDates($calendrier, $date));

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

    //  // méthode randomisant 100 équipes
    //  public function load(ObjectManager $objManager): void
    //  {
    //      for ($i = 0; $i < 100; $i++) {
    //          // génération d'une équipe
    //          $equipe = $this->randEquipe($objManager);
    //          // persistance de chaque équipe
    //          $objManager->persist($equipe);
    //      }
    //      $objManager->flush();
    //  }
 
     // méthode randomisant des caractéristiques de joueur
     private function randCaracts(EntityManager $em) : Caracteristique {
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
     private function randJoueur(EntityManager $em, $position) : Joueur {
         // randomisation de caracteristiques
         $cars = $this->randCaracts($em);
 
         // randomisation d'un joueur est goal
         $joueur = new Joueur(
             $position,
             /* prénom */
             $this->faker->firstName(),
             /* nom */
             $this->faker->lastName(),
             /* statut */
             $this->faker->randomElements($status = ['Principal-e', 'Réserve', 'Remplaçant-e'])[0]
         );
 
         // s'il s'agit d'un gardien de but
         if ($position == 'Goal') {
             // on lui ajoute une caractéristique arrêt (notation en %)
             $cars->setArret($this->faker->randomFloat(2, 0, 100));
         }
 
         // on fait persister les caractéristiques
         $em->persist($cars);
 
         // on assigne les caractéristiques obtenues au joueur
         $joueur->setCaracteristiques($cars);
 
         return $joueur;
     }
 
     // méthode générant un joueur aléatoire selon sa position sur le terrain et le faisant persister
     private function addRandJoueur(EntityManager $em, string $position) : Joueur {
         $joueur = $this->randJoueur($em, $position);
         // persistance du joueur
         $em->persist($joueur);
 
         return $joueur;
     }
 
     private function randEquipe(EntityManager $em) : Equipe {
         $positions = ['Avant', 'Centre', 'Arrière', 'Goal'];
 
         // nom de la ville
         $city = $this->faker->city();
 
         // instanciation d'une équipe aléatoire
         $equipe = new Equipe (
             /* ville */
             $this->faker->city(),
             /* nom de l'équipe */
             $this->faker->randomElements($prefixe = ['O', 'FC', 'SC', 'AJ', 'Stade'])[0] . " ". $city,
             /* budget */
             $this->faker->randomFloat(2, 60000, 5555000000),
             /* renommée */
             $this->faker->randomFloat(2, 0, 100)
         );
 
         // composition de l'équipe
         foreach($positions as $position) {
             // s'il s'agit du goal
             if ($position == 'Goal') {
                 // on n'en ajoute qu'un
                 $equipe->addJoueur($this->addRandJoueur($em, $position));
                 break;
             }
             // sinon, on ajoute 3 joueurs de chaque position
             for ($i=0; $i<3; $i++) {
                 $equipe->addJoueur($this->addRandJoueur($em, $position));
             }
         } 
 
         // génération d'un staff
         $staff = $this->randStaff();
         foreach ($staff as $manager) {
             // ajout de chaque membre du staff à l'équipe
             $equipe->addStaff($manager);
             // persistance de chaque membre du staff
             $em->persist($manager);
         }
 
         // on fait persister l'équipe
         $em->persist($equipe);
 
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
 
         // randomisation/persistance/ajout des managers au staff
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
