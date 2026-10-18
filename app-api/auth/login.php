<?php
$u = isset($_REQUEST['username']) ? $_REQUEST['username'] : '';
$p = isset($_REQUEST['password']) ? $_REQUEST['password'] : '';

if (!$u || !$p)
    json_out(['status' => false, 'message' => 'Isi username & password'], 400);

$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$u]);
$user = $stmt->fetch();

// Ini bagian pengecekan password
if (!$user || !password_verify($p, $user['password']))
    json_out(['status' => false, 'message' => 'Username/password salah'], 401);

$token = bin2hex(random_bytes(16));
$pdo->prepare("UPDATE users SET token = ?, token_exp = DATE_ADD(NOW(), INTERVAL 7 DAY) WHERE id = ?")
    ->execute([$token, $user['id']]);

json_out([
    'status'  => true,
    'message' => 'Login berhasil',
    'token'   => $token,
    'user'    => [
        'id'       => (int)$user['id'],
        'username' => $user['username'],
        'nama'     => $user['nama'],
        'role'     => $user['role'],
    ],
]);