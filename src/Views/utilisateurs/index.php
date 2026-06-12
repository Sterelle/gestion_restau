<h1>Gestion des utilisateurs</h1>

<?php if ($message): ?>
    <p class="succes"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<!-- ================= FORMULAIRE AJOUT / MODIFICATION ================= -->
<form class="formulaire" method="POST" action="index.php?page=utilisateurs">
    <h3><?= $utilisateurAModifier ? "Modifier l'utilisateur" : "Ajouter un utilisateur" ?></h3>

    <?php if ($utilisateurAModifier): ?>
        <input type="hidden" name="id" value="<?= $utilisateurAModifier->id ?>">
    <?php endif; ?>

    <label for="nom">Nom complet</label>
    <input type="text" id="nom" name="nom" required
           value="<?= htmlspecialchars($utilisateurAModifier->nom ?? '') ?>">

    <label for="email">Email</label>
    <input type="email" id="email" name="email" required
           value="<?= htmlspecialchars($utilisateurAModifier->email ?? '') ?>">

    <label for="mot_de_passe">Mot de passe <?= $utilisateurAModifier ? '(laisser vide pour ne pas changer)' : '' ?></label>
    <input type="password" id="mot_de_passe" name="mot_de_passe" <?= $utilisateurAModifier ? '' : 'required' ?>>

    <label for="role">Rôle</label>
    <select id="role" name="role" required>
        <?php foreach ($roles as $role):
            $selected = ($utilisateurAModifier !== null && $utilisateurAModifier->role === $role) ? 'selected' : '';
        ?>
            <option value="<?= $role ?>" <?= $selected ?>><?= ucfirst($role) ?></option>
        <?php endforeach; ?>
    </select>

    <?php if ($utilisateurAModifier): ?>
        <button type="submit" name="modifier">Enregistrer les modifications</button>
        <a class="btn btn-secondaire" href="index.php?page=utilisateurs">Annuler</a>
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
    <?php foreach ($utilisateurs as $utilisateur): ?>
        <tr>
            <td><?= htmlspecialchars($utilisateur->nom) ?></td>
            <td><?= htmlspecialchars($utilisateur->email) ?></td>
            <td><?= ucfirst($utilisateur->role) ?></td>
            <td><?= date('d/m/Y', strtotime($utilisateur->dateCreation)) ?></td>
            <td class="actions">
                <a class="btn btn-sm" href="index.php?page=utilisateurs&modifier=<?= $utilisateur->id ?>">Modifier</a>
                <a class="btn btn-sm btn-danger" href="index.php?page=utilisateurs&supprimer=<?= $utilisateur->id ?>"
                   onclick="return confirm('Supprimer cet utilisateur ?');">Supprimer</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
