<?php

namespace App\Core;

/**
 * Contrôleur de base : fournit des méthodes utilitaires communes
 * (rendu de vue, redirection, accès à la session).
 *
 * Les contrôleurs concrets héritent de cette classe (DRY) mais
 * restent libres d'implémenter leur propre logique métier en
 * délégant aux Services (SRP / dépendance vers les abstractions).
 */
abstract class Controller
{
    protected View $view;
    protected Session $session;

    public function __construct(View $view, Session $session)
    {
        $this->view = $view;
        $this->session = $session;
    }

    protected function rendre(string $vue, array $donnees = [], ?string $layout = 'layouts/principal'): void
    {
        echo $this->view->rendre($vue, $donnees, $layout);
    }

    protected function rendreSeul(string $vue, array $donnees = []): void
    {
        echo $this->view->rendreSeul($vue, $donnees);
    }

    protected function rediriger(string $url): void
    {
        header("Location: $url");
        exit();
    }

    /**
     * Vérifie que l'utilisateur est connecté, sinon redirige vers la connexion.
     */
    protected function exigerConnexion(): void
    {
        if (!$this->session->estConnecte()) {
            $this->rediriger('index.php?page=connexion');
        }
    }

    /**
     * Vérifie que l'utilisateur possède un des rôles autorisés.
     *
     * @param string[] $rolesAutorises
     */
    protected function exigerRole(array $rolesAutorises): bool
    {
        return in_array($this->session->get('role'), $rolesAutorises, true);
    }
}
