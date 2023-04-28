<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Equipe;
use App\Entity\Caracteristique;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\EquipeRepository;
use App\Repository\JoueurRepository;

class CreateController extends AbstractController
{
    #[Route('/create/team', name: 'app_create_team')]
    public function index(string $msg = NULL, JoueurRepository $repoJr): Response
    {
        $joueurs = $repoJr->findAll();
        return $this->render('create/index.html.twig', [
            'title' => 'CREATE TEAM',
            'message' => $msg,
            'joueurs' => $joueurs,
        ]);
    }

    #[Route('/create/team/process', name: 'app_create_team_process')]
    public function processTeam(EquipeRepository $repoEq): Response
    {
        $newEquipe = new Equipe(
            $_POST['ville'],
            $_POST['nom'],
            mt_rand(60000, 5000000000),
            mt_rand(0, 100),
            $_POST['desc']
        );

        $_POST = [];

        $repoEq->save($newEquipe, TRUE);

        return $this->index('Equipe créée avec succès !');
    }

    #[Route('/create/managers', name: 'app_create_managers')]
    public function indexManagers(string $msg = NULL): Response
    {
        // dump("hhhhhhhh");
        return $this->render('managers/index.html.twig', [
            'title' => 'CREATE TEAM',
            // 'message' => $msg,
        ]);
    }

    #[Route('/create/joueurs', name: 'app_create_joueurs')]
    public function indexJoueurs(string $msg = NULL): Response
    {
        // dump("hhhhhhhh");
        return $this->render('joueurs/index.html.twig', [
            'title' => 'CREATE TEAM',
            // 'message' => $msg,
        ]);
    }

}
