<?php

namespace App\Repositories;

use App\Models\Recette;

interface RecetteRepositoryInterface
{
    /** @return Recette[] */
    public function toutes(): array;

    public function trouverParId(int $id): ?Recette;

    public function creer(Recette $recette): int;

    public function mettreAJour(Recette $recette): bool;

    public function supprimer(int $id): bool;
}
