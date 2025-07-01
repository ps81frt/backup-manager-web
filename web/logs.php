<?php
session_start();

$LOG_DIR = '/var/log/sauvegardes';

// Fonction pour lister les fichiers de log
function getLogFiles() {
    global $LOG_DIR;
    $logs = [];
    
    if (!is_dir($LOG_DIR)) return $logs;
    
    $files = glob($LOG_DIR . '/sauvegarde_*.log');
    rsort($files); // Plus récents en premier
    
    foreach ($files as $file) {
        $logs[] = [
            'file' => basename($file),
            'path' => $file,
            'size' => filesize($file),
            'date' => date('Y-m-d H:i:s', filemtime($file))
        ];
    }
    
    return $logs;
}

// Fonction pour lire un fichier de log
function readLogFile($filename) {
    global $LOG_DIR;
    $filepath = $LOG_DIR . '/' . basename($filename);
    
    if (!file_exists($filepath)) return "Fichier non trouvé";
    
    return file_get_contents($filepath);
}

$selectedLog = $_GET['log'] ?? '';
$logContent = '';

if ($selectedLog) {
    $logContent = readLogFile($selectedLog);
}

$logFiles = getLogFiles();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logs de Sauvegarde</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>📋 Logs de Sauvegarde</h1>
            <nav>
                <a href="index.php">Accueil</a>
                <a href="manage.php">Gérer</a>
                <a href="logs.php" class="active">Logs</a>
            </nav>
        </header>

        <div class="grid">
            <div class="card">
                <h2>Fichiers de Log</h2>
                <?php if (empty($logFiles)): ?>
                    <p>Aucun fichier de log trouvé dans <?= htmlspecialchars($LOG_DIR) ?></p>
                <?php else: ?>
                    <div class="log-list">
                        <?php foreach ($logFiles as $log): ?>
                            <div class="log-item">
                                <a href="?log=<?= urlencode($log['file']) ?>" 
                                   class="log-link <?= $selectedLog === $log['file'] ? 'active' : '' ?>">
                                    <span class="log-name"><?= htmlspecialchars($log['file']) ?></span>
                                    <span class="log-info">
                                        <?= $log['date'] ?> - <?= number_format($log['size'] / 1024, 1) ?> KB
                                    </span>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($selectedLog && $logContent): ?>
            <div class="card log-viewer">
                <h2>Contenu : <?= htmlspecialchars($selectedLog) ?></h2>
                <div class="log-content">
                    <pre><?= htmlspecialchars($logContent) ?></pre>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="card">
            <h2>Log en Temps Réel</h2>
            <p>Pour suivre les logs en temps réel, utilisez :</p>
            <code>tail -f <?= htmlspecialchars($LOG_DIR) ?>/sauvegarde_$(date +%Y%m%d).log</code>
        </div>
    </div>
</body>
</html>