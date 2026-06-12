<?php

namespace App\Models;

/**
 * Représente la fiche technique (recette) associée à un plat.
 */
class Recette
{
    public ?int $id;
    public int $menuId;
    public string $ingredients;
    public int $tempsPreparation;
    public string $instructions;
    public ?string $menuNom;

    public function __construct(
        ?int $id,
        int $menuId,
        string $ingredients,
        int $tempsPreparation,
        string $instructions,
        ?string $menuNom = null
    ) {
        $this->id = $id;
        $this->menuId = $menuId;
        $this->ingredients = $ingredients;
        $this->tempsPreparation = $tempsPreparation;
        $this->instructions = $instructions;
        $this->menuNom = $menuNom;
    }

    public static function depuisTableau(array $ligne): self
    {
        return new self(
            (int) $ligne['id'],
            (int) $ligne['menu_id'],
            $ligne['ingredients'],
            (int) $ligne['temps_preparation'],
            $ligne['instructions'] ?? '',
            $ligne['menu_nom'] ?? null
        );
    }
}
