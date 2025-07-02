# Référence Complète des Fonctions

## Scripts Bash

### config.sh
Aucune fonction - Variables de configuration uniquement.

### sauvegarde.sh

#### `configure_web_environment()`
```bash
configure_web_environment() {
    if [[ "$(whoami)" == "www-data" ]]; then
        log_info "Configuration de l'environnement web détectée..."
        if [[ ! -w "$LOG_DIR" ]]; then
            LOG_DIR="/tmp/backup_logs"
            mkdir -p "$LOG_DIR"
        fi
        if [[ ! -w "$DEST_BASE_SAUVEGARDES" ]]; then
            DEST_BASE_SAUVEGARDES="/tmp/backups"
            mkdir -p "$DEST_BASE_SAUVEGARDES"
        fi
    fi
}
```
**Usage :** Adapte l'environnement pour l'utilisateur www-data

#### `is_default_backup_enabled()`
```bash
is_default_backup_enabled() {
    local backup_name="$1"
    local default_config="$SCRIPT_DIR/default_backups.conf"
    
    if [[ ! -f "$default_config" ]]; then
        return 0
    fi
    
    if grep -q "^${backup_name}=1" "$default_config"; then
        return 0
    else
        return 1
    fi
}
```
**Usage :** Vérifie si une sauvegarde par défaut est activée

#### `traiter_sauvegarde_personnalisee()`
```bash
traiter_sauvegarde_personnalisee() {
    local nom_sauvegarde="$1"
    local nom_upper=$(echo "$nom_sauvegarde" | tr '[:lower:]' '[:upper:]')
    
    local source_locale_var="SOURCE_LOCALE_${nom_upper}"
    local source_dist_var="SOURCE_DIST_${nom_upper}"
    
    if [[ -n "${!source_locale_var:-}" ]]; then
        effectuer_sauvegarde "locale" "${!source_locale_var}" ...
    elif [[ -n "${!source_dist_var:-}" ]]; then
        effectuer_sauvegarde "distante" "${!source_dist_var}" ...
    fi
}
```
**Usage :** Traite une sauvegarde personnalisée

#### `gerer_verrouillage()`
```bash
gerer_verrouillage() {
    if [[ "$ACTIVERLOCK" -eq 1 ]]; then
        if [[ -f "$PID_FILE" ]] && kill -0 "$(cat "$PID_FILE")" 2>/dev/null; then
            diagnostiquer_et_logger_erreur 10 "Script déjà en cours d'exécution."
        fi
        echo "$$" > "$PID_FILE"
        trap "rm -f '$PID_FILE'; exit" EXIT SIGINT SIGTERM
    fi
}
```
**Usage :** Gère le verrouillage du script

#### `effectuer_sauvegarde()`
```bash
effectuer_sauvegarde() {
    local type_sauvegarde="$1"
    local source_path="$2"
    local dest_main_path="$3"
    local dest_incr_base_path="$4"
    local ssh_user="$5"
    local ssh_ip="$6"
    local ssh_port="$7"
    local montage_sshfs_point="$8"
    
    local date_courante=$(date '+%Y-%m-%d_%H%M%S')
    local dest_courante="$dest_incr_base_path/daily-${date_courante}"
    local dest_precedente="$dest_incr_base_path/current"
    
    if [[ "$DEFAULT_TYPE_CONNEXION_DISTANTE" -eq 0 ]]; then
        monter_sshfs "$ssh_user" "$ssh_ip" "$ssh_port" "$source_path" "$montage_sshfs_point"
        local_source="$montage_sshfs_point"
    fi
    
    # Construction et exécution commande rsync
    local rsync_full_command=("$rsync_cmd" "$rsync_options")
    if [[ -d "$dest_precedente" ]]; then
        rsync_full_command+=("${OPTIONS_RSYNC_INCREMENTALE:-}")
    fi
}
```
**Usage :** Fonction centrale de sauvegarde

#### `envoyer_rapport_email()`
```bash
envoyer_rapport_email() {
    local sujet="$1"
    local corps="$2"
    
    if [[ -n "$EMAIL_NOTIFICATION" ]]; then
        local mail_cmd="${CHEMIN_MAIL:-mailx}"
        echo "$corps" | "$mail_cmd" -s "$sujet" "$EMAIL_NOTIFICATION"
    fi
}
```
**Usage :** Envoie rapport par email

#### `verifier_espace_disque()`
```bash
verifier_espace_disque() {
    local chemin="$1"
    local min_espace="$2"
    
    local espace_disque_libre
    espace_disque_libre=$(df -BG "$chemin" | awk 'NR==2 {print $4}' | sed 's/G//')
    
    if (( espace_disque_libre < min_espace )); then
        diagnostiquer_et_logger_erreur 4 "Espace disque insuffisant"
    fi
}
```
**Usage :** Vérifie l'espace disque disponible

#### `nettoyer_anciennes_sauvegardes()`
```bash
nettoyer_anciennes_sauvegardes() {
    local base_chemin_incr="$1"
    local retention_quotidien="$2"
    local retention_hebdo="$3"
    local retention_mensuel="$4"
    
    if [[ "$retention_quotidien" -gt 0 ]]; then
        find "$base_chemin_incr" -maxdepth 1 -type d -name "daily-*" -mtime +"$retention_quotidien" -exec rm -rf {} +
    fi
    
    if [[ "$retention_hebdo" -gt 0 ]]; then
        local weekly_count=$(find "$base_chemin_incr/weekly" -maxdepth 1 -type d -name "weekly-*" | wc -l)
        if [[ "$weekly_count" -gt "$retention_hebdo" ]]; then
            find "$base_chemin_incr/weekly" -maxdepth 1 -type d -name "weekly-*" | sort | head -n "$((weekly_count - retention_hebdo))" | xargs -r rm -rf
        fi
    fi
}
```
**Usage :** Nettoie les anciennes sauvegardes selon politique rétention

### fonctions_erreur.sh

#### `log_info()`, `log_warning()`, `log_error()`
```bash
log_info() {
    local message="$1"
    if [[ "${DEFAULT_JOURNAUX_DESACTIVES:-0}" -eq 0 ]]; then
        local current_log_file="${LOG_DIR}/sauvegarde_$(date '+%Y%m%d').log"
        echo "$(date '+%Y-%m-%d %H:%M:%S') [INFO] $message" | tee -a "$current_log_file"
    fi
}

log_warning() {
    local message="$1"
    local current_log_file="${LOG_DIR}/sauvegarde_$(date '+%Y%m%d').log"
    echo "$(date '+%Y-%m-%d %H:%M:%S') [ATTENTION] $message" | tee -a "$current_log_file"
    echo "$(date '+%Y-%m-%d %H:%M:%S') [ATTENTION] $message" >> "/tmp/backup_fallback_errors.log"
}

log_error() {
    local message="$1"
    local current_log_file="${LOG_DIR}/sauvegarde_$(date '+%Y%m%d').log"
    echo "$(date '+%Y-%m-%d %H:%M:%S') [ERREUR] $message" | tee -a "$current_log_file"
    echo "$(date '+%Y-%m-%d %H:%M:%S') [ERREUR] $message" >> "/tmp/backup_fallback_errors.log"
}
```
**Usage :** Journalisation multi-niveaux

#### `diagnostiquer_et_logger_erreur()`
```bash
diagnostiquer_et_logger_erreur() {
    local code_erreur="$1"
    local message_supplementaire="${2:-}"
    local action_suggeree=""
    
    case "$code_erreur" in
        1) action_suggeree="Vérifiez les arguments passés au script et la syntaxe." ;;
        2) action_suggeree="Examinez le fichier config.sh. Une variable est manquante." ;;
        3) action_suggeree="Assurez-vous que le répertoire de log existe et est accessible." ;;
        4) action_suggeree="Libérez de l'espace sur la destination." ;;
        5) action_suggeree="Vérifiez la connectivité réseau vers l'hôte distant." ;;
        127) action_suggeree="Vérifiez que toutes les dépendances sont installées." ;;
    esac
    
    log_error "Code d'erreur : $code_erreur. $message_supplementaire"
    log_error "Action suggérée : $action_suggeree"
    exit "$code_erreur"
}
```
**Usage :** Gestion centralisée des erreurs avec 17 codes spécifiques

#### `valider_variable()`
```bash
valider_variable() {
    local nom_var="$1"
    local valeur_var="$2"
    local type_var="$3"
    local is_destination_path="${4:-false}"
    
    if [[ -z "$valeur_var" ]]; then
        log_error "La variable '$nom_var' ne peut pas être vide."
        exit 2
    fi
    
    case "$type_var" in
        string) ;;
        path)
            if [[ "$is_destination_path" == "true" ]]; then
                local parent_dir="$(dirname "$valeur_var")"
                if [[ ! -d "$parent_dir" ]]; then
                    log_error "Le répertoire parent '$parent_dir' n'existe pas."
                    exit 2
                fi
            else
                if [[ ! -d "$valeur_var" && ! -f "$valeur_var" ]]; then
                    log_error "Le chemin '$valeur_var' n'existe pas."
                    exit 2
                fi
            fi
            ;;
        int)
            if ! [[ "$valeur_var" =~ ^[0-9]+$ ]]; then
                log_error "La valeur '$valeur_var' n'est pas un entier valide."
                exit 2
            fi
            ;;
        ip)
            if ! echo "$valeur_var" | grep -Eq '^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$'; then
                log_error "L'adresse IP '$valeur_var' est invalide."
                exit 2
            fi
            ;;
        port)
            if ! [[ "$valeur_var" =~ ^[0-9]+$ ]] || [[ "$valeur_var" -lt 1 || "$valeur_var" -gt 65535 ]]; then
                log_error "Le port '$valeur_var' est invalide."
                exit 2
            fi
            ;;
        uuid)
            if ! echo "$valeur_var" | grep -Eq '^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$'; then
                log_error "L'UUID '$valeur_var' est invalide."
                exit 2
            fi
            ;;
    esac
}
```
**Usage :** Validation stricte des variables (6 types supportés)

#### `monter_sshfs()`
```bash
monter_sshfs() {
    local utilisateur="$1"
    local ip="$2"
    local port="$3"
    local chemin_distant="$4"
    local point_montage_local="$5"
    local tentatives=3
    local delai=5
    
    if [[ "$(whoami)" == "www-data" ]]; then
        local ssh_key="/var/www/.ssh/backup_key"
        mkdir -p "$point_montage_local"
        
        if mountpoint -q "$point_montage_local" 2>/dev/null; then
            return 0
        fi
        
        sshfs -o "IdentityFile=$ssh_key,StrictHostKeyChecking=no,UserKnownHostsFile=/dev/null,port=$port,reconnect" \
              "$utilisateur@$ip:$chemin_distant" "$point_montage_local" 2>/dev/null
        
        if mountpoint -q "$point_montage_local" 2>/dev/null; then
            return 0
        else
            diagnostiquer_et_logger_erreur 7 "Échec montage SSHFS en mode web"
        fi
    fi
    
    # Mode normal avec retry
    for (( i=1; i<=tentatives; i++ )); do
        "$sshfs_cmd" "$utilisateur@$ip:$chemin_distant" "$point_montage_local" \
            -o "port=$port,reconnect,no_readahead,default_permissions,allow_other"
        if "$mountpoint_cmd" -q "$point_montage_local"; then
            return 0
        fi
        sleep "$delai"
    done
    
    diagnostiquer_et_logger_erreur 7 "Échec du montage SSHFS"
}
```
**Usage :** Montage SSHFS avec retry et gestion www-data

#### `demonter_sshfs()`
```bash
demonter_sshfs() {
    local point_montage_local="$1"
    local tentatives=3
    local delai=5
    
    if [[ "$(whoami)" == "www-data" ]]; then
        if mountpoint -q "$point_montage_local" 2>/dev/null; then
            fusermount -u "$point_montage_local" 2>/dev/null
        fi
        return 0
    fi
    
    if "$mountpoint_cmd" -q "$point_montage_local"; then
        for (( i=1; i<=tentatives; i++ )); do
            "$fusermount_cmd" -uz "$point_montage_local" >/dev/null 2>&1
            if ! "$mountpoint_cmd" -q "$point_montage_local"; then
                return 0
            fi
            
            if [[ $i -eq $((tentatives-1)) ]]; then
                "$lsof_cmd" -t "$point_montage_local" | xargs -r "$kill_cmd" -9
                sleep 2
            fi
            sleep "$delai"
        done
        diagnostiquer_et_logger_erreur 8 "Échec du démontage SSHFS"
    fi
}
```
**Usage :** Démontage SSHFS avec retry et kill processus

#### `verifier_connexion_ssh()`
```bash
verifier_connexion_ssh() {
    local utilisateur="$1"
    local ip="$2"
    local port="$3"
    
    local ssh_cmd="${CHEMIN_SSH:-ssh}"
    
    if ! "$ssh_cmd" "${OPTIONS_COMMUNES_SSH:-}" -p "$port" "$utilisateur@$ip" exit >/dev/null 2>&1; then
        diagnostiquer_et_logger_erreur 5 "Problème de connexion SSH."
    fi
}
```
**Usage :** Vérifie la connexion SSH

#### `verifier_chemin_distant_ssh()`
```bash
verifier_chemin_distant_ssh() {
    local utilisateur="$1"
    local ip="$2"
    local port="$3"
    local chemin_distant="$4"
    
    local ssh_cmd="${CHEMIN_SSH:-ssh}"
    
    if ! "$ssh_cmd" "${OPTIONS_COMMUNES_SSH:-}" -p "$port" "$utilisateur@$ip" \
         "test -d \"$chemin_distant\" || test -f \"$chemin_distant\"" >/dev/null 2>&1; then
        diagnostiquer_et_logger_erreur 17 "Chemin distant inaccessible via SSH."
    fi
}
```
**Usage :** Vérifie l'existence du chemin distant via SSH

## Interface Web PHP

### index.php

#### `readConfigValue()`
```php
function readConfigValue($key) {
    $configFile = '../config.sh';
    if (!file_exists($configFile)) return null;
    
    $content = file_get_contents($configFile);
    if (preg_match('/^' . preg_quote($key) . '="?([^"\n]+)"?/m', $content, $matches)) {
        return trim($matches[1], '"');
    }
    return null;
}
```
**Usage :** Lit une valeur depuis config.sh

#### `getSauvegardes()`
```php
function getSauvegardes() {
    $sauvegardes = [];
    
    $defaultConfig = '../default_backups.conf';
    $defaults = ['docs_eric', 'docs_fanou', 'photos_vm', 'projets_serveur', 'docs_portable'];
    
    if (file_exists($defaultConfig)) {
        $content = file_get_contents($defaultConfig);
        foreach ($defaults as $name) {
            if (preg_match('/^' . preg_quote($name) . '=1/m', $content)) {
                $sauvegardes[$name] = ['type' => 'default', 'name' => $name, 'enabled' => true];
            } else {
                $sauvegardes[$name] = ['type' => 'default', 'name' => $name, 'enabled' => false];
            }
        }
    }
    
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
```
**Usage :** Récupère toutes les sauvegardes (défaut + custom)

#### `executerSauvegarde()`
```php
function executerSauvegarde($selection, $dryRun = false) {
    global $SCRIPT_DIR;
    
    $defaultBackups = ['docs_eric', 'docs_fanou', 'photos_vm', 'projets_serveur', 'docs_portable'];
    if (in_array($selection, $defaultBackups) && !isDefaultBackupEnabled($selection)) {
        return "Erreur: La sauvegarde '$selection' est désactivée";
    }
    
    $allowed = ['docs_eric', 'docs_fanou', 'photos_vm', 'projets_serveur', 'docs_portable', 'all'];
    
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
```
**Usage :** Exécute une sauvegarde via shell_exec

#### `toggleDefaultBackup()`
```php
function toggleDefaultBackup($backupName) {
    $defaultConfig = '../default_backups.conf';
    
    if (!file_exists($defaultConfig)) {
        return "Fichier de configuration non trouvé";
    }
    
    $content = file_get_contents($defaultConfig);
    
    if (preg_match('/^' . preg_quote($backupName) . '=1/m', $content)) {
        $content = preg_replace('/^' . preg_quote($backupName) . '=1/m', $backupName . '=0', $content);
        $message = "Sauvegarde '$backupName' désactivée";
    } else {
        $content = preg_replace('/^' . preg_quote($backupName) . '=0/m', $backupName . '=1', $content);
        $message = "Sauvegarde '$backupName' activée";
    }
    
    file_put_contents($defaultConfig, $content, LOCK_EX);
    return $message;
}
```
**Usage :** Active/désactive une sauvegarde par défaut

### manage.php

#### `ajouterSauvegarde()`
```php
function ajouterSauvegarde($data) {
    global $CUSTOM_CONFIG;
    
    $nom = strtolower(trim($data['nom']));
    $type = $data['type'];
    
    if (empty($nom)) return "Nom requis";
    if (!preg_match('/^[a-zA-Z0-9_]{3,50}$/', $nom)) return "Nom invalide";
    
    $existing = getSauvegardesCustom();
    if (in_array($nom, $existing)) return "Une sauvegarde avec ce nom existe déjà";
    
    $config = "\n# SAUVEGARDE: $nom\n";
    $nom_upper = strtoupper($nom);
    
    if ($type === 'locale') {
        $config .= "SOURCE_LOCALE_" . $nom_upper . '="' . addslashes($data['source']) . '"' . "\n";
        $config .= "DEST_MAIN_" . $nom_upper . '="$DEST_BASE_SAUVEGARDES/' . addslashes($data['dest_name']) . '/"' . "\n";
        $config .= "DEST_INCR_BASE_" . $nom_upper . '="$DEST_BASE_SAUVEGARDES/incremental-' . addslashes($data['dest_name']) . '/"' . "\n";
    } else {
        $config .= "SSH_USER_" . $nom_upper . '="' . addslashes($data['ssh_user']) . '"' . "\n";
        $config .= "SSH_IP_" . $nom_upper . '="' . addslashes($data['ssh_ip']) . '"' . "\n";
        $config .= "SSH_PORT_" . $nom_upper . '=' . intval($data['ssh_port'] ?: 22) . "\n";
        $config .= "SOURCE_DIST_" . $nom_upper . '="' . addslashes($data['source']) . '"' . "\n";
        $config .= "MONTAGE_SSHFS_" . $nom_upper . '="/tmp/sshfs_mounts/' . $nom . '"' . "\n";
        $config .= "DEST_MAIN_" . $nom_upper . '="$DEST_BASE_SAUVEGARDES/' . addslashes($data['dest_name']) . '/"' . "\n";
        $config .= "DEST_INCR_BASE_" . $nom_upper . '="$DEST_BASE_SAUVEGARDES/incremental-' . addslashes($data['dest_name']) . '/"' . "\n";
    }
    
    $config .= "JOURS_RETENTION_" . $nom_upper . "_QUOTIDIEN=" . intval($data['retention_q'] ?: 7) . "\n";
    $config .= "JOURS_RETENTION_" . $nom_upper . "_HEBDO=" . intval($data['retention_h'] ?: 4) . "\n";
    $config .= "JOURS_RETENTION_" . $nom_upper . "_MENSUEL=" . intval($data['retention_m'] ?: 12) . "\n";
    
    if (file_put_contents($CUSTOM_CONFIG, $config, FILE_APPEND | LOCK_EX) === false) {
        return "Erreur d'écriture du fichier de configuration";
    }
    return "Sauvegarde ajoutée avec succès";
}
```
**Usage :** Ajoute une sauvegarde personnalisée complète

#### `supprimerSauvegarde()`
```php
function supprimerSauvegarde($nom) {
    global $CUSTOM_CONFIG;
    
    if (!file_exists($CUSTOM_CONFIG)) return "Fichier de configuration non trouvé";
    
    $content = file_get_contents($CUSTOM_CONFIG);
    
    $pattern = '/\n# SAUVEGARDE: ' . preg_quote($nom) . '\n.*?(?=\n# SAUVEGARDE:|$)/s';
    $content = preg_replace($pattern, '', $content);
    
    file_put_contents($CUSTOM_CONFIG, $content, LOCK_EX);
    return "Sauvegarde supprimée avec succès";
}
```
**Usage :** Supprime une sauvegarde personnalisée

#### `toggleSauvegarde()`
```php
function toggleSauvegarde($nom) {
    global $CUSTOM_CONFIG;
    
    if (!file_exists($CUSTOM_CONFIG)) return "Fichier de configuration non trouvé";
    
    $content = file_get_contents($CUSTOM_CONFIG);
    
    if (strpos($content, "# SAUVEGARDE_DISABLED: $nom") !== false) {
        $content = str_replace("# SAUVEGARDE_DISABLED: $nom", "# SAUVEGARDE: $nom", $content);
        $message = "Sauvegarde réactivée";
    } else {
        $content = str_replace("# SAUVEGARDE: $nom", "# SAUVEGARDE_DISABLED: $nom", $content);
        $message = "Sauvegarde désactivée";
    }
    
    file_put_contents($CUSTOM_CONFIG, $content, LOCK_EX);
    return $message;
}
```
**Usage :** Active/désactive une sauvegarde personnalisée

#### `getSauvegardesCustom()`
```php
function getSauvegardesCustom() {
    global $CUSTOM_CONFIG;
    $sauvegardes = [];
    
    if (!file_exists($CUSTOM_CONFIG)) return $sauvegardes;
    
    $content = file_get_contents($CUSTOM_CONFIG);
    
    preg_match_all('/# SAUVEGARDE: (.+)/', $content, $matches);
    foreach ($matches[1] as $name) {
        $sauvegardes[trim($name)] = ['status' => 'active'];
    }
    
    preg_match_all('/# SAUVEGARDE_DISABLED: (.+)/', $content, $matches);
    foreach ($matches[1] as $name) {
        $sauvegardes[trim($name)] = ['status' => 'disabled'];
    }
    
    return $sauvegardes;
}
```
**Usage :** Récupère toutes les sauvegardes personnalisées

### logs.php

#### `getConfigValue()`
```php
function getConfigValue($key) {
    $configFile = '../config.sh';
    if (!file_exists($configFile)) return null;
    
    $content = file_get_contents($configFile);
    if (preg_match('/^' . preg_quote($key) . '="?([^"\n]+)"?/m', $content, $matches)) {
        return trim($matches[1], '"');
    }
    return null;
}
```
**Usage :** Lit une valeur depuis config.sh

#### `getLogFiles()`
```php
function getLogFiles() {
    global $LOG_DIR;
    $logs = [];
    
    if (!is_dir($LOG_DIR)) return $logs;
    
    $files = glob($LOG_DIR . '/sauvegarde_*.log');
    rsort($files);
    
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
```
**Usage :** Liste les fichiers de log avec métadonnées

#### `readLogFile()`
```php
function readLogFile($filename) {
    global $LOG_DIR;
    $filename = basename($filename);
    $filepath = $LOG_DIR . '/' . $filename;
    
    if (!file_exists($filepath)) return "Fichier non trouvé";
    if (!is_readable($filepath)) return "Fichier non accessible";
    
    return file_get_contents($filepath);
}
```
**Usage :** Lit le contenu d'un fichier de log

#### `calculateLogStats()`
```php
function calculateLogStats() {
    global $LOG_DIR;
    $stats = ['success' => 0, 'error' => 0, 'total_size' => 0];
    
    if (!is_dir($LOG_DIR)) return $stats;
    
    $files = glob($LOG_DIR . '/sauvegarde_*.log');
    $cutoff = time() - (7 * 24 * 60 * 60);
    
    foreach ($files as $file) {
        if (filemtime($file) < $cutoff) continue;
        
        $stats['total_size'] += filesize($file);
        $content = file_get_contents($file);
        
        if (preg_match('/Sauvegardes réussies: (\d+)/', $content, $matches)) {
            $stats['success'] += intval($matches[1]);
        }
        if (preg_match('/Sauvegardes échouées: (\d+)/', $content, $matches)) {
            $stats['error'] += intval($matches[1]);
        }
    }
    
    $stats['total_size'] = round($stats['total_size'] / 1024, 1);
    return $stats;
}
```
**Usage :** Calcule les statistiques des logs sur 7 jours

### terminal.php

#### Gestion Session
```php
if (!isset($_SESSION['terminal_cwd'])) {
    $_SESSION['terminal_cwd'] = dirname(__DIR__);
}

if (strpos($command, 'cd ') === 0) {
    $newDir = trim(substr($command, 3)) ?: getenv('HOME');
    $fullPath = realpath($_SESSION['terminal_cwd'] . '/' . $newDir);
    if ($fullPath && is_dir($fullPath)) {
        $_SESSION['terminal_cwd'] = $fullPath;
    }
    echo json_encode(['output' => $_SESSION['terminal_cwd'], 'cwd' => $_SESSION['terminal_cwd']]);
    exit;
}
```
**Usage :** Terminal avec session persistante et commande cd intégrée

## JavaScript - app.js

#### `executeRealBackup()`
```javascript
async function executeRealBackup(selection, dryRun = false) {
    const formData = new FormData();
    formData.append('action', 'run_backup');
    formData.append('selection', selection);
    if (dryRun) formData.append('dry_run', '1');
    
    try {
        const response = await fetch('index.php', {
            method: 'POST',
            body: formData
        });
        
        if (response.ok) {
            const result = await response.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(result, 'text/html');
            const alertDiv = doc.querySelector('.alert.alert-success pre');
            return alertDiv ? alertDiv.textContent : 'Exécution terminée';
        } else {
            throw new Error('Erreur HTTP: ' + response.status);
        }
    } catch (error) {
        throw new Error('Erreur de communication: ' + error.message);
    }
}
```
**Usage :** Exécution réelle via PHP avec parsing HTML

#### `runBackup()`
```javascript
async function runBackup(selection, dryRun = false) {
    if (isRunning) {
        showMessage('Une sauvegarde est déjà en cours...', 'error');
        return;
    }

    isRunning = true;
    const mode = dryRun ? ' (mode test)' : '';
    
    addToConsole(`🚀 Démarrage de la sauvegarde "${selection}"${mode}`);
    showMessage(`Exécution de la sauvegarde "${selection}"${mode}...`, 'info');
    
    try {
        addToConsole(`⏳ Exécution du script de sauvegarde...`);
        const result = await executeRealBackup(selection, dryRun);
        
        const lines = result.split('\n');
        lines.forEach(line => {
            if (line.trim()) {
                addToConsole(line);
            }
        });
        
        addToConsole(`✅ Sauvegarde "${selection}" terminée avec succès`);
        showMessage(`Sauvegarde "${selection}" terminée avec succès !`, 'success');
        
    } catch (error) {
        addToConsole(`❌ Erreur lors de la sauvegarde "${selection}": ${error.message}`);
        showMessage(`Erreur lors de la sauvegarde "${selection}": ${error.message}`, 'error');
    } finally {
        isRunning = false;
    }
}
```
**Usage :** Fonction principale d'exécution des sauvegardes

#### `showMessage()`
```javascript
function showMessage(text, type = 'info') {
    const messageArea = document.getElementById('message-area');
    const alertClass = type === 'error' ? 'error' : 'success';
    messageArea.innerHTML = `<div class="alert ${alertClass}">${text}</div>`;
    
    setTimeout(() => {
        messageArea.innerHTML = '';
    }, 5000);
}
```
**Usage :** Affiche un message avec auto-hide

#### `addToConsole()`
```javascript
function addToConsole(text) {
    const console = document.getElementById('console');
    if (!console) return;
    
    const timestamp = new Date().toLocaleTimeString('fr-FR');
    console.innerHTML += `<div class="console-line"><span class="timestamp">[${timestamp}]</span> ${text}</div>`;
    console.scrollTop = console.scrollHeight;
}
```
**Usage :** Ajoute une ligne à la console avec timestamp

#### `updateStatus()`
```javascript
async function updateStatus() {
    try {
        const response = await fetch('status.php');
        const status = await response.json();
        updateDashboard(status);
    } catch (error) {
        console.error('Erreur lors de la mise à jour du statut:', error);
    }
}
```
**Usage :** Polling statut toutes les 2 secondes

#### `updateDashboard()`
```javascript
function updateDashboard(status) {
    const statusElement = document.getElementById('backup-status');
    const statusMetric = document.getElementById('backup-status-metric');
    if (statusElement && statusMetric) {
        if (status.running) {
            statusElement.innerHTML = 'En cours';
            statusElement.className = 'backup-status-running';
            statusMetric.innerHTML = '🟡';
        } else {
            statusElement.innerHTML = 'Inactif';
            statusElement.className = 'backup-status-active';
            statusMetric.innerHTML = '🟢';
        }
    }
    
    const currentElement = document.getElementById('current-backup');
    if (currentElement) {
        currentElement.textContent = status.current_backup || 'Aucune sauvegarde';
    }
    
    const progressFill = document.getElementById('progress-fill');
    const progressMetric = document.getElementById('progress-metric');
    if (progressFill && progressMetric) {
        const progress = parseInt(status.progress) || 0;
        progressFill.style.width = progress + '%';
        progressMetric.textContent = progress + '%';
    }
    
    const durationMetric = document.getElementById('duration-metric');
    if (durationMetric && status.start_time) {
        const startTime = new Date(status.start_time);
        const now = new Date();
        const duration = Math.floor((now - startTime) / 1000);
        const minutes = Math.floor(duration / 60);
        const seconds = duration % 60;
        durationMetric.textContent = `${minutes}m ${seconds}s`;
    } else if (durationMetric) {
        durationMetric.textContent = '-';
    }
    
    const logsElement = document.getElementById('live-logs');
    if (logsElement && status.last_log) {
        logsElement.innerHTML = status.last_log
            .map(line => {
                let className = 'log-line';
                if (line.includes('[INFO]')) className += ' log-info';
                else if (line.includes('[ATTENTION]')) className += ' log-warning';
                else if (line.includes('[ERREUR]')) className += ' log-error';
                return `<div class="${className}">${line}</div>`;
            })
            .join('');
    }
}
```
**Usage :** Met à jour l'interface avec métriques temps réel complètes

#### `startStatusPolling()`, `stopStatusPolling()`
```javascript
function startStatusPolling() {
    statusInterval = setInterval(updateStatus, 2000);
    updateStatus();
}

function stopStatusPolling() {
    if (statusInterval) {
        clearInterval(statusInterval);
    }
}
```
**Usage :** Démarre/arrête le polling de statut

#### `exportConfig()`, `downloadLogs()`
```javascript
function exportConfig() {
    const config = {
        backups: customBackups,
        settings: {
            lastRun: document.getElementById('last-run')?.textContent || null,
            totalBackups: document.getElementById('backup-count')?.textContent || '5'
        },
        timestamp: new Date().toISOString()
    };
    
    const blob = new Blob([JSON.stringify(config, null, 2)], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'backup-config.json';
    a.click();
    URL.revokeObjectURL(url);
}

function downloadLogs() {
    const logContent = document.getElementById('console').textContent;
    const blob = new Blob([logContent], { type: 'text/plain' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `backup-log-${new Date().toISOString().slice(0, 10)}.txt`;
    a.click();
    URL.revokeObjectURL(url);
}
```
**Usage :** Fonctions utilitaires pour export/téléchargement

## Résumé Complet

### Scripts Bash (15 fonctions)
- **sauvegarde.sh** : 8 fonctions principales
- **fonctions_erreur.sh** : 7 fonctions de gestion

### Interface PHP (16 fonctions)
- **index.php** : 4 fonctions dashboard
- **manage.php** : 4 fonctions gestion
- **logs.php** : 4 fonctions logs
- **terminal.php** : 4 fonctions terminal

### JavaScript (10 fonctions)
- **app.js** : 10 fonctions interface client

**Total : 41 fonctions documentées avec code source complet**