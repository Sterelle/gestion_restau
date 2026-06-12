<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Core\View;
use App\Services\TableService;

class TableController extends Controller
{
    private TableService $tableService;

    public function __construct(View $view, Session $session, TableService $tableService)
    {
        parent::__construct($view, $session);
        $this->tableService = $tableService;
    }

    public function index(): void
    {
        $this->exigerConnexion();

        $message = "";

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter'])) {
            $this->tableService->ajouter((int) $_POST['numero'], (int) $_POST['capacite']);
            $message = "La table a été ajoutée avec succès.";
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['changer_statut'])) {
            $this->tableService->changerStatut((int) $_POST['id'], $_POST['statut']);
            $message = "Le statut de la table a été mis à jour.";
        }

        if (isset($_GET['supprimer'])) {
            $this->tableService->supprimer((int) $_GET['supprimer']);
            $message = "La table a été supprimée.";
        }

        $this->rendre('tables/index', [
            'message' => $message,
            'tables' => $this->tableService->toutes(),
            'statutsPossibles' => TableService::STATUTS_POSSIBLES,
        ]);
    }
}
