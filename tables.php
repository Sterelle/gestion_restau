<?php require 'header.php'; ?>

<h1>Gestion des tables du restaurant</h1>

<?php
$message = "";

// ---------------------------------------------------------
// AJOUT D'UNE TABLE
// ---------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter'])) {
    $numero = $_POST['numero'];
    $capacite = $_POST['capacite'];

    $sql = "INSERT INTO tables_restaurant (numero, capacite, statut) VALUES (?, ?, 'libre')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $numero, $capacite);
    $stmt->execute();
    $stmt->close();
    $message = "La table a été ajoutée avec succès.";
}

// ---------------------------------------------------------
// MODIFICATION DU STATUT D'UNE TABLE
// ---------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['changer_statut'])) {
    $id = $_POST['id'];
    $statut = $_POST['statut'];

    $stmt = $conn->prepare("UPDATE tables_restaurant SET statut = ? WHERE id = ?");
    $stmt->bind_param("si", $statut, $id);
    $stmt->execute();
    $stmt->close();
    $message = "Le statut de la table a été mis à jour.";
}

// ---------------------------------------------------------
// SUPPRESSION D'UNE TABLE
// ---------------------------------------------------------
if (isset($_GET['supprimer'])) {
    $id = $_GET['supprimer'];
    $stmt = $conn->prepare("DELETE FROM tables_restaurant WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    $message = "La table a été supprimée.";
}
?>

<?php if ($message): ?>
    <p class="succes"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<!-- ================= FORMULAIRE AJOUT D'UNE TABLE ================= -->
<form class="formulaire" method="POST" action="tables.php">
    <h3>Ajouter une table</h3>

    <label for="numero">Numéro de la table</label>
    <input type="number" id="numero" name="numero" min="1" required>

    <label for="capacite">Capacité (nombre de places)</label>
    <input type="number" id="capacite" name="capacite" min="1" value="4" required>

    <button type="submit" name="ajouter">Ajouter la table</button>
</form>

<!-- ================= LISTE DES TABLES ================= -->
<h2>Liste des tables</h2>
<table>
    <tr>
        <th>Numéro</th>
        <th>Capacité</th>
        <th>Statut</th>
        <th>Changer le statut</th>
        <th>Action</th>
    </tr>
    <?php
    $res = $conn->query("SELECT * FROM tables_restaurant ORDER BY numero");
    while ($table = $res->fetch_assoc()):
    ?>
        <tr>
            <td>Table <?= $table['numero'] ?></td>
            <td><?= $table['capacite'] ?> places</td>
            <td><span class="badge badge-<?= $table['statut'] ?>"><?= ucfirst($table['statut']) ?></span></td>
            <td>
                <form method="POST" action="tables.php" style="display:flex; gap:5px;">
                    <input type="hidden" name="id" value="<?= $table['id'] ?>">
                    <select name="statut" style="width:auto;">
                        <?php foreach (['libre','occupee','reservee'] as $statut):
                            $selected = ($statut === $table['statut']) ? 'selected' : '';
                        ?>
                            <option value="<?= $statut ?>" <?= $selected ?>><?= ucfirst($statut) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" name="changer_statut" class="btn-sm">OK</button>
                </form>
            </td>
            <td class="actions">
                <a class="btn btn-sm btn-danger" href="tables.php?supprimer=<?= $table['id'] ?>"
                   onclick="return confirm('Supprimer cette table ?');">Supprimer</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<?php require 'footer.php'; ?>
