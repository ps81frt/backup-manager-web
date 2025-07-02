<?php
session_start();

// Configuration
$SCRIPT_DIR = dirname(__DIR__);

// Fonction pour lire config.sh
function readConfigValue($key) {
    $configFile = '../config.sh';
    if (!file_exists($configFile)) return null;
    
    $content = file_get_contents($configFile);
    if (preg_match('/^' . preg_quote($key) . '="?([^"\n]+)"?/m', $content, $matches)) {
        return trim($matches[1], '"');
    }
    return null;
}

// Fonction pour lire les sauvegardes depuis config.sh
function getSauvegardes() {
    global $CONFIG_FILE;
    $sauvegardes = [];
    
    // Sauvegardes par défaut (depuis fichier de config)
    $defaultConfig = '../default_backups.conf';
    $defaults = ['docs_eric', 'docs_fanou', 'photos_vm', 'projets_serveur', 'docs_portable'];
    
    if (file_exists($defaultConfig)) {
        $content = file_get_contents($defaultConfig);
        foreach ($defaults as $name) {
            // Vérifier si la sauvegarde est activée (pas commentée)
            if (preg_match('/^' . preg_quote($name) . '=1/m', $content)) {
                $sauvegardes[$name] = ['type' => 'default', 'name' => $name, 'enabled' => true];
            } else {
                $sauvegardes[$name] = ['type' => 'default', 'name' => $name, 'enabled' => false];
            }
        }
    } else {
        // Fallback si le fichier n'existe pas
        foreach ($defaults as $name) {
            $sauvegardes[$name] = ['type' => 'default', 'name' => $name, 'enabled' => true];
        }
    }
    
    // Ajouter les sauvegardes personnalisées
    $customConfig = '../sauvegardes_custom.conf';
    if (file_exists($customConfig)) {
        $content = file_get_contents($customConfig);
        preg_match_all('/# SAUVEGARDE: (.+)/', $content, $matches);
        
        foreach ($matches[1] as $name) {
            $name = trim($name);
            $sauvegardes[$name] = ['type' => 'custom', 'name' => $name];
        }
    }
    
    return $sauvegardes;
}

// Fonction pour vérifier si une sauvegarde par défaut est activée
function isDefaultBackupEnabled($backupName) {
    $defaultConfig = '../default_backups.conf';
    
    if (!file_exists($defaultConfig)) {
        return true; // Si pas de fichier, tout activé par défaut
    }
    
    $content = file_get_contents($defaultConfig);
    return preg_match('/^' . preg_quote($backupName) . '=1/m', $content);
}

// Fonction pour activer/désactiver une sauvegarde par défaut
function toggleDefaultBackup($backupName) {
    $defaultConfig = '../default_backups.conf';
    
    if (!file_exists($defaultConfig)) {
        return "Fichier de configuration non trouvé";
    }
    
    $content = file_get_contents($defaultConfig);
    
    // Vérifier l'état actuel
    if (preg_match('/^' . preg_quote($backupName) . '=1/m', $content)) {
        // Actuellement activée, désactiver
        $content = preg_replace('/^' . preg_quote($backupName) . '=1/m', $backupName . '=0', $content);
        $message = "Sauvegarde '$backupName' désactivée";
    } else {
        // Actuellement désactivée, activer
        $content = preg_replace('/^' . preg_quote($backupName) . '=0/m', $backupName . '=1', $content);
        $message = "Sauvegarde '$backupName' activée";
    }
    
    file_put_contents($defaultConfig, $content, LOCK_EX);
    return $message;
}

// Fonction pour exécuter une sauvegarde
function executerSauvegarde($selection, $dryRun = false) {
    global $SCRIPT_DIR;
    
    // Vérifier si c'est une sauvegarde par défaut désactivée
    $defaultBackups = ['docs_eric', 'docs_fanou', 'photos_vm', 'projets_serveur', 'docs_portable'];
    if (in_array($selection, $defaultBackups) && !isDefaultBackupEnabled($selection)) {
        return "Erreur: La sauvegarde '$selection' est désactivée dans default_backups.conf";
    }
    
    // Validation sécurisée - inclure les sauvegardes personnalisées
    $allowed = ['docs_eric', 'docs_fanou', 'photos_vm', 'projets_serveur', 'docs_portable', 'all'];
    
    // Ajouter les sauvegardes personnalisées à la liste autorisée
    $customConfig = '../sauvegardes_custom.conf';
    if (file_exists($customConfig)) {
        $content = file_get_contents($customConfig);
        preg_match_all('/# SAUVEGARDE: (.+)/', $content, $matches);
        foreach ($matches[1] as $name) {
            $allowed[] = trim($name);
        }
    }
    
    if (!in_array($selection, $allowed)) {
        return "Erreur: Sélection non autorisée";
    }
    
    $selection = escapeshellarg($selection);
    $script = escapeshellarg("$SCRIPT_DIR/sauvegarde.sh");
    
    $cmd = "cd \"$SCRIPT_DIR\" && ";
    $cmd .= $dryRun ? "$script --dry-run $selection" : "$script $selection";
    $cmd .= " 2>&1";
    
    return shell_exec($cmd);
}

