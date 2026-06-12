<?php

namespace App\Repositories;

use App\Core\Database;
use App\Models\Recette;

class RecetteRepository implements RecetteRepositoryInterface
{
    private \mysqli $conn;

    public function __construct(Database $database)
    {
        $this->conn = $database->getConnexion();
    }

    public function toutes(): array
    {
        $sql = "SELECT r.*, m.nom AS menu_nom
                FROM recettes r
                JOIN menus m ON r.menu_id = m.id
                ORDER BY r.id DESC";
        $resultat = $this->conn->query($sql);

        $recettes = [];
        while ($ligne = $resultat->fetch_assoc()) {
            $recettes[] = Recette::depuisTableau($ligne);
        }
        return $recettes;
    }

    public function trouverParId(int $id): ?Recette
    {
        $stmt = $this->conn->prepare("SELECT * FROM recettes WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $ligne = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $ligne ? Recette::depuisTableau($ligne) : null;
    }

    public function creer(Recette $recette): int
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO recettes (menu_id, ingredients, temps_preparation, instructions) VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param(
            "isis",
            $recette->menuId,
            $recette->ingredients,
            $recette->tempsPreparation,
            $recette->instructions
        );
        $stmt->execute();
        $id = (int) $stmt->insert_id;
        $stmt->close();

        return $id;
    }

    public function mettreAJour(Recette $recette): bool
    {
        $stmt = $this->conn->prepare(
            "UPDATE recettes SET menu_id=?, ingredients=?, temps_preparation=?, instructions=? WHERE id=?"
        );
        $stmt->bind_param(
            "isisi",
            $recette->menuId,
            $recette->ingredients,
            $recette->tempsPreparation,
            $recette->instructions,
            $recette->id
        );
        $ok = $stmt->execute();
        $stmt->close();

        return $ok;
    }

    public function supprimer(int $id): bool
    {
        $stmt = $this->conn->prepare("DELETE FROM recettes WHERE id = ?");
        $stmt->bind_param("i", $id);
        $ok = $stmt->execute();
        $stmt->close();

        return $ok;
    }
}
