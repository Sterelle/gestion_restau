<?php
session_start();
require_once 'config.php';

// Si déjà connecté, rediriger vers le tableau de bord
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$erreur = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $mot_de_passe = $_POST['mot_de_passe'];

    $sql = "SELECT * FROM utilisateurs WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultat = $stmt->get_result();

    if ($resultat->num_rows === 1) {
        $utilisateur = $resultat->fetch_assoc();

        if (password_verify($mot_de_passe, $utilisateur['mot_de_passe'])) {
            // Connexion réussie : on enregistre les infos en session
            $_SESSION['user_id'] = $utilisateur['id'];
            $_SESSION['nom'] = $utilisateur['nom'];
            $_SESSION['role'] = $utilisateur['role'];

            header("Location: index.php");
            exit();
        } else {
            $erreur = "Mot de passe incorrect.";
        }
    } else {
        $erreur = "Aucun utilisateur trouvé avec cet email.";
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - RestoManager</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="login-box">
        <h2>🍽️ RestoManager</h2>
        <p>Connexion à votre espace</p>

        <?php if ($erreur): ?>
            <p class="erreur"><?= htmlspecialchars($erreur) ?></p>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="email">Adresse email</label>
            <input type="email" id="email" name="email" required>

            <label for="mot_de_passe">Mot de passe</label>
            <input type="password" id="mot_de_passe" name="mot_de_passe" required>

            <button type="submit">Se connecter</button>
        </form>

        <p style="margin-top:15px; font-size:13px; color:#7f8c8d;">
            Compte de démonstration :<br>
            admin@resto.com / admin123
        </p>
    </div>
</body>
</html>
