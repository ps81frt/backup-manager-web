<?php
session_start();

// Lire la configuration depuis config.sh
function getConfigValue($key) {
    $configFile = '../config.sh';
    if (!file_exists($configFile)) return null;
    
    $content = file_get_contents($configFile);
    if (preg_match('/^' . preg_quote($key) . '="?([^"\n]+)"?/m', $content, $matches)) {
        return trim($matches[1], '"');
    }
    return null;
}

// Adapter le répertoire de logs selon le contexte
$configLogDir = getConfigValue('LOG_DIR');
$LOG_DIR = ($configLogDir && is_writable($configLogDir)) ? $configLogDir : '/tmp/backup_logs';

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
    $filename = basename($filename); // Sécurité: éviter path traversal
    $filepath = $LOG_DIR . '/' . $filename;
    
    if (!file_exists($filepath)) return "Fichier non trouvé";
    if (!is_readable($filepath)) return "Fichier non accessible";
    
    return file_get_contents($filepath);
}

// Fonction pour calculer les statistiques des logs
function calculateLogStats() {
    global $LOG_DIR;
    $stats = ['success' => 0, 'error' => 0, 'total_size' => 0];
    
    if (!is_dir($LOG_DIR)) return $stats;
    
    $files = glob($LOG_DIR . '/sauvegarde_*.log');
    $cutoff = time() - (7 * 24 * 60 * 60); // 7 jours
    
    foreach ($files as $file) {
        if (filemtime($file) < $cutoff) continue; // Ignorer les fichiers plus anciens que 7 jours
        
        $stats['total_size'] += filesize($file);
        $content = file_get_contents($file);
        
        // Compter les succès et erreurs
        if (preg_match('/Sauvegardes réussies: (\d+)/', $content, $matches)) {
            $stats['success'] += intval($matches[1]);
        }
        if (preg_match('/Sauvegardes échouées: (\d+)/', $content, $matches)) {
            $stats['error'] += intval($matches[1]);
        }
    }
    
    $stats['total_size'] = round($stats['total_size'] / 1024, 1); // Convertir en KB
    return $stats;
}

$selectedLog = $_GET['log'] ?? '';
$showStats = $_GET['stats'] ?? '';
$logContent = '';

if ($selectedLog) {
    $logContent = readLogFile($selectedLog);
} elseif ($showStats) {
    // Calculer les statistiques réelles
    $stats = calculateLogStats();
    header('Content-Type: application/json');
    echo json_encode($stats);
    exit;
}

$logFiles = getLogFiles();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Backup Manager - Logs</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="app-layout">
        <nav class="sidebar">
            <div class="sidebar-header">
                <h2>🛡️ Backup Manager</h2>
            </div>
            <ul class="nav-menu">
                <li><a href="index.php"><span class="icon">📊</span>Dashboard</a></li>
                <li><a href="manage.php"><span class="icon">⚙️</span>Sauvegardes</a></li>
                <li><a href="logs.php" class="active"><span class="icon">📋</span>Logs</a></li>
                <li><a href="terminal.php"><span class="icon">💻</span>Terminal</a></li>
            </ul>
        </nav>
        
        <main class="main-content">
            <header class="page-header">
                <h1>Journaux de Sauvegarde</h1>
            </header>
            
            <div class="content-area">
                
                <div class="dashboard-grid">
                    <!-- Statistiques des logs -->
                    <div class="metric-card">
                        <div class="metric-value"><?= count($logFiles) ?></div>
                        <div class="metric-label">Fichiers de Log</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-value"><?= htmlspecialchars($LOG_DIR) ?></div>
                        <div class="metric-label">Répertoire</div>
                    </div>
                </div>

                <!-- Liste des fichiers de log -->
                <div class="card">
                    <div class="card-header">
                        <h3>Fichiers de Log Disponibles</h3>
                    </div>
                    <div class="card-body">
                        <?php if (empty($logFiles)): ?>
                            <div class="alert alert-info">
                                Aucun fichier de log trouvé dans <?= htmlspecialchars($LOG_DIR) ?>
                            </div>
                        <?php else: ?>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Fichier</th>
                                        <th>Date</th>
                                        <th>Taille</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($logFiles as $log): ?>
                                        <tr class="<?= $selectedLog === $log['file'] ? 'table-active' : '' ?>">
                                            <td><strong><?= htmlspecialchars($log['file']) ?></strong></td>
                                            <td><?= $log['date'] ?></td>
                                            <td><?= number_format($log['size'] / 1024, 1) ?> KB</td>
                                            <td>
                                                <a href="?log=<?= urlencode($log['file']) ?>" 
                                                   class="btn <?= $selectedLog === $log['file'] ? 'btn-primary' : 'btn-secondary' ?>">
                                                    <?= $selectedLog === $log['file'] ? 'Affiché' : 'Voir' ?>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Contenu du log sélectionné -->
                <?php if ($selectedLog && $logContent): ?>
                <div class="card">
                    <div class="card-header">
                        <h3>Contenu : <?= htmlspecialchars($selectedLog) ?></h3>
                    </div>
                    <div class="card-body">
                        <div class="logs"><?= htmlspecialchars($logContent) ?></div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Commandes utiles -->
                <div class="card">
                    <div class="card-header">
                        <h3>Commandes Utiles</h3>
                    </div>
                    <div class="card-body">
                        <div class="command-list">
                            <div class="command-item">
                                <strong>Suivre les logs en temps réel :</strong>
                                <code>tail -f <?= htmlspecialchars($LOG_DIR) ?>/sauvegarde_$(date +%Y%m%d).log</code>
                            </div>
                            <div class="command-item">
                                <strong>Rechercher des erreurs :</strong>
                                <code>grep "ERREUR" <?= htmlspecialchars($LOG_DIR) ?>/sauvegarde_*.log</code>
                            </div>
                            <div class="command-item">
                                <strong>Compter les succès :</strong>
                                <code>grep -c "Sauvegarde réussie" <?= htmlspecialchars($LOG_DIR) ?>/sauvegarde_*.log</code>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>