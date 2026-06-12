<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Core\View;
use App\Services\MenuService;

class MenuController extends Controller
{
    private MenuService $menuService;

    public function __construct(View $view, Session $session, MenuService $menuService)
    {
        parent::__construct($view, $session);
        $this->menuService = $menuService;
    }

    public function index(): void
    {
        $this->exigerConnexion();

        $message = "";

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter'])) {
            $this->menuService->creer(
                trim($_POST['nom']),
                trim($_POST['description']),
                (float) $_POST['prix'],
                (int) $_POST['categorie_id'],
                isset($_POST['disponible'])
            );
            $message = "Le plat a été ajouté avec succès.";
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modifier'])) {
            $this->menuService->mettreAJour(
                (int) $_POST['id'],
                trim($_POST['nom']),
                trim($_POST['description']),
                (float) $_POST['prix'],
                (int) $_POST['categorie_id'],
                isset($_POST['disponible'])
            );
            $message = "Le plat a été modifié avec succès.";
        }

        if (isset($_GET['supprimer'])) {
            $this->menuService->supprimer((int) $_GET['supprimer']);
            $message = "Le plat a été supprimé.";
        }

        $menuAModifier = null;
        if (isset($_GET['modifier'])) {
            $menuAModifier = $this->menuService->trouverParId((int) $_GET['modifier']);
        }

        $this->rendre('menus/index', [
            'message' => $message,
            'menus' => $this->menuService->tous(),
            'categories' => $this->menuService->categories(),
            'menuAModifier' => $menuAModifier,
        ]);
    }
}
