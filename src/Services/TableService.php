<?php

namespace App\Services;

use App\Models\TableRestaurant;
use App\Repositories\TableRepositoryInterface;

/**
 * Logique métier liée à la gestion des tables du restaurant.
 */
class TableService
{
    public const STATUTS_POSSIBLES = [
        TableRestaurant::STATUT_LIBRE,
        TableRestaurant::STATUT_OCCUPEE,
        TableRestaurant::STATUT_RESERVEE,
    ];

    private TableRepositoryInterface $tables;

    public function __construct(TableRepositoryInterface $tables)
    {
        $this->tables = $tables;
    }

    /** @return TableRestaurant[] */
    public function toutes(): array
    {
        return $this->tables->toutes();
    }

    public function ajouter(int $numero, int $capacite): void
    {
        $table = new TableRestaurant(null, $numero, $capacite, TableRestaurant::STATUT_LIBRE);
        $this->tables->creer($table);
    }

    public function changerStatut(int $id, string $statut): void
    {
        $this->tables->changerStatut($id, $statut);
    }

    public function supprimer(int $id): void
    {
        $this->tables->supprimer($id);
    }
}
