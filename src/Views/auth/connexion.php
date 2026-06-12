<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - RestoManager</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
    <div class="login-box" style="background: #0f1724; border:1px solid rgba(255,255,255,0.03);">
        <h2 style="color:#fff;">🍽️ RestoManager</h2>
        <p style="color:#9aa4b2;">Connexion à votre espace</p>

        <?php if ($erreur): ?>
            <p class="erreur"><?= htmlspecialchars($erreur) ?></p>
        <?php endif; ?>

        <form method="POST" action="index.php?page=connexion">
            <label for="email">Adresse email</label>
            <input type="email" id="email" name="email" required>

            <label for="mot_de_passe">Mot de passe</label>
            <input type="password" id="mot_de_passe" name="mot_de_passe" required>

            <button type="submit" class="btn">Se connecter</button>
        </form>

        <p style="margin-top:15px; font-size:13px; color:#7f8c8d;">
            Compte de démonstration :<br>
            admin@resto.com / admin123
        </p>
    </div>
</body>
</html>
