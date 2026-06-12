<?php require 'header.php'; ?>

<h1>Gestion des recettes</h1>

<?php
$message = "";

// ---------------------------------------------------------
// AJOUT D'UNE RECETTE
// ---------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter'])) {
    $menu_id = $_POST['menu_id'];
    $ingredients = trim($_POST['ingredients']);
    $temps_preparation = $_POST['temps_preparation'];
    $instructions = trim($_POST['instructions']);

    $sql = "INSERT INTO recettes (menu_id, ingredients, temps_preparation, instructions) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isis", $menu_id, $ingredients, $temps_preparation, $instructions);
    $stmt->execute();
    $stmt->close();
    $message = "La recette a été ajoutée avec succès.";
}

// ---------------------------------------------------------
// MODIFICATION D'UNE RECETTE
// ---------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modifier'])) {
    $id = $_POST['id'];
    $menu_id = $_POST['menu_id'];
    $ingredients = trim($_POST['ingredients']);
    $temps_preparation = $_POST['temps_preparation'];
    $instructions = trim($_POST['instructions']);

    $sql = "UPDATE recettes SET menu_id=?, ingredients=?, temps_preparation=?, instructions=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isisi", $menu_id, $ingredients, $temps_preparation, $instructions, $id);
    $stmt->execute();
    $stmt->close();
    $message = "La recette a été modifiée avec succès.";
}

// ---------------------------------------------------------
// SUPPRESSION D'UNE RECETTE
// ---------------------------------------------------------
if (isset($_GET['supprimer'])) {
    $id = $_GET['supprimer'];
    $stmt = $conn->prepare("DELETE FROM recettes WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    $message = "La recette a été supprimée.";
}

// ---------------------------------------------------------
// Récupération d'une recette à modifier
// ---------------------------------------------------------
$recette_a_modifier = null;
if (isset($_GET['modifier'])) {
    $id = $_GET['modifier'];
    $stmt = $conn->prepare("SELECT * FROM recettes WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $recette_a_modifier = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// Liste des plats pour le formulaire
$menus = $conn->query("SELECT id, nom FROM menus ORDER BY nom");
?>

<?php if ($message): ?>
    <p class="succes"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<!-- ================= FORMULAIRE AJOUT / MODIFICATION ================= -->
<form class="formulaire" method="POST" action="recettes.php">
    <h3><?= $recette_a_modifier ? "Modifier la recette" : "Ajouter une nouvelle recette" ?></h3>

    <?php if ($recette_a_modifier): ?>
        <input type="hidden" name="id" value="<?= $recette_a_modifier['id'] ?>">
    <?php endif; ?>

    <label for="menu_id">Plat concerné</label>
    <select id="menu_id" name="menu_id" required>
        <option value="">-- Choisir un plat --</option>
        <?php
        $menus->data_seek(0);
        while ($menu = $menus->fetch_assoc()):
            $selected = (isset($recette_a_modifier['menu_id']) && $recette_a_modifier['menu_id'] == $menu['id']) ? 'selected' : '';
        ?>
            <option value="<?= $menu['id'] ?>" <?= $selected ?>><?= htmlspecialchars($menu['nom']) ?></option>
        <?php endwhile; ?>
    </select>

    <label for="ingredients">Ingrédients (un par ligne ou séparés par des virgules)</label>
    <textarea id="ingredients" name="ingredients" rows="3" required><?= htmlspecialchars($recette_a_modifier['ingredients'] ?? '') ?></textarea>

    <label for="temps_preparation">Temps de préparation (en minutes)</label>
    <input type="number" id="temps_preparation" name="temps_preparation" min="0"
           value="<?= htmlspecialchars($recette_a_modifier['temps_preparation'] ?? '') ?>">

    <label for="instructions">Instructions / étapes de préparation</label>
    <textarea id="instructions" name="instructions" rows="4"><?= htmlspecialchars($recette_a_modifier['instructions'] ?? '') ?></textarea>

    <?php if ($recette_a_modifier): ?>
        <button type="submit" name="modifier">Enregistrer les modifications</button>
        <a class="btn btn-secondaire" href="recettes.php">Annuler</a>
    <?php else: ?>
        <button type="submit" name="ajouter">Ajouter la recette</button>
    <?php endif; ?>
</form>

<!-- ================= LISTE DES RECETTES ================= -->
<h2>Liste des recettes</h2>
<table>
    <tr>
        <th>Plat</th>
        <th>Ingrédients</th>
        <th>Temps (min)</th>
        <th>Actions</th>
    </tr>
    <?php
    $sql = "SELECT r.*, m.nom AS menu_nom
            FROM recettes r
            JOIN menus m ON r.menu_id = m.id
            ORDER BY r.id DESC";
    $res = $conn->query($sql);
    while ($recette = $res->fetch_assoc()):
    ?>
        <tr>
            <td><?= htmlspecialchars($recette['menu_nom']) ?></td>
            <td><?= htmlspecialchars(mb_strimwidth($recette['ingredients'], 0, 60, '...')) ?></td>
            <td><?= htmlspecialchars($recette['temps_preparation']) ?></td>
            <td class="actions">
                <a class="btn btn-sm" href="recettes.php?modifier=<?= $recette['id'] ?>">Modifier</a>
                <a class="btn btn-sm btn-danger" href="recettes.php?supprimer=<?= $recette['id'] ?>"
                   onclick="return confirm('Supprimer cette recette ?');">Supprimer</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<?php require 'footer.php'; ?>
