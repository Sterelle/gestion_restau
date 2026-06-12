# RestoManager - Plateforme de gestion de restaurant

Projet basique en HTML / CSS / PHP / MySQL conçu pour XAMPP, destiné à un débutant.

## 📁 Structure du projet

```
restaurant_app/
├── config.php              -> connexion à la base de données
├── database.sql            -> script SQL à importer (tables + données de test)
├── login.php                -> page de connexion
├── logout.php               -> déconnexion
├── header.php / footer.php  -> structure commune (menu de navigation)
├── index.php                -> tableau de bord (statistiques)
├── menus.php                -> gestion des plats / menus (CRUD)
├── recettes.php             -> gestion des recettes liées aux plats (CRUD)
├── tables.php               -> gestion des tables du restaurant
├── commandes.php            -> gestion des commandes (création + statut)
├── commande_details.php     -> détail d'une commande (ajout/retrait de plats)
├── utilisateurs.php         -> gestion des utilisateurs (admin uniquement)
└── css/
    └── style.css             -> mise en forme générale
```

## ⚙️ Installation avec XAMPP

1. Démarrez **Apache** et **MySQL** depuis le panneau de contrôle XAMPP.
2. Copiez le dossier `restaurant_app` dans `C:\xampp\htdocs\` (Windows)
   ou `/Applications/XAMPP/htdocs/` (Mac).
3. Ouvrez **phpMyAdmin** (http://localhost/phpmyadmin).
4. Créez une nouvelle base ou importez directement le fichier `database.sql`
   (il crée la base `restaurant_db` automatiquement avec l'option
   "Importer" dans phpMyAdmin).
5. Ouvrez votre navigateur à l'adresse :
   `http://localhost/restaurant_app/login.php`

## 🔑 Compte de démonstration

- **Email** : admin@resto.com
- **Mot de passe** : admin123
- **Rôle** : admin (accès complet, y compris la gestion des utilisateurs)

D'autres comptes de test (même mot de passe `admin123`) :
- jean@resto.com (rôle : serveur)
- paul@resto.com (rôle : cuisinier)

## 🍽️ Fonctionnalités incluses

- **Authentification** : connexion / déconnexion avec sessions PHP et mots
  de passe hachés (`password_hash` / `password_verify`).
- **Gestion des utilisateurs** (admin) : ajout, modification, suppression,
  gestion des rôles (admin, manager, serveur, cuisinier).
- **Gestion des menus** : ajout/modification/suppression des plats,
  catégories, prix, disponibilité.
- **Gestion des recettes** : fiche technique liée à chaque plat
  (ingrédients, temps de préparation, instructions).
- **Gestion des tables** : ajout de tables, changement de statut
  (libre / occupée / réservée).
- **Gestion des commandes** :
  - création d'une commande liée à une table,
  - ajout/retrait de plats dans la commande,
  - calcul automatique du total,
  - changement du statut (en attente, en préparation, servie, payée, annulée),
  - libération automatique de la table une fois la commande payée ou annulée.
- **Tableau de bord** : statistiques en temps réel (commandes en attente,
  tables occupées, chiffre d'affaires du jour, dernières commandes).

## 🛠️ Pistes d'amélioration (pour aller plus loin)

- Ajouter la gestion du stock / des ingrédients.
- Ajouter l'impression de tickets de caisse (PDF).
- Ajouter des statistiques par période (jour, semaine, mois) avec des graphiques.
- Ajouter la possibilité d'uploader des photos pour chaque plat.
# gestion_restau
