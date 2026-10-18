<?php
require __DIR__ . '/config.php';

$action = input('action');

switch ($action) {
    case 'login':     require __DIR__ . '/auth/login.php';    break;
    case 'register':  require __DIR__ . '/auth/register.php'; break;
    case 'dashboard': require __DIR__ . '/dashboard.php';     break;
    case 'tentang':   require __DIR__ . '/tentang.php';       break;
    case 'pc':        require __DIR__ . '/pc.php';            break;
    case 'booking':   require __DIR__ . '/booking.php';       break;
    default:          json_out(['status' => false, 'message' => 'Unknown action'], 404);
}