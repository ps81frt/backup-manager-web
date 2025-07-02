<?php
session_start();

$CUSTOM_CONFIG = '../sauvegardes_custom.conf';

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

// Fonction pour ajouter une sauvegarde personnalisée
function ajouterSauvegarde($data) {
    global $CUSTOM_CONFIG;
    
    $nom = strtolower(trim($data['nom']));
    $type = $data['type'];
    
    if (empty($nom)) return "Nom requis";
    // Validation sur le nom
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $nom)) return "Nom invalide (lettres, chiffres, _ uniquement)";
    
    // Vérifier que le nom n'existe pas déjà
    $existing = getSauvegardesCustom();
    if (in_array($nom, $existing)) return "Une sauvegarde avec ce nom existe déjà";
    
    // Vérifier la longueur
    if (strlen($nom) < 3 || strlen($nom) > 50) return "Le nom doit contenir entre 3 et 50 caractères";
    
    $config = "\n# SAUVEGARDE: $nom\n";
    
    if ($type === 'locale') {
        $config .= "SOURCE_LOCALE_" . strtoupper($nom) . '="' . addslashes($data['source']) . '"' . "\n";
        $config .= "DEST_MAIN_" . strtoupper($nom) . '="$DEST_BASE_SAUVEGARDES/' . addslashes($data['dest_name']) . '/"' . "\n";
        $config .= "DEST_INCR_BASE_" . strtoupper($nom) . '="$DEST_BASE_SAUVEGARDES/incremental-' . addslashes($data['dest_name']) . '/"' . "\n";
    } else {
        // Générer des noms de variables cohérents avec config.sh
        $nom_upper = strtoupper($nom);
        $config .= "SSH_USER_" . $nom_upper . '="' . addslashes($data['ssh_user']) . '"' . "\n";
        $config .= "SSH_IP_" . $nom_upper . '="' . addslashes($data['ssh_ip']) . '"' . "\n";
        $config .= "SSH_PORT_" . $nom_upper . '=' . intval($data['ssh_port'] ?: 22) . "\n";
        $config .= "SOURCE_DIST_" . $nom_upper . '="' . addslashes($data['source']) . '"' . "\n";
        $config .= "MONTAGE_SSHFS_" . $nom_upper . '="/tmp/sshfs_mounts/' . $nom . '"' . "\n";
        $config .= "DEST_MAIN_" . $nom_upper . '="$DEST_BASE_SAUVEGARDES/' . addslashes($data['dest_name']) . '/"' . "\n";
        $config .= "DEST_INCR_BASE_" . $nom_upper . '="$DEST_BASE_SAUVEGARDES/incremental-' . addslashes($data['dest_name']) . '/"' . "\n";
    }
    
    // Variables de rétention cohérentes
    $nom_upper = strtoupper($nom);
    $config .= "JOURS_RETENTION_" . $nom_upper . "_QUOTIDIEN=" . intval($data['retention_q'] ?: 7) . "\n";
    $config .= "JOURS_RETENTION_" . $nom_upper . "_HEBDO=" . intval($data['retention_h'] ?: 4) . "\n";
    $config .= "JOURS_RETENTION_" . $nom_upper . "_MENSUEL=" . intval($data['retention_m'] ?: 12) . "\n";
    
    if (file_put_contents($CUSTOM_CONFIG, $config, FILE_APPEND | LOCK_EX) === false) {
        return "Erreur d'écriture du fichier de configuration";
    }
    return "Sauvegarde ajoutée avec succès";
}

// Fonction pour supprimer une sauvegarde personnalisée
function supprimerSauvegarde($nom) {
    global $CUSTOM_CONFIG;
    
    if (!file_exists($CUSTOM_CONFIG)) return "Fichier de configuration non trouvé";
    
    $content = file_get_contents($CUSTOM_CONFIG);
    $nom_upper = strtoupper($nom);
    
    // Supprimer toutes les lignes de cette sauvegarde
    $pattern = '/\n# SAUVEGARDE: ' . preg_quote($nom) . '\n.*?(?=\n# SAUVEGARDE:|$)/s';
    $content = preg_replace($pattern, '', $content);
    
    file_put_contents($CUSTOM_CONFIG, $content, LOCK_EX);
    return "Sauvegarde supprimée avec succès";
}

// Fonction pour désactiver/activer une sauvegarde
function toggleSauvegarde($nom) {
    global $CUSTOM_CONFIG;
    
    if (!file_exists($CUSTOM_CONFIG)) return "Fichier de configuration non trouvé";
    
    $content = file_get_contents($CUSTOM_CONFIG);
    
    // Vérifier si déjà désactivée
    if (strpos($content, "# SAUVEGARDE_DISABLED: $nom") !== false) {
        // Réactiver
        $content = str_replace("# SAUVEGARDE_DISABLED: $nom", "# SAUVEGARDE: $nom", $content);
        $message = "Sauvegarde réactivée";
    } else {
        // Désactiver
        $content = str_replace("# SAUVEGARDE: $nom", "# SAUVEGARDE_DISABLED: $nom", $content);
        $message = "Sauvegarde désactivée";
    }
    
    file_put_contents($CUSTOM_CONFIG, $content, LOCK_EX);
    return $message;
}

// Fonction pour lister les sauvegardes personnalisées
function getSauvegardesCustom() {
    global $CUSTOM_CONFIG;
    $sauvegardes = [];
    
    if (!file_exists($CUSTOM_CONFIG)) return $sauvegardes;
    
    $content = file_get_contents($CUSTOM_CONFIG);
    
    // Sauvegardes actives
    preg_match_all('/# SAUVEGARDE: (.+)/', $content, $matches);
    foreach ($matches[1] as $name) {
        $sauvegardes[trim($name)] = ['status' => 'active'];
    }
    
    // Sauvegardes désactivées
    preg_match_all('/# SAUVEGARDE_DISABLED: (.+)/', $content, $matches);
    foreach ($matches[1] as $name) {
        $sauvegardes[trim($name)] = ['status' => 'disabled'];
    }
    
    return $sauvegardes;
}

$message = '';
$error = '';

if ($_POST) {
    if (isset($_POST['action'])) {
        try {
            switch ($_POST['action']) {
                case 'add_backup':
                    $result = ajouterSauvegarde($_POST);
                    $message = $result;
                    break;
                case 'delete_backup':
                    $result = supprimerSauvegarde($_POST['backup_name']);
                    $message = $result;
                    break;
                case 'toggle_backup':
                    $result = toggleSauvegarde($_POST['backup_name']);
                    $message = $result;
                    break;
            }
        } catch (Exception $e) {
            $error = "Erreur : " . $e->getMessage();
        }
    }
}

$customBackups = getSauvegardesCustom();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Backup Manager - Gestion</title>
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
                <li><a href="manage.php" class="active"><span class="icon">⚙️</span>Sauvegardes</a></li>
                <li><a href="logs.php"><span class="icon">📋</span>Logs</a></li>
                <li><a href="terminal.php"><span class="icon">💻</span>Terminal</a></li>
            </ul>
        </nav>
        
        <main class="main-content">
            <header class="page-header">
                <h1>Gestion des Sauvegardes</h1>
            </header>
            
            <div class="content-area">
                
                <?php if ($message): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <!-- Formulaire d'ajout -->
                <div class="card">
                    <div class="card-header">
                        <h3>Ajouter une Nouvelle Sauvegarde</h3>
                    </div>
                    <div class="card-body">
                        <form method="post">
                            <input type="hidden" name="action" value="add_backup">
                            
                            <div class="form-grid">
                                <div class="form-group">
                                    <label>Nom de la sauvegarde</label>
                                    <input type="text" name="nom" required placeholder="ex: photos_serveur2" 
                                           pattern="[a-zA-Z0-9_]{3,50}" class="form-input"
                                           title="Lettres, chiffres et _ uniquement (3-50 caractères)">
                                    <small>Lettres, chiffres et _ uniquement (3-50 caractères)</small>
                                </div>

                                <div class="form-group">
                                    <label>Type</label>
                                    <select name="type" id="backup-type" onchange="toggleFields()" class="form-input">
                                        <option value="locale">Locale</option>
                                        <option value="distante">Distante (SSH/SSHFS)</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Chemin source</label>
                                    <input type="text" name="source" required placeholder="/chemin/vers/source" class="form-input">
                                </div>

                                <div class="form-group">
                                    <label>Nom dossier destination</label>
                                    <input type="text" name="dest_name" required placeholder="MonDossier" class="form-input">
                                </div>
                            </div>

                            <div id="ssh-fields" style="display: none;">
                                <h4>Paramètres SSH</h4>
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label>Utilisateur SSH</label>
                                        <input type="text" name="ssh_user" placeholder="utilisateur" class="form-input">
                                    </div>
                                    <div class="form-group">
                                        <label>IP/Hostname</label>
                                        <input type="text" name="ssh_ip" placeholder="192.168.1.100" class="form-input">
                                    </div>
                                    <div class="form-group">
                                        <label>Port SSH</label>
                                        <input type="number" name="ssh_port" value="22" class="form-input">
                                    </div>
                                </div>
                            </div>

                            <h4>Politiques de Rétention</h4>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label>Quotidien (jours)</label>
                                    <input type="number" name="retention_q" value="7" class="form-input">
                                </div>
                                <div class="form-group">
                                    <label>Hebdomadaire</label>
                                    <input type="number" name="retention_h" value="4" class="form-input">
                                </div>
                                <div class="form-group">
                                    <label>Mensuel</label>
                                    <input type="number" name="retention_m" value="12" class="form-input">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">Ajouter la Sauvegarde</button>
                        </form>
                    </div>
                </div>

                <!-- Liste des sauvegardes personnalisées -->
                <div class="card">
                    <div class="card-header">
                        <h3>Sauvegardes Personnalisées</h3>
                    </div>
                    <div class="card-body">
                        <?php if (empty($customBackups)): ?>
                            <p>Aucune sauvegarde personnalisée configurée.</p>
                        <?php else: ?>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Nom</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($customBackups as $name => $info): ?>
                                        <tr>
                                            <td><strong><?= htmlspecialchars($name) ?></strong></td>
                                            <td>
                                                <?php if ($info['status'] === 'disabled'): ?>
                                                    <span class="backup-status-disabled">🔴 Désactivée</span>
                                                <?php else: ?>
                                                    <span class="backup-status-active">🟢 Active</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <form method="post" style="display: inline;">
                                                    <input type="hidden" name="action" value="toggle_backup">
                                                    <input type="hidden" name="backup_name" value="<?= htmlspecialchars($name) ?>">
                                                    <button type="submit" class="btn btn-secondary">
                                                        <?= $info['status'] === 'disabled' ? 'Activer' : 'Désactiver' ?>
                                                    </button>
                                                </form>
                                                <form method="post" style="display: inline;" onsubmit="return confirm('Supprimer définitivement cette sauvegarde ?')">
                                                    <input type="hidden" name="action" value="delete_backup">
                                                    <input type="hidden" name="backup_name" value="<?= htmlspecialchars($name) ?>">
                                                    <button type="submit" class="btn btn-danger">Supprimer</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        function toggleFields() {
            const type = document.getElementById('backup-type').value;
            const sshFields = document.getElementById('ssh-fields');
            sshFields.style.display = type === 'distante' ? 'block' : 'none';
        }
    </script>
    
    <style>
        .backup-actions {
            display: flex;
            gap: 5px;
            margin-top: 5px;
        }
        .btn-sm {
            padding: 5px 10px;
            font-size: 12px;
        }
        .backup-status.disabled {
            color: #dc3545;
        }
        .backup-status.active {
            color: #28a745;
        }
        .backup-item {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }
    </style>
</body>
</html>