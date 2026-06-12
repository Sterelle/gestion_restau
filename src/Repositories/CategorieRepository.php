<?php

namespace App\Repositories;

use App\Core\Database;
use App\Models\Categorie;

class CategorieRepository implements CategorieRepositoryInterface
{
    private \mysqli $conn;

    public function __construct(Database $database)
    {
        $this->conn = $database->getConnexion();
    }

    public function toutes(): array
    {
        $resultat = $this->conn->query("SELECT * FROM categories ORDER BY nom");
        $categories = [];
        while ($ligne = $resultat->fetch_assoc()) {
            $categories[] = Categorie::depuisTableau($ligne);
        }
        return $categories;
    }
}
