<?php

namespace App\Controllers\Api;

use App\Exceptions\RegleMetierException;
use App\Models\CommandeModel;
use App\Services\CommandeService;

class Commandes extends ApiController
{
    /** GET /api/commandes */
    public function index()
    {
        $statut = $this->request->getGet('statut');
        $date = $this->request->getGet('date');
        
        return $this->respond(model(CommandeModel::class)->listerAvecTotal($statut, $date));
    }

    /** GET /api/commandes/{id} — la commande et ses lignes */
    public function show($id = null)
    {
        $commande = model(CommandeModel::class)->detail((int) $id);

        if (! $commande) {
            return $this->erreur("Commande $id introuvable.", 404);
        }

        $service = new CommandeService();
        $commande['transitions_possibles'] = $service::TRANSITIONS[$commande['statut']] ?? [];

        return $this->respond($commande);
    }

    /**
     * POST /api/commandes
     * Corps attendu :
     * {
     *   "numero_table": 4,
     *   "lignes": [ { "id_plat": 2, "quantite": 1 }, ... ]
     * }
     */
    public function create()
    {
        try {
            $id = (new CommandeService())->creer($this->donneesJson());
        } catch (RegleMetierException $e) {
            return $this->erreurMetier($e);
        }

        return $this->respondCreated(model(CommandeModel::class)->detail($id));
    }

    /** PUT /api/commandes/{id}/statut */
    public function changerStatut($id = null)
    {
        $donnees = $this->donneesJson();
        $nouveauStatut = $donnees['statut'] ?? null;

        if (! $nouveauStatut) {
            return $this->erreurValidation(['statut' => 'Le statut est obligatoire.']);
        }

        try {
            $commande = (new CommandeService())->changerStatut((int) $id, $nouveauStatut);
            return $this->respond($commande);
        } catch (RegleMetierException $e) {
            return $this->erreurMetier($e);
        }
    }
}
