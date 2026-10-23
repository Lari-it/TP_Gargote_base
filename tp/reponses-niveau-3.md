# Reponses niveau 3

## 3.1 Schema et migration

1. La vue v_stock_actuel est une vue SQL qui calcule le stock actuel de chaque ingredient en faisant la somme des quantites de tous les mouvements de stock. Elle permet d'obtenir le stock sans avoir a faire la somme a chaque requete, et elle est toujours a jour car elle est basee sur les donnees reelles de mouvement_stock.

2. ON DELETE CASCADE sur recette(id_plat) signifie que si on supprime un plat, toutes les recettes associees sont automatiquement supprimees. C'est logique car sans le plat, les recettes n'ont plus de sens.

3. ON DELETE RESTRICT sur recette(id_ingredient) signifie qu'on ne peut pas supprimer un ingredient s'il est utilise dans au moins une recette. Il faut d'abord supprimer ou modifier les recettes avant de pouvoir supprimer l'ingredient. Cela protege l'integrite des donnees.

## 3.2 Stock et commandes

4. La consommation du stock se fait au moment ou la commande passe au statut "servie" dans CommandeService::changerStatut(). C'est le bon moment car c'est quand les plats sont reellement prepares et servis.

5. La verification du stock se fait dans CommandeService::creer() avant d'accepter la commande. Si le stock est insuffisant, la commande est refusee avec une erreur 409. Cela evite de prendre des commandes qu'on ne pourra pas honorer.

## 3.3 Questions

6. Si deux serveurs prennent une commande pour la meme table en meme temps, l'index unique partiel uq_commande_table_ouverte garantit qu'une seule operation reussira. L'autre recevra une erreur de conflit (409) qu'on peut gerer en affichant un message a l'utilisateur.

7. Pour eviter les problemes de concurrence sur le stock, on pourrait utiliser une transaction avec verrouillage (SELECT ... FOR UPDATE) dans StockService::verifierStockPourPlat() et StockService::consommerPourCommande(). Cela garantirait que personne ne peut modifier le stock pendant la verification et la consommation.

8. L'affichage du nombre de portions possibles dans la carte (PlatList) permet de voir rapidement quels plats peuvent etre prepares avec le stock actuel. Cela aide a la gestion et evite de prendre des commandes pour des plats en rupture de stock.
