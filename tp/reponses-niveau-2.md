# Reponses niveau 2

## 2.1 Schema et migration

1. La contrainte ck_commande_paiement garantit l'integrite des donnees : on ne peut pas avoir un mode_paiement sans date_paiement, ni une date_paiement sans mode_paiement. Les deux doivent etre NULL ou remplis simultanement.

2. L'index unique partiel uq_commande_table_ouverte permet d'avoir plusieurs commandes pour une meme table dans l'historique, mais une seule commande en cours (statut = 'en_cours') par table. C'est plus flexible qu'une contrainte UNIQUE simple.

3. La contrainte UNIQUE (id_commande, id_plat) sur commande_ligne empeche d'avoir deux lignes pour le meme plat dans une commande. Si on veut commander 2 fois le meme plat, on doit mettre quantite = 2 sur une seule ligne.

## 2.2 Configuration

4. Centraliser les parametres dans Config/Gargote.php permet de les modifier facilement sans chercher dans tout le code. C'est plus maintenable et evite les valeurs en dur.

## 2.3 et 2.4 Tests API

5. Les requetes de test ont ete verifiees avec curl et renvoient les codes attendus (200, 422, 409).

## 2.5 Questions

6. Si on met statut = 'payee' sans mode_paiement, la contrainte ck_commande_paiement rejettera l'operation et PostgreSQL renverra une erreur. Le backend devrait attraper cette erreur et renvoyer une 422 avec un message clair.

7. La table 1 peut avoir plusieurs commandes dans le temps, mais une seule commande en cours a la fois grace a l'index unique partiel. Une fois la commande payee, on peut en creer une nouvelle pour la meme table.

8. Si deux serveurs essaient de creer une commande pour la table 5 en meme temps, l'index unique partiel garantit qu'une seule operation reussira. L'autre recevra une erreur de conflit (409) qu'on peut gerer en affichant un message a l'utilisateur.
