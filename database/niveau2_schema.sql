-- =============================================================
--  Gargote — Niveau 2 : Le service s'ameliore
--  Migration du schema existant
--  Exécution : psql -U gargote -d gargote -f niveau2_schema.sql
-- =============================================================

-- a) Creation table serveur
CREATE TABLE IF NOT EXISTS serveur (
    id SERIAL PRIMARY KEY,
    nom VARCHAR(50) NOT NULL,
    actif BOOLEAN NOT NULL DEFAULT true
);

-- b) Ajout colonne id_serveur a commande (nullable d'abord)
ALTER TABLE commande ADD COLUMN IF NOT EXISTS id_serveur INTEGER;

-- c) Ajout colonne statut a commande
ALTER TABLE commande ADD COLUMN IF NOT EXISTS statut VARCHAR(20) NOT NULL DEFAULT 'en_cours';

-- d) Ajout colonne mode_paiement a commande
ALTER TABLE commande ADD COLUMN IF NOT EXISTS mode_paiement VARCHAR(20);

-- e) Ajout colonne date_paiement a commande
ALTER TABLE commande ADD COLUMN IF NOT EXISTS date_paiement TIMESTAMP(0);

-- f) Ajout cle etrangere id_serveur
ALTER TABLE commande ADD CONSTRAINT fk_commande_serveur 
    FOREIGN KEY (id_serveur) REFERENCES serveur(id);

-- g) Ajout contrainte ck_commande_paiement
ALTER TABLE commande ADD CONSTRAINT ck_commande_paiement 
    CHECK (mode_paiement IS NULL AND date_paiement IS NULL OR 
           mode_paiement IS NOT NULL AND date_paiement IS NOT NULL);

-- h) Ajout contrainte quantite positive sur commande_ligne
ALTER TABLE commande_ligne ADD CONSTRAINT ck_commande_ligne_quantite 
    CHECK (quantite > 0);

-- i) Ajout UNIQUE (id_commande, id_plat) sur commande_ligne
ALTER TABLE commande_ligne ADD CONSTRAINT uq_commande_ligne 
    UNIQUE (id_commande, id_plat);

-- j) Creation index unique partiel pour commande ouverte par table
CREATE UNIQUE INDEX IF NOT EXISTS uq_commande_table_ouverte 
    ON commande(numero_table) 
    WHERE statut = 'en_cours';
