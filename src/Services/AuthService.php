<?php

namespace App\Services;

use App\Core\Session;
use App\Models\Utilisateur;
use App\Repositories\UtilisateurRepositoryInterface;

/**
 * Service d'authentification.
 *
 * SRP : centralise la logique de connexion / déconnexion, indépendamment
 * de la manière dont les utilisateurs sont stockés (UtilisateurRepositoryInterface)
 * ou dont la session est gérée (Session).
 */
class AuthService
{
    private UtilisateurRepositoryInterface $utilisateurs;
    private Session $session;

    public function __construct(UtilisateurRepositoryInterface $utilisateurs, Session $session)
    {
        $this->utilisateurs = $utilisateurs;
        $this->session = $session;
    }

    /**
     * Tente une connexion. Retourne null en cas de succès, ou un message d'erreur.
     */
    public function connecter(string $email, string $motDePasse): ?string
    {
        $utilisateur = $this->utilisateurs->trouverParEmail($email);

        if ($utilisateur === null) {
            return "Aucun utilisateur trouvé avec cet email.";
        }

        if (!password_verify($motDePasse, $utilisateur->motDePasse)) {
            return "Mot de passe incorrect.";
        }

        $this->ouvrirSession($utilisateur);

        return null;
    }

    public function deconnecter(): void
    {
        $this->session->destroy();
    }

    private function ouvrirSession(Utilisateur $utilisateur): void
    {
        $this->session->set('user_id', $utilisateur->id);
        $this->session->set('nom', $utilisateur->nom);
        $this->session->set('role', $utilisateur->role);
    }
}
