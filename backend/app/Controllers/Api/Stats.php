<?php

namespace App\Controllers\Api;

use App\Services\StatsService;

class Stats extends ApiController
{
    private StatsService $stats;

    public function __construct()
    {
        $this->stats = new StatsService();
    }

    public function index()
    {
        return $this->respond($this->stats->obtenirStats());
    }
}
