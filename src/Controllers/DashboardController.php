<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Core\View;
use App\Services\DashboardService;

class DashboardController extends Controller
{
    private DashboardService $dashboardService;

    public function __construct(View $view, Session $session, DashboardService $dashboardService)
    {
        parent::__construct($view, $session);
        $this->dashboardService = $dashboardService;
    }

    public function index(): void
    {
        $this->exigerConnexion();

        $stats = $this->dashboardService->statistiques();

        $this->rendre('dashboard/index', $stats);
    }
}
