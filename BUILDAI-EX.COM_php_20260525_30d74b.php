<?php
$request = $_SERVER['REQUEST_URI'];
switch ($request) {
    case '/':
    case '/home':
        require __DIR__ . '/views/home.php';
        break;
    case '/contact':
        require __DIR__ . '/views/contact.php';
        break;
    default:
        http_response_code(404);
        require __DIR__ . '/views/404.php';
        break;
}