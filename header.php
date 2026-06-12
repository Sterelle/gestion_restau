<?php
// =========================================================
// En-tête commun : démarrage de session, vérification de
// connexion et affichage de la barre de navigation
// =========================================================
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'config.php';

$role_utilisateur = $_SESSION['role'];
?>
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
            <a href="index.php">Tableau de bord</a>
            <a href="commandes.php">Commandes</a>
            <a href="menus.php">Menus</a>
            <a href="recettes.php">Recettes</a>
            <a href="tables.php">Tables</a>
            <?php if ($role_utilisateur === 'admin' || $role_utilisateur === 'manager'): ?>
                <a href="utilisateurs.php">Utilisateurs</a>
            <?php endif; ?>
            <a href="logout.php">Déconnexion (<?= htmlspecialchars($_SESSION['nom']) ?>)</a>
        </nav>
    </header>
    <main class="container">
