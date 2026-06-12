<?php

namespace App\Services;

use App\Models\Commande;
use App\Models\TableRestaurant;
use App\Repositories\CommandeRepositoryInterface;
use App\Repositories\MenuRepositoryInterface;
use App\Repositories\TableRepositoryInterface;

/**
 * Logique métier liée aux commandes : création, ajout/retrait de plats,
 * recalcul du total et synchronisation du statut des tables.
 *
 * En regroupant ces règles ici (plutôt que dans les contrôleurs ou les
 * vues comme dans la version originale), on respecte le SRP et on évite
 * la duplication entre commandes.php et commande_details.php.
 */
class CommandeService
{
    private CommandeRepositoryInterface $commandes;
    private TableRepositoryInterface $tables;
    private MenuRepositoryInterface $menus;

    public function __construct(
        CommandeRepositoryInterface $commandes,
        TableRepositoryInterface $tables,
        MenuRepositoryInterface $menus
    ) {
        $this->commandes = $commandes;
        $this->tables = $tables;
        $this->menus = $menus;
    }

    /**
     * Crée une nouvelle commande pour une table et marque la table occupée.
     */
    public function creerCommande(int $tableId, int $utilisateurId): int
    {
        $commandeId = $this->commandes->creer($tableId, $utilisateurId);
        $this->tables->changerStatut($tableId, TableRestaurant::STATUT_OCCUPEE);

        return $commandeId;
    }

    /**
     * Change le statut d'une commande et libère la table si nécessaire.
     */
    public function changerStatut(int $commandeId, string $nouveauStatut): void
    {
        $this->commandes->changerStatut($commandeId, $nouveauStatut);

        $commande = $this->commandes->trouverParId($commandeId);

        if ($commande !== null && $commande->libereLaTable() && $commande->tableId !== null) {
            $this->tables->changerStatut($commande->tableId, TableRestaurant::STATUT_LIBRE);
        }
    }

    /**
     * Ajoute un plat à une commande au prix courant du menu, puis recalcule le total.
     */
    public function ajouterPlat(int $commandeId, int $menuId, int $quantite): void
    {
        $menu = $this->menus->trouverParId($menuId);

        if ($menu === null) {
            throw new \InvalidArgumentException("Plat introuvable.");
        }

        $this->commandes->ajouterLigne($commandeId, $menuId, $quantite, $menu->prix);
        $this->recalculerTotal($commandeId);
    }

    /**
     * Retire une ligne de la commande puis recalcule le total.
     */
    public function retirerLigne(int $ligneId, int $commandeId): void
    {
        $this->commandes->supprimerLigne($ligneId, $commandeId);
        $this->recalculerTotal($commandeId);
    }

    /**
     * Recalcule et persiste le total d'une commande à partir de ses lignes.
     */
    public function recalculerTotal(int $commandeId): float
    {
        $total = $this->commandes->totalDeCommande($commandeId);
        $this->commandes->mettreAJourTotal($commandeId, $total);

        return $total;
    }

    public function trouverParId(int $id): ?Commande
    {
        return $this->commandes->trouverParId($id);
    }

    /** @return Commande[] */
    public function toutes(): array
    {
        return $this->commandes->toutes();
    }

    /** @return \App\Models\CommandeLigne[] */
    public function lignes(int $commandeId): array
    {
        return $this->commandes->lignesDeCommande($commandeId);
    }

    /** @return \App\Models\TableRestaurant[] */
    public function tablesLibres(): array
    {
        return $this->tables->libres();
    }

    /** @return \App\Models\Menu[] */
    public function platsDisponibles(): array
    {
        return $this->menus->disponibles();
    }
}
