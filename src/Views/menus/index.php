<h1>Gestion des menus</h1>

<?php if ($message): ?>
    <p class="succes"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<!-- ================= FORMULAIRE AJOUT / MODIFICATION ================= -->
<form class="formulaire" method="POST" action="index.php?page=menus">
</form>
<div class="panel">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
        <h1>Menus</h1>
        <a class="btn" href="index.php?page=menus&action=nouveau">Ajouter un plat</a>
    </div>

    <table>
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
