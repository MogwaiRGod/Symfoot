<?php

namespace App\DataFixtures;

use App\Entity\Equipe;
use App\Entity\Joueur;
use App\Entity\Manager;
use App\Entity\Caracteristique;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Doctrine\ORM\EntityManager;

class EquipeFixture extends Fixture
{
    private $faker;

    public function __construct() {
        $this->faker = Factory::create();
    }

    // méthode randomisant 100 équipes
    public function load(ObjectManager $objManager): void
    {
        for ($i = 0; $i < 100; $i++) {
            // génération d'une équipe
            $equipe = $this->randEquipe($objManager);
            // persistance de chaque équipe
            $objManager->persist($equipe);
        }
        $objManager->flush();
    }

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
