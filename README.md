# BUILDAI-EX.COM

Site web et services numériques pour freelances, PME et particuliers.

## Objectif

Aider les indépendants et les petites entreprises à créer une présence en ligne claire, moderne et efficace au travers :

- d'un site internet professionnel,
- d'une plateforme web,
- d'une application web ou mobile.

## Structure

- `buildai-ex.com/index.php` : page d'accueil du site avec formulaire sécurisé.
- `buildai-ex.com/styles.css` : styles du site.
- `buildai-ex.com/contact.php` : backend PHP pour traiter le formulaire de contact.
- `buildai-ex.com/stripe-create.php` : backend PHP pour créer une session Stripe Checkout.
- `buildai-ex.com/success.php` : page de confirmation après paiement Stripe.
- `buildai-ex.com/cancel.php` : page d'annulation Stripe.
- `buildai-ex.com/stripe-config.php` : configuration Stripe et chargement automatique de `.env`.
- `messages.txt` : stockage local des demandes de contact à la racine du dépôt (ignoré par Git).

## Usage

Ce site doit être servi via PHP pour que le formulaire et Stripe fonctionnent.

Par exemple :

```bash
cd buildai-ex.com
php -S localhost:8000
```

Ensuite, ouvrir `http://localhost:8000` dans un navigateur.

## Configuration Stripe et contact

Crée un fichier `.env` à la racine du dépôt ou configure ces variables d'environnement :

- `STRIPE_SECRET_KEY` : clé secrète Stripe (test ou live)
- `STRIPE_PRODUCT_ID` : identifiant du produit Stripe contenant tes plans d’abonnement
- `CONTACT_EMAIL` : adresse email qui reçoit les demandes client
- `ADMIN_PASSWORD` : mot de passe pour accéder au tableau de bord admin

La page `index.php` charge automatiquement les plans actifs depuis Stripe. Si `STRIPE_PRODUCT_ID` n’est pas défini, un plan de secours `STRIPE_PRICE_ID` peut être utilisé.

Le formulaire de contact sécurise les données clients et envoie une notification par email à `CONTACT_EMAIL`.

Le tableau de bord admin est disponible ici :

`/buildai-ex.com/admin/login.php`

L’admin permet de gérer les plans Stripe, d’exporter les demandes clients au format CSV et de supprimer les demandes individuellement.

## Déploiement

Pour la configuration de domaine et l’hébergement PHP, consulte le guide suivant :

- [DEPLOYMENT.md](./DEPLOYMENT.md)

En local avec un serveur PHP intégré :

```bash
export STRIPE_SECRET_KEY="sk_test_..."
export STRIPE_PRODUCT_ID="prod_..."
export CONTACT_EMAIL="ton.email@example.com"
export ADMIN_PASSWORD="motdepasseadmin"
php -S localhost:8000
```

Si tu utilises `.env`, crée ce fichier à la racine du dépôt :

```env
STRIPE_SECRET_KEY=sk_test_...
STRIPE_PRODUCT_ID=prod_...
CONTACT_EMAIL=ton.email@example.com
ADMIN_PASSWORD=motdepasseadmin
```
