<?php
header('Content-Type: application/json; charset=utf-8');

$pdo = new PDO(
    'mysql:host=localhost;dbname=app_warnet;charset=utf8mb4',
    'root',
    '',
    [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]
);

function json_out($data, $code = 200) {
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function input($key, $default = '') {
    if (isset($_POST[$key])) return trim($_POST[$key]);
    if (isset($_GET[$key]))  return trim($_GET[$key]);
    return $default;
}

function require_token($pdo) {
    $token = $_SERVER['HTTP_AUTHORIZATION'] ?? input('token');
    $token = str_replace('Bearer ', '', $token);
    if (!$token) json_out(['status' => false, 'message' => 'Token kosong'], 401);

    $stmt = $pdo->prepare("SELECT id, username, nama, role, created_at
                           FROM users
                           WHERE token = ? AND token_exp > NOW()");
    $stmt->execute([$token]);
    $u = $stmt->fetch();
    if (!$u) json_out(['status' => false, 'message' => 'Token tidak valid'], 401);
    return $u;
}

function require_admin($user) {
    if ($user['role'] !== 'admin')
        json_out(['status' => false, 'message' => 'Akses admin saja'], 403);
}