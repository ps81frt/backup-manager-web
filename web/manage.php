<?php
session_start();

$CONFIG_FILE = '../config.sh';
$CUSTOM_CONFIG = '../sauvegardes_custom.conf';

// Fonction pour ajouter une sauvegarde personnalisée
function ajouterSauvegarde($data) {
    global $CUSTOM_CONFIG;
    
    $nom = strtolower(trim($data['nom']));
    $type = $data['type'];
    
    if (empty($nom)) return "Nom requis";
    
    $config = "\n# SAUVEGARDE: $nom\n";
    
    if ($type === 'locale') {
        $config .= "SOURCE_LOCALE_" . strtoupper($nom) . '="' . $data['source'] . '"' . "\n";
        $config .= "DEST_MAIN_" . strtoupper($nom) . '="$DEST_BASE_SAUVEGARDES/' . $data['dest_name'] . '/"' . "\n";
        $config .= "DEST_INCR_BASE_" . strtoupper($nom) . '="$DEST_BASE_SAUVEGARDES/incremental-' . $data['dest_name'] . '/"' . "\n";
    } else {
        $config .= "SSH_USER_" . strtoupper($nom) . '="' . $data['ssh_user'] . '"' . "\n";
        $config .= "SSH_IP_" . strtoupper($nom) . '="' . $data['ssh_ip'] . '"' . "\n";
        $config .= "SSH_PORT_" . strtoupper($nom) . '=' . ($data['ssh_port'] ?: 22) . "\n";
        $config .= "SOURCE_DIST_" . strtoupper($nom) . '="' . $data['source'] . '"' . "\n";
        $config .= "MONTAGE_SSHFS_" . strtoupper($nom) . '="/tmp/sshfs_mounts/' . $nom . '"' . "\n";
        $config .= "DEST_MAIN_" . strtoupper($nom) . '="$DEST_BASE_SAUVEGARDES/' . $data['dest_name'] . '/"' . "\n";
        $config .= "DEST_INCR_BASE_" . strtoupper($nom) . '="$DEST_BASE_SAUVEGARDES/incremental-' . $data['dest_name'] . '/"' . "\n";
    }
    
    $config .= "JOURS_RETENTION_" . strtoupper($nom) . "_QUOTIDIEN=" . ($data['retention_q'] ?: 7) . "\n";
    $config .= "JOURS_RETENTION_" . strtoupper($nom) . "_HEBDO=" . ($data['retention_h'] ?: 4) . "\n";
    $config .= "JOURS_RETENTION_" . strtoupper($nom) . "_MENSUEL=" . ($data['retention_m'] ?: 12) . "\n";
    
    file_put_contents($CUSTOM_CONFIG, $config, FILE_APPEND | LOCK_EX);
    return "Sauvegarde ajoutée avec succès";
}

// Fonction pour lister les sauvegardes personnalisées
function getSauvegardesCustom() {
    global $CUSTOM_CONFIG;
    $sauvegardes = [];
    
    if (!file_exists($CUSTOM_CONFIG)) return $sauvegardes;
    
    $content = file_get_contents($CUSTOM_CONFIG);
    preg_match_all('/# SAUVEGARDE: (.+)/', $content, $matches);
    
    foreach ($matches[1] as $name) {
        $sauvegardes[] = trim($name);
    }
    
    return $sauvegardes;
}

$message = '';
$error = '';

if ($_POST) {
    if (isset($_POST['action']) && $_POST['action'] === 'add_backup') {
        try {
            $result = ajouterSauvegarde($_POST);
            $message = $result;
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
    <title>Gérer les Sauvegardes</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>⚙️ Gérer les Sauvegardes</h1>
            <nav>
                <a href="index.php">Accueil</a>
                <a href="manage.php" class="active">Gérer</a>
                <a href="logs.php">Logs</a>
            </nav>
        </header>

        <?php if ($message): ?>
            <div class="alert success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="grid">
            <div class="card">
                <h2>Ajouter une Nouvelle Sauvegarde</h2>
                <form method="post" class="form">
                    <input type="hidden" name="action" value="add_backup">
                    
                    <div class="form-group">
                        <label>Nom de la sauvegarde :</label>
                        <input type="text" name="nom" required placeholder="ex: photos_serveur2">
                    </div>

                    <div class="form-group">
                        <label>Type :</label>
                        <select name="type" id="backup-type" onchange="toggleFields()">
                            <option value="locale">Locale</option>
                            <option value="distante">Distante (SSH/SSHFS)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Chemin source :</label>
                        <input type="text" name="source" required placeholder="/chemin/vers/source">
                    </div>

                    <div class="form-group">
                        <label>Nom dossier destination :</label>
                        <input type="text" name="dest_name" required placeholder="MonDossier">
                    </div>

                    <div id="ssh-fields" style="display: none;">
                        <div class="form-group">
                            <label>Utilisateur SSH :</label>
                            <input type="text" name="ssh_user" placeholder="utilisateur">
                        </div>
                        <div class="form-group">
                            <label>IP/Hostname :</label>
                            <input type="text" name="ssh_ip" placeholder="192.168.1.100">
                        </div>
                        <div class="form-group">
                            <label>Port SSH :</label>
                            <input type="number" name="ssh_port" value="22">
                        </div>
                    </div>

                    <h3>Rétention</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Quotidien (jours) :</label>
                            <input type="number" name="retention_q" value="7">
                        </div>
                        <div class="form-group">
                            <label>Hebdomadaire :</label>
                            <input type="number" name="retention_h" value="4">
                        </div>
                        <div class="form-group">
                            <label>Mensuel :</label>
                            <input type="number" name="retention_m" value="12">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Ajouter la Sauvegarde</button>
                </form>
            </div>

            <div class="card">
                <h2>Sauvegardes Personnalisées</h2>
                <?php if (empty($customBackups)): ?>
                    <p>Aucune sauvegarde personnalisée configurée.</p>
                <?php else: ?>
                    <div class="backup-list">
                        <?php foreach ($customBackups as $backup): ?>
                            <div class="backup-item">
                                <span class="backup-name"><?= htmlspecialchars($backup) ?></span>
                                <span class="backup-type">Custom</span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        function toggleFields() {
            const type = document.getElementById('backup-type').value;
            const sshFields = document.getElementById('ssh-fields');
            sshFields.style.display = type === 'distante' ? 'block' : 'none';
        }
    </script>
</body>
</html>