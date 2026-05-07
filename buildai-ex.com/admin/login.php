<?php
session_start();
require_once __DIR__ . '/../stripe-config.php';

$errors = [];
$password = '';

if (!empty($_SESSION['admin_authenticated'])) {
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = trim($_POST['password'] ?? '');
    $adminPassword = getenv('ADMIN_PASSWORD') ?: '';

    if ($adminPassword === '') {
        $errors[] = 'La variable ADMIN_PASSWORD n’est pas configurée.';
    } elseif ($password === '') {
        $errors[] = 'Le mot de passe est requis.';
    } elseif (!hash_equals($adminPassword, $password)) {
        $errors[] = 'Mot de passe incorrect.';
    } else {
        $_SESSION['admin_authenticated'] = true;
        header('Location: dashboard.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin BuildAI-EX — Connexion</title>
  <link rel="stylesheet" href="../styles.css" />
</head>
<body>
  <main class="section contact">
    <div class="container">
      <h2>Connexion admin</h2>
      <?php if (!empty($errors)): ?>
        <div class="form-errors">
          <ul>
            <?php foreach ($errors as $error): ?>
              <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>
      <form class="contact-form" action="login.php" method="POST">
        <div class="form-field">
          <label for="password">Mot de passe admin</label>
          <input id="password" name="password" class="form-input" type="password" required />
        </div>
        <button type="submit" class="button primary">Se connecter</button>
      </form>
      <p class="form-note">Le tableau de bord admin permet de gérer les plans Stripe et les demandes clients enregistrées.</p>
      <p><a href="../index.php" class="button secondary">Retour au site</a></p>
    </div>
  </main>
</body>
</html>
