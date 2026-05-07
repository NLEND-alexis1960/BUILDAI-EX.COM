<?php
function loadDotenv($path = __DIR__ . '/../.env') {
    if (!file_exists($path)) {
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '#') === 0) {
            continue;
        }

        if (strpos($line, '=') === false) {
            continue;
        }

        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);

        if (getenv($name) === false) {
            putenv($name . '=' . $value);
        }
    }
}

loadDotenv();

function env($name, $default = null) {
    $value = getenv($name);
    return $value !== false ? $value : $default;
}

function getStripeSecretKey() {
    return env('STRIPE_SECRET_KEY');
}

function getStripeProductId() {
    return env('STRIPE_PRODUCT_ID');
}

function stripeApiRequest(string $endpoint, array $params = []): ?array {
    $secretKey = getStripeSecretKey();
    if (!$secretKey) {
        return null;
    }

    $url = 'https://api.stripe.com/v1/' . $endpoint;
    if (!empty($params)) {
        $url .= '?' . http_build_query($params);
    }

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD, $secretKey . ':');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FAILONERROR, false);

    $response = curl_exec($ch);
    $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($response === false || $httpStatus >= 400) {
        return null;
    }

    $data = json_decode($response, true);
    return is_array($data) ? $data : null;
}

function formatStripePrice(int $amount, string $currency): string {
    $amount = $amount / 100;
    $currency = strtoupper($currency);
    if ($amount == intval($amount)) {
        $amount = intval($amount);
    }
    return number_format($amount, 0, ',', ' ') . ' ' . $currency;
}

function getStripePlans(): array {
    $result = stripeApiRequest('prices', [
        'limit' => 20,
        'active' => 'true',
        'expand[]' => 'data.product',
    ]);

    if (!is_array($result) || empty($result['data'])) {
        return [];
    }

    $plans = [];
    foreach ($result['data'] as $price) {
        if (empty($price['active']) || empty($price['recurring'])) {
            continue;
        }

        if ($productId = getStripeProductId()) {
            if (empty($price['product']['id']) || $price['product']['id'] !== $productId) {
                continue;
            }
        }

        $plans[] = [
            'id' => $price['id'],
            'nickname' => $price['nickname'] ?: ($price['product']['name'] ?? 'Abonnement'),
            'amount' => $price['unit_amount'],
            'currency' => $price['currency'],
            'interval' => $price['recurring']['interval'] ?? 'month',
            'interval_count' => $price['recurring']['interval_count'] ?? 1,
            'active' => $price['active'] ?? true,
            'product_name' => $price['product']['name'] ?? '',
            'description' => $price['product']['description'] ?? '',
        ];
    }

    usort($plans, function ($a, $b) {
        return $a['amount'] <=> $b['amount'];
    });

    return $plans;
}

function getFallbackPlans(): array {
    $priceId = env('STRIPE_PRICE_ID');
    if (!$priceId) {
        return [];
    }

    return [
        [
            'id' => $priceId,
            'nickname' => 'Offre par défaut',
            'amount' => 4900,
            'currency' => 'EUR',
            'interval' => 'month',
            'interval_count' => 1,
            'product_name' => 'Abonnement BuildAI-EX',
            'description' => 'Offre unique de création et maintenance',
        ],
    ];
}

function getAvailableStripePlans(): array {
    $plans = getStripePlans();
    if (!empty($plans)) {
        return $plans;
    }
    return getFallbackPlans();
}

function isValidStripePlanId(string $priceId): bool {
    $plans = getAvailableStripePlans();
    foreach ($plans as $plan) {
        if ($plan['id'] === $priceId) {
            return true;
        }
    }
    return false;
}

function createStripePrice(array $data): ?array {
    $secretKey = getStripeSecretKey();
    if (!$secretKey) {
        return null;
    }

    $payload = http_build_query($data);
    $ch = curl_init('https://api.stripe.com/v1/prices');
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD, $secretKey . ':');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response === false || $httpStatus >= 400) {
        return null;
    }

    $data = json_decode($response, true);
    return is_array($data) ? $data : null;
}

function updateStripePrice(string $priceId, array $data): ?array {
    $secretKey = getStripeSecretKey();
    if (!$secretKey) {
        return null;
    }

    $payload = http_build_query($data);
    $ch = curl_init('https://api.stripe.com/v1/prices/' . urlencode($priceId));
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD, $secretKey . ':');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response === false || $httpStatus >= 400) {
        return null;
    }

    $data = json_decode($response, true);
    return is_array($data) ? $data : null;
}

function getPlanById(string $priceId): ?array {
    $plans = getAvailableStripePlans();
    foreach ($plans as $plan) {
        if ($plan['id'] === $priceId) {
            return $plan;
        }
    }
    return null;
}
