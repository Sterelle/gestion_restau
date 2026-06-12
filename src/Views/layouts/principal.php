<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RestoManager</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="app">
        <aside class="sidebar">
            <div class="logo"><span class="icon">🍽</span> RestoPro</div>
            <nav>
                <a href="index.php?page=accueil" class="active">Dashboard</a>
                <a href="index.php?page=commandes">Commandes</a>
                <a href="index.php?page=menus">Menu</a>
                <a href="index.php?page=recettes">Catégories</a>
                <a href="index.php?page=tables">Tables</a>
                <?php if ($role === 'admin' || $role === 'manager'): ?>
                    <a href="index.php?page=utilisateurs">Utilisateurs</a>
                <?php endif; ?>
            </nav>
            <div class="footer">Le Gourmet • <?= htmlspecialchars($nomUtilisateur ?? '') ?></div>
        </aside>

        <div class="main">
            <div class="topbar">
                <div class="search">Recherche…</div>
                <div class="user">
                    <div class="date"><?= date('d M Y') ?></div>
                    <img src="img/avatar.svg" alt="avatar">
                </div>
            </div>

            <div class="container">
                <?= $contenu ?>
            </div>

            <footer>
                <p>&copy; <?= date("Y") ?> RestoManager - Plateforme de gestion de restaurant</p>
            </footer>
        </div>
    </div>
</body>
</html>
