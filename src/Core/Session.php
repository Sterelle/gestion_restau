<?php

namespace App\Core;

/**
 * Encapsule la gestion de la session PHP.
 *
 * SRP : centralise toute manipulation de $_SESSION afin que les
 * contrôleurs n'aient pas à connaître les détails de l'API native.
 */
class Session
{
    public function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function set(string $cle, $valeur): void
    {
        $_SESSION[$cle] = $valeur;
    }

    public function get(string $cle, $defaut = null)
    {
        return $_SESSION[$cle] ?? $defaut;
    }

    public function has(string $cle): bool
    {
        return isset($_SESSION[$cle]);
    }

    public function destroy(): void
    {
        $_SESSION = [];
        session_destroy();
    }

    public function estConnecte(): bool
    {
        return $this->has('user_id');
    }
}
