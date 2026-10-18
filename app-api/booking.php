<?php
$user = require_token($pdo);
$mode = input('mode', 'list');

// ============ USER ============

// Lihat riwayat booking sendiri
if ($mode === 'list') {
    $stmt = $pdo->prepare("SELECT b.*, p.nama AS pc_nama
                           FROM bookings b
                           JOIN pcs p ON p.id = b.pc_id
                           WHERE b.user_id = ?
                           ORDER BY b.id DESC");
    $stmt->execute([$user['id']]);
    json_out(['status' => true, 'bookings' => $stmt->fetchAll()]);
}

// Buat booking baru
if ($mode === 'tambah') {
    $pc_id   = (int)input('pc_id');
    $tanggal = input('tanggal');       // YYYY-MM-DD
    $jam     = input('jam_mulai');     // HH:MM
    $durasi  = (int)input('durasi_jam');
    $catatan = input('catatan');

    if (!$pc_id || !$tanggal || !$jam || $durasi < 1)
        json_out(['status' => false, 'message' => 'Lengkapi data booking'], 400);

    // cek PC tersedia
    $pc = $pdo->prepare("SELECT * FROM pcs WHERE id = ? AND status = 'tersedia'");
    $pc->execute([$pc_id]);
    $pcData = $pc->fetch();
    if (!$pcData)
        json_out(['status' => false, 'message' => 'PC tidak tersedia'], 400);

    // cek bentrok jadwal
    $bentrok = $pdo->prepare("
        SELECT id FROM bookings
        WHERE pc_id = ? AND tanggal = ?
          AND status IN ('pending','confirmed')
          AND NOT (TIME(?) + INTERVAL ? HOUR <= jam_mulai
                OR TIME(?) >= jam_mulai + INTERVAL durasi_jam HOUR)
    ");
    $bentrok->execute([$pc_id, $tanggal, $jam, $durasi, $jam]);
    if ($bentrok->fetch())
        json_out(['status' => false, 'message' => 'Jadwal bentrok, pilih jam lain'], 409);

    $total = $pcData['harga_per_jam'] * $durasi;

    $pdo->prepare("INSERT INTO bookings
                   (user_id, pc_id, tanggal, jam_mulai, durasi_jam, total_harga, catatan)
                   VALUES (?,?,?,?,?,?,?)")
        ->execute([$user['id'], $pc_id, $tanggal, $jam, $durasi, $total, $catatan]);

    json_out([
        'status'      => true,
        'message'     => 'Booking dibuat, menunggu konfirmasi admin',
        'total_harga' => $total,
    ]);
}

// Batalkan booking sendiri (hanya yang pending)
if ($mode === 'batal') {
    $id = (int)input('id');
    $pdo->prepare("UPDATE bookings SET status = 'cancelled'
                   WHERE id = ? AND user_id = ? AND status = 'pending'")
        ->execute([$id, $user['id']]);
    json_out(['status' => true, 'message' => 'Booking dibatalkan']);
}

// ============ ADMIN ============

// Lihat semua booking
if ($mode === 'admin-list') {
    require_admin($user);
    $rows = $pdo->query("
        SELECT b.*, p.nama AS pc_nama, u.nama AS user_nama, u.username
        FROM bookings b
        JOIN pcs p   ON p.id = b.pc_id
        JOIN users u ON u.id = b.user_id
        ORDER BY b.id DESC
    ")->fetchAll();
    json_out(['status' => true, 'bookings' => $rows]);
}

// Ubah status booking
if ($mode === 'admin-update') {
    require_admin($user);
    $id     = (int)input('id');
    $status = input('status');

    if (!in_array($status, ['confirmed','done','cancelled']))
        json_out(['status' => false, 'message' => 'Status tidak valid'], 400);

    $pdo->prepare("UPDATE bookings SET status = ? WHERE id = ?")
        ->execute([$status, $id]);
    json_out(['status' => true, 'message' => 'Status booking diubah']);
}

json_out(['status' => false, 'message' => 'Mode tidak dikenal'], 400);