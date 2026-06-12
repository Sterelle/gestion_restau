<?php

/**
 * =========================================================
 * Contrôleur frontal (Front Controller)
 * =========================================================
 *
 * Point d'entrée unique de l'application. Il :
 *   1. initialise les dépendances communes (BDD, session, vues),
 *   2. câble les Repositories -> Services -> Controllers (Injection
 *      de dépendances manuelle, conforme au DIP),
 *   3. route la requête vers le bon contrôleur/action selon "page".
 *
 * Toutes les routes nécessitant une connexion sont protégées via
 * Controller::exigerConnexion(), elle-même appelée par chaque
 * contrôleur concerné.
 */

require_once __DIR__ . '/../autoload.php';

use App\Controllers\AuthController;
use App\Controllers\CommandeController;
use App\Controllers\DashboardController;
use App\Controllers\MenuController;
use App\Controllers\RecetteController;
use App\Controllers\TableController;
use App\Controllers\UtilisateurController;
use App\Core\Database;
use App\Core\Session;
use App\Core\View;
use App\Repositories\CategorieRepository;
use App\Repositories\CommandeRepository;
use App\Repositories\MenuRepository;
use App\Repositories\RecetteRepository;
use App\Repositories\TableRepository;
use App\Repositories\UtilisateurRepository;
use App\Services\AuthService;
use App\Services\CommandeService;
use App\Services\DashboardService;
use App\Services\MenuService;
use App\Services\RecetteService;
use App\Services\TableService;
use App\Services\UtilisateurService;

// ---------------------------------------------------------
// Initialisation des composants transverses
// ---------------------------------------------------------
$session = new Session();
$session->start();

$configBdd = require __DIR__ . '/../config/database.php';

try {
    $database = new Database(
        $configBdd['host'],
        $configBdd['utilisateur'],
        $configBdd['mot_de_passe'],
        $configBdd['base']
    );
} catch (\RuntimeException $e) {
    die($e->getMessage());
}

$view = new View(__DIR__ . '/../src/Views');

// Données communes à la mise en page (barre de navigation)
$view->definirDonneesGlobales([
    'role' => $session->get('role'),
    'nomUtilisateur' => $session->get('nom'),
]);

// ---------------------------------------------------------
// Construction des Repositories (implémentations concrètes)
// ---------------------------------------------------------
$utilisateurRepository = new UtilisateurRepository($database);
$categorieRepository = new CategorieRepository($database);
$menuRepository = new MenuRepository($database);
$recetteRepository = new RecetteRepository($database);
$tableRepository = new TableRepository($database);
$commandeRepository = new CommandeRepository($database);

// ---------------------------------------------------------
// Construction des Services (logique métier), injectés via
// les interfaces des Repositories (DIP)
// ---------------------------------------------------------
$authService = new AuthService($utilisateurRepository, $session);
$dashboardService = new DashboardService($commandeRepository, $menuRepository, $tableRepository);
$commandeService = new CommandeService($commandeRepository, $tableRepository, $menuRepository);
$menuService = new MenuService($menuRepository, $categorieRepository);
$recetteService = new RecetteService($recetteRepository, $menuRepository);
$tableService = new TableService($tableRepository);
$utilisateurService = new UtilisateurService($utilisateurRepository);

// ---------------------------------------------------------
// Construction des contrôleurs
// ---------------------------------------------------------
$authController = new AuthController($view, $session, $authService);
$dashboardController = new DashboardController($view, $session, $dashboardService);
$commandeController = new CommandeController($view, $session, $commandeService);
$menuController = new MenuController($view, $session, $menuService);
$recetteController = new RecetteController($view, $session, $recetteService);
$tableController = new TableController($view, $session, $tableService);
$utilisateurController = new UtilisateurController($view, $session, $utilisateurService);

// ---------------------------------------------------------
// Table des routes : "page" => [contrôleur, méthode]
// ---------------------------------------------------------
$routes = [
    'accueil' => [$dashboardController, 'index'],
    'connexion' => [$authController, 'connexion'],
    'deconnexion' => [$authController, 'deconnexion'],
    'commandes' => [$commandeController, 'index'],
    'commande_details' => [$commandeController, 'details'],
    'menus' => [$menuController, 'index'],
    'recettes' => [$recetteController, 'index'],
    'tables' => [$tableController, 'index'],
    'utilisateurs' => [$utilisateurController, 'index'],
];

// ---------------------------------------------------------
// Résolution de la route demandée et exécution
// ---------------------------------------------------------
$page = $_GET['page'] ?? 'accueil';

if (!isset($routes[$page])) {
    $page = $session->estConnecte() ? 'accueil' : 'connexion';
}

[$controller, $methode] = $routes[$page];
$controller->$methode();
