<?php require 'header.php'; ?>

<h1>Gestion des menus</h1>

<?php
$message = "";

// ---------------------------------------------------------
// AJOUT D'UN MENU
// ---------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter'])) {
    $nom = trim($_POST['nom']);
    $description = trim($_POST['description']);
    $prix = $_POST['prix'];
    $categorie_id = $_POST['categorie_id'];
    $disponible = isset($_POST['disponible']) ? 1 : 0;

    $sql = "INSERT INTO menus (nom, description, prix, categorie_id, disponible) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdii", $nom, $description, $prix, $categorie_id, $disponible);
    $stmt->execute();
    $stmt->close();
    $message = "Le plat a été ajouté avec succès.";
}

// ---------------------------------------------------------
// MODIFICATION D'UN MENU
// ---------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modifier'])) {
    $id = $_POST['id'];
    $nom = trim($_POST['nom']);
    $description = trim($_POST['description']);
    $prix = $_POST['prix'];
    $categorie_id = $_POST['categorie_id'];
    $disponible = isset($_POST['disponible']) ? 1 : 0;

    $sql = "UPDATE menus SET nom=?, description=?, prix=?, categorie_id=?, disponible=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdiii", $nom, $description, $prix, $categorie_id, $disponible, $id);
    $stmt->execute();
    $stmt->close();
    $message = "Le plat a été modifié avec succès.";
}

// ---------------------------------------------------------
// SUPPRESSION D'UN MENU
// ---------------------------------------------------------
if (isset($_GET['supprimer'])) {
    $id = $_GET['supprimer'];
    $stmt = $conn->prepare("DELETE FROM menus WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    $message = "Le plat a été supprimé.";
}

// ---------------------------------------------------------
// Si on clique sur "Modifier", on récupère les infos du plat
// ---------------------------------------------------------
$menu_a_modifier = null;
if (isset($_GET['modifier'])) {
    $id = $_GET['modifier'];
    $stmt = $conn->prepare("SELECT * FROM menus WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $menu_a_modifier = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// Liste des catégories pour le formulaire
$categories = $conn->query("SELECT * FROM categories ORDER BY nom");
?>

<?php if ($message): ?>
    <p class="succes"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<!-- ================= FORMULAIRE AJOUT / MODIFICATION ================= -->
<form class="formulaire" method="POST" action="menus.php">
    <h3><?= $menu_a_modifier ? "Modifier le plat" : "Ajouter un nouveau plat" ?></h3>

    <?php if ($menu_a_modifier): ?>
        <input type="hidden" name="id" value="<?= $menu_a_modifier['id'] ?>">
    <?php endif; ?>

    <label for="nom">Nom du plat</label>
    <input type="text" id="nom" name="nom" required
           value="<?= htmlspecialchars($menu_a_modifier['nom'] ?? '') ?>">

    <label for="description">Description</label>
    <textarea id="description" name="description" rows="3"><?= htmlspecialchars($menu_a_modifier['description'] ?? '') ?></textarea>

    <label for="prix">Prix (FCFA)</label>
    <input type="number" step="0.01" id="prix" name="prix" required
           value="<?= htmlspecialchars($menu_a_modifier['prix'] ?? '') ?>">

    <label for="categorie_id">Catégorie</label>
    <select id="categorie_id" name="categorie_id" required>
        <option value="">-- Choisir une catégorie --</option>
        <?php
        $categories->data_seek(0);
        while ($cat = $categories->fetch_assoc()):
            $selected = (isset($menu_a_modifier['categorie_id']) && $menu_a_modifier['categorie_id'] == $cat['id']) ? 'selected' : '';
        ?>
            <option value="<?= $cat['id'] ?>" <?= $selected ?>><?= htmlspecialchars($cat['nom']) ?></option>
        <?php endwhile; ?>
    </select>

    <label>
        <input type="checkbox" name="disponible" style="width:auto; display:inline;"
            <?= (!isset($menu_a_modifier) || $menu_a_modifier['disponible']) ? 'checked' : '' ?>>
        Disponible à la vente
    </label>

    <?php if ($menu_a_modifier): ?>
        <button type="submit" name="modifier">Enregistrer les modifications</button>
        <a class="btn btn-secondaire" href="menus.php">Annuler</a>
    <?php else: ?>
        <button type="submit" name="ajouter">Ajouter le plat</button>
    <?php endif; ?>
</form>

<!-- ================= LISTE DES MENUS ================= -->
<h2>Liste des plats</h2>
<table>
    <tr>
        <th>Nom</th>
        <th>Catégorie</th>
        <th>Prix</th>
        <th>Disponible</th>
        <th>Actions</th>
    </tr>
    <?php
    $sql = "SELECT m.*, c.nom AS categorie_nom
            FROM menus m
            LEFT JOIN categories c ON m.categorie_id = c.id
            ORDER BY m.id DESC";
    $res = $conn->query($sql);
    while ($menu = $res->fetch_assoc()):
    ?>
        <tr>
            <td><?= htmlspecialchars($menu['nom']) ?></td>
            <td><?= htmlspecialchars($menu['categorie_nom'] ?? '-') ?></td>
            <td><?= number_format($menu['prix'], 0, ',', ' ') ?> FCFA</td>
            <td><?= $menu['disponible'] ? 'Oui' : 'Non' ?></td>
            <td class="actions">
                <a class="btn btn-sm" href="menus.php?modifier=<?= $menu['id'] ?>">Modifier</a>
                <a class="btn btn-sm btn-danger" href="menus.php?supprimer=<?= $menu['id'] ?>"
                   onclick="return confirm('Supprimer ce plat ?');">Supprimer</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<?php require 'footer.php'; ?>
