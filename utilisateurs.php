<?php require 'header.php'; ?>

<?php
// Seul l'administrateur peut gérer les utilisateurs
if ($_SESSION['role'] !== 'admin') {
    echo "<h1>Accès refusé</h1><p>Cette page est réservée à l'administrateur.</p>";
    require 'footer.php';
    exit();
}

$message = "";

// ---------------------------------------------------------
// AJOUT D'UN UTILISATEUR
// ---------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter'])) {
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $mot_de_passe = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $sql = "INSERT INTO utilisateurs (nom, email, mot_de_passe, role) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $nom, $email, $mot_de_passe, $role);

    if ($stmt->execute()) {
        $message = "L'utilisateur a été ajouté avec succès.";
    } else {
        $message = "Erreur : cet email est peut-être déjà utilisé.";
    }
    $stmt->close();
}

// ---------------------------------------------------------
// MODIFICATION D'UN UTILISATEUR
// ---------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modifier'])) {
    $id = $_POST['id'];
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];

    if (!empty($_POST['mot_de_passe'])) {
        // Si un nouveau mot de passe est fourni, on le met à jour aussi
        $mot_de_passe = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);
        $sql = "UPDATE utilisateurs SET nom=?, email=?, role=?, mot_de_passe=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $nom, $email, $role, $mot_de_passe, $id);
    } else {
        $sql = "UPDATE utilisateurs SET nom=?, email=?, role=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $nom, $email, $role, $id);
    }

    $stmt->execute();
    $stmt->close();
    $message = "L'utilisateur a été modifié avec succès.";
}

// ---------------------------------------------------------
// SUPPRESSION D'UN UTILISATEUR
// ---------------------------------------------------------
if (isset($_GET['supprimer'])) {
    $id = $_GET['supprimer'];

    if ($id == $_SESSION['user_id']) {
        $message = "Vous ne pouvez pas supprimer votre propre compte.";
    } else {
        $stmt = $conn->prepare("DELETE FROM utilisateurs WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        $message = "L'utilisateur a été supprimé.";
    }
}

// ---------------------------------------------------------
// Récupération d'un utilisateur à modifier
// ---------------------------------------------------------
$utilisateur_a_modifier = null;
if (isset($_GET['modifier'])) {
    $id = $_GET['modifier'];
    $stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $utilisateur_a_modifier = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

$roles = ['admin', 'manager', 'serveur', 'cuisinier'];
?>

<h1>Gestion des utilisateurs</h1>

<?php if ($message): ?>
    <p class="succes"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<!-- ================= FORMULAIRE AJOUT / MODIFICATION ================= -->
<form class="formulaire" method="POST" action="utilisateurs.php">
    <h3><?= $utilisateur_a_modifier ? "Modifier l'utilisateur" : "Ajouter un utilisateur" ?></h3>

    <?php if ($utilisateur_a_modifier): ?>
        <input type="hidden" name="id" value="<?= $utilisateur_a_modifier['id'] ?>">
    <?php endif; ?>

    <label for="nom">Nom complet</label>
    <input type="text" id="nom" name="nom" required
           value="<?= htmlspecialchars($utilisateur_a_modifier['nom'] ?? '') ?>">

    <label for="email">Email</label>
    <input type="email" id="email" name="email" required
           value="<?= htmlspecialchars($utilisateur_a_modifier['email'] ?? '') ?>">

    <label for="mot_de_passe">Mot de passe <?= $utilisateur_a_modifier ? '(laisser vide pour ne pas changer)' : '' ?></label>
    <input type="password" id="mot_de_passe" name="mot_de_passe" <?= $utilisateur_a_modifier ? '' : 'required' ?>>

    <label for="role">Rôle</label>
    <select id="role" name="role" required>
        <?php foreach ($roles as $role):
            $selected = (isset($utilisateur_a_modifier['role']) && $utilisateur_a_modifier['role'] === $role) ? 'selected' : '';
        ?>
            <option value="<?= $role ?>" <?= $selected ?>><?= ucfirst($role) ?></option>
        <?php endforeach; ?>
    </select>

    <?php if ($utilisateur_a_modifier): ?>
        <button type="submit" name="modifier">Enregistrer les modifications</button>
        <a class="btn btn-secondaire" href="utilisateurs.php">Annuler</a>
    <?php else: ?>
        <button type="submit" name="ajouter">Ajouter l'utilisateur</button>
    <?php endif; ?>
</form>

<!-- ================= LISTE DES UTILISATEURS ================= -->
<h2>Liste des utilisateurs</h2>
<table>
    <tr>
        <th>Nom</th>
        <th>Email</th>
        <th>Rôle</th>
        <th>Date de création</th>
        <th>Actions</th>
    </tr>
    <?php
    $res = $conn->query("SELECT * FROM utilisateurs ORDER BY id");
    while ($utilisateur = $res->fetch_assoc()):
    ?>
        <tr>
            <td><?= htmlspecialchars($utilisateur['nom']) ?></td>
            <td><?= htmlspecialchars($utilisateur['email']) ?></td>
            <td><?= ucfirst($utilisateur['role']) ?></td>
            <td><?= date('d/m/Y', strtotime($utilisateur['date_creation'])) ?></td>
            <td class="actions">
                <a class="btn btn-sm" href="utilisateurs.php?modifier=<?= $utilisateur['id'] ?>">Modifier</a>
                <a class="btn btn-sm btn-danger" href="utilisateurs.php?supprimer=<?= $utilisateur['id'] ?>"
                   onclick="return confirm('Supprimer cet utilisateur ?');">Supprimer</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<?php require 'footer.php'; ?>
