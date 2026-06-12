<?php

/**
 * Autoloader minimal de type PSR-4 pour le namespace App\.
 *
 * Évite une dépendance à Composer pour ce TP : associe le namespace
 * App\ au dossier src/.
 */
spl_autoload_register(function (string $classe) {
    $prefixe = 'App\\';
    $dossierBase = __DIR__ . '/src/';

    if (!str_starts_with($classe, $prefixe)) {
        return;
    }

    $chemin = $dossierBase . str_replace('\\', '/', substr($classe, strlen($prefixe))) . '.php';

    if (file_exists($chemin)) {
        require $chemin;
    }
});
