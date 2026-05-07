<?php
session_start();
require_once __DIR__ . '/../stripe-config.php';

if (empty($_SESSION['admin_authenticated'])) {
    header('Location: login.php');
    exit;
}

$flash = $_SESSION['admin_flash'] ?? null;
unset($_SESSION['admin_flash']);

function sanitize($value) {
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

$plans = getAvailableStripePlans();
$messageFile = __DIR__ . '/../../messages.txt';
$messages = [];
if (file_exists($messageFile)) {
    $lines = file($messageFile, FILE_IGNORE_NEW_LINES);
    foreach ($lines as $lineIndex => $line) {
        $entry = json_decode($line, true);
        if (is_array($entry)) {
            $entry['_line'] = $lineIndex;
            $messages[] = $entry;
        }
    }
}

$filterFrom = trim($_REQUEST['from'] ?? '');
$filterTo = trim($_REQUEST['to'] ?? '');
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 10;

$filteredMessages = array_filter($messages, function ($message) use ($filterFrom, $filterTo) {
    if ($filterFrom !== '') {
        $fromTs = strtotime($filterFrom);
        $msgTs = strtotime($message['date'] ?? '');
        if ($fromTs !== false && $msgTs !== false && $msgTs < $fromTs) {
            return false;
        }
    }
    if ($filterTo !== '') {
        $toTs = strtotime($filterTo);
        $msgTs = strtotime($message['date'] ?? '');
        if ($toTs !== false && $msgTs !== false && $msgTs > ($toTs + 86399)) {
            return false;
        }
    }
    return true;
});

usort($filteredMessages, function ($a, $b) {
    return strtotime($b['date'] ?? '') <=> strtotime($a['date'] ?? '');
});

$totalMessages = count($filteredMessages);
$totalPages = max(1, ceil($totalMessages / $perPage));
$page = min($page, $totalPages);
$displayMessages = array_slice($filteredMessages, ($page - 1) * $perPage, $perPage);
$exportAllCount = $totalMessages;
$exportPageCount = count($displayMessages);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'create_plan') {
        $name = sanitize($_POST['name'] ?? '');
        $amount = floatval(str_replace(',', '.', $_POST['amount'] ?? '0'));
        $currency = strtoupper(trim($_POST['currency'] ?? 'eur'));
        $interval = trim($_POST['interval'] ?? 'month');
        $interval_count = intval($_POST['interval_count'] ?? 1);
        $productId = getStripeProductId();

        if ($name === '' || $amount <= 0 || $productId === '') {
            $_SESSION['admin_flash'] = 'Remplis correctement le formulaire et vérifie STRIPE_PRODUCT_ID.';
            header('Location: dashboard.php');
            exit;
        }

        $priceData = [
            'product' => $productId,
            'nickname' => $name,
            'unit_amount' => intval(round($amount * 100)),
            'currency' => $currency,
            'recurring[interval]' => $interval,
            'recurring[interval_count]' => max(1, $interval_count),
            'active' => 'true',
        ];

        $result = createStripePrice($priceData);
        $_SESSION['admin_flash'] = $result ? 'Plan créé avec succès.' : 'Impossible de créer le plan Stripe.';
        header('Location: dashboard.php');
        exit;
    }

    if ($action === 'update_plan') {
        $priceId = sanitize($_POST['price_id'] ?? '');
        $nickname = sanitize($_POST['nickname'] ?? '');
        $active = isset($_POST['active']) ? 'true' : 'false';

        if ($priceId === '') {
            $_SESSION['admin_flash'] = 'Prix Stripe invalide.';
            header('Location: dashboard.php');
            exit;
        }

        $result = updateStripePrice($priceId, [
            'nickname' => $nickname,
            'active' => $active,
        ]);

        $_SESSION['admin_flash'] = $result ? 'Plan mis à jour.' : 'Impossible de mettre à jour le plan.';
        header('Location: dashboard.php');
        exit;
    }

    if ($action === 'delete_message') {
        $lineIndex = intval($_POST['line_index'] ?? -1);
        $lines = file($messageFile, FILE_IGNORE_NEW_LINES);
        if (isset($lines[$lineIndex])) {
            unset($lines[$lineIndex]);
            file_put_contents($messageFile, implode("\n", $lines) . "\n", LOCK_EX);
            $_SESSION['admin_flash'] = 'Demande client supprimée.';
        } else {
            $_SESSION['admin_flash'] = 'Demande introuvable.';
        }
        header('Location: dashboard.php');
        exit;
    }

    if ($action === 'export_csv') {
        $exportScope = $_POST['export_scope'] ?? 'all';
        $csvFileName = 'buildai-ex-messages-' . date('Ymd-His') . '-' . $exportScope . '.csv';
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $csvFileName . '"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Date', 'Nom', 'Email', 'Société', 'Téléphone', 'Projet', 'Budget', 'Détails', 'IP']);
        $exportRows = $exportScope === 'page' ? $displayMessages : $filteredMessages;
        foreach ($exportRows as $message) {
            fputcsv($output, [
                $message['date'] ?? '',
                $message['name'] ?? '',
                $message['email'] ?? '',
                $message['company'] ?? '',
                $message['phone'] ?? '',
                $message['project'] ?? '',
                $message['budget'] ?? '',
                $message['details'] ?? '',
                $message['ip'] ?? '',
            ]);
        }
        fclose($output);
        exit;
    }

    if ($action === 'clear_messages') {
        file_put_contents($messageFile, '');
        $_SESSION['admin_flash'] = 'Toutes les demandes clients ont été supprimées.';
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
  <title>Admin BuildAI-EX</title>
  <link rel="stylesheet" href="../styles.css" />
</head>
<body>
  <header class="site-header">
    <div class="container header-inner">
      <div class="brand">
        <strong>BuildAI-EX Admin</strong>
        <span>Gestion Stripe et demandes clients</span>
      </div>
      <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="logout.php">Déconnexion</a>
      </nav>
    </div>
  </header>

  <main>
    <section class="section">
      <div class="container">
        <h2>Tableau de bord admin</h2>
        <?php if ($flash): ?>
          <div class="form-success"><?php echo htmlspecialchars($flash, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <div class="cards pricing-cards">
          <article class="card pricing-card">
            <h3>Plans Stripe</h3>
            <?php if (!empty($plans)): ?>
              <table class="admin-table">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Prix</th>
                    <th>Statut</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($plans as $plan): ?>
                    <tr>
                      <td><?php echo htmlspecialchars($plan['id'], ENT_QUOTES, 'UTF-8'); ?></td>
                      <td><?php echo htmlspecialchars($plan['nickname'], ENT_QUOTES, 'UTF-8'); ?></td>
                      <td><?php echo htmlspecialchars(formatStripePrice($plan['amount'], $plan['currency']), ENT_QUOTES, 'UTF-8'); ?> / <?php echo htmlspecialchars($plan['interval'], ENT_QUOTES, 'UTF-8'); ?></td>
                      <td><?php echo htmlspecialchars($plan['active'] ?? 'actif', ENT_QUOTES, 'UTF-8'); ?></td>
                      <td>
                        <form action="dashboard.php" method="POST" class="admin-inline-form">
                          <input type="hidden" name="action" value="update_plan" />
                          <input type="hidden" name="price_id" value="<?php echo htmlspecialchars($plan['id'], ENT_QUOTES, 'UTF-8'); ?>" />
                          <input type="text" name="nickname" value="<?php echo htmlspecialchars($plan['nickname'], ENT_QUOTES, 'UTF-8'); ?>" class="form-input" />
                          <label><input type="checkbox" name="active" checked /> Actif</label>
                          <button type="submit" class="button secondary">Mettre à jour</button>
                        </form>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            <?php else: ?>
              <p>Aucun plan Stripe actif trouvé.</p>
            <?php endif; ?>
          </article>

          <article class="card pricing-card">
            <h3>Créer un plan</h3>
            <form action="dashboard.php" method="POST" class="contact-form">
              <input type="hidden" name="action" value="create_plan" />
              <div class="form-field">
                <label for="name">Nom du plan</label>
                <input id="name" name="name" class="form-input" type="text" required />
              </div>
              <div class="form-field">
                <label for="amount">Montant (€)</label>
                <input id="amount" name="amount" class="form-input" type="number" step="0.01" required />
              </div>
              <div class="form-field">
                <label for="currency">Devise</label>
                <input id="currency" name="currency" class="form-input" type="text" value="eur" required />
              </div>
              <div class="form-field">
                <label for="interval">Périodicité</label>
                <select id="interval" name="interval" class="form-input" required>
                  <option value="month">Mensuel</option>
                  <option value="year">Annuel</option>
                </select>
              </div>
              <div class="form-field">
                <label for="interval_count">Intervalle</label>
                <input id="interval_count" name="interval_count" class="form-input" type="number" value="1" min="1" required />
              </div>
              <button type="submit" class="button primary">Créer le plan</button>
            </form>
          </article>
        </div>

        <article class="card pricing-card">
          <h3>Demandes clients</h3>
          <?php if (!empty($messages)): ?>
            <form method="GET" action="dashboard.php" class="admin-inline-form">
              <div class="form-field">
                <label for="from">De</label>
                <input id="from" name="from" class="form-input" type="date" value="<?php echo htmlspecialchars($filterFrom, ENT_QUOTES, 'UTF-8'); ?>" />
              </div>
              <div class="form-field">
                <label for="to">À</label>
                <input id="to" name="to" class="form-input" type="date" value="<?php echo htmlspecialchars($filterTo, ENT_QUOTES, 'UTF-8'); ?>" />
              </div>
              <button type="submit" class="button secondary">Filtrer</button>
              <a href="dashboard.php" class="button primary">Réinitialiser</a>
            </form>
            <div class="admin-controls">
              <button form="clear-messages" class="button secondary">Effacer toutes les demandes</button>
              <form id="export-csv" action="dashboard.php" method="POST" class="admin-inline-form">
                <input type="hidden" name="action" value="export_csv" />
                <input type="hidden" name="from" value="<?php echo htmlspecialchars($filterFrom, ENT_QUOTES, 'UTF-8'); ?>" />
                <input type="hidden" name="to" value="<?php echo htmlspecialchars($filterTo, ENT_QUOTES, 'UTF-8'); ?>" />
                <input type="hidden" name="page" value="<?php echo htmlspecialchars($page, ENT_QUOTES, 'UTF-8'); ?>" />
                <button type="submit" name="export_scope" value="all" class="button primary">Exporter tous les filtres (<?php echo $exportAllCount; ?>)</button>
                <button type="submit" name="export_scope" value="page" class="button secondary">Exporter cette page (<?php echo $exportPageCount; ?>)</button>
              </form>
            </div>
            <p><?php echo $totalMessages; ?> demande(s) affichée(s) sur <?php echo count($messages); ?> au total.</p>
            <?php if ($totalMessages > 0): ?>
              <div class="table-responsive">
                <table class="admin-table">
                  <thead>
                    <tr>
                      <th>Date</th>
                      <th>Nom</th>
                      <th>Email</th>
                      <th>Projet</th>
                      <th>Budget</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                <?php foreach ($displayMessages as $message): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($message['date'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($message['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($message['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($message['project'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($message['budget'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                    <td>
                      <form action="dashboard.php" method="POST" class="admin-inline-form">
                        <input type="hidden" name="action" value="delete_message" />
                        <input type="hidden" name="line_index" value="<?php echo htmlspecialchars($message['_line'], ENT_QUOTES, 'UTF-8'); ?>" />
                        <button type="submit" class="button secondary">Supprimer</button>
                      </form>
                    </td>
                  </tr>
                <?php endforeach; ?>
                </tbody>
              </table>
              </div>
              <div class="pagination">
                <?php if ($page > 1): ?>
                  <a class="button secondary" href="dashboard.php?page=<?php echo $page - 1; ?>&from=<?php echo urlencode($filterFrom); ?>&to=<?php echo urlencode($filterTo); ?>">Précédent</a>
                <?php endif; ?>
                <span>Page <?php echo $page; ?> / <?php echo $totalPages; ?></span>
                <?php if ($page < $totalPages): ?>
                  <a class="button secondary" href="dashboard.php?page=<?php echo $page + 1; ?>&from=<?php echo urlencode($filterFrom); ?>&to=<?php echo urlencode($filterTo); ?>">Suivant</a>
                <?php endif; ?>
              </div>
            <?php else: ?>
              <p>Aucune demande client correspondant aux filtres.</p>
            <?php endif; ?>
            <form id="clear-messages" action="dashboard.php" method="POST">
              <input type="hidden" name="action" value="clear_messages" />
            </form>
          <?php else: ?>
            <p>Aucune demande client enregistrée.</p>
          <?php endif; ?>
        </article>
      </div>
    </section>
  </main>
</body>
</html>
