<?php
header('Content-Type: application/json');

// Lire les fichiers de statut
$status = [
    'running' => file_exists('/tmp/backup_running.flag'),
    'current_backup' => file_exists('/tmp/current_backup.txt') ? trim(file_get_contents('/tmp/current_backup.txt')) : null,
    'progress' => file_exists('/tmp/backup_progress.txt') ? trim(file_get_contents('/tmp/backup_progress.txt')) : '0',
    'start_time' => file_exists('/tmp/backup_start_time.txt') ? trim(file_get_contents('/tmp/backup_start_time.txt')) : null,
    'last_log' => []
];

// Lire les dernières lignes du log courant
$logFile = '/var/log/sauvegardes/sauvegarde_' . date('Ymd') . '.log';
if (file_exists($logFile)) {
    $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $status['last_log'] = array_slice($lines, -5); // 5 dernières lignes
}

// Statistiques rapides
$status['stats'] = [
    'total_backups' => 5, // Nombre de sauvegardes configurées
    'last_success' => file_exists('/tmp/last_success.txt') ? trim(file_get_contents('/tmp/last_success.txt')) : null,
    'last_error' => file_exists('/tmp/last_error.txt') ? trim(file_get_contents('/tmp/last_error.txt')) : null
];

echo json_encode($status);
?>