# Guide : configurer le paiement FedaPay (Parasel-Bio)

Ce guide t’aide à régler les réglages FedaPay pour que les transactions ne soient plus refusées (« Transaction échouée »).

---

## 1. Autoriser ton domaine dans FedaPay

Sans domaine autorisé, FedaPay peut refuser les paiements.

1. Va sur **https://sandbox.fedapay.com** et connecte-toi.
2. En haut à droite, clique sur ton **profil** (photo ou nom).
3. Cherche la section **« Applications »** ou **« Domaines »** / **« Authorize domain »** (selon l’interface).
4. Clique sur **« Autoriser un domaine »** / **« Authorize »**.
5. Saisis **exactement** l’adresse depuis laquelle tu paies :
   - En local : `127.0.0.1` ou `localhost`
   - En production : `parasel-bio.sc2.bj` (sans `https://`)
6. Enregistre. Attends quelques minutes si besoin.

---

## 2. Vérifier les clés et l’environnement dans ton projet

1. Ouvre le fichier **`.env`** à la racine du projet (pas `.env.example`).
2. Vérifie que ces lignes existent et sont correctes :

```env
FEDAPAY_PUBLIC_KEY=pk_sandbox_xxxxxxxxxxxx
FEDAPAY_SECRET_KEY=sk_sandbox_xxxxxxxxxxxx
FEDAPAY_ENVIRONMENT=sandbox
```

- Pour les **tests**, les clés doivent commencer par `pk_sandbox_` et `sk_sandbox_`.
- `FEDAPAY_ENVIRONMENT` doit être **`sandbox`** (pas `live`).

3. Où trouver les clés :
   - FedaPay Sandbox → **Paramètres** ou **Settings** → **Clés API** / **API Keys**.
   - Copie la **clé publique** et la **clé secrète** du **sandbox** et colle-les dans `.env`.

4. Après modification du `.env`, redémarre le serveur Laravel (arrêter puis relancer `php artisan serve` ou ton serveur habituel).

---

## 3. Utiliser un numéro de test (sandbox)

En sandbox, FedaPay n’accepte souvent que des **numéros de test** pour le Bénin.

1. Consulte la doc FedaPay pour le **sandbox** et les **numéros de test** (Bénin / Mobile Money).
2. Exemple typique : un numéro du type **01 64 00 00 01** ou celui indiqué dans la doc.
3. Sur ton site :
   - soit tu renseignes ce numéro dans **Mon espace** → profil (téléphone), et il sera prérempli sur la page de paiement ;
   - soit tu le saisis à la main dans le champ **Numéro de téléphone** sur la page « Finaliser votre paiement ».

---

## 4. Vérifier les méthodes de paiement activées

1. Sur **sandbox.fedapay.com** → **Préférences** → **Méthodes de paiement**.
2. Coche au moins une méthode utilisée pour tes tests (ex. **Momo Test**).
3. Clique sur **Enregistrer**.

Si tu veux des opérateurs réels (MTN, Moov, Celtiis) pour le Bénin, il faut les activer depuis ce même écran si disponibles, ou contacter le support FedaPay.

---

## 5. Tester le paiement

1. Lance ton site (ex. `php artisan serve`).
2. Passe une commande jusqu’à **« Finaliser votre paiement »**.
3. Sur la page FedaPay :
   - Choisis l’opérateur (ex. Momo Test).
   - Saisis un **numéro de test** (10 chiffres, commençant par 01 pour le Bénin).
4. Clique sur **Payer**.

Si ça échoue encore :
- Ouvre les **outils développeur** (F12) → onglet **Réseau** / **Network**.
- Refais un paiement et regarde les requêtes vers `fedapay.com` : une requête en rouge indiquera souvent le message d’erreur exact (ex. domaine non autorisé, numéro invalide).

---

## 6. Webhook (optionnel, pour mettre la commande à jour après paiement)

Pour que la commande passe en « payée » automatiquement après un paiement réussi :

1. FedaPay Sandbox → **Paramètres** ou **Webhooks**.
2. Ajoute une URL de webhook :  
   `https://ton-domaine.com/webhook/payment`  
   (remplace par ton vrai domaine, ex. `https://parasel-bio.sc2.bj/webhook/payment`).
3. En local, FedaPay ne peut pas appeler `127.0.0.1`. Pour tester le webhook en local, utilise un outil comme **ngrok** pour exposer ton `localhost` avec une URL publique.

La route du webhook dans le projet est déjà gérée dans `PaymentController@handleWebhook` (route : `POST /webhook/payment`, voir `routes/web.php`).

---

## Récap rapide

| Étape | Action |
|-------|--------|
| 1 | Autoriser le domaine (ex. `parasel-bio.sc2.bj` ou `127.0.0.1`) dans FedaPay. |
| 2 | Vérifier `.env` : clés sandbox + `FEDAPAY_ENVIRONMENT=sandbox`. |
| 3 | Utiliser un numéro de test Bénin (ex. 01 64 00 00 01) depuis la doc FedaPay. |
| 4 | Activer au moins une méthode (ex. Momo Test) dans Préférences FedaPay. |
| 5 | Tester en payant ; en cas d’échec, regarder l’onglet Réseau (F12). |

Si après tout ça la transaction échoue encore, le message d’erreur dans l’onglet **Réseau** (réponse de l’API FedaPay) te dira la cause exacte.
