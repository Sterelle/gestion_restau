<?php

namespace App\Repositories;

use App\Core\Database;
use App\Models\Commande;
use App\Models\CommandeLigne;

class CommandeRepository implements CommandeRepositoryInterface
{
    private \mysqli $conn;

    private const SELECT_BASE = "SELECT c.id, c.table_id, c.utilisateur_id, c.statut, c.date_commande, c.total,
                                          t.numero AS table_numero, u.nom AS serveur
                                   FROM commandes c
                                   LEFT JOIN tables_restaurant t ON c.table_id = t.id
                                   LEFT JOIN utilisateurs u ON c.utilisateur_id = u.id";

    public function __construct(Database $database)
    {
        $this->conn = $database->getConnexion();
    }

    public function toutes(): array
    {
        $sql = self::SELECT_BASE . " ORDER BY c.date_commande DESC";
        return $this->hydraterPlusieurs($this->conn->query($sql));
    }

    public function recentes(int $limite): array
    {
        $stmt = $this->conn->prepare(self::SELECT_BASE . " ORDER BY c.date_commande DESC LIMIT ?");
        $stmt->bind_param("i", $limite);
        $stmt->execute();
        $resultat = $this->hydraterPlusieurs($stmt->get_result());
        $stmt->close();

        return $resultat;
    }

    public function trouverParId(int $id): ?Commande
    {
        $stmt = $this->conn->prepare(self::SELECT_BASE . " WHERE c.id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $ligne = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $ligne ? Commande::depuisTableau($ligne) : null;
    }

    public function creer(int $tableId, int $utilisateurId): int
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO commandes (table_id, utilisateur_id, statut, total) VALUES (?, ?, 'en_attente', 0)"
        );
        $stmt->bind_param("ii", $tableId, $utilisateurId);
        $stmt->execute();
        $id = (int) $stmt->insert_id;
        $stmt->close();

        return $id;
    }

    public function changerStatut(int $id, string $statut): bool
    {
        $stmt = $this->conn->prepare("UPDATE commandes SET statut = ? WHERE id = ?");
        $stmt->bind_param("si", $statut, $id);
        $ok = $stmt->execute();
        $stmt->close();

        return $ok;
    }

    public function mettreAJourTotal(int $id, float $total): bool
    {
        $stmt = $this->conn->prepare("UPDATE commandes SET total = ? WHERE id = ?");
        $stmt->bind_param("di", $total, $id);
        $ok = $stmt->execute();
        $stmt->close();

        return $ok;
    }

    public function lignesDeCommande(int $commandeId): array
    {
        $sql = "SELECT cd.*, m.nom AS menu_nom
                FROM commande_details cd
                JOIN menus m ON cd.menu_id = m.id
                WHERE cd.commande_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $commandeId);
        $stmt->execute();
        $resultat = $stmt->get_result();

        $lignes = [];
        while ($ligne = $resultat->fetch_assoc()) {
            $lignes[] = CommandeLigne::depuisTableau($ligne);
        }
        $stmt->close();

        return $lignes;
    }

    public function ajouterLigne(int $commandeId, int $menuId, int $quantite, float $prixUnitaire): int
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO commande_details (commande_id, menu_id, quantite, prix_unitaire) VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param("iiid", $commandeId, $menuId, $quantite, $prixUnitaire);
        $stmt->execute();
        $id = (int) $stmt->insert_id;
        $stmt->close();

        return $id;
    }

    public function supprimerLigne(int $ligneId, int $commandeId): bool
    {
        $stmt = $this->conn->prepare("DELETE FROM commande_details WHERE id = ? AND commande_id = ?");
        $stmt->bind_param("ii", $ligneId, $commandeId);
        $ok = $stmt->execute();
        $stmt->close();

        return $ok;
    }

    public function totalDeCommande(int $commandeId): float
    {
        $stmt = $this->conn->prepare(
            "SELECT COALESCE(SUM(quantite * prix_unitaire),0) AS total FROM commande_details WHERE commande_id = ?"
        );
        $stmt->bind_param("i", $commandeId);
        $stmt->execute();
        $total = (float) $stmt->get_result()->fetch_assoc()['total'];
        $stmt->close();

        return $total;
    }

    public function compterParStatut(string $statut): int
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) AS total FROM commandes WHERE statut = ?");
        $stmt->bind_param("s", $statut);
        $stmt->execute();
        $total = (int) $stmt->get_result()->fetch_assoc()['total'];
        $stmt->close();

        return $total;
    }

    public function chiffreAffairesDuJour(): float
    {
        $resultat = $this->conn->query(
            "SELECT COALESCE(SUM(total),0) AS somme FROM commandes WHERE statut = 'payee' AND DATE(date_commande) = CURDATE()"
        );
        return (float) $resultat->fetch_assoc()['somme'];
    }

    private function hydraterPlusieurs(\mysqli_result $resultat): array
    {
        $commandes = [];
        while ($ligne = $resultat->fetch_assoc()) {
            $commandes[] = Commande::depuisTableau($ligne);
        }
        return $commandes;
    }
}
