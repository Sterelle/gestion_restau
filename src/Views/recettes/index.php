<h1>Gestion des recettes</h1>

<?php if ($message): ?>
    <p class="succes"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<!-- ================= FORMULAIRE AJOUT / MODIFICATION ================= -->
<form class="formulaire" method="POST" action="index.php?page=recettes">
    <h3><?= $recetteAModifier ? "Modifier la recette" : "Ajouter une nouvelle recette" ?></h3>

    <?php if ($recetteAModifier): ?>
        <input type="hidden" name="id" value="<?= $recetteAModifier->id ?>">
    <?php endif; ?>

    <label for="menu_id">Plat concerné</label>
    <select id="menu_id" name="menu_id" required>
        <option value="">-- Choisir un plat --</option>
        <?php foreach ($plats as $plat):
            $selected = ($recetteAModifier !== null && $recetteAModifier->menuId == $plat->id) ? 'selected' : '';
        ?>
            <option value="<?= $plat->id ?>" <?= $selected ?>><?= htmlspecialchars($plat->nom) ?></option>
        <?php endforeach; ?>
    </select>

    <label for="ingredients">Ingrédients (un par ligne ou séparés par des virgules)</label>
    <textarea id="ingredients" name="ingredients" rows="3" required><?= htmlspecialchars($recetteAModifier->ingredients ?? '') ?></textarea>

    <label for="temps_preparation">Temps de préparation (en minutes)</label>
    <input type="number" id="temps_preparation" name="temps_preparation" min="0"
           value="<?= htmlspecialchars($recetteAModifier->tempsPreparation ?? '') ?>">

    <label for="instructions">Instructions / étapes de préparation</label>
    <textarea id="instructions" name="instructions" rows="4"><?= htmlspecialchars($recetteAModifier->instructions ?? '') ?></textarea>

    <?php if ($recetteAModifier): ?>
        <button type="submit" name="modifier">Enregistrer les modifications</button>
        <a class="btn btn-secondaire" href="index.php?page=recettes">Annuler</a>
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
    <?php foreach ($recettes as $recette): ?>
        <tr>
            <td><?= htmlspecialchars($recette->menuNom) ?></td>
            <td><?= htmlspecialchars(mb_strimwidth($recette->ingredients, 0, 60, '...')) ?></td>
            <td><?= $recette->tempsPreparation ?></td>
            <td class="actions">
                <a class="btn btn-sm" href="index.php?page=recettes&modifier=<?= $recette->id ?>">Modifier</a>
                <a class="btn btn-sm btn-danger" href="index.php?page=recettes&supprimer=<?= $recette->id ?>"
                   onclick="return confirm('Supprimer cette recette ?');">Supprimer</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
