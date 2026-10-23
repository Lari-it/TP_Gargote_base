<?php

namespace App\Services;

use App\Exceptions\RegleMetierException;
use App\Models\PlatDuJourModel;
use App\Models\PlatModel;

/**
 * Service pour la gestion du plat du jour
 */
class PlatDuJourService
{
    /**
     * Lit le plat du jour d aujourd'hui
     */
    public function lire(): ?array
    {
        $model = model(PlatDuJourModel::class);
        $platDuJour = $model->where('date_jour', date('Y-m-d'))->first();

        if (!$platDuJour) {
            return null;
        }

        $platModel = model(PlatModel::class);
        $plat = $platModel->find($platDuJour['id_plat']);

        return $plat;
    }

    /**
     * Definit ou remplace le plat du jour d aujourd'hui
     */
    public function definir(int $idPlat): array
    {
        $platModel = model(PlatModel::class);
        $plat = $platModel->find($idPlat);

        // R1 : le plat existe
        if (!$plat) {
            throw new RegleMetierException('Ce plat n existe pas.', [
                'id_plat' => 'Ce plat n existe pas.'
            ], 422);
        }

        // R2 : un plat epuise ne peut pas devenir plat du jour
        if (!$plat['disponible']) {
            throw new RegleMetierException(
                "Le {$plat['nom']} est épuisé : il ne peut pas être plat du jour.",
                [],
                409
            );
        }

        // R3 : choisir un plat du jour alors qu il en existe deja aujourd'hui le remplace
        $model = model(PlatDuJourModel::class);
        $db = db_connect();

        $sql = "INSERT INTO plat_du_jour (date_jour, id_plat) VALUES (CURRENT_DATE, ?)
                ON CONFLICT (date_jour) DO UPDATE SET id_plat = EXCLUDED.id_plat";
        
        $db->query($sql, [$idPlat]);

        return $plat;
    }

    /**
     * Historique des 7 derniers jours
     */
    public function historique(): array
    {
        $model = model(PlatDuJourModel::class);
        $platModel = model(PlatModel::class);

        $historique = $model
            ->where('date_jour >=', date('Y-m-d', strtotime('-6 days')))
            ->orderBy('date_jour', 'DESC')
            ->findAll();

        foreach ($historique as &$entry) {
            $plat = $platModel->find($entry['id_plat']);
            $entry['plat'] = $plat;
        }

        return $historique;
    }
}
