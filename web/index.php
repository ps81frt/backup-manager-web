<?php
session_start();

// Configuration
$CONFIG_FILE = '../config.sh';
$SCRIPT_DIR = dirname(__DIR__);

// Fonction pour lire les sauvegardes depuis config.sh
function getSauvegardes() {
    global $CONFIG_FILE;
    $sauvegardes = [];
    
    // Sauvegardes par défaut
    $defaults = ['docs_eric', 'docs_fanou', 'photos_vm', 'projets_serveur', 'docs_portable'];
    foreach ($defaults as $name) {
        $sauvegardes[$name] = ['type' => 'default', 'name' => $name];
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

// Fonction pour exécuter une sauvegarde
function executerSauvegarde($selection, $dryRun = false) {
    global $SCRIPT_DIR;
    $cmd = "cd \"$SCRIPT_DIR\" && ";
    $cmd .= $dryRun ? "./sauvegarde.sh --dry-run $selection" : "./sauvegarde.sh $selection";
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
    <title>Gestionnaire de Sauvegardes</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>🔄 Gestionnaire de Sauvegardes</h1>
            <nav>
                <a href="index.php" class="active">Accueil</a>
                <a href="manage.php">Gérer</a>
                <a href="logs.php">Logs</a>
            </nav>
        </header>

        <?php if ($message): ?>
            <div class="alert success">
                <pre><?= htmlspecialchars($message) ?></pre>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="grid">
            <div class="card">
                <h2>Sauvegardes Disponibles</h2>
                <div class="backup-list">
                    <?php foreach ($sauvegardes as $name => $info): ?>
                        <div class="backup-item">
                            <span class="backup-name"><?= htmlspecialchars($name) ?></span>
                            <span class="backup-type"><?= $info['type'] === 'default' ? 'Défaut' : 'Custom' ?></span>
                            <form method="post" style="display: inline;">
                                <input type="hidden" name="action" value="run_backup">
                                <input type="hidden" name="selection" value="<?= htmlspecialchars($name) ?>">
                                <button type="submit" class="btn btn-primary">Exécuter</button>
                                <label>
                                    <input type="checkbox" name="dry_run"> Test
                                </label>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="card">
                <h2>Actions Rapides</h2>
                <form method="post">
                    <input type="hidden" name="action" value="run_backup">
                    <input type="hidden" name="selection" value="all">
                    <button type="submit" class="btn btn-success btn-large">
                        🚀 Exécuter Toutes les Sauvegardes
                    </button>
                    <label>
                        <input type="checkbox" name="dry_run"> Mode test uniquement
                    </label>
                </form>
            </div>

            <div class="card">
                <h2>État du Système</h2>
                <div class="status">
                    <div class="status-item">
                        <span>Scripts de base :</span>
                        <span class="status-ok">✓ Opérationnels</span>
                    </div>
                    <div class="status-item">
                        <span>Sauvegardes configurées :</span>
                        <span><?= count($sauvegardes) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>