<?php

namespace App\Services;

use App\Config\Gargote;
use App\Exceptions\RegleMetierException;
use App\Models\CommandeLigneModel;
use App\Models\CommandeModel;
use App\Models\PlatModel;
use App\Services\StockService;

/**
 * Le CODE MÉTIER des commandes vit ici, pas dans le contrôleur.
 *
 * Le contrôleur reçoit la requête HTTP et renvoie la réponse ;
 * le service décide si l'opération est permise et l'exécute.
 * Chaque nouvelle règle métier = une vérification dans ce fichier
 * qui lève une RegleMetierException si elle n'est pas respectée.
 */
class CommandeService
{
    /**
     * Crée une commande et ses lignes. Renvoie l'id de la commande.
     *
     * @throws RegleMetierException
     */
    public function creer(array $donnees): int
    {
        $numeroTable = $donnees['numero_table'] ?? null;
        $lignes      = $donnees['lignes'] ?? [];

        // --- Règles métier -------------------------------------------------
        if (! is_numeric($numeroTable)) {
            throw new RegleMetierException('Numéro de table manquant.', [
                'numero_table' => 'Indiquez le numéro de la table.',
            ]);
        }

        if ($numeroTable < 1 || $numeroTable > Gargote::$tables) {
            throw new RegleMetierException('Numéro de table invalide.', [
                'numero_table' => "La table doit être entre 1 et " . Gargote::$tables . ".",
            ]);
        }

        if (! is_array($lignes) || $lignes === []) {
            throw new RegleMetierException('La commande est vide.', [
                'lignes' => 'Ajoutez au moins un plat.',
            ]);
        }

        $plats = model(PlatModel::class);

        // On prépare les lignes en récupérant le prix ACTUEL de chaque plat.
        $lignesPretes = [];
        $quantitesParPlat = [];
        
        foreach ($lignes as $i => $ligne) {
            $plat = $plats->find($ligne['id_plat'] ?? 0);
            if ($plat === null) {
                throw new RegleMetierException('Plat inconnu.', [
                    "lignes.$i.id_plat" => 'Ce plat n’existe pas.',
                ]);
            }

            if (! $plat['disponible']) {
                throw new RegleMetierException(
                    "Le {$plat['nom']} est épuisé.",
                    ["lignes.$i.id_plat" => 'Ce plat est épuisé.'],
                    409
                );
            }

            $quantite = (int) ($ligne['quantite'] ?? 1);
            if ($quantite < 1) {
                throw new RegleMetierException('Quantité invalide.', [
                    "lignes.$i.quantite" => 'La quantité doit être au moins 1.',
                ]);
            }

            $idPlat = (int) $plat['id'];
            if (! isset($quantitesParPlat[$idPlat])) {
                $quantitesParPlat[$idPlat] = 0;
            }
            $quantitesParPlat[$idPlat] += $quantite;

            if ($quantitesParPlat[$idPlat] > Gargote::$quantiteMax) {
                throw new RegleMetierException(
                    "Quantité maximale dépassée pour {$plat['nom']}.",
                    ["lignes.$i.quantite" => "Maximum " . Gargote::$quantiteMax . " par plat."],
                    422
                );
            }

            $lignesPretes[] = [
                'id_plat'       => $idPlat,
                'quantite'      => $quantite,
                'prix_unitaire' => (int) $plat['prix'],
            ];

            $stockService = new StockService();
            $stockService->verifierStockPourPlat($idPlat, $quantite);
        }

        if ($numeroTable > 0) {
            $commandeModel = model(CommandeModel::class);
            $tableOccupee = $commandeModel
                ->where('numero_table', $numeroTable)
                ->where('statut', 'en_cours')
                ->first();
            
            if ($tableOccupee) {
                throw new RegleMetierException(
                    "La table $numeroTable est déjà occupée.",
                    ['numero_table' => 'Cette table a déjà une commande en cours.'],
                    409
                );
            }
        }

        // --- Enregistrement : tout ou rien (transaction) -------------------
        $db = db_connect();
        $db->transStart();

        $idCommande = model(CommandeModel::class)->insert([
            'numero_table' => (int) $numeroTable,
        ]);

        $lignesModel = model(CommandeLigneModel::class);
        foreach ($lignesPretes as $ligne) {
            $ligne['id_commande'] = $idCommande;
            $lignesModel->insert($ligne);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            throw new RegleMetierException('Erreur lors de l’enregistrement de la commande.', [], 500);
        }

        return (int) $idCommande;
    }

    public const TRANSITIONS = [
        'en_cours' => ['en_preparation'],
        'en_preparation' => ['servie'],
        'servie' => ['payee'],
        'payee' => [],
    ];

    public function changerStatut(int $idCommande, string $nouveauStatut): array
    {
        $commandeModel = model(CommandeModel::class);
        $commande = $commandeModel->find($idCommande);

        if (! $commande) {
            throw new RegleMetierException('Commande introuvable.', [], 404);
        }

        $statutActuel = $commande['statut'];
        $transitionsPossibles = self::TRANSITIONS[$statutActuel] ?? [];

        if (! in_array($nouveauStatut, $transitionsPossibles)) {
            throw new RegleMetierException(
                "Transition impossible de {$statutActuel} vers {$nouveauStatut}.",
                ['statut' => 'Statut non autorisé.'],
                422
            );
        }

        if ($nouveauStatut === 'servie' && $statutActuel !== 'servie') {
            $stockService = new StockService();
            $stockService->consommerPourCommande($idCommande);
        }

        $commandeModel->update($idCommande, ['statut' => $nouveauStatut]);

        return $commandeModel->find($idCommande);
    }
}
