<?php

namespace App\Core;

use mysqli;

/**
 * Encapsule la connexion mysqli.
 *
 * Respecte le SRP : cette classe a la seule responsabilité de fournir
 * une connexion à la base de données. Elle est injectée dans les
 * Repositories plutôt que d'être appelée via une variable globale.
 */
class Database
{
    private mysqli $connexion;

    public function __construct(string $host, string $utilisateur, string $motDePasse, string $base)
    {
        $this->connexion = new mysqli($host, $utilisateur, $motDePasse, $base);

        if ($this->connexion->connect_error) {
            throw new \RuntimeException("Erreur de connexion à la base de données : " . $this->connexion->connect_error);
        }

        $this->connexion->set_charset("utf8mb4");
    }

    public function getConnexion(): mysqli
    {
        return $this->connexion;
    }
}
