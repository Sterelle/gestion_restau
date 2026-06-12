<?php

namespace App\Repositories;

use App\Models\Utilisateur;

/**
 * Contrat de persistance pour les utilisateurs.
 *
 * Principe d'inversion des dépendances (DIP) : les Services dépendent
 * de cette interface, pas d'une implémentation MySQL concrète.
 * On pourrait fournir une implémentation alternative (ex : tests,
 * autre SGBD) sans modifier le code appelant.
 */
interface UtilisateurRepositoryInterface
{
    public function trouverParEmail(string $email): ?Utilisateur;

    public function trouverParId(int $id): ?Utilisateur;

    /** @return Utilisateur[] */
    public function tousLesUtilisateurs(): array;

    public function creer(Utilisateur $utilisateur): int;

    public function mettreAJour(Utilisateur $utilisateur, bool $changerMotDePasse): bool;

    public function supprimer(int $id): bool;
}
