<?php

namespace App\Services;

use App\Exceptions\RegleMetierException;
use App\Models\IngredientModel;

class StockService
{
    public function listerStock(): array
    {
        return model(IngredientModel::class)->listerAvecStock();
    }

    public function consommerPourCommande(int $idCommande): void
    {
        $db = db_connect();
        $commande = $db->table('commande_ligne cl')
            ->select('cl.id_plat, cl.quantite')
            ->where('cl.id_commande', $idCommande)
            ->get()->getResultArray();

        foreach ($commande as $ligne) {
            $recettes = $db->table('recette r')
                ->select('r.id_ingredient, r.quantite')
                ->where('r.id_plat', $ligne['id_plat'])
                ->get()->getResultArray();

            foreach ($recettes as $recette) {
                $quantiteConso = $recette['quantite'] * $ligne['quantite'];
                
                $db->table('mouvement_stock')->insert([
                    'id_ingredient' => $recette['id_ingredient'],
                    'quantite' => -$quantiteConso,
                    'motif' => "Commande #{$idCommande}",
                ]);
            }
        }
    }

    public function verifierStockPourPlat(int $idPlat, int $quantite): void
    {
        $db = db_connect();
        $recettes = $db->table('recette r')
            ->select('r.id_ingredient, r.quantite')
            ->where('r.id_plat', $idPlat)
            ->get()->getResultArray();

        foreach ($recettes as $recette) {
            $stock = $db->table('v_stock_actuel')
                ->select('stock')
                ->where('id', $recette['id_ingredient'])
                ->get()->getRowArray();

            if ($stock === null) {
                throw new RegleMetierException('Ingrédient introuvable.', [], 404);
            }

            $besoin = $recette['quantite'] * $quantite;
            if ($stock['stock'] < $besoin) {
                throw new RegleMetierException(
                    'Stock insuffisant pour un ingrédient.',
                    [],
                    409
                );
            }
        }
    }
}
