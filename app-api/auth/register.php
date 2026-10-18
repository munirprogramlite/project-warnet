<?php
$u = input('username');
$p = input('password');
$n = input('nama');

if (!$u || !$p || !$n)
    json_out(['status' => false, 'message' => 'Lengkapi data'], 400);
if (strlen($p) < 6)
    json_out(['status' => false, 'message' => 'Password min 6 karakter'], 400);

$cek = $pdo->prepare("SELECT id FROM users WHERE username = ?");
$cek->execute([$u]);
if ($cek->fetch())
    json_out(['status' => false, 'message' => 'Username sudah dipakai'], 409);

$hash = password_hash($p, PASSWORD_DEFAULT);
$pdo->prepare("INSERT INTO users (username, password, nama, role) VALUES (?,?,?,'user')")
    ->execute([$u, $hash, $n]);

json_out(['status' => true, 'message' => 'Registrasi berhasil, silakan login']);