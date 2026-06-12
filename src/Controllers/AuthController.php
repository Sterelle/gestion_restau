<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Core\View;
use App\Services\AuthService;

/**
 * Contrôleur dédié à l'authentification.
 */
class AuthController extends Controller
{
    private AuthService $authService;

    public function __construct(View $view, Session $session, AuthService $authService)
    {
        parent::__construct($view, $session);
        $this->authService = $authService;
    }

    public function connexion(): void
    {
        if ($this->session->estConnecte()) {
            $this->rediriger('index.php');
        }

        $erreur = "";

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $motDePasse = $_POST['mot_de_passe'] ?? '';

            $erreur = $this->authService->connecter($email, $motDePasse);

            if ($erreur === null) {
                $this->rediriger('index.php');
            }
        }

        $this->rendreSeul('auth/connexion', ['erreur' => $erreur]);
    }

    public function deconnexion(): void
    {
        $this->authService->deconnecter();
        $this->rediriger('index.php?page=connexion');
    }
}
