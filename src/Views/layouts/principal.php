<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RestoManager</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header class="navbar">
        <div class="logo">🍽️ RestoManager</div>
        <nav>
            <a href="index.php?page=accueil">Tableau de bord</a>
            <a href="index.php?page=commandes">Commandes</a>
            <a href="index.php?page=menus">Menus</a>
            <a href="index.php?page=recettes">Recettes</a>
            <a href="index.php?page=tables">Tables</a>
            <?php if ($role === 'admin' || $role === 'manager'): ?>
                <a href="index.php?page=utilisateurs">Utilisateurs</a>
            <?php endif; ?>
            <a href="index.php?page=deconnexion">Déconnexion (<?= htmlspecialchars($nomUtilisateur) ?>)</a>
        </nav>
    </header>
    <main class="container">
        <?= $contenu ?>
    </main>
    <footer>
        <p>&copy; <?= date("Y") ?> RestoManager - Plateforme de gestion de restaurant</p>
    </footer>
</body>
</html>
