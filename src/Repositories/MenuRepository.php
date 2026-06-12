<?php

namespace App\Repositories;

use App\Core\Database;
use App\Models\Menu;

class MenuRepository implements MenuRepositoryInterface
{
    private \mysqli $conn;

    public function __construct(Database $database)
    {
        $this->conn = $database->getConnexion();
    }

    public function tous(): array
    {
        $sql = "SELECT m.*, c.nom AS categorie_nom
                FROM menus m
                LEFT JOIN categories c ON m.categorie_id = c.id
                ORDER BY m.id DESC";
        $resultat = $this->conn->query($sql);

        $menus = [];
        while ($ligne = $resultat->fetch_assoc()) {
            $menus[] = Menu::depuisTableau($ligne);
        }
        return $menus;
    }

    public function disponibles(): array
    {
        $resultat = $this->conn->query("SELECT id, nom, prix, description, categorie_id, disponible FROM menus WHERE disponible = 1 ORDER BY nom");
        $menus = [];
        while ($ligne = $resultat->fetch_assoc()) {
            $menus[] = Menu::depuisTableau($ligne);
        }
        return $menus;
    }

    public function trouverParId(int $id): ?Menu
    {
        $stmt = $this->conn->prepare("SELECT * FROM menus WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $ligne = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $ligne ? Menu::depuisTableau($ligne) : null;
    }

    public function creer(Menu $menu): int
    {
        $disponible = $menu->disponible ? 1 : 0;
        $stmt = $this->conn->prepare(
            "INSERT INTO menus (nom, description, prix, categorie_id, disponible) VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->bind_param(
            "ssdii",
            $menu->nom,
            $menu->description,
            $menu->prix,
            $menu->categorieId,
            $disponible
        );
        $stmt->execute();
        $id = (int) $stmt->insert_id;
        $stmt->close();

        return $id;
    }

    public function mettreAJour(Menu $menu): bool
    {
        $disponible = $menu->disponible ? 1 : 0;
        $stmt = $this->conn->prepare(
            "UPDATE menus SET nom=?, description=?, prix=?, categorie_id=?, disponible=? WHERE id=?"
        );
        $stmt->bind_param(
            "ssdiii",
            $menu->nom,
            $menu->description,
            $menu->prix,
            $menu->categorieId,
            $disponible,
            $menu->id
        );
        $ok = $stmt->execute();
        $stmt->close();

        return $ok;
    }

    public function supprimer(int $id): bool
    {
        $stmt = $this->conn->prepare("DELETE FROM menus WHERE id = ?");
        $stmt->bind_param("i", $id);
        $ok = $stmt->execute();
        $stmt->close();

        return $ok;
    }
}
