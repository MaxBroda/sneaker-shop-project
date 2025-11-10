<?php
$startupFile = __DIR__ . '/startup_time.txt';

if (file_exists($startupFile)) {
    $startupTime = (int) file_get_contents($startupFile);
    $uptimeSeconds = time() - $startupTime;

    $days = floor($uptimeSeconds / 86400);
    $hours = floor($uptimeSeconds / 3600);
    $minutes = floor(($uptimeSeconds % 3600) / 60);
    $seconds = $uptimeSeconds % 60;

    $uptime = sprintf('%02dd %02dh %02dm %02ds', $days, $hours, $minutes, $seconds);
} else {
    $uptime = 'Unknown (startup_time.txt not found)';
}

header('Content-Type: application/json');
echo json_encode([
    'status' => 'ok',
    'message' => 'Sneaker Shop Backend API läuft!',
    'uptime' => $uptime,
    'time' => date('Y-m-d H:i:s')
]);
