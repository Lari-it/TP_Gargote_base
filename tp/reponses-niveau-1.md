# Reponses niveau 1

## 1.1 Schema et migration

1. La cle primaire de plat_du_jour est date_jour. Ce choix garantit qu il ne peut y avoir qu un plat du jour par jour car la cle primaire doit etre unique. PostgreSQL rejetera automatiquement toute tentative d inserer un deuxieme plat pour la meme date.

2. Si on ajoutait une colonne est_plat_du_jour BOOLEAN dans plat, on perdrait :
   - L historique : on ne saurait pas quels plats ont ete proposes les jours precedents
   - La garantie d unicite : rien n empecherait d avoir plusieurs plats marques comme plat du jour en meme temps

## 1.3 et 1.4 Tests API

Les 8 requetes du tableau 1.7 ont ete testees avec curl et donnent les codes attendus :
- POST avec nom existant : 422
- PUT avec meme nom : 200
- POST avec prix 0 : 422
- POST avec categorie inconnue : 422
- PUT avec disponible "oui" : 422
- PUT avec disponible false : 200
- PUT plat du jour epuise : 409
- GET avec categorie et disponible : 200

## 1.5 Questions

4. R2 renvoie 409 (conflit) et pas 422 (donnees invalides) car le plat existe et ses donnees sont valides, mais l operation est impossible dans l etat actuel (le plat est epuise). C est une regle metier, pas une erreur de validation de donnees.

5. Si Bao marque comme epuise le plat qui est deja plat du jour, je propose que le plat du jour reste affiche mais avec un badge "Epuise" et qu on ne puisse plus l ajouter au panier. Cela permet de garder l historique tout en empechant les commandes impossibles.

## 1.6 Questions

6. Avant la regle is_not_unique, la quatrieme requete renvoyait 500 car PostgreSQL detectait l erreur de cle etrangere et levait une exception que PHP ne gerait pas. C est un probleme car une erreur 500 signifie un bug du serveur, alors que c est une erreur de validation qui devrait renvoyer 422. PostgreSQL detecte l erreur mais le message technique n est pas comprehensible par Bao.
