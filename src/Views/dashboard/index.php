<?php
// Récupération des données (à adapter avec votre BDD)
$commandes_attente = 3;
$commandes_preparation = 2;
$tables_occupees = 5;
$nb_menus = 12;
$ca_jour = 245750; // FCFA

// Exemple de classe Commande (remplacez par votre modèle)
class Commande {
    public $id;
    public $tableNumero;
    public $serveurNom;
    public $statut;
    public $dateCommande;
    public $total;

    public function statutLisible() {
        $map = [
            'attente' => 'En attente',
            'preparation' => 'En préparation',
            'terminee' => 'Terminée',
            'livree' => 'Livrée',
            'annulee' => 'Annulée'
        ];
        return $map[$this->statut] ?? ucfirst($this->statut);
    }

    public function totalFormate() {
        return number_format($this->total, 0, ',', ' ') . ' FCFA';
    }
}

// Dernières commandes (exemple)
$dernieres_commandes = [
    (object)['id'=>101, 'tableNumero'=>5, 'serveurNom'=>'Alice', 'statut'=>'attente', 'dateCommande'=>'2025-03-15 12:30:00', 'total'=>12500],
    (object)['id'=>102, 'tableNumero'=>3, 'serveurNom'=>'Bob', 'statut'=>'preparation', 'dateCommande'=>'2025-03-15 12:45:00', 'total'=>8900],
    (object)['id'=>103, 'tableNumero'=>null, 'serveurNom'=>'Charlie', 'statut'=>'terminee', 'dateCommande'=>'2025-03-15 11:20:00', 'total'=>23400],
    (object)['id'=>104, 'tableNumero'=>8, 'serveurNom'=>'Diana', 'statut'=>'livree', 'dateCommande'=>'2025-03-15 10:15:00', 'total'=>5600],
];
// Convertir en objets de la classe Commande
foreach ($dernieres_commandes as &$cmd) {
    $c = new Commande();
    $c->id = $cmd->id;
    $c->tableNumero = $cmd->tableNumero;
    $c->serveurNom = $cmd->serveurNom;
    $c->statut = $cmd->statut;
    $c->dateCommande = $cmd->dateCommande;
    $c->total = $cmd->total;
    $cmd = $c;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - Restaurant</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h1>Tableau de bord</h1>

<div class="cards">
    <div class="card">
        <h3><?= $commandes_attente ?></h3>
        <p>Commandes en attente</p>
    </div>
    <div class="card">
        <h3><?= $commandes_preparation ?></h3>
        <p>Commandes en préparation</p>
    </div>
    <div class="card">
        <h3><?= $tables_occupees ?></h3>
        <p>Tables occupées</p>
    </div>
    <div class="card">
        <h3><?= $nb_menus ?></h3>
        <p>Plats au menu</p>
    </div>
    <div class="card">
        <h3><?= number_format($ca_jour, 0, ',', ' ') ?> FCFA</h3>
        <p>Chiffre d'affaires du jour</p>
    </div>
</div>

<h2>Dernières commandes</h2>
<table>
    <thead>
        <tr>
            <th>N°</th>
            <th>Table</th>
            <th>Serveur</th>
            <th>Statut</th>
            <th>Total</th>
            <th>Date</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($dernieres_commandes as $cmd): ?>
        <tr>
            <td data-label="N°">#<?= $cmd->id ?></td>
            <td data-label="Table"><?= $cmd->tableNumero !== null ? "Table " . $cmd->tableNumero : '-' ?></td>
            <td data-label="Serveur"><?= htmlspecialchars($cmd->serveurNom ?? '-') ?></td>
            <td data-label="Statut">
                <span class="badge badge-<?= $cmd->statut ?>"><?= $cmd->statutLisible() ?></span>
            </td>
            <td data-label="Total"><?= $cmd->totalFormate() ?></td>
            <td data-label="Date"><?= date('d/m/Y H:i', strtotime($cmd->dateCommande)) ?></td>
            <td class="actions" data-label="Action">
                <a class="btn btn-sm" href="index.php?page=commande_details&id=<?= $cmd->id ?>">Voir</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>