<h1>Gestion des commandes</h1>

<?php if ($message): ?>
    <p class="succes"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<!-- ================= FORMULAIRE NOUVELLE COMMANDE ================= -->
<form class="formulaire" method="POST" action="index.php?page=commandes">
    <h3>Créer une nouvelle commande</h3>
    <label for="table_id">Table</label>
    <select id="table_id" name="table_id" required>
        <option value="">-- Choisir une table libre --</option>
        <?php foreach ($tablesLibres as $table): ?>
            <option value="<?= $table->id ?>">Table <?= $table->numero ?> (<?= $table->capacite ?> places)</option>
        <?php endforeach; ?>
    </select>
    <button type="submit" name="creer_commande">Créer la commande</button>
</form>

<!-- ================= LISTE DES COMMANDES ================= -->
<h2>Toutes les commandes</h2>
<table>
    <tr>
        <th>N°</th>
        <th>Table</th>
        <th>Serveur</th>
        <th>Total</th>
        <th>Statut</th>
        <th>Date</th>
        <th>Actions</th>
    </tr>
    <?php foreach ($commandes as $cmd): ?>
        <tr>
            <td>#<?= $cmd->id ?></td>
            <td><?= $cmd->tableNumero !== null ? "Table " . $cmd->tableNumero : '-' ?></td>
            <td><?= htmlspecialchars($cmd->serveurNom ?? '-') ?></td>
            <td><?= $cmd->totalFormate() ?></td>
            <td>
                <form method="POST" action="index.php?page=commandes" style="display:flex; gap:5px;">
                    <input type="hidden" name="id" value="<?= $cmd->id ?>">
                    <select name="statut" style="width:auto;">
                        <?php foreach ($statuts as $statut):
                            $selected = ($statut === $cmd->statut) ? 'selected' : '';
                        ?>
                            <option value="<?= $statut ?>" <?= $selected ?>><?= str_replace('_', ' ', $statut) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" name="changer_statut" class="btn-sm">OK</button>
                </form>
            </td>
            <td><?= date('d/m/Y H:i', strtotime($cmd->dateCommande)) ?></td>
            <td class="actions">
                <a class="btn btn-sm" href="index.php?page=commande_details&id=<?= $cmd->id ?>">Détails</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
