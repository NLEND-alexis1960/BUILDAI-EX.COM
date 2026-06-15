<?php
require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

\Stripe\Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

define('STRIPE_PRODUCT_ID', $_ENV['STRIPE_PRODUCT_ID']);
define('CONTACT_EMAIL', $_ENV['CONTACT_EMAIL']);
define('ADMIN_PASSWORD_HASH', password_hash($_ENV['ADMIN_PASSWORD'], PASSWORD_DEFAULT));