<?php

namespace App\Repositories;

use App\Core\Database;
use App\Models\Utilisateur;

/**
 * Implémentation MySQL du dépôt des utilisateurs.
 *
 * SRP : cette classe ne fait que dialoguer avec la base de données
 * et convertir les lignes en objets Utilisateur.
 */
class UtilisateurRepository implements UtilisateurRepositoryInterface
{
    private \mysqli $conn;

    public function __construct(Database $database)
    {
        $this->conn = $database->getConnexion();
    }

    public function trouverParEmail(string $email): ?Utilisateur
    {
        $stmt = $this->conn->prepare("SELECT * FROM utilisateurs WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultat = $stmt->get_result();
        $ligne = $resultat->fetch_assoc();
        $stmt->close();

        return $ligne ? Utilisateur::depuisTableau($ligne) : null;
    }

    public function trouverParId(int $id): ?Utilisateur
    {
        $stmt = $this->conn->prepare("SELECT * FROM utilisateurs WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $ligne = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $ligne ? Utilisateur::depuisTableau($ligne) : null;
    }

    public function tousLesUtilisateurs(): array
    {
        $resultat = $this->conn->query("SELECT * FROM utilisateurs ORDER BY id");
        $utilisateurs = [];
        while ($ligne = $resultat->fetch_assoc()) {
            $utilisateurs[] = Utilisateur::depuisTableau($ligne);
        }
        return $utilisateurs;
    }

    public function creer(Utilisateur $utilisateur): int
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO utilisateurs (nom, email, mot_de_passe, role) VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param(
            "ssss",
            $utilisateur->nom,
            $utilisateur->email,
            $utilisateur->motDePasse,
            $utilisateur->role
        );
        $stmt->execute();
        $id = (int) $stmt->insert_id;
        $stmt->close();

        return $id;
    }

    public function mettreAJour(Utilisateur $utilisateur, bool $changerMotDePasse): bool
    {
        if ($changerMotDePasse) {
            $stmt = $this->conn->prepare(
                "UPDATE utilisateurs SET nom=?, email=?, role=?, mot_de_passe=? WHERE id=?"
            );
            $stmt->bind_param(
                "ssssi",
                $utilisateur->nom,
                $utilisateur->email,
                $utilisateur->role,
                $utilisateur->motDePasse,
                $utilisateur->id
            );
        } else {
            $stmt = $this->conn->prepare(
                "UPDATE utilisateurs SET nom=?, email=?, role=? WHERE id=?"
            );
            $stmt->bind_param(
                "sssi",
                $utilisateur->nom,
                $utilisateur->email,
                $utilisateur->role,
                $utilisateur->id
            );
        }

        $ok = $stmt->execute();
        $stmt->close();

        return $ok;
    }

    public function supprimer(int $id): bool
    {
        $stmt = $this->conn->prepare("DELETE FROM utilisateurs WHERE id = ?");
        $stmt->bind_param("i", $id);
        $ok = $stmt->execute();
        $stmt->close();

        return $ok;
    }
}
