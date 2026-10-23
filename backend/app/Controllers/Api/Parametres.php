<?php

namespace App\Controllers\Api;

use App\Config\Gargote;

class Parametres extends ApiController
{
    public function index()
    {
        return $this->respond([
            'tables' => Gargote::$tables,
            'quantite_max' => Gargote::$quantiteMax,
            'delai_retard' => Gargote::$delaiRetard,
        ]);
    }
}
