<?php require 'header.php'; ?>

<?php
if (!isset($_GET['id'])) {
    header("Location: commandes.php");
    exit();
}
$commande_id = $_GET['id'];
$message = "";

// ---------------------------------------------------------
// AJOUT D'UN PLAT A LA COMMANDE
// ---------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_plat'])) {
    $menu_id = $_POST['menu_id'];
    $quantite = $_POST['quantite'];

    // Récupérer le prix actuel du plat
    $stmt = $conn->prepare("SELECT prix FROM menus WHERE id = ?");
    $stmt->bind_param("i", $menu_id);
    $stmt->execute();
    $prix = $stmt->get_result()->fetch_assoc()['prix'];
    $stmt->close();

    $stmt = $conn->prepare("INSERT INTO commande_details (commande_id, menu_id, quantite, prix_unitaire) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiid", $commande_id, $menu_id, $quantite, $prix);
    $stmt->execute();
    $stmt->close();

    $message = "Le plat a été ajouté à la commande.";
}

// ---------------------------------------------------------
// SUPPRESSION D'UN ARTICLE DE LA COMMANDE
// ---------------------------------------------------------
if (isset($_GET['supprimer_ligne'])) {
    $ligne_id = $_GET['supprimer_ligne'];
    $stmt = $conn->prepare("DELETE FROM commande_details WHERE id = ? AND commande_id = ?");
    $stmt->bind_param("ii", $ligne_id, $commande_id);
    $stmt->execute();
    $stmt->close();
    $message = "L'article a été retiré de la commande.";
}

// ---------------------------------------------------------
// RECALCUL DU TOTAL DE LA COMMANDE
// ---------------------------------------------------------
$stmt = $conn->prepare("SELECT COALESCE(SUM(quantite * prix_unitaire),0) AS total FROM commande_details WHERE commande_id = ?");
$stmt->bind_param("i", $commande_id);
$stmt->execute();
$total = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

$stmt = $conn->prepare("UPDATE commandes SET total = ? WHERE id = ?");
$stmt->bind_param("di", $total, $commande_id);
$stmt->execute();
$stmt->close();

// ---------------------------------------------------------
// INFORMATIONS DE LA COMMANDE
// ---------------------------------------------------------
$stmt = $conn->prepare("SELECT c.*, t.numero AS table_numero, u.nom AS serveur
                        FROM commandes c
                        LEFT JOIN tables_restaurant t ON c.table_id = t.id
                        LEFT JOIN utilisateurs u ON c.utilisateur_id = u.id
                        WHERE c.id = ?");
$stmt->bind_param("i", $commande_id);
$stmt->execute();
$commande = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$commande) {
    echo "<p class='erreur'>Commande introuvable.</p>";
    require 'footer.php';
    exit();
}

// Plats disponibles pour le formulaire d'ajout
$menus = $conn->query("SELECT id, nom, prix FROM menus WHERE disponible = 1 ORDER BY nom");
?>

<h1>Commande #<?= $commande['id'] ?> - Table <?= htmlspecialchars($commande['table_numero'] ?? '-') ?></h1>
<p>
    Serveur : <strong><?= htmlspecialchars($commande['serveur'] ?? '-') ?></strong> |
    Statut : <span class="badge badge-<?= $commande['statut'] ?>"><?= str_replace('_',' ', $commande['statut']) ?></span> |
    Date : <?= date('d/m/Y H:i', strtotime($commande['date_commande'])) ?>
</p>

<?php if ($message): ?>
    <p class="succes"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<!-- ================= FORMULAIRE AJOUT D'UN PLAT ================= -->
<form class="formulaire" method="POST" action="commande_details.php?id=<?= $commande_id ?>">
    <h3>Ajouter un plat à la commande</h3>

    <label for="menu_id">Plat</label>
    <select id="menu_id" name="menu_id" required>
        <option value="">-- Choisir un plat --</option>
        <?php while ($menu = $menus->fetch_assoc()): ?>
            <option value="<?= $menu['id'] ?>"><?= htmlspecialchars($menu['nom']) ?> - <?= number_format($menu['prix'], 0, ',', ' ') ?> FCFA</option>
        <?php endwhile; ?>
    </select>

    <label for="quantite">Quantité</label>
    <input type="number" id="quantite" name="quantite" min="1" value="1" required>

    <button type="submit" name="ajouter_plat">Ajouter</button>
</form>

<!-- ================= ARTICLES DE LA COMMANDE ================= -->
<h2>Articles commandés</h2>
<table>
    <tr>
        <th>Plat</th>
        <th>Quantité</th>
        <th>Prix unitaire</th>
        <th>Sous-total</th>
        <th>Action</th>
    </tr>
    <?php
    $sql = "SELECT cd.*, m.nom AS menu_nom
            FROM commande_details cd
            JOIN menus m ON cd.menu_id = m.id
            WHERE cd.commande_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $commande_id);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($ligne = $res->fetch_assoc()):
        $sous_total = $ligne['quantite'] * $ligne['prix_unitaire'];
    ?>
        <tr>
            <td><?= htmlspecialchars($ligne['menu_nom']) ?></td>
            <td><?= $ligne['quantite'] ?></td>
            <td><?= number_format($ligne['prix_unitaire'], 0, ',', ' ') ?> FCFA</td>
            <td><?= number_format($sous_total, 0, ',', ' ') ?> FCFA</td>
            <td class="actions">
                <a class="btn btn-sm btn-danger" href="commande_details.php?id=<?= $commande_id ?>&supprimer_ligne=<?= $ligne['id'] ?>"
                   onclick="return confirm('Retirer cet article ?');">Retirer</a>
            </td>
        </tr>
    <?php endwhile; $stmt->close(); ?>
</table>

<h3>Total de la commande : <?= number_format($total, 0, ',', ' ') ?> FCFA</h3>

<a class="btn btn-secondaire" href="commandes.php">Retour aux commandes</a>

<?php require 'footer.php'; ?>
