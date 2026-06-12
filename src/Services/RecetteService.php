<?php

namespace App\Services;

use App\Models\Recette;
use App\Repositories\MenuRepositoryInterface;
use App\Repositories\RecetteRepositoryInterface;

/**
 * Logique métier liée à la gestion des recettes (fiches techniques).
 */
class RecetteService
{
    private RecetteRepositoryInterface $recettes;
    private MenuRepositoryInterface $menus;

    public function __construct(RecetteRepositoryInterface $recettes, MenuRepositoryInterface $menus)
    {
        $this->recettes = $recettes;
        $this->menus = $menus;
    }

    /** @return Recette[] */
    public function toutes(): array
    {
        return $this->recettes->toutes();
    }

    public function trouverParId(int $id): ?Recette
    {
        return $this->recettes->trouverParId($id);
    }

    /** @return \App\Models\Menu[] */
    public function plats(): array
    {
        return $this->menus->tous();
    }

    public function creer(int $menuId, string $ingredients, int $tempsPreparation, string $instructions): void
    {
        $recette = new Recette(null, $menuId, $ingredients, $tempsPreparation, $instructions);
        $this->recettes->creer($recette);
    }

    public function mettreAJour(int $id, int $menuId, string $ingredients, int $tempsPreparation, string $instructions): void
    {
        $recette = new Recette($id, $menuId, $ingredients, $tempsPreparation, $instructions);
        $this->recettes->mettreAJour($recette);
    }

    public function supprimer(int $id): void
    {
        $this->recettes->supprimer($id);
    }
}
