<?php
// =========================================================
// Fichier de connexion à la base de données
// A adapter si vos identifiants XAMPP sont différents
// =========================================================

$host = "localhost";
$utilisateur = "root";
$mot_de_passe = "";
$base = "restaurant_db";

// Création de la connexion
$conn = new mysqli($host, $utilisateur, $mot_de_passe, $base);

// Vérification de la connexion
if ($conn->connect_error) {
    die("Erreur de connexion à la base de données : " . $conn->connect_error);
}

// Encodage pour gérer correctement les accents
$conn->set_charset("utf8mb4");
?>