// Traitement des actions
$message = '';
$error = '';

if ($_POST) {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'run_backup':
                $selection = $_POST['selection'] ?? '';
                $dryRun = isset($_POST['dry_run']);
                if ($selection) {
                    $result = executerSauvegarde($selection, $dryRun);
                    $message = "Sauvegarde exécutée : " . ($dryRun ? "(simulation)" : "") . "\n" . $result;
                }
                break;
            case 'toggle_default_backup':
                $backupName = $_POST['backup_name'] ?? '';
                if ($backupName) {
                    $result = toggleDefaultBackup($backupName);
                    $message = $result;
                }
                break;
        }
    }
}

$sauvegardes = getSauvegardes();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Backup Manager - Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="app-layout">
        <nav class="sidebar">
            <div class="sidebar-header">
                <h2>🛡️ Backup Manager</h2>
            </div>
            <ul class="nav-menu">
                <li><a href="index.php" class="active"><span class="icon">📊</span>Dashboard</a></li>
                <li><a href="manage.php"><span class="icon">⚙️</span>Sauvegardes</a></li>
                <li><a href="logs.php"><span class="icon">📋</span>Logs</a></li>
                <li><a href="terminal.php"><span class="icon">💻</span>Terminal</a></li>
            </ul>
        </nav>
        
        <main class="main-content">
            <header class="page-header">
                <h1>Dashboard</h1>
                <div class="system-status">
                    <span class="status-indicator"></span>
                    Système opérationnel
                </div>
            </header>

            <div class="content-area">
                <?php if ($message): ?>
                    <div class="alert alert-success">
                        <pre><?= htmlspecialchars($message) ?></pre>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <!-- Métriques principales -->
                <div class="dashboard-grid">
                    <div class="metric-card">
                        <div class="metric-value" id="backup-status-metric">🟢</div>
                        <div class="metric-label">Statut</div>
                        <div id="backup-status" class="backup-status-active">Inactif</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-value" id="progress-metric">0%</div>
                        <div class="metric-label">Progression</div>
                        <div class="progress">
                            <div id="progress-fill" class="progress-bar" style="width: 0%"></div>
                        </div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-value" id="duration-metric">-</div>
                        <div class="metric-label">Durée</div>
                        <div id="current-backup">Aucune sauvegarde</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-value"><?= count($sauvegardes) ?></div>
                        <div class="metric-label">Sauvegardes</div>
                        <div>Configurées</div>
                    </div>
                </div>
                
                <!-- Logs en temps réel -->
                <div class="card">
                    <div class="card-header">
                        <h3>Activité en temps réel</h3>
                    </div>
                    <div class="card-body">
                        <div id="live-logs" class="logs"></div>
                    </div>
                </div>

                <!-- Sauvegardes disponibles -->
                <div class="card">
                    <div class="card-header">
                        <h3>Sauvegardes Disponibles</h3>
                        <button class="btn btn-primary" onclick="runBackup('all')">
                            🚀 Exécuter Toutes
                        </button>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Type</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($sauvegardes as $name => $info): ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($name) ?></strong></td>
                                        <td>
                                            <span class="badge"><?= $info['type'] === 'default' ? 'Défaut' : 'Custom' ?></span>
                                        </td>
                                        <td>
                                            <?php if (isset($info['enabled']) && !$info['enabled']): ?>
                                                <span class="backup-status-disabled">❌ Désactivée</span>
                                            <?php else: ?>
                                                <span class="backup-status-active">✅ Active</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (isset($info['enabled']) && !$info['enabled']): ?>
                                                <?php if ($info['type'] === 'default'): ?>
                                                    <form method="post" style="display: inline;">
                                                        <input type="hidden" name="action" value="toggle_default_backup">
                                                        <input type="hidden" name="backup_name" value="<?= htmlspecialchars($name) ?>">
                                                        <button type="submit" class="btn btn-success">Activer</button>
                                                    </form>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <button class="btn btn-primary" onclick="runBackup('<?= htmlspecialchars($name) ?>')">
                                                    Exécuter
                                                </button>
                                                <button class="btn" onclick="runBackup('<?= htmlspecialchars($name) ?>', true)">
                                                    Test
                                                </button>
                                                <?php if ($info['type'] === 'default'): ?>
                                                    <form method="post" style="display: inline;">
                                                        <input type="hidden" name="action" value="toggle_default_backup">
                                                        <input type="hidden" name="backup_name" value="<?= htmlspecialchars($name) ?>">
                                                        <button type="submit" class="btn btn-warning">Désactiver</button>
                                                    </form>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <!-- Zone de messages -->
    <div id="message-area"></div>
    <script src="app.js"></script>
</body>
</html>