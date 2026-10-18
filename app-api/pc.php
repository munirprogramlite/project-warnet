<?php
$user = require_token($pdo);
$mode = input('mode', 'list');

// --- Semua user: lihat daftar PC
if ($mode === 'list') {
    $rows = $pdo->query("SELECT id, nama, spesifikasi, harga_per_jam, status
                         FROM pcs ORDER BY nama")->fetchAll();
    json_out(['status' => true, 'pcs' => $rows]);
}

// --- Admin: tambah PC
if ($mode === 'tambah') {
    require_admin($user);
    $nama  = input('nama');
    $spek  = input('spesifikasi');
    $harga = (int)input('harga_per_jam');

    if (!$nama || !$harga)
        json_out(['status' => false, 'message' => 'Nama & harga wajib'], 400);

    $pdo->prepare("INSERT INTO pcs (nama, spesifikasi, harga_per_jam) VALUES (?,?,?)")
        ->execute([$nama, $spek, $harga]);
    json_out(['status' => true, 'message' => 'PC ditambahkan']);
}

// --- Admin: toggle tersedia <-> maintenance
if ($mode === 'toggle-status') {
    require_admin($user);
    $id = (int)input('id');
    $pdo->prepare("UPDATE pcs
                   SET status = IF(status='tersedia','maintenance','tersedia')
                   WHERE id = ?")->execute([$id]);
    json_out(['status' => true, 'message' => 'Status PC diubah']);
}

// --- Admin: hapus PC
if ($mode === 'hapus') {
    require_admin($user);
    $id = (int)input('id');
    $pdo->prepare("DELETE FROM pcs WHERE id = ?")->execute([$id]);
    json_out(['status' => true, 'message' => 'PC dihapus']);
}

json_out(['status' => false, 'message' => 'Mode tidak dikenal'], 400);