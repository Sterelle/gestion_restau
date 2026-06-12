<?php require 'header.php'; ?>

<h1>Gestion des commandes</h1>

<?php
$message = "";

// ---------------------------------------------------------
// CREATION D'UNE NOUVELLE COMMANDE
// ---------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['creer_commande'])) {
    $table_id = $_POST['table_id'];
    $utilisateur_id = $_SESSION['user_id'];

    $sql = "INSERT INTO commandes (table_id, utilisateur_id, statut, total) VALUES (?, ?, 'en_attente', 0)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $table_id, $utilisateur_id);
    $stmt->execute();
    $nouvelle_commande_id = $stmt->insert_id;
    $stmt->close();

    // La table devient occupée
    $stmt = $conn->prepare("UPDATE tables_restaurant SET statut = 'occupee' WHERE id = ?");
    $stmt->bind_param("i", $table_id);
    $stmt->execute();
    $stmt->close();

    // Redirection directe vers la commande pour ajouter les plats
    header("Location: commande_details.php?id=" . $nouvelle_commande_id);
    exit();
}

// ---------------------------------------------------------
// CHANGEMENT DE STATUT D'UNE COMMANDE
// ---------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['changer_statut'])) {
    $id = $_POST['id'];
    $nouveau_statut = $_POST['statut'];

    $stmt = $conn->prepare("UPDATE commandes SET statut = ? WHERE id = ?");
    $stmt->bind_param("si", $nouveau_statut, $id);
    $stmt->execute();
    $stmt->close();

    // Si la commande est payée ou annulée, la table redevient libre
    if ($nouveau_statut === 'payee' || $nouveau_statut === 'annulee') {
        $stmt = $conn->prepare("SELECT table_id FROM commandes WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $table_id = $stmt->get_result()->fetch_assoc()['table_id'];
        $stmt->close();

        if ($table_id) {
            $stmt = $conn->prepare("UPDATE tables_restaurant SET statut = 'libre' WHERE id = ?");
            $stmt->bind_param("i", $table_id);
            $stmt->execute();
            $stmt->close();
        }
    }

    $message = "Le statut de la commande #$id a été mis à jour.";
}

// Tables libres pour le formulaire de création
$tables_libres = $conn->query("SELECT * FROM tables_restaurant WHERE statut = 'libre' ORDER BY numero");
?>

<?php if ($message): ?>
    <p class="succes"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<!-- ================= FORMULAIRE NOUVELLE COMMANDE ================= -->
<form class="formulaire" method="POST" action="commandes.php">
    <h3>Créer une nouvelle commande</h3>
    <label for="table_id">Table</label>
    <select id="table_id" name="table_id" required>
        <option value="">-- Choisir une table libre --</option>
        <?php while ($table = $tables_libres->fetch_assoc()): ?>
            <option value="<?= $table['id'] ?>">Table <?= $table['numero'] ?> (<?= $table['capacite'] ?> places)</option>
        <?php endwhile; ?>
    </select>
    <button type="submit" name="creer_commande">Créer la commande</button>
</form>

<!-- ================= LISTE DES COMMANDES ================= -->
<h2>Toutes les commandes</h2>
<table>
    <tr>
        <th>N°</th>
        <th>Table</th>
        <th>Serveur</th>
        <th>Total</th>
        <th>Statut</th>
        <th>Date</th>
        <th>Actions</th>
    </tr>
    <?php
    $sql = "SELECT c.id, t.numero AS table_numero, u.nom AS serveur, c.statut, c.total, c.date_commande
            FROM commandes c
            LEFT JOIN tables_restaurant t ON c.table_id = t.id
            LEFT JOIN utilisateurs u ON c.utilisateur_id = u.id
            ORDER BY c.date_commande DESC";
    $res = $conn->query($sql);
    while ($cmd = $res->fetch_assoc()):
    ?>
        <tr>
            <td>#<?= $cmd['id'] ?></td>
            <td><?= $cmd['table_numero'] !== null ? "Table ".$cmd['table_numero'] : '-' ?></td>
            <td><?= htmlspecialchars($cmd['serveur'] ?? '-') ?></td>
            <td><?= number_format($cmd['total'], 0, ',', ' ') ?> FCFA</td>
            <td>
                <form method="POST" action="commandes.php" style="display:flex; gap:5px;">
                    <input type="hidden" name="id" value="<?= $cmd['id'] ?>">
                    <select name="statut" style="width:auto;">
                        <?php
                        $statuts = ['en_attente','en_preparation','servie','payee','annulee'];
                        foreach ($statuts as $statut):
                            $selected = ($statut === $cmd['statut']) ? 'selected' : '';
                        ?>
                            <option value="<?= $statut ?>" <?= $selected ?>><?= str_replace('_',' ', $statut) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" name="changer_statut" class="btn-sm">OK</button>
                </form>
            </td>
            <td><?= date('d/m/Y H:i', strtotime($cmd['date_commande'])) ?></td>
            <td class="actions">
                <a class="btn btn-sm" href="commande_details.php?id=<?= $cmd['id'] ?>">Détails</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<?php require 'footer.php'; ?>
