<?php require 'header.php'; ?>

<h1>Tableau de bord</h1>

<?php
// --- Nombre de commandes en attente ---
$res = $conn->query("SELECT COUNT(*) AS total FROM commandes WHERE statut = 'en_attente'");
$commandes_attente = $res->fetch_assoc()['total'];

// --- Nombre de commandes en préparation ---
$res = $conn->query("SELECT COUNT(*) AS total FROM commandes WHERE statut = 'en_preparation'");
$commandes_preparation = $res->fetch_assoc()['total'];

// --- Nombre total de plats au menu ---
$res = $conn->query("SELECT COUNT(*) AS total FROM menus");
$nb_menus = $res->fetch_assoc()['total'];

// --- Nombre de tables occupées ---
$res = $conn->query("SELECT COUNT(*) AS total FROM tables_restaurant WHERE statut = 'occupee'");
$tables_occupees = $res->fetch_assoc()['total'];

// --- Chiffre d'affaires du jour (commandes payées aujourd'hui) ---
$res = $conn->query("SELECT COALESCE(SUM(total),0) AS somme FROM commandes WHERE statut = 'payee' AND DATE(date_commande) = CURDATE()");
$ca_jour = $res->fetch_assoc()['somme'];
?>

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
    <tr>
        <th>N°</th>
        <th>Table</th>
        <th>Serveur</th>
        <th>Statut</th>
        <th>Total</th>
        <th>Date</th>
        <th>Action</th>
    </tr>
    <?php
    $sql = "SELECT c.id, t.numero AS table_numero, u.nom AS serveur, c.statut, c.total, c.date_commande
            FROM commandes c
            LEFT JOIN tables_restaurant t ON c.table_id = t.id
            LEFT JOIN utilisateurs u ON c.utilisateur_id = u.id
            ORDER BY c.date_commande DESC
            LIMIT 5";
    $res = $conn->query($sql);
    while ($cmd = $res->fetch_assoc()):
    ?>
        <tr>
            <td>#<?= $cmd['id'] ?></td>
            <td><?= $cmd['table_numero'] !== null ? "Table ".$cmd['table_numero'] : '-' ?></td>
            <td><?= htmlspecialchars($cmd['serveur'] ?? '-') ?></td>
            <td><span class="badge badge-<?= $cmd['statut'] ?>"><?= str_replace('_',' ', $cmd['statut']) ?></span></td>
            <td><?= number_format($cmd['total'], 0, ',', ' ') ?> FCFA</td>
            <td><?= date('d/m/Y H:i', strtotime($cmd['date_commande'])) ?></td>
            <td class="actions"><a class="btn btn-sm" href="commande_details.php?id=<?= $cmd['id'] ?>">Voir</a></td>
        </tr>
    <?php endwhile; ?>
</table>

<?php require 'footer.php'; ?>
