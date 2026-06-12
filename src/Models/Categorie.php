<?php

namespace App\Models;

/**
 * Représente une catégorie de menu (Entrées, Plats principaux, etc.)
 */
class Categorie
{
    public ?int $id;
    public string $nom;

    public function __construct(?int $id, string $nom)
    {
        $this->id = $id;
        $this->nom = $nom;
    }

    public static function depuisTableau(array $ligne): self
    {
        return new self((int) $ligne['id'], $ligne['nom']);
    }
}
