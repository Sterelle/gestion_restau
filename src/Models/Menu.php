<?php

namespace App\Models;

/**
 * Représente un plat / article du menu.
 */
class Menu
{
    public ?int $id;
    public string $nom;
    public string $description;
    public float $prix;
    public ?int $categorieId;
    public bool $disponible;
    public ?string $categorieNom;

    public function __construct(
        ?int $id,
        string $nom,
        string $description,
        float $prix,
        ?int $categorieId,
        bool $disponible,
        ?string $categorieNom = null
    ) {
        $this->id = $id;
        $this->nom = $nom;
        $this->description = $description;
        $this->prix = $prix;
        $this->categorieId = $categorieId;
        $this->disponible = $disponible;
        $this->categorieNom = $categorieNom;
    }

    public static function depuisTableau(array $ligne): self
    {
        return new self(
            (int) $ligne['id'],
            $ligne['nom'],
            $ligne['description'] ?? '',
            (float) $ligne['prix'],
            isset($ligne['categorie_id']) ? (int) $ligne['categorie_id'] : null,
            (bool) $ligne['disponible'],
            $ligne['categorie_nom'] ?? null
        );
    }

    public function prixFormate(): string
    {
        return number_format($this->prix, 0, ',', ' ') . ' FCFA';
    }
}
