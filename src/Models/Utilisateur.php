<?php

namespace App\Models;

/**
 * Représente un utilisateur de l'application (admin, manager, serveur, cuisinier).
 *
 * Un Modèle est un simple objet de données (entité). Il ne contient
 * aucune logique de persistance : celle-ci est déléguée au Repository
 * (séparation des responsabilités).
 */
class Utilisateur
{
    public ?int $id;
    public string $nom;
    public string $email;
    public string $motDePasse;
    public string $role;
    public ?string $dateCreation;

    public function __construct(
        ?int $id,
        string $nom,
        string $email,
        string $motDePasse,
        string $role,
        ?string $dateCreation = null
    ) {
        $this->id = $id;
        $this->nom = $nom;
        $this->email = $email;
        $this->motDePasse = $motDePasse;
        $this->role = $role;
        $this->dateCreation = $dateCreation;
    }

    public static function depuisTableau(array $ligne): self
    {
        return new self(
            (int) $ligne['id'],
            $ligne['nom'],
            $ligne['email'],
            $ligne['mot_de_passe'],
            $ligne['role'],
            $ligne['date_creation'] ?? null
        );
    }

    public function estAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function peutGererUtilisateurs(): bool
    {
        return in_array($this->role, ['admin', 'manager'], true);
    }
}
