# Déploiement de BUILDai-EX.COM

Ce projet est un site PHP qui doit être hébergé sur un serveur compatible PHP.

## Hébergement requis

Le site contient des pages PHP dynamiques et des intégrations Stripe. Il ne peut pas être déployé sur un service purement statique comme GitHub Pages, Netlify ou Vercel sans support PHP.

Les environnements suivants fonctionnent bien :

- Hébergement mutualisé PHP standard
- VPS ou serveur dédié avec PHP installé
- Plateforme PHP/PaaS (par exemple, AWS Elastic Beanstalk PHP, Heroku avec buildpack PHP, Cloudways, o2switch, etc.)
- Conteneur Docker avec PHP + serveur web

## Fichiers importants

- `buildai-ex.com/index.php` : page d'accueil
- `buildai-ex.com/contact.php` : traitement du formulaire de contact
- `buildai-ex.com/stripe-create.php` : création de session Stripe Checkout
- `buildai-ex.com/stripe-config.php` : configuration Stripe et chargement des variables d'environnement
- `buildai-ex.com/admin/` : tableau de bord admin

## Configuration du domaine

1. Associe ton domaine `buildai-ex.com` à l'hébergement :
   - DNS `A` pointant vers l'adresse IP du serveur
   - ou `CNAME` si ton hébergeur l'exige

2. Assure-toi que le domaine est configuré dans l'interface de ton hébergeur.

3. Attends la propagation DNS (parfois jusqu'à 24h).

4. Active le SSL/TLS si possible pour `https://buildai-ex.com`.

## Variables d'environnement

Le site utilise ces variables :

- `STRIPE_SECRET_KEY` : clé secrète Stripe
- `STRIPE_PRODUCT_ID` : identifiant du produit Stripe pour les plans
- `CONTACT_EMAIL` : adresse email recevant les demandes
- `ADMIN_PASSWORD` : mot de passe pour l’administration

### Exemples

#### Sur un hébergement UNIX avec variables d'environnement

```bash
export STRIPE_SECRET_KEY="sk_test_..."
export STRIPE_PRODUCT_ID="prod_..."
export CONTACT_EMAIL="ton.email@example.com"
export ADMIN_PASSWORD="motdepasseadmin"
```

#### Avec un fichier `.env`

Crée un fichier `.env` à la racine du dépôt et ajoute :

```env
STRIPE_SECRET_KEY=sk_test_...
STRIPE_PRODUCT_ID=prod_...
CONTACT_EMAIL=ton.email@example.com
ADMIN_PASSWORD=motdepasseadmin
```

⚠️ Assure-toi que `.env` est bien ignoré par Git (il l'est déjà via `.gitignore`).

## Exemples de déploiement

### Serveur local pour tests

```bash
cd buildai-ex.com
php -S localhost:8000
```

Puis ouvre `http://localhost:8000`.

### Hébergement PHP classique

- Dépose le contenu du dossier `buildai-ex.com` dans le répertoire racine du domaine.
- Assure-toi que le serveur web (Apache/Nginx) dirige le domaine vers ce dossier.
- Vérifie que PHP est activé.

### Problèmes de domaine

- Vérifie le DNS (A/CNAME)
- Vérifie la configuration du domaine chez l'hébergeur
- Vérifie que le répertoire racine du site contient bien `index.php`
- Assure-toi que le serveur exécute PHP
- Vérifie les logs serveur pour voir les erreurs d'accès ou de PHP

## Résumé

Pour déployer correctement, tu dois :

1. Héberger le projet sur un serveur PHP
2. Configurer le domaine vers ce serveur
3. Définir les variables Stripe et admin
4. Activer SSL si possible

Si tu veux, je peux aussi te créer une configuration Docker simple pour ce projet.
