<?php

namespace App\Models;

/**
 * Représente une commande passée par un client (associée à une table et un serveur).
 */
class Commande
{
    public const STATUT_EN_ATTENTE = 'en_attente';
    public const STATUT_EN_PREPARATION = 'en_preparation';
    public const STATUT_SERVIE = 'servie';
    public const STATUT_PAYEE = 'payee';
    public const STATUT_ANNULEE = 'annulee';

    /** @var string[] Liste ordonnée des statuts valides (pour les formulaires) */
    public const STATUTS = [
        self::STATUT_EN_ATTENTE,
        self::STATUT_EN_PREPARATION,
        self::STATUT_SERVIE,
        self::STATUT_PAYEE,
        self::STATUT_ANNULEE,
    ];

    public ?int $id;
    public ?int $tableId;
    public ?int $utilisateurId;
    public string $statut;
    public string $dateCommande;
    public float $total;
    public ?int $tableNumero;
    public ?string $serveurNom;

    public function __construct(
        ?int $id,
        ?int $tableId,
        ?int $utilisateurId,
        string $statut,
        string $dateCommande,
        float $total,
        ?int $tableNumero = null,
        ?string $serveurNom = null
    ) {
        $this->id = $id;
        $this->tableId = $tableId;
        $this->utilisateurId = $utilisateurId;
        $this->statut = $statut;
        $this->dateCommande = $dateCommande;
        $this->total = $total;
        $this->tableNumero = $tableNumero;
        $this->serveurNom = $serveurNom;
    }

    public static function depuisTableau(array $ligne): self
    {
        return new self(
            (int) $ligne['id'],
            isset($ligne['table_id']) ? (int) $ligne['table_id'] : null,
            isset($ligne['utilisateur_id']) ? (int) $ligne['utilisateur_id'] : null,
            $ligne['statut'],
            $ligne['date_commande'],
            (float) $ligne['total'],
            isset($ligne['table_numero']) ? (int) $ligne['table_numero'] : null,
            $ligne['serveur'] ?? null
        );
    }

    /**
     * Indique si, après ce changement de statut, la table associée
     * doit redevenir libre (règle métier centralisée ici plutôt
     * que dispersée dans les contrôleurs).
     */
    public function libereLaTable(): bool
    {
        return in_array($this->statut, [self::STATUT_PAYEE, self::STATUT_ANNULEE], true);
    }

    public function totalFormate(): string
    {
        return number_format($this->total, 0, ',', ' ') . ' FCFA';
    }

    public function statutLisible(): string
    {
        return str_replace('_', ' ', $this->statut);
    }
}
