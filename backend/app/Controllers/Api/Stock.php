<?php

namespace App\Controllers\Api;

use App\Services\StockService;

class Stock extends ApiController
{
    private StockService $stock;

    public function __construct()
    {
        $this->stock = new StockService();
    }

    public function index()
    {
        return $this->respond($this->stock->listerStock());
    }
}
