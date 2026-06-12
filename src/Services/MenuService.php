<?php

namespace App\Services;

use App\Models\Menu;
use App\Repositories\CategorieRepositoryInterface;
use App\Repositories\MenuRepositoryInterface;

/**
 * Logique métier liée à la gestion des plats du menu.
 */
class MenuService
{
    private MenuRepositoryInterface $menus;
    private CategorieRepositoryInterface $categories;

    public function __construct(MenuRepositoryInterface $menus, CategorieRepositoryInterface $categories)
    {
        $this->menus = $menus;
        $this->categories = $categories;
    }

    /** @return Menu[] */
    public function tous(): array
    {
        return $this->menus->tous();
    }

    public function trouverParId(int $id): ?Menu
    {
        return $this->menus->trouverParId($id);
    }

    /** @return \App\Models\Categorie[] */
    public function categories(): array
    {
        return $this->categories->toutes();
    }

    public function creer(string $nom, string $description, float $prix, int $categorieId, bool $disponible): void
    {
        $menu = new Menu(null, $nom, $description, $prix, $categorieId, $disponible);
        $this->menus->creer($menu);
    }

    public function mettreAJour(int $id, string $nom, string $description, float $prix, int $categorieId, bool $disponible): void
    {
        $menu = new Menu($id, $nom, $description, $prix, $categorieId, $disponible);
        $this->menus->mettreAJour($menu);
    }

    public function supprimer(int $id): void
    {
        $this->menus->supprimer($id);
    }
}
