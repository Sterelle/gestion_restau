<h1>Gestion des tables du restaurant</h1>

<?php if ($message): ?>
    <p class="succes"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<!-- ================= FORMULAIRE AJOUT D'UNE TABLE ================= -->
<form class="formulaire" method="POST" action="index.php?page=tables">
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
    <?php foreach ($tables as $table): ?>
        <tr>
            <td>Table <?= $table->numero ?></td>
            <td><?= $table->capacite ?> places</td>
            <td><span class="badge badge-<?= $table->statut ?>"><?= ucfirst($table->statut) ?></span></td>
            <td>
                <form method="POST" action="index.php?page=tables" style="display:flex; gap:5px;">
                    <input type="hidden" name="id" value="<?= $table->id ?>">
                    <select name="statut" style="width:auto;">
                        <?php foreach ($statutsPossibles as $statut):
                            $selected = ($statut === $table->statut) ? 'selected' : '';
                        ?>
                            <option value="<?= $statut ?>" <?= $selected ?>><?= ucfirst($statut) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" name="changer_statut" class="btn-sm">OK</button>
                </form>
            </td>
            <td class="actions">
                <a class="btn btn-sm btn-danger" href="index.php?page=tables&supprimer=<?= $table->id ?>"
                   onclick="return confirm('Supprimer cette table ?');">Supprimer</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
