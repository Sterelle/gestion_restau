<?php

namespace App\Repositories;

use App\Models\Menu;

interface MenuRepositoryInterface
{
    /** @return Menu[] */
    public function tous(): array;

    /** @return Menu[] */
    public function disponibles(): array;

    public function trouverParId(int $id): ?Menu;

    public function creer(Menu $menu): int;

    public function mettreAJour(Menu $menu): bool;

    public function supprimer(int $id): bool;
}
