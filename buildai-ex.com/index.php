<?php
session_start();
require_once __DIR__ . '/stripe-config.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(24));
}
$csrf = $_SESSION['csrf_token'];
$plans = getAvailableStripePlans();

function formatPriceText(array $plan): string {
    return formatStripePrice($plan['amount'], $plan['currency']) . ' / ' . $plan['interval'];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>buildai-ex.com — Site web & applications pour freelances, PME et particuliers</title>
  <link rel="stylesheet" href="styles.css" />
</head>
<body>
  <header class="site-header">
    <div class="container header-inner">
      <div class="brand">
        <strong>BuildAI-EX</strong>
        <span>Présence web, plateforme et appli</span>
      </div>
      <nav>
        <a href="#produits">Produits</a>
        <a href="#approche">Approche</a>
        <a href="#tarifs">Tarifs</a>
        <a href="#abonnement">Abonnement</a>
        <a href="#inscription">S'inscrire</a>
      </nav>
    </div>
  </header>

  <main>
    <section class="hero">
      <div class="container">
        <h1>Votre présence en ligne créée pour vendre, convaincre et grandir</h1>
        <p>Nous aidons les freelances, PME et particuliers à lancer leur site internet, plateforme ou application avec un accompagnement clair, simple et agile.</p>
        <div class="hero-actions">
          <a class="button primary" href="#produits">Découvrir</a>
          <a class="button secondary" href="#inscription">S'inscrire</a>
        </div>
        <?php if (!empty($plans)): ?>
          <div class="hero-actions hero-plan-actions">
            <a class="button tertiary" href="#tarifs">Voir les tarifs</a>
            <a class="button tertiary" href="#abonnement">Abonnement</a>
          </div>
        <?php endif; ?>
      </div>
    </section>

    <section id="produits" class="section services">
      <div class="container">
        <h2>Nos produits</h2>
        <p>Des offres prêtes à l’emploi et des solutions sur mesure selon votre projet : site, boutique, plateforme ou application.</p>
        <div class="product-grid">
          <article class="card product-card">
            <div class="product-icon">
              <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.5"/>
                <path d="M3 12H21" stroke="currentColor" stroke-width="1.5"/>
                <path d="M12 3C13.5 5 14.5 7.5 14.83 10.5C14.5 13.5 13.5 16 12 18" stroke="currentColor" stroke-width="1.5"/>
                <path d="M12 3C10.5 5 9.5 7.5 9.17 10.5C9.5 13.5 10.5 16 12 18" stroke="currentColor" stroke-width="1.5"/>
              </svg>
            </div>
            <h3>Site vitrine & landing page</h3>
            <p>Idéal pour présenter votre activité, collecter des leads et séduire rapidement vos visiteurs.</p>
            <ul>
              <li>Pages claires et modernes</li>
              <li>Design responsive adapté mobile</li>
              <li>Optimisé pour la conversion</li>
            </ul>
            <a class="button tertiary" href="#inscription">Je veux un site</a>
          </article>
          <article class="card product-card">
            <div class="product-icon">
              <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M6 6H20L18 14H8L6 6Z" stroke="currentColor" stroke-width="1.5"/>
                <path d="M6 6L4 2H2" stroke="currentColor" stroke-width="1.5"/>
                <circle cx="9" cy="19" r="1.5" fill="currentColor"/>
                <circle cx="17" cy="19" r="1.5" fill="currentColor"/>
              </svg>
            </div>
            <h3>Boutique en ligne</h3>
            <p>Vendez vos services ou produits avec une boutique simple, sécurisée et facile à gérer.</p>
            <ul>
              <li>Fiches produits personnalisées</li>
              <li>Panier et paiement intégrés</li>
              <li>Gestion facile des commandes</li>
            </ul>
            <a class="button tertiary" href="#inscription">Je veux une boutique</a>
          </article>
          <article class="card product-card">
            <div class="product-icon">
              <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M6 7H18V17H6V7Z" stroke="currentColor" stroke-width="1.5"/>
                <path d="M6 12H18" stroke="currentColor" stroke-width="1.5"/>
                <path d="M12 7V17" stroke="currentColor" stroke-width="1.5"/>
                <circle cx="9" cy="9" r="1" fill="currentColor"/>
                <circle cx="15" cy="15" r="1" fill="currentColor"/>
              </svg>
            </div>
            <h3>Plateforme métier</h3>
            <p>Construisez un espace client, un outil de réservation ou un service interne adapté à votre activité.</p>
            <ul>
              <li>Fonctions métier sur mesure</li>
              <li>Accès sécurisé pour vos utilisateurs</li>
              <li>Workflow optimisé et évolutif</li>
            </ul>
            <a class="button tertiary" href="#inscription">Je veux une plateforme</a>
          </article>
          <article class="card product-card">
            <div class="product-icon">
              <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect x="7" y="3" width="10" height="18" rx="2" stroke="currentColor" stroke-width="1.5"/>
                <path d="M12 18H12.01" stroke="currentColor" stroke-width="1.5"/>
              </svg>
            </div>
            <h3>Application web & mobile</h3>
            <p>Lancez une application fluide, responsive et orientée utilisateur pour vos clients ou votre équipe.</p>
            <ul>
              <li>Expérience utilisateur intuitive</li>
              <li>Performances optimisées</li>
              <li>Architecture prête à évoluer</li>
            </ul>
            <a class="button tertiary" href="#inscription">Je veux une appli</a>
          </article>
        </div>
      </div>
    </section>

    <section id="approche" class="section approach">
      <div class="container">
        <h2>Notre approche</h2>
        <div class="grid">
          <div>
            <h3>1. Écoute et diagnostic</h3>
            <p>Nous comprenons vos besoins, votre cible et vos objectifs pour créer un projet utile et efficace.</p>
          </div>
          <div>
            <h3>2. Conception claire</h3>
            <p>Design simple, navigation fluide et contenu adapté pour une expérience professionnelle et accessible.</p>
          </div>
          <div>
            <h3>3. Livraison rapide</h3>
            <p>Un site ou une application prête à être utilisée, avec un suivi personnalisé et des conseils de déploiement.</p>
          </div>
        </div>
      </div>
    </section>

    <section class="section advantages">
      <div class="container">
        <h2>Pourquoi choisir BuildAI-EX ?</h2>
        <ul>
          <li>Accompagnement adapté aux indépendants, PME et projets personnels</li>
          <li>Solutions claires, sans jargon technique inutile</li>
          <li>Design moderne et responsive, pensé pour convertir</li>
          <li>Suivi réel après la mise en ligne</li>
        </ul>
      </div>
    </section>

    <section id="tarifs" class="section pricing">
      <div class="container">
        <h2>Tarifs</h2>
        <p>Nos offres d’abonnement</p>
        <?php if (!empty($plans)): ?>
          <div class="cards pricing-cards">
            <?php foreach ($plans as $plan): ?>
              <article class="card pricing-card<?php echo isset($plan['featured']) ? ' featured' : ''; ?>">
                <h3><?php echo htmlspecialchars($plan['nickname'], ENT_QUOTES, 'UTF-8'); ?></h3>
                <p class="subscription-price"><?php echo htmlspecialchars(formatPriceText($plan), ENT_QUOTES, 'UTF-8'); ?></p>
                <p><?php echo htmlspecialchars($plan['product_name'] ?: $plan['description'] ?: 'Abonnement Stripe', ENT_QUOTES, 'UTF-8'); ?></p>
                <ul>
                  <li>Facturation : <?php echo $plan['interval_count'] > 1 ? $plan['interval_count'] . ' ' . $plan['interval'] : $plan['interval']; ?></li>
                </ul>
                <a class="button tertiary" href="#abonnement">Choisir ce plan</a>
              </article>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <p class="form-note">Aucun plan Stripe actif n’est trouvé. Vérifie ta configuration Stripe et ajoute `STRIPE_PRODUCT_ID` dans `.env`.</p>
        <?php endif; ?>
      </div>
    </section>

    <section id="abonnement" class="section subscription">
      <div class="container">
        <h2>Abonnement</h2>
        <p>Choisis ton plan Stripe et active ton abonnement en quelques clics.</p>
        <?php if (!empty($plans)): ?>
          <form action="stripe-create.php" method="POST" class="subscription-form">
            <div class="form-field">
              <?php foreach ($plans as $plan): ?>
                <label class="radio-label">
                  <input type="radio" name="price_id" value="<?php echo htmlspecialchars($plan['id'], ENT_QUOTES, 'UTF-8'); ?>" <?php echo $plan === reset($plans) ? 'checked' : ''; ?> />
                  <?php echo htmlspecialchars($plan['nickname'], ENT_QUOTES, 'UTF-8'); ?> — <?php echo htmlspecialchars(formatPriceText($plan), ENT_QUOTES, 'UTF-8'); ?>
                </label>
              <?php endforeach; ?>
            </div>
            <div class="form-field">
              <label for="subscriber_email">Email</label>
              <input id="subscriber_email" name="email" class="form-input" type="email" required placeholder="ton email" />
            </div>
            <div class="form-field">
              <label for="subscriber_company">Nom de la société / Freelance</label>
              <input id="subscriber_company" name="company" class="form-input" type="text" placeholder="Entreprise ou nom" />
            </div>
            <button type="submit" class="button primary">S'abonner</button>
            <p class="form-note">Le prix affiché est réel et dépend du plan Stripe sélectionné.</p>
          </form>
        <?php else: ?>
          <p class="form-note">Aucun plan Stripe n’est configuré. Ajoute `STRIPE_PRODUCT_ID` à `.env` pour charger automatiquement les plans Stripe.</p>
        <?php endif; ?>
      </div>
    </section>

    <section id="inscription" class="section contact">
      <div class="container contact-inner">
        <div>
          <h2>Inscription</h2>
          <p>Inscris ton projet, indique ton besoin et nous te recontactons avec une proposition adaptée.</p>
        </div>
        <form class="contact-form" action="contact.php" method="POST">
          <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>" />
          <div class="form-field">
            <label for="name">Nom / Société</label>
            <input id="name" name="name" class="form-input" type="text" required />
          </div>
          <div class="form-field">
            <label for="email">Email</label>
            <input id="email" name="email" class="form-input" type="email" required />
          </div>
          <div class="form-field">
            <label for="company">Société / Freelance</label>
            <input id="company" name="company" class="form-input" type="text" />
          </div>
          <div class="form-field">
            <label for="phone">Téléphone</label>
            <input id="phone" name="phone" class="form-input" type="text" placeholder="+33 6 12 34 56 78" />
          </div>
          <div class="form-field">
            <label for="project">Projet</label>
            <input id="project" name="project" class="form-input" type="text" placeholder="Site web, plateforme, appli..." required />
          </div>
          <div class="form-field">
            <label for="budget">Budget estimé</label>
            <input id="budget" name="budget" class="form-input" type="text" placeholder="Ex. 1 000€ - 3 000€" />
          </div>
          <div class="form-field">
            <label for="details">Détails du besoin</label>
            <textarea id="details" name="details" class="form-textarea" rows="5" placeholder="Décris ton besoin en quelques lignes" required></textarea>
          </div>
          <button type="submit" class="button primary">S'inscrire</button>
          <p class="form-note">Ce formulaire est sécurisé et permet de démarrer ton inscription et ta demande de projet.</p>
        </form>
      </div>
    </section>
  </main>

  <footer class="site-footer">
    <div class="container">
      <p>© 2026 BuildAI-EX. Site pour freelances, PME et particuliers.</p>
    </div>
  </footer>
</body>
</html>
