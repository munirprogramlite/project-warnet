<?php
$user = require_token($pdo);
$created = new DateTime($user['created_at']);

json_out([
    'status' => true,
    'profil' => [
        'nama'         => $user['nama'],
        'username'     => $user['username'],
        'role'         => $user['role'],
        'inisial'      => strtoupper(substr($user['nama'], 0, 1)),
        'member_sejak' => $created->format('d M Y'),
    ],
    'app' => [
        'nama'      => 'Warnet Booking',
        'versi'     => '1.0.0',
        'deskripsi' => 'Aplikasi booking PC warnet online',
        'developer' => 'Tim Developer',
    ],
]);