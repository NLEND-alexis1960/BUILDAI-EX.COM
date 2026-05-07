<?php
session_start();
require_once __DIR__ . '/stripe-config.php';

function sanitize($value) {
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(24));
}

$errors = [];
$message = '';
$name = '';
$email = '';
$company = '';
$phone = '';
$project = '';
$budget = '';
$details = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $token)) {
        $errors[] = 'Jeton CSRF invalide. Rechargez la page et réessayez.';
    }

    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $company = sanitize($_POST['company'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $project = sanitize($_POST['project'] ?? '');
    $budget = sanitize($_POST['budget'] ?? '');
    $details = sanitize($_POST['details'] ?? '');

    if ($name === '') {
        $errors[] = 'Le nom ou le nom de société est requis.';
    }

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Une adresse email valide est requise.';
    }

    if ($project === '') {
        $errors[] = 'Le type de projet est requis.';
    }

    if ($details === '') {
        $errors[] = 'Merci de décrire votre besoin.';
    }

    if ($phone !== '' && !preg_match('/^[0-9+\s().-]{6,30}$/', $phone)) {
        $errors[] = 'Le numéro de téléphone n’est pas valide.';
    }

    if (empty($errors)) {
        $entry = [
            'date' => date('c'),
            'name' => $name,
            'email' => $email,
            'company' => $company,
            'phone' => $phone,
            'project' => $project,
            'budget' => $budget,
            'details' => $details,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        ];

        $filePath = __DIR__ . '/../messages.txt';
        $line = json_encode($entry, JSON_UNESCAPED_UNICODE) . "\n";
        file_put_contents($filePath, $line, FILE_APPEND | LOCK_EX);

        $ownerEmail = env('CONTACT_EMAIL', 'contact@buildai-ex.com');
        $subject = 'Nouvelle demande BuildAI-EX';
        $body = "Nouvelle demande de contact:\n"
              . "Date: {$entry['date']}\n"
              . "Nom / Société: {$entry['name']}\n"
              . "Email: {$entry['email']}\n"
              . "Société: {$entry['company']}\n"
              . "Téléphone: {$entry['phone']}\n"
              . "Projet: {$entry['project']}\n"
              . "Budget: {$entry['budget']}\n"
              . "Détails: {$entry['details']}\n"
              . "IP: {$entry['ip']}\n";

        $headers = 'From: no-reply@buildai-ex.com\r\n';
        $headers .= 'Reply-To: ' . $entry['email'] . '\r\n';
        $headers .= 'Content-Type: text/plain; charset=UTF-8\r\n';
        @mail($ownerEmail, $subject, $body, $headers);

        $message = 'Merci, votre demande a bien été envoyée. Nous vous répondrons rapidement.';
        $name = $email = $company = $phone = $project = $budget = $details = '';
        $_SESSION['csrf_token'] = bin2hex(random_bytes(24));
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Contact — BuildAI-EX</title>
  <link rel="stylesheet" href="styles.css" />
</head>
<body>
  <header class="site-header">
    <div class="container header-inner">
      <div class="brand">
        <strong>BuildAI-EX</strong>
        <span>Contact</span>
      </div>
      <nav>
        <a href="index.php#services">Services</a>
        <a href="index.php#approche">Approche</a>
        <a href="index.php#contact">Contact</a>
      </nav>
    </div>
  </header>

  <main>
    <section class="section contact">
      <div class="container">
        <h2>Formulaire de contact</h2>

        <?php if ($message): ?>
          <div class="form-success"><?php echo $message; ?></div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
          <div class="form-errors">
            <ul>
              <?php foreach ($errors as $error): ?>
                <li><?php echo $error; ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <form class="contact-form" action="contact.php" method="POST">
          <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>" />
          <div class="form-field">
            <label for="name">Nom / Société</label>
            <input id="name" name="name" class="form-input" type="text" value="<?php echo $name; ?>" required />
          </div>
          <div class="form-field">
            <label for="email">Email</label>
            <input id="email" name="email" class="form-input" type="email" value="<?php echo $email; ?>" required />
          </div>
          <div class="form-field">
            <label for="company">Société / Freelance</label>
            <input id="company" name="company" class="form-input" type="text" value="<?php echo $company; ?>" />
          </div>
          <div class="form-field">
            <label for="phone">Téléphone</label>
            <input id="phone" name="phone" class="form-input" type="text" value="<?php echo $phone; ?>" placeholder="+33 6 12 34 56 78" />
          </div>
          <div class="form-field">
            <label for="project">Projet</label>
            <input id="project" name="project" class="form-input" type="text" value="<?php echo $project; ?>" required />
          </div>
          <div class="form-field">
            <label for="budget">Budget estimé</label>
            <input id="budget" name="budget" class="form-input" type="text" value="<?php echo $budget; ?>" placeholder="Ex. 1 000€ - 3 000€" />
          </div>
          <div class="form-field">
            <label for="details">Détails du besoin</label>
            <textarea id="details" name="details" class="form-textarea" rows="5" required><?php echo $details; ?></textarea>
          </div>
          <button type="submit" class="button primary">Envoyer ma demande</button>
        </form>

        <p class="form-note">Les demandes sont enregistrées dans le fichier <code>messages.txt</code>.</p>
        <p><a href="index.php" class="button secondary">Retour à la page d'accueil</a></p>
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
