<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Core\View;
use App\Services\UtilisateurService;

class UtilisateurController extends Controller
{
    private UtilisateurService $utilisateurService;

    public function __construct(View $view, Session $session, UtilisateurService $utilisateurService)
    {
        parent::__construct($view, $session);
        $this->utilisateurService = $utilisateurService;
    }

    public function index(): void
    {
        $this->exigerConnexion();

        // Seul l'administrateur peut gérer les utilisateurs
        if (!$this->exigerRole(['admin'])) {
            $this->rendre('utilisateurs/acces_refuse', []);
            return;
        }

        $message = "";

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter'])) {
            $erreur = $this->utilisateurService->creer(
                trim($_POST['nom']),
                trim($_POST['email']),
                $_POST['mot_de_passe'],
                $_POST['role']
            );
            $message = $erreur ?? "L'utilisateur a été ajouté avec succès.";
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modifier'])) {
            $this->utilisateurService->mettreAJour(
                (int) $_POST['id'],
                trim($_POST['nom']),
                trim($_POST['email']),
                $_POST['role'],
                $_POST['mot_de_passe'] !== '' ? $_POST['mot_de_passe'] : null
            );
            $message = "L'utilisateur a été modifié avec succès.";
        }

        if (isset($_GET['supprimer'])) {
            $erreur = $this->utilisateurService->supprimer(
                (int) $_GET['supprimer'],
                (int) $this->session->get('user_id')
            );
            $message = $erreur ?? "L'utilisateur a été supprimé.";
        }

        $utilisateurAModifier = null;
        if (isset($_GET['modifier'])) {
            $utilisateurAModifier = $this->utilisateurService->trouverParId((int) $_GET['modifier']);
        }

        $this->rendre('utilisateurs/index', [
            'message' => $message,
            'utilisateurs' => $this->utilisateurService->tous(),
            'roles' => UtilisateurService::ROLES_POSSIBLES,
            'utilisateurAModifier' => $utilisateurAModifier,
        ]);
    }
}
