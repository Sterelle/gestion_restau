# RestoManager — Version MVC / SOLID

## Installation (XAMPP)

1. Copier le dossier `mvc` dans `htdocs` (ex : `htdocs/restaurant_app`).
2. Importer `database.sql` dans phpMyAdmin (crée la base `restaurant_db`).
3. Vérifier les identifiants dans `config/database.php` (par défaut : `root` / mot de passe vide).
4. Ouvrir `http://localhost/restaurant_app/public/index.php`.
5. Connexion : `admin@resto.com` / `admin123`.

## Architecture MVC

```
mvc/
├── autoload.php              # autoloader PSR-4 (namespace App\)
├── config/database.php       # configuration BDD
├── database.sql
├── public/
│   ├── index.php              # Front Controller (point d'entrée, routage, DI)
│   └── css/style.css
└── src/
    ├── Core/                  # Composants transverses
    │   ├── Database.php       # connexion mysqli
    │   ├── Session.php        # gestion de session
    │   ├── View.php            # moteur de rendu
    │   └── Controller.php      # contrôleur abstrait (helpers communs)
    ├── Models/                # Entités (Utilisateur, Menu, Commande, ...)
    ├── Repositories/          # Accès aux données (interfaces + implémentations MySQL)
    ├── Services/              # Logique métier (règles, calculs, validations)
    ├── Controllers/           # Contrôleurs (orchestrent Service <-> Vue)
    └── Views/                 # Templates PHP (présentation uniquement)
```

## Respect des principes SOLID

- **S — Single Responsibility** : chaque classe a un seul rôle.
  `Database` (connexion), `Session` (état HTTP), `View` (rendu),
  un `Repository` par entité (persistance), un `Service` par domaine
  (règles métier), un `Controller` par domaine (orchestration HTTP).

- **O — Open/Closed** : les contrôleurs et services dépendent
  d'interfaces (`*RepositoryInterface`). On peut ajouter une nouvelle
  implémentation (ex : cache, autre SGBD, jeu de tests) sans modifier
  le code existant.

- **L — Liskov Substitution** : toute implémentation d'une interface
  de Repository (ex : `MenuRepository`) peut remplacer une autre sans
  casser le `MenuService` qui l'utilise, car les contrats (signatures
  et types de retour) sont respectés.

- **I — Interface Segregation** : chaque Repository a sa propre
  interface, restreinte aux opérations réellement nécessaires à son
  entité (pas d'interface fourre-tout `RepositoryInterface` générique).

- **D — Dependency Inversion** : les `Services` et `Controllers`
  dépendent des interfaces (`UtilisateurRepositoryInterface`,
  `CommandeRepositoryInterface`, ...), et c'est le Front Controller
  (`public/index.php`) qui injecte les implémentations concrètes
  (Injection de Dépendances manuelle, sans framework).

## Flux d'une requête

1. `public/index.php` reçoit la requête, lit `?page=...`.
2. Le routeur associe la page à `[Controller, méthode]`.
3. Le `Controller` valide la session/rôle, lit `$_GET`/`$_POST`,
   appelle le `Service` correspondant.
4. Le `Service` applique les règles métier en s'appuyant sur les
   `Repository` (via leurs interfaces).
5. Le `Controller` transmet les données (Modèles) à la `View`, qui
   les insère dans le layout commun (`layouts/principal.php`).

## Différences avec la version originale

- Code SQL et HTML précédemment mélangés dans des fichiers uniques
  (ex : `commandes.php`) sont désormais séparés en couches distinctes.
- Le hachage des mots de passe et le recalcul des totaux de commande
  sont centralisés dans les Services (évite la duplication).
- Les règles métier (ex : libérer une table quand une commande est
  payée/annulée) sont déplacées du contrôleur vers `CommandeService`.
