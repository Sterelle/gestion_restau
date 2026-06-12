<?php

namespace App\Repositories;

use App\Core\Database;
use App\Models\TableRestaurant;

class TableRepository implements TableRepositoryInterface
{
    private \mysqli $conn;

    public function __construct(Database $database)
    {
        $this->conn = $database->getConnexion();
    }

    public function toutes(): array
    {
        $resultat = $this->conn->query("SELECT * FROM tables_restaurant ORDER BY numero");
        $tables = [];
        while ($ligne = $resultat->fetch_assoc()) {
            $tables[] = TableRestaurant::depuisTableau($ligne);
        }
        return $tables;
    }

    public function libres(): array
    {
        $resultat = $this->conn->query("SELECT * FROM tables_restaurant WHERE statut = 'libre' ORDER BY numero");
        $tables = [];
        while ($ligne = $resultat->fetch_assoc()) {
            $tables[] = TableRestaurant::depuisTableau($ligne);
        }
        return $tables;
    }

    public function trouverParId(int $id): ?TableRestaurant
    {
        $stmt = $this->conn->prepare("SELECT * FROM tables_restaurant WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $ligne = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $ligne ? TableRestaurant::depuisTableau($ligne) : null;
    }

    public function creer(TableRestaurant $table): int
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO tables_restaurant (numero, capacite, statut) VALUES (?, ?, 'libre')"
        );
        $stmt->bind_param("ii", $table->numero, $table->capacite);
        $stmt->execute();
        $id = (int) $stmt->insert_id;
        $stmt->close();

        return $id;
    }

    public function changerStatut(int $id, string $statut): bool
    {
        $stmt = $this->conn->prepare("UPDATE tables_restaurant SET statut = ? WHERE id = ?");
        $stmt->bind_param("si", $statut, $id);
        $ok = $stmt->execute();
        $stmt->close();

        return $ok;
    }

    public function supprimer(int $id): bool
    {
        $stmt = $this->conn->prepare("DELETE FROM tables_restaurant WHERE id = ?");
        $stmt->bind_param("i", $id);
        $ok = $stmt->execute();
        $stmt->close();

        return $ok;
    }
}
