<?php

namespace App\Controllers\Api;

use App\Services\PlatDuJourService;

class PlatDuJour extends ApiController
{
    private PlatDuJourService $service;

    public function __construct()
    {
        $this->service = new PlatDuJourService();
    }

    /**
     * GET /api/plat-du-jour
     * Le plat du jour d aujourd'hui, ou null s il n est pas encore choisi
     */
    public function index()
    {
        return $this->respond($this->service->lire());
    }

    /**
     * PUT /api/plat-du-jour
     * Corps {"id_plat": 4} : choisit ou remplace le plat du jour d aujourd'hui
     */
    public function update()
    {
        $donnees = $this->donneesJson();
        $idPlat = $donnees['id_plat'] ?? null;

        if ($idPlat === null) {
            return $this->erreurValidation(['id_plat' => 'L id du plat est obligatoire.']);
        }

        try {
            $plat = $this->service->definir((int) $idPlat);
            return $this->respond($plat);
        } catch (\App\Exceptions\RegleMetierException $e) {
            return $this->erreur($e->getMessage(), $e->getCode(), $e->getErrors());
        }
    }

    /**
     * GET /api/plat-du-jour/historique
     * Les 7 derniers jours, du plus recent au plus ancien
     */
    public function historique()
    {
        return $this->respond($this->service->historique());
    }
}
