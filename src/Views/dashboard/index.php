<div class="panel">
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
</div>

<h2>Dernières commandes</h2>
<div class="panel">
<table>
    <tr>
        <th>N°</th>
        <th>Table</th>
        <th>Serveur</th>
        <th>Statut</th>
        <th>Total</th>
        <th>Date</th>
        <th>Action</th>
    </tr>
    <?php foreach ($dernieres_commandes as $cmd): ?>
        <tr>
            <td>#<?= $cmd->id ?></td>
            <td><?= $cmd->tableNumero !== null ? "Table " . $cmd->tableNumero : '-' ?></td>
            <td><?= htmlspecialchars($cmd->serveurNom ?? '-') ?></td>
            <td><span class="badge badge-<?= $cmd->statut ?>"><?= $cmd->statutLisible() ?></span></td>
            <td><?= $cmd->totalFormate() ?></td>
            <td><?= date('d/m/Y H:i', strtotime($cmd->dateCommande)) ?></td>
            <td class="actions"><a class="btn btn-sm" href="index.php?page=commande_details&id=<?= $cmd->id ?>">Voir</a></td>
        </tr>
    <?php endforeach; ?>
</table>
</div>
