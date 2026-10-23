-- =============================================================
--  Gargote — Niveau 2 : Donnees de test
--  Exécution : psql -U gargote -d gargote -f niveau2_donnees.sql
-- =============================================================

-- a) Ajouter 3 serveurs actifs et 1 inactif
INSERT INTO serveur (nom, actif) VALUES 
    ('Rado', true),
    ('Niry', true),
    ('Fara', true),
    ('Jean (conge)', false);

-- b) Remplir id_serveur pour commandes existantes
UPDATE commande SET id_serveur = 1 WHERE id IN (1, 2, 3);
UPDATE commande SET id_serveur = 2 WHERE id IN (4, 5, 6);
UPDATE commande SET id_serveur = 3 WHERE id IN (7, 8, 9);

-- c) Rendre id_serveur NOT NULL
ALTER TABLE commande ALTER COLUMN id_serveur SET NOT NULL;

-- d) Ajouter 15 commandes avec differents statuts
INSERT INTO commande (numero_table, id_serveur, statut, mode_paiement, date_paiement, date_commande)
SELECT 
    (n % 12) + 1 as numero_table,
    (n % 3) + 1 as id_serveur,
    CASE n % 4
        WHEN 0 THEN 'en_preparation'
        WHEN 1 THEN 'servie'
        WHEN 2 THEN 'payee'
        WHEN 3 THEN 'payee'
    END as statut,
    CASE WHEN n % 4 >= 2 THEN 'especes' ELSE NULL END as mode_paiement,
    CASE WHEN n % 4 >= 2 THEN NOW() - INTERVAL '1 hour' ELSE NULL END as date_paiement,
    NOW() - INTERVAL '1 day' * (n % 7) as date_commande
FROM generate_series(0, 14) AS n;

-- e) Ajouter lignes pour les nouvelles commandes
INSERT INTO commande_ligne (id_commande, id_plat, quantite, prix_unitaire)
SELECT 
    (SELECT id FROM commande ORDER BY id DESC LIMIT 1) - n,
    (n % 10) + 1 as id_plat,
    (n % 3) + 1 as quantite,
    (SELECT prix FROM plat WHERE id = (n % 10) + 1) as prix_unitaire
FROM generate_series(0, 14) AS n;
