<?php
require_once __DIR__ . '/stripe-config.php';

function redirect(string $url) {
    header('Location: ' . $url);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}

$stripeSecretKey = getStripeSecretKey();
$priceId = $_POST['price_id'] ?? '';
$email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL) ? $_POST['email'] : null;

$errors = [];
if (!$stripeSecretKey) {
    $errors[] = 'La variable STRIPE_SECRET_KEY doit être configurée.';
}
if (!$priceId) {
    $errors[] = 'Le plan Stripe sélectionné est invalide.';
} elseif (!isValidStripePlanId($priceId)) {
    $errors[] = 'Le plan Stripe choisi n’est pas autorisé.';
}
if (!$email) {
    $errors[] = 'Une adresse email valide est requise pour l’abonnement.';
}

if (!empty($errors)) {
    http_response_code(400);
    echo '<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"><title>Erreur Stripe</title></head><body>';
    echo '<h1>Erreur de paiement Stripe</h1>';
    echo '<ul>';
    foreach ($errors as $error) {
        echo '<li>' . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . '</li>';
    }
    echo '</ul>';
    echo '<p><a href="index.php">Retour</a></p>';
    echo '</body></html>';
    exit;
}

$payload = http_build_query([
    'mode' => 'subscription',
    'success_url' => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/success.php?session_id={CHECKOUT_SESSION_ID}',
    'cancel_url' => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/cancel.php',
    'payment_method_types[]' => 'card',
    'line_items[0][price]' => $priceId,
    'line_items[0][quantity]' => 1,
    'billing_address_collection' => 'auto',
    'customer_email' => $email,
]);

$ch = curl_init('https://api.stripe.com/v1/checkout/sessions');
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($ch, CURLOPT_USERPWD, $stripeSecretKey . ':');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($response === false) {
    http_response_code(500);
    echo 'Erreur Stripe : ' . htmlspecialchars($curlError, ENT_QUOTES, 'UTF-8');
    exit;
}

$data = json_decode($response, true);

if ($httpStatus !== 200 || !isset($data['url'])) {
    http_response_code($httpStatus);
    echo '<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"><title>Erreur Stripe</title></head><body>';
    echo '<h1>Erreur Stripe</h1>';
    echo '<pre>' . htmlspecialchars($response, ENT_QUOTES, 'UTF-8') . '</pre>';
    echo '<p><a href="index.php">Retour</a></p>';
    echo '</body></html>';
    exit;
}

redirect($data['url']);
