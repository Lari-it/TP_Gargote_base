-- =============================================================
--  Gargote — Niveau 3 : Stock et tableau de bord
--  Migration du schema existant
--  Exécution : psql -U gargote -d gargote -f niveau3_schema.sql
-- =============================================================

-- a) Creation table ingredient
CREATE TABLE IF NOT EXISTS ingredient (
    id SERIAL PRIMARY KEY,
    nom VARCHAR(100) NOT NULL UNIQUE,
    unite VARCHAR(20) NOT NULL
);

-- b) Creation table recette
CREATE TABLE IF NOT EXISTS recette (
    id_plat INTEGER NOT NULL,
    id_ingredient INTEGER NOT NULL,
    quantite NUMERIC(10,2) NOT NULL,
    PRIMARY KEY (id_plat, id_ingredient),
    FOREIGN KEY (id_plat) REFERENCES plat(id) ON DELETE CASCADE,
    FOREIGN KEY (id_ingredient) REFERENCES ingredient(id) ON DELETE RESTRICT
);

-- c) Creation table mouvement_stock
CREATE TABLE IF NOT EXISTS mouvement_stock (
    id SERIAL PRIMARY KEY,
    id_ingredient INTEGER NOT NULL,
    quantite NUMERIC(10,2) NOT NULL,
    date_mouvement TIMESTAMP(0) NOT NULL DEFAULT NOW(),
    motif VARCHAR(100),
    FOREIGN KEY (id_ingredient) REFERENCES ingredient(id) ON DELETE RESTRICT
);

-- d) Creation vue v_stock_actuel
CREATE OR REPLACE VIEW v_stock_actuel AS
SELECT 
    i.id,
    i.nom,
    i.unite,
    COALESCE(SUM(m.quantite), 0) AS stock
FROM ingredient i
LEFT JOIN mouvement_stock m ON m.id_ingredient = i.id
GROUP BY i.id, i.nom, i.unite
ORDER BY i.nom;
