<?php

namespace App\Models;

/**
 * Représente une table physique du restaurant.
 */
class TableRestaurant
{
    public const STATUT_LIBRE = 'libre';
    public const STATUT_OCCUPEE = 'occupee';
    public const STATUT_RESERVEE = 'reservee';

    public ?int $id;
    public int $numero;
    public int $capacite;
    public string $statut;

    public function __construct(?int $id, int $numero, int $capacite, string $statut)
    {
        $this->id = $id;
        $this->numero = $numero;
        $this->capacite = $capacite;
        $this->statut = $statut;
    }

    public static function depuisTableau(array $ligne): self
    {
        return new self(
            (int) $ligne['id'],
            (int) $ligne['numero'],
            (int) $ligne['capacite'],
            $ligne['statut']
        );
    }
}
