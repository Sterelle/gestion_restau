<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Core\View;
use App\Models\Commande;
use App\Services\CommandeService;

/**
 * Contrôleur de gestion des commandes (liste, création, changement de statut).
 */
class CommandeController extends Controller
{
    private CommandeService $commandeService;

    public function __construct(View $view, Session $session, CommandeService $commandeService)
    {
        parent::__construct($view, $session);
        $this->commandeService = $commandeService;
    }

    public function index(): void
    {
        $this->exigerConnexion();

        $message = "";

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['creer_commande'])) {
            $tableId = (int) $_POST['table_id'];
            $utilisateurId = (int) $this->session->get('user_id');

            $commandeId = $this->commandeService->creerCommande($tableId, $utilisateurId);

            $this->rediriger("index.php?page=commande_details&id=$commandeId");
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['changer_statut'])) {
            $id = (int) $_POST['id'];
            $statut = $_POST['statut'];

            $this->commandeService->changerStatut($id, $statut);

            $message = "Le statut de la commande #$id a été mis à jour.";
        }

        $this->rendre('commandes/index', [
            'message' => $message,
            'commandes' => $this->commandeService->toutes(),
            'tablesLibres' => $this->commandeService->tablesLibres(),
            'statuts' => Commande::STATUTS,
        ]);
    }

    public function details(): void
    {
        $this->exigerConnexion();

        if (!isset($_GET['id'])) {
            $this->rediriger('index.php?page=commandes');
        }

        $commandeId = (int) $_GET['id'];
        $message = "";

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_plat'])) {
            $menuId = (int) $_POST['menu_id'];
            $quantite = (int) $_POST['quantite'];

            $this->commandeService->ajouterPlat($commandeId, $menuId, $quantite);
            $message = "Le plat a été ajouté à la commande.";
        }

        if (isset($_GET['supprimer_ligne'])) {
            $ligneId = (int) $_GET['supprimer_ligne'];
            $this->commandeService->retirerLigne($ligneId, $commandeId);
            $message = "L'article a été retiré de la commande.";
        }

        $total = $this->commandeService->recalculerTotal($commandeId);
        $commande = $this->commandeService->trouverParId($commandeId);

        if ($commande === null) {
            $this->rendre('commandes/introuvable', []);
            return;
        }

        $this->rendre('commandes/details', [
            'message' => $message,
            'commande' => $commande,
            'total' => $total,
            'lignes' => $this->commandeService->lignes($commandeId),
            'plats' => $this->commandeService->platsDisponibles(),
        ]);
    }
}
