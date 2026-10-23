-- =============================================================
--  Gargote — Niveau 3 : Donnees de test
--  Exécution : psql -U gargote -d gargote -f niveau3_donnees.sql
-- =============================================================

-- a) Ajouter ingredients
INSERT INTO ingredient (nom, unite) VALUES 
    ('Riz', 'kg'),
    ('Poulet', 'kg'),
    ('Lait', 'L'),
    ('Sucre', 'kg'),
    ('Farine', 'kg'),
    ('Huile', 'L'),
    ('Oeufs', 'unite'),
    ('Vanille', 'mL'),
    ('Tomate', 'kg'),
    ('Oignon', 'kg');

-- b) Ajouter recettes (assumant que les plats 1-10 existent)
-- Vary Afo (plat 1)
INSERT INTO recette (id_plat, id_ingredient, quantite) VALUES 
    (1, 1, 0.3),  -- Riz
    (1, 2, 0.2),  -- Poulet
    (1, 5, 0.1);  -- Farine

-- Riz Saucé (plat 2)
INSERT INTO recette (id_plat, id_ingredient, quantite) VALUES 
    (2, 1, 0.25),
    (2, 9, 0.1),  -- Tomate
    (2, 10, 0.05); -- Oignon

-- Lait Caillé (plat 3)
INSERT INTO recette (id_plat, id_ingredient, quantite) VALUES 
    (3, 3, 0.5);  -- Lait

-- Mofo Gasy (plat 4)
INSERT INTO recette (id_plat, id_ingredient, quantite) VALUES 
    (4, 1, 0.1),
    (4, 5, 0.05),
    (4, 7, 1);   -- Oeufs

-- Sakay (plat 5)
INSERT INTO recette (id_plat, id_ingredient, quantite) VALUES 
    (5, 9, 0.2),
    (5, 10, 0.1);

-- c) Ajouter mouvements de stock (stock initial)
INSERT INTO mouvement_stock (id_ingredient, quantite, motif) VALUES 
    (1, 50, 'Stock initial'),
    (2, 30, 'Stock initial'),
    (3, 100, 'Stock initial'),
    (4, 20, 'Stock initial'),
    (5, 25, 'Stock initial'),
    (6, 15, 'Stock initial'),
    (7, 100, 'Stock initial'),
    (8, 500, 'Stock initial'),
    (9, 40, 'Stock initial'),
    (10, 30, 'Stock initial');

-- d) Ajouter quelques mouvements de consommation
INSERT INTO mouvement_stock (id_ingredient, quantite, motif) VALUES 
    (1, -5, 'Consommation journee'),
    (2, -3, 'Consommation journee'),
    (3, -2, 'Consommation journee'),
    (5, -1, 'Consommation journee');
