<div class="panel">
    <h1>Commande #<?= $commande->id ?> - Table <?= htmlspecialchars((string) ($commande->tableNumero ?? '-')) ?></h1>
    <p>
        Serveur : <strong><?= htmlspecialchars($commande->serveurNom ?? '-') ?></strong> |
        Statut : <span class="badge badge-<?= $commande->statut ?>"><?= $commande->statutLisible() ?></span> |
        Date : <?= date('d/m/Y H:i', strtotime($commande->dateCommande)) ?>
    </p>

    <?php if ($message): ?>
        <p class="succes"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form class="formulaire" method="POST" action="index.php?page=commande_details&id=<?= $commande->id ?>">
        <h3>Ajouter un plat à la commande</h3>

        <label for="menu_id">Plat</label>
        <select id="menu_id" name="menu_id" required>
            <option value="">-- Choisir un plat --</option>
            <?php foreach ($plats as $plat): ?>
                <option value="<?= $plat->id ?>"><?= htmlspecialchars($plat->nom) ?> - <?= $plat->prixFormate() ?></option>
            <?php endforeach; ?>
        </select>

        <label for="quantite">Quantité</label>
        <input type="number" id="quantite" name="quantite" min="1" value="1" required>

        <button type="submit" name="ajouter_plat" class="btn">Ajouter</button>
    </form>
</div>

<h2>Articles commandés</h2>
<div class="panel">
<table>
    <tr>
        <th>Plat</th>
        <th>Quantité</th>
        <th>Prix unitaire</th>
        <th>Sous-total</th>
        <th>Action</th>
    </tr>
    <?php foreach ($lignes as $ligne): ?>
        <tr>
            <td><?= htmlspecialchars($ligne->menuNom) ?></td>
            <td><?= $ligne->quantite ?></td>
            <td><?= $ligne->prixUnitaireFormate() ?></td>
            <td><?= $ligne->sousTotalFormate() ?></td>
            <td class="actions">
                <a class="btn btn-sm btn-danger" href="index.php?page=commande_details&id=<?= $commande->id ?>&supprimer_ligne=<?= $ligne->id ?>"
                   onclick="return confirm('Retirer cet article ?');">Retirer</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<h3>Total de la commande : <?= number_format($total, 0, ',', ' ') ?> FCFA</h3>

<a class="btn btn-secondaire" href="index.php?page=commandes">Retour aux commandes</a>
