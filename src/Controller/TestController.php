<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Rencontre;
use App\Entity\Equipe;
use App\Entity\Joueur;
use App\Entity\Caracteristique;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class TestController extends AbstractController
{
    private $faker;
    
    public function __construct() {
        $this->faker = Factory::create();
    }

    #[Route('/test', name: 'app_test')]
    public function index(): Response
    {
        
        dump($this->faker->randomElements($array = array ('a','b','c'), $count = 1));
        return $this->render('test/index.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }
}
