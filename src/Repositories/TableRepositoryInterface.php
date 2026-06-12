<?php

namespace App\Repositories;

use App\Models\TableRestaurant;

interface TableRepositoryInterface
{
    /** @return TableRestaurant[] */
    public function toutes(): array;

    /** @return TableRestaurant[] */
    public function libres(): array;

    public function trouverParId(int $id): ?TableRestaurant;

    public function creer(TableRestaurant $table): int;

    public function changerStatut(int $id, string $statut): bool;

    public function supprimer(int $id): bool;
}
