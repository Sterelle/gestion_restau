<?php

namespace App\Services;

use App\Models\Utilisateur;
use App\Repositories\UtilisateurRepositoryInterface;

/**
 * Logique métier liée à la gestion des comptes utilisateurs.
 *
 * Le hachage des mots de passe est centralisé ici (SRP) au lieu
 * d'être répété dans plusieurs contrôleurs.
 */
class UtilisateurService
{
    public const ROLES_POSSIBLES = ['admin', 'manager', 'serveur', 'cuisinier'];

    private UtilisateurRepositoryInterface $utilisateurs;

    public function __construct(UtilisateurRepositoryInterface $utilisateurs)
    {
        $this->utilisateurs = $utilisateurs;
    }

    /** @return Utilisateur[] */
    public function tous(): array
    {
        return $this->utilisateurs->tousLesUtilisateurs();
    }

    public function trouverParId(int $id): ?Utilisateur
    {
        return $this->utilisateurs->trouverParId($id);
    }

    /**
     * Crée un nouvel utilisateur. Retourne un message d'erreur ou null si succès.
     */
    public function creer(string $nom, string $email, string $motDePasse, string $role): ?string
    {
        $hash = password_hash($motDePasse, PASSWORD_DEFAULT);
        $utilisateur = new Utilisateur(null, $nom, $email, $hash, $role);

        if (!$this->utilisateurs->creer($utilisateur)) {
            return "Erreur : cet email est peut-être déjà utilisé.";
        }

        return null;
    }

    public function mettreAJour(int $id, string $nom, string $email, string $role, ?string $nouveauMotDePasse): void
    {
        $changerMotDePasse = !empty($nouveauMotDePasse);
        $hash = $changerMotDePasse ? password_hash($nouveauMotDePasse, PASSWORD_DEFAULT) : '';

        $utilisateur = new Utilisateur($id, $nom, $email, $hash, $role);
        $this->utilisateurs->mettreAJour($utilisateur, $changerMotDePasse);
    }

    /**
     * Supprime un utilisateur, sauf s'il s'agit du compte actuellement connecté.
     * Retourne un message d'erreur ou null si succès.
     */
    public function supprimer(int $id, int $utilisateurConnecteId): ?string
    {
        if ($id === $utilisateurConnecteId) {
            return "Vous ne pouvez pas supprimer votre propre compte.";
        }

        $this->utilisateurs->supprimer($id);

        return null;
    }
}
