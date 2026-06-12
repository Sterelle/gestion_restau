<?php

namespace App\Repositories;

use App\Models\Commande;
use App\Models\CommandeLigne;

interface CommandeRepositoryInterface
{
    /** @return Commande[] */
    public function toutes(): array;

    /** @return Commande[] Les 'n' commandes les plus récentes */
    public function recentes(int $limite): array;

    public function trouverParId(int $id): ?Commande;

    public function creer(int $tableId, int $utilisateurId): int;

    public function changerStatut(int $id, string $statut): bool;

    public function mettreAJourTotal(int $id, float $total): bool;

    /** @return CommandeLigne[] */
    public function lignesDeCommande(int $commandeId): array;

    public function ajouterLigne(int $commandeId, int $menuId, int $quantite, float $prixUnitaire): int;

    public function supprimerLigne(int $ligneId, int $commandeId): bool;

    public function totalDeCommande(int $commandeId): float;

    /** Statistiques pour le tableau de bord */
    public function compterParStatut(string $statut): int;

    public function chiffreAffairesDuJour(): float;
}
