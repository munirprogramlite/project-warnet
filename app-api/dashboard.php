<?php
$user = require_token($pdo);

$total = $pdo->prepare("SELECT COUNT(*) AS n FROM bookings WHERE user_id = ?");
$total->execute([$user['id']]);
$totalBooking = (int)$total->fetch()['n'];

$aktif = $pdo->prepare("SELECT COUNT(*) AS n FROM bookings
                        WHERE user_id = ? AND status IN ('pending','confirmed')");
$aktif->execute([$user['id']]);
$bookingAktif = (int)$aktif->fetch()['n'];

$created = new DateTime($user['created_at']);
$hari    = (int)$created->diff(new DateTime())->days;

json_out([
    'status' => true,
    'user'   => [
        'nama'     => $user['nama'],
        'username' => $user['username'],
        'role'     => $user['role'],
    ],
    'statistik' => [
        'total_booking'  => $totalBooking,
        'booking_aktif'  => $bookingAktif,
        'hari_bergabung' => $hari,
    ],
]);