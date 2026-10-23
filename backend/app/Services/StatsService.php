<?php

namespace App\Services;

class StatsService
{
    public function obtenirStats(): array
    {
        $db = db_connect();

        $commandesAujourdhui = $db->table('commande')
            ->where('DATE(date_commande)', date('Y-m-d'))
            ->countAll();

        $totalAujourdhui = $db->table('commande c')
            ->select('COALESCE(SUM(cl.quantite * cl.prix_unitaire), 0) AS total')
            ->join('commande_ligne cl', 'cl.id_commande = c.id')
            ->where('DATE(c.date_commande)', date('Y-m-d'))
            ->get()->getRowArray();

        $platsPopulaires = $db->table('commande_ligne cl')
            ->select('p.nom, SUM(cl.quantite) AS total_vendu')
            ->join('plat p', 'p.id = cl.id_plat')
            ->groupBy('p.id, p.nom')
            ->orderBy('total_vendu', 'DESC')
            ->limit(5)
            ->get()->getResultArray();

        $stockBas = $db->table('v_stock_actuel')
            ->where('stock <', 10)
            ->orderBy('stock', 'ASC')
            ->get()->getResultArray();

        return [
            'commandes_aujourdhui' => $commandesAujourdhui,
            'total_aujourdhui' => (int) ($totalAujourdhui['total'] ?? 0),
            'plats_populaires' => $platsPopulaires,
            'stock_bas' => $stockBas,
        ];
    }
}
