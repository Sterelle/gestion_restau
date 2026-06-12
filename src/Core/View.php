<?php

namespace App\Core;

/**
 * Moteur de rendu de vues très simple.
 *
 * SRP : seule responsabilité = transformer un nom de vue + des données
 * en HTML, en insérant éventuellement le résultat dans une mise en page.
 */
class View
{
    private string $dossierVues;

    /** @var array<string, mixed> Données injectées dans toutes les vues avec layout (ex : utilisateur connecté) */
    private array $donneesGlobales = [];

    public function __construct(string $dossierVues)
    {
        $this->dossierVues = rtrim($dossierVues, '/');
    }

    /**
     * Définit des données globales fusionnées automatiquement avec
     * celles fournies à rendre() (ex : informations de la barre de navigation).
     *
     * @param array<string, mixed> $donnees
     */
    public function definirDonneesGlobales(array $donnees): void
    {
        $this->donneesGlobales = $donnees;
    }

    /**
     * Rend une vue avec mise en page (header/footer communs).
     *
     * @param array<string, mixed> $donnees
     */
    public function rendre(string $vue, array $donnees = [], ?string $layout = 'layouts/principal'): string
    {
        $contenu = $this->rendreFichier($vue, $donnees);

        if ($layout === null) {
            return $contenu;
        }

        $donneesLayout = array_merge($this->donneesGlobales, $donnees);
        $donneesLayout['contenu'] = $contenu;

        return $this->rendreFichier($layout, $donneesLayout);
    }

    /**
     * Rend une vue sans mise en page (utile pour les pages de connexion).
     *
     * @param array<string, mixed> $donnees
     */
    public function rendreSeul(string $vue, array $donnees = []): string
    {
        return $this->rendreFichier($vue, $donnees);
    }

    /**
     * @param array<string, mixed> $donnees
     */
    private function rendreFichier(string $vue, array $donnees): string
    {
        $chemin = $this->dossierVues . '/' . $vue . '.php';

        if (!file_exists($chemin)) {
            throw new \RuntimeException("Vue introuvable : $chemin");
        }

        extract($donnees, EXTR_SKIP);

        ob_start();
        require $chemin;
        return ob_get_clean();
    }
}
