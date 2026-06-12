<?php

namespace App\Models;

/**
 * Représente une ligne d'une commande (un plat + quantité).
 */
class CommandeLigne
{
    public ?int $id;
    public int $commandeId;
    public int $menuId;
    public int $quantite;
    public float $prixUnitaire;
    public ?string $menuNom;

    public function __construct(
        ?int $id,
        int $commandeId,
        int $menuId,
        int $quantite,
        float $prixUnitaire,
        ?string $menuNom = null
    ) {
        $this->id = $id;
        $this->commandeId = $commandeId;
        $this->menuId = $menuId;
        $this->quantite = $quantite;
        $this->prixUnitaire = $prixUnitaire;
        $this->menuNom = $menuNom;
    }

    public static function depuisTableau(array $ligne): self
    {
        return new self(
            (int) $ligne['id'],
            (int) $ligne['commande_id'],
            (int) $ligne['menu_id'],
            (int) $ligne['quantite'],
            (float) $ligne['prix_unitaire'],
            $ligne['menu_nom'] ?? null
        );
    }

    public function sousTotal(): float
    {
        return $this->quantite * $this->prixUnitaire;
    }

    public function sousTotalFormate(): string
    {
        return number_format($this->sousTotal(), 0, ',', ' ') . ' FCFA';
    }

    public function prixUnitaireFormate(): string
    {
        return number_format($this->prixUnitaire, 0, ',', ' ') . ' FCFA';
    }
}
