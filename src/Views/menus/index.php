<h1>Gestion des menus</h1>

<?php if ($message): ?>
    <p class="succes"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<!-- ================= FORMULAIRE AJOUT / MODIFICATION ================= -->
<form class="formulaire" method="POST" action="index.php?page=menus">
    <h3><?= $menuAModifier ? "Modifier le plat" : "Ajouter un nouveau plat" ?></h3>

    <?php if ($menuAModifier): ?>
        <input type="hidden" name="id" value="<?= $menuAModifier->id ?>">
    <?php endif; ?>

    <label for="nom">Nom du plat</label>
    <input type="text" id="nom" name="nom" required
           value="<?= htmlspecialchars($menuAModifier->nom ?? '') ?>">

    <label for="description">Description</label>
    <textarea id="description" name="description" rows="3"><?= htmlspecialchars($menuAModifier->description ?? '') ?></textarea>

    <label for="prix">Prix (FCFA)</label>
    <input type="number" step="0.01" id="prix" name="prix" required
           value="<?= htmlspecialchars($menuAModifier->prix ?? '') ?>">

    <label for="categorie_id">Catégorie</label>
    <select id="categorie_id" name="categorie_id" required>
        <option value="">-- Choisir une catégorie --</option>
        <?php foreach ($categories as $cat):
            $selected = ($menuAModifier !== null && $menuAModifier->categorieId == $cat->id) ? 'selected' : '';
        ?>
            <option value="<?= $cat->id ?>" <?= $selected ?>><?= htmlspecialchars($cat->nom) ?></option>
        <?php endforeach; ?>
    </select>

    <label>
        <input type="checkbox" name="disponible" style="width:auto; display:inline;"
            <?= ($menuAModifier === null || $menuAModifier->disponible) ? 'checked' : '' ?>>
        Disponible à la vente
    </label>

    <?php if ($menuAModifier): ?>
        <button type="submit" name="modifier">Enregistrer les modifications</button>
        <a class="btn btn-secondaire" href="index.php?page=menus">Annuler</a>
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
    <?php foreach ($menus as $menu): ?>
        <tr>
            <td><?= htmlspecialchars($menu->nom) ?></td>
            <td><?= htmlspecialchars($menu->categorieNom ?? '-') ?></td>
            <td><?= $menu->prixFormate() ?></td>
            <td><?= $menu->disponible ? 'Oui' : 'Non' ?></td>
            <td class="actions">
                <a class="btn btn-sm" href="index.php?page=menus&modifier=<?= $menu->id ?>">Modifier</a>
                <a class="btn btn-sm btn-danger" href="index.php?page=menus&supprimer=<?= $menu->id ?>"
                   onclick="return confirm('Supprimer ce plat ?');">Supprimer</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
