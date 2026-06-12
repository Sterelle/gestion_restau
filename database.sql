-- =========================================================
-- Base de données : restaurant_db
-- A importer dans phpMyAdmin (XAMPP)
-- =========================================================

CREATE DATABASE IF NOT EXISTS restaurant_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE restaurant_db;

-- ---------------------------------------------------------
-- Table : utilisateurs
-- ---------------------------------------------------------
CREATE TABLE utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    role ENUM('admin','manager','serveur','cuisinier') NOT NULL DEFAULT 'serveur',
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ---------------------------------------------------------
-- Table : categories (catégories de menu : entrées, plats, boissons...)
-- ---------------------------------------------------------
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL
);

-- ---------------------------------------------------------
-- Table : menus (les plats / articles proposés)
-- ---------------------------------------------------------
CREATE TABLE menus (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(150) NOT NULL,
    description TEXT,
    prix DECIMAL(10,2) NOT NULL,
    categorie_id INT,
    disponible TINYINT(1) DEFAULT 1,
    FOREIGN KEY (categorie_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- ---------------------------------------------------------
-- Table : recettes (fiche technique d'un plat)
-- ---------------------------------------------------------
CREATE TABLE recettes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    menu_id INT NOT NULL,
    ingredients TEXT NOT NULL,
    temps_preparation INT DEFAULT 0 COMMENT 'en minutes',
    instructions TEXT,
    FOREIGN KEY (menu_id) REFERENCES menus(id) ON DELETE CASCADE
);

-- ---------------------------------------------------------
-- Table : tables_restaurant (les tables physiques de la salle)
-- ---------------------------------------------------------
CREATE TABLE tables_restaurant (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero INT NOT NULL,
    capacite INT DEFAULT 4,
    statut ENUM('libre','occupee','reservee') DEFAULT 'libre'
);

-- ---------------------------------------------------------
-- Table : commandes
-- ---------------------------------------------------------
CREATE TABLE commandes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    table_id INT,
    utilisateur_id INT,
    statut ENUM('en_attente','en_preparation','servie','payee','annulee') DEFAULT 'en_attente',
    date_commande TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    total DECIMAL(10,2) DEFAULT 0,
    FOREIGN KEY (table_id) REFERENCES tables_restaurant(id) ON DELETE SET NULL,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE SET NULL
);

-- ---------------------------------------------------------
-- Table : commande_details (lignes / articles d'une commande)
-- ---------------------------------------------------------
CREATE TABLE commande_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    commande_id INT NOT NULL,
    menu_id INT NOT NULL,
    quantite INT NOT NULL DEFAULT 1,
    prix_unitaire DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (commande_id) REFERENCES commandes(id) ON DELETE CASCADE,
    FOREIGN KEY (menu_id) REFERENCES menus(id)
);

-- =========================================================
-- Données de démonstration
-- =========================================================

-- Compte admin par défaut : email = admin@resto.com / mot de passe = admin123
INSERT INTO utilisateurs (nom, email, mot_de_passe, role) VALUES
('Administrateur', 'admin@resto.com', '$2b$12$.zV2tmy8Hln.4.E.DOZaDuex9rZYF4e0PXw98vjEeSnimIMaCw3kW', 'admin'),
('Jean Serveur', 'jean@resto.com', '$2b$12$.zV2tmy8Hln.4.E.DOZaDuex9rZYF4e0PXw98vjEeSnimIMaCw3kW', 'serveur'),
('Paul Cuisinier', 'paul@resto.com', '$2b$12$.zV2tmy8Hln.4.E.DOZaDuex9rZYF4e0PXw98vjEeSnimIMaCw3kW', 'cuisinier');

INSERT INTO categories (nom) VALUES
('Entrées'),
('Plats principaux'),
('Desserts'),
('Boissons');

INSERT INTO menus (nom, description, prix, categorie_id, disponible) VALUES
('Salade César', 'Salade, poulet grillé, parmesan, sauce césar', 2500, 1, 1),
('Poulet DG', 'Poulet sauté aux légumes et plantains', 4500, 2, 1),
('Poisson braisé', 'Poisson braisé accompagné de bâton de manioc', 5000, 2, 1),
('Tiramisu', 'Dessert italien au café et mascarpone', 1500, 3, 1),
('Jus de gingembre', 'Boisson rafraîchissante maison', 800, 4, 1);

INSERT INTO recettes (menu_id, ingredients, temps_preparation, instructions) VALUES
(1, 'Laitue, poulet, parmesan, croûtons, sauce césar', 15, 'Griller le poulet, mélanger tous les ingrédients avec la sauce et servir frais.'),
(2, 'Poulet, plantains, carottes, poivrons, oignons, épices', 30, 'Faire frire le poulet et les plantains, sauter les légumes, puis mélanger le tout.'),
(3, 'Poisson, épices, manioc', 40, 'Mariner le poisson, le braiser sur la braise, et préparer le bâton de manioc.');

INSERT INTO tables_restaurant (numero, capacite, statut) VALUES
(1, 2, 'libre'),
(2, 4, 'libre'),
(3, 4, 'libre'),
(4, 6, 'libre'),
(5, 2, 'libre');
