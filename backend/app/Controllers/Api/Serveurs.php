<?php

namespace App\Controllers\Api;

use App\Models\ServeurModel;

class Serveurs extends ApiController
{
    private ServeurModel $serveurs;

    public function __construct()
    {
        $this->serveurs = model(ServeurModel::class);
    }

    public function index()
    {
        return $this->respond($this->serveurs->listerActifs());
    }
}
