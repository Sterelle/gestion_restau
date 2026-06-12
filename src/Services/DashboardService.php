<?php

namespace App\Services;

use App\Models\Commande;
use App\Models\TableRestaurant;
use App\Repositories\CommandeRepositoryInterface;
use App\Repositories\MenuRepositoryInterface;
use App\Repositories\TableRepositoryInterface;

/**
 * Service du tableau de bord : agrège les statistiques affichées
 * sur la page d'accueil. Dépend uniquement des interfaces des dépôts.
 */
class DashboardService
{
    private CommandeRepositoryInterface $commandes;
    private MenuRepositoryInterface $menus;
    private TableRepositoryInterface $tables;

    public function __construct(
        CommandeRepositoryInterface $commandes,
        MenuRepositoryInterface $menus,
        TableRepositoryInterface $tables
    ) {
        $this->commandes = $commandes;
        $this->menus = $menus;
        $this->tables = $tables;
    }

    /**
     * @return array{
     *     commandes_attente: int,
     *     commandes_preparation: int,
     *     tables_occupees: int,
     *     nb_menus: int,
     *     ca_jour: float,
     *     dernieres_commandes: Commande[]
     * }
     */
    public function statistiques(): array
    {
        return [
            'commandes_attente' => $this->commandes->compterParStatut(Commande::STATUT_EN_ATTENTE),
            'commandes_preparation' => $this->commandes->compterParStatut(Commande::STATUT_EN_PREPARATION),
            'tables_occupees' => $this->compterTablesOccupees(),
            'nb_menus' => count($this->menus->tous()),
            'ca_jour' => $this->commandes->chiffreAffairesDuJour(),
            'dernieres_commandes' => $this->commandes->recentes(5),
        ];
    }

    private function compterTablesOccupees(): int
    {
        $occupees = 0;
        foreach ($this->tables->toutes() as $table) {
            if ($table->statut === TableRestaurant::STATUT_OCCUPEE) {
                $occupees++;
            }
        }
        return $occupees;
    }
}
