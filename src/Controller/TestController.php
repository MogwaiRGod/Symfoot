<?php

namespace App\Controller;

use App\Entity\Equipe;
use App\Entity\Joueur;
use App\Entity\Championnat;
use App\Entity\Rencontre;
use App\Entity\Manager;
use App\Repository\ChampionnatRepository;
use App\Entity\Caracteristique;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Persistence\ManagerRegistry;

class TestController extends AbstractController
{
    private $faker;
    
    public function __construct() {
        $this->faker = Factory::create();
    }

    #[Route('/test', name: 'app_test')]
    public function index(ChampionnatRepository $repo): Response
    {
        dump($repo->lastYear());
    }

    
 

    // méthode générant un joueur aléatoire selon sa position sur le terrain et le faisant persister
    private function addRandJoueur(string $position) : Joueur {
        $joueur = $this->randJoueur($position);

        return $joueur;
    }
    
    private function randEquipe() : Equipe {
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
            $this->faker->randomFloat(2, 0, 100)
        );

        // composition de l'équipe
        foreach($positions as $position) {
            // s'il s'agit du goal
            if ($position == 'Goal') {
                // on n'en ajoute qu'un
                $equipe->addJoueur($this->addRandJoueur($position));
                break;
            }
            // sinon, on ajoute 3 joueurs de chaque position
            for ($i=0; $i<3; $i++) {
                $equipe->addJoueur($this->addRandJoueur($position));
            }
        } 

        // génération d'un staff
        $staff = $this->randStaff();
        foreach ($staff as $manager) {
            // ajout de chaque membre du staff à l'équipe
            $equipe->addStaff($manager);
            // persistance de chaque membre du staff
            // $em->persist($manager);
        }

        // on fait persister l'équipe
        // $em->persist($equipe);

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

