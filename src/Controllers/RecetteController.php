<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Core\View;
use App\Services\RecetteService;

class RecetteController extends Controller
{
    private RecetteService $recetteService;

    public function __construct(View $view, Session $session, RecetteService $recetteService)
    {
        parent::__construct($view, $session);
        $this->recetteService = $recetteService;
    }

    public function index(): void
    {
        $this->exigerConnexion();

        $message = "";

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter'])) {
            $this->recetteService->creer(
                (int) $_POST['menu_id'],
                trim($_POST['ingredients']),
                (int) $_POST['temps_preparation'],
                trim($_POST['instructions'])
            );
            $message = "La recette a été ajoutée avec succès.";
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modifier'])) {
            $this->recetteService->mettreAJour(
                (int) $_POST['id'],
                (int) $_POST['menu_id'],
                trim($_POST['ingredients']),
                (int) $_POST['temps_preparation'],
                trim($_POST['instructions'])
            );
            $message = "La recette a été modifiée avec succès.";
        }

        if (isset($_GET['supprimer'])) {
            $this->recetteService->supprimer((int) $_GET['supprimer']);
            $message = "La recette a été supprimée.";
        }

        $recetteAModifier = null;
        if (isset($_GET['modifier'])) {
            $recetteAModifier = $this->recetteService->trouverParId((int) $_GET['modifier']);
        }

        $this->rendre('recettes/index', [
            'message' => $message,
            'recettes' => $this->recetteService->toutes(),
            'plats' => $this->recetteService->plats(),
            'recetteAModifier' => $recetteAModifier,
        ]);
    }
}
