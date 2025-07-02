# Description Fonctionnelle

## Architecture Générale du Système

### Vue d'Ensemble

Backup Manager Web suit une architecture modulaire hybride combinant :
- **Couche Métier** : Scripts Bash pour la logique de sauvegarde
- **Couche Présentation** : Interface web PHP/HTML/JavaScript
- **Couche Configuration** : Fichiers de configuration centralisés
- **Couche Données** : Logs et fichiers de statut

### Diagramme d'Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                    BACKUP MANAGER WEB                       │
├─────────────────────────────────────────────────────────────┤
│  Interface Web (PHP/HTML/JS)                               │
│  ├── Dashboard (index.php)                                 │
│  ├── Gestion (manage.php)                                  │
│  ├── Logs (logs.php)                                       │
│  ├── Terminal (terminal.php)                               │
│  └── API Status (status.php)                               │
├─────────────────────────────────────────────────────────────┤
│  Scripts Bash                                              │
│  ├── sauvegarde.sh (Script principal)                      │
│  ├── config.sh (Configuration)                             │
│  ├── fonctions_erreur.sh (Gestion erreurs)                 │
│  └── setup-web.sh (Installation)                           │
├─────────────────────────────────────────────────────────────┤
│  Configuration                                             │
│  ├── default_backups.conf                                  │
│  └── sauvegardes_custom.conf                               │
├─────────────────────────────────────────────────────────────┤
│  Outils Système                                            │
│  ├── rsync (Synchronisation)                               │
│  ├── ssh/sshfs (Connexions distantes)                      │
│  └── cron (Programmation)                                  │
└─────────────────────────────────────────────────────────────┘
```

## Analyse Exhaustive du Code Source

### Scripts Bash - Couche Métier

#### config.sh - Configuration Centralisée (200+ lignes)

**Variables Système Principales :**
| Variable | Type | Valeur Réelle | Portée | Usage |
|----------|------|---------------|--------|-------|
| `EMAIL_NOTIFICATION` | String | "votre_email@example.com" | Globale | Notifications automatiques |
| `DEST_BASE_SAUVEGARDES` | String | "/mnt/backup_nas" | Globale | Racine toutes sauvegardes |
| `LOG_DIR` | String | "/var/log/sauvegardes" | Globale | Répertoire logs |
| `ESPACE_DISQUE_MIN_GO` | Integer | 5 | Globale | Espace minimum requis |
| `PID_FILE` | String | "/var/run/$DEFAULT_NOM_SCRIPT.pid" | Globale | Verrouillage |

**Variables Rsync :**
| Variable | Type | Valeur Réelle | Usage |
|----------|------|---------------|-------|
| `DEFAULT_RSYNC_OPTIONS` | String | "-avh --partial --progress --info=progress2,misc0,name0" | Options par défaut |
| `RSYNC_DELETE` | Integer | 0 | Contrôle --delete (sécurisé) |
| `OPTIONS_RSYNC_INCREMENTALE` | String | "--link-dest=../current" | Sauvegardes incrémentales |
| `DELAI_OPERATION_RSYNC_SECONDES` | Integer | 0 | Timeout rsync |

**Variables Chemins Exécutables :**
| Variable | Type | Valeur Réelle | Rôle |
|----------|------|---------------|------|
| `CHEMIN_RSYNC` | String | "/usr/bin/rsync" | Chemin rsync |
| `CHEMIN_SSH` | String | "/usr/bin/ssh" | Chemin ssh |
| `CHEMIN_SSHFS` | String | "/usr/bin/sshfs" | Chemin sshfs |
| `CHEMIN_FUSEMOUNT` | String | "/usr/bin/fusermount" | Chemin fusermount |
| `CHEMIN_MOUNTPOINT` | String | "/usr/bin/mountpoint" | Chemin mountpoint |
| `CHEMIN_LSOF` | String | "/usr/bin/lsof" | Chemin lsof |
| `CHEMIN_KILL` | String | "/usr/bin/kill" | Chemin kill |
| `CHEMIN_MKDIR` | String | "/usr/bin/mkdir" | Chemin mkdir |
| `CHEMIN_MAIL` | String | "/usr/bin/mailx" | Chemin mail |

**Variables SSH Avancées :**
| Variable | Type | Valeur Réelle | Usage |
|----------|------|---------------|-------|
| `DELAI_CONNEXION_SSH_SECONDES` | Integer | 10 | Timeout connexion |
| `OPTIONS_COMMUNES_SSH` | String | "-o BatchMode=yes -o ConnectTimeout=${DELAI_CONNEXION_SSH_SECONDES}" | Options communes |
| `StrictHostKeyChecking_SSH` | String | "no" | Vérification clés |

**Variables Sauvegardes Prédéfinies :**

*docs_eric (locale) :*
- `SOURCE_LOCALE_DOCS_ERIC="/home/eric/Documents"`
- `DEST_MAIN_DOCS_ERIC="$DEST_BASE_SAUVEGARDES/DocumentsEric/"`
- `DEST_INCR_BASE_DOCS_ERIC="$DEST_BASE_SAUVEGARDES/incremental-DocumentsEric/"`
- `JOURS_RETENTION_DOCS_ERIC_QUOTIDIEN=7`
- `JOURS_RETENTION_DOCS_ERIC_HEBDO=4`
- `JOURS_RETENTION_DOCS_ERIC_MENSUEL=12`

*docs_fanou (locale) :*
- `SOURCE_LOCALE_DOCS_FANOU="/home/fanou/Documents"`
- `DEST_MAIN_DOCS_FANOU="$DEST_BASE_SAUVEGARDES/DocumentsFanou/"`
- `DEST_INCR_BASE_DOCS_FANOU="$DEST_BASE_SAUVEGARDES/incremental-DocumentsFanou/"`

*photos_vm (distante SSHFS) :*
- `SSH_USER_PHOTOS="votre_utilisateur_vm_photos"`
- `SSH_IP_PHOTOS="192.168.1.100"`
- `SSH_PORT_PHOTOS=22`
- `SOURCE_DIST_PHOTOS_VM="/chemin/sur/vm/Photos"`
- `MONTAGE_SSHFS_PHOTOS="/tmp/sshfs_mounts/photos_vm"`
- `DEST_MAIN_PHOTOS="$DEST_BASE_SAUVEGARDES/PhotosVM/"`

*projets_serveur (distante SSHFS) :*
- `SSH_USER_PROJETS="votre_utilisateur_serveur_projets"`
- `SSH_IP_PROJETS="192.168.1.101"`
- `SOURCE_DIST_PROJETS_SERVEUR="/Projets/Serveur/"`
- `MONTAGE_SSHFS_PROJETS="/tmp/sshfs_mounts/projets_serveur"`

*docs_portable (distante SSHFS) :*
- `SSH_USER_DOCS_PORTABLE="votre_utilisateur_portable"`
- `SSH_IP_DOCS_PORTABLE="192.168.1.102"`
- `SOURCE_DIST_DOCS_PORTABLE="/home/votre_utilisateur_portable/Documents/"`
- `MONTAGE_SSHFS_DOCS_PORTABLE="/tmp/sshfs_mounts/docs_portable"`

#### sauvegarde.sh - Script Principal (800+ lignes)

**Variables Globales d'État :**
| Variable | Type | Initialisation | Usage |
|----------|------|----------------|-------|
| `SCRIPT_DIR` | String | `$(dirname "$(readlink -f "$0")")` | Répertoire script |
| `LOG_FILE` | String | `${LOG_DIR}/sauvegarde_$(date '+%Y%m%d').log` | Log courant |
| `DRY_RUN` | Integer | 0 | Mode simulation |
| `LIST_MODE` | Integer | 0 | Mode liste |
| `SAUVEGARDES_A_TRAITER` | Array | () | Sauvegardes sélectionnées |
| `sauvegardes_reussies` | Integer | 0 | Compteur succès |
| `sauvegardes_echouees` | Integer | 0 | Compteur échecs |
| `nombre_sauvegardes` | Integer | 0 | Total sauvegardes |

**Fonctions Principales et Variables :**

*configure_web_environment() :*
- Détecte `$(whoami) == "www-data"`
- Adapte `LOG_DIR="/tmp/backup_logs"`
- Recalcule `DEST_BASE_SAUVEGARDES="/tmp/backups"`
- Crée points montage `/tmp/sshfs_mounts/{photos_vm,projets_serveur,docs_portable}`

*is_default_backup_enabled() :*
- Paramètre : `backup_name`
- Lit `default_backups.conf`
- Vérifie pattern `^${backup_name}=1`

*traiter_sauvegarde_personnalisee() :*
- Paramètre : `nom_sauvegarde`
- Variables : `nom_upper`, `source_locale_var`, `source_dist_var`
- Appelle `effectuer_sauvegarde()` avec paramètres dynamiques

*effectuer_sauvegarde() - Fonction Centrale :*
- 8 paramètres : `type_sauvegarde`, `source_path`, `dest_main_path`, `dest_incr_base_path`, `ssh_user`, `ssh_ip`, `ssh_port`, `montage_sshfs_point`
- Variables locales :
  - `date_courante=$(date '+%Y-%m-%d_%H%M%S')`
  - `dest_courante="$dest_incr_base_path/daily-${date_courante}"`
  - `dest_precedente="$dest_incr_base_path/current"`
  - `rsync_full_command=()` (array)
  - `rsync_exit_code=0`

*gerer_verrouillage() :*
- Vérifie `ACTIVERLOCK=1`
- Teste existence PID avec `kill -0`
- Crée trap `EXIT SIGINT SIGTERM`

#### fonctions_erreur.sh - Gestion Erreurs (400+ lignes)

**Codes d'Erreur (17 codes + 127) :**
| Code | Signification | Action Suggérée |
|------|---------------|-----------------|
| 1 | Erreur arguments | Vérifier syntaxe |
| 2 | Configuration invalide | Examiner config.sh |
| 3 | Répertoire log inaccessible | Vérifier permissions LOG_DIR |
| 4 | Espace disque insuffisant | Libérer espace DEST_BASE_SAUVEGARDES |
| 5 | Connexion SSH échouée | Vérifier réseau/identifiants |
| 6 | Point montage SSHFS occupé | Démonter manuellement |
| 7 | Montage SSHFS échoué | Vérifier permissions/config SSH |
| 8 | Démontage SSHFS échoué | Point montage occupé |
| 9 | Erreur rsync | Examiner logs rsync |
| 10 | Script déjà en cours | Supprimer PID_FILE si nécessaire |
| 11 | Espace disque insuffisant | Nettoyer destination |
| 12 | Création répertoire échouée | Vérifier permissions parent |
| 13 | Source inexistante | Vérifier chemin source |
| 14 | Configuration sauvegarde | Vérifier définitions config.sh |
| 15 | Envoi email échoué | Vérifier MTA et EMAIL_NOTIFICATION |
| 16 | Nettoyage sauvegardes échoué | Vérifier permissions |
| 17 | Chemin distant inaccessible | Vérifier chemin et permissions SSH |
| 127 | Commande non trouvée | Installer dépendances manquantes |

**Fonctions de Validation :**

*valider_variable() :*
- Types supportés : string, path, int, ip, port, uuid
- Paramètre `is_destination_path` pour chemins destination
- Validation IPv4 : regex `^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.{3}`
- Validation ports : 1-65535
- Validation UUID : pattern standard

*monter_sshfs() :*
- 5 paramètres : utilisateur, ip, port, chemin_distant, point_montage_local
- Variables : `tentatives=3`, `delai=5`
- Mode web : détection www-data, clé `/var/www/.ssh/backup_key`
- Options SSHFS : `reconnect,no_readahead,default_permissions,allow_other`

*demonter_sshfs() :*
- Retry avec `fusermount -uz`
- Kill processus avec `lsof` + `kill -9`
- 3 tentatives avec délai 5s

#### setup-web.sh - Installation (100+ lignes)

**Détection Distributions :**
```bash
if command -v apt-get >/dev/null 2>&1; then
    # Debian/Ubuntu
elif command -v yum >/dev/null 2>&1; then
    # RHEL/CentOS 7
elif command -v dnf >/dev/null 2>&1; then
    # RHEL/CentOS 8+/Fedora
```

**Dépendances Installées :**
- apache2/httpd, php, libapache2-mod-php/php-cli
- rsync, openssh-client/openssh-clients, sshfs, fuse
- mailutils/mailx, timeout

**Configuration Système :**
- PHP : suppression `disable_functions`, `max_execution_time=300`
- www-data : groupe fuse, shell `/bin/bash`, sudo NOPASSWD
- Clé SSH : `/var/www/.ssh/backup_key` (RSA, sans passphrase)
- Répertoires : `/tmp/{backup_logs,backups,sshfs_mounts}`

### Interface Web - Couche Présentation

#### index.php - Dashboard (300+ lignes)

**Variables Globales PHP :**
| Variable | Type | Usage |
|----------|------|-------|
| `$SCRIPT_DIR` | String | `dirname(__DIR__)` |
| `$message` | String | Messages de retour |
| `$error` | String | Messages d'erreur |
| `$sauvegardes` | Array | Liste sauvegardes |

**Fonctions Principales :**

*readConfigValue($key) :*
- Lit config.sh avec regex `/^key="?([^"\n]+)"?/m`
- Retourne valeur nettoyée avec `trim()`

*getSauvegardes() :*
- Lit `default_backups.conf` pour sauvegardes par défaut
- Lit `sauvegardes_custom.conf` avec pattern `# SAUVEGARDE: (.+)`
- Retourne array avec type, name, enabled

*executerSauvegarde($selection, $dryRun) :*
- Validation liste blanche + sauvegardes custom
- Commande : `cd "$SCRIPT_DIR" && $script $selection 2>&1`
- Sécurité : `escapeshellarg()` sur tous paramètres

*toggleDefaultBackup($backupName) :*
- Bascule entre `backup=1` et `backup=0`
- Utilise `preg_replace()` pour modification

#### manage.php - Gestion Sauvegardes (250+ lignes)

**Variables Globales :**
- `$CUSTOM_CONFIG = '../sauvegardes_custom.conf'`

**Fonctions de Gestion :**

*ajouterSauvegarde($data) :*
- Validation nom : regex `[a-zA-Z0-9_]{3,50}`
- Génération variables selon type :
  - Locale : `SOURCE_LOCALE_*`, `DEST_MAIN_*`, `DEST_INCR_BASE_*`
  - Distante : + `SSH_USER_*`, `SSH_IP_*`, `SSH_PORT_*`, `MONTAGE_SSHFS_*`
- Rétention : `JOURS_RETENTION_*_QUOTIDIEN/HEBDO/MENSUEL`
- Format : `# SAUVEGARDE: nom` + variables

*supprimerSauvegarde($nom) :*
- Pattern regex : `/\n# SAUVEGARDE: nom\n.*?(?=\n# SAUVEGARDE:|$)/s`
- Suppression complète du bloc

*toggleSauvegarde($nom) :*
- Bascule `# SAUVEGARDE:` ↔ `# SAUVEGARDE_DISABLED:`

#### logs.php - Visualisation Logs (200+ lignes)

**Variables de Configuration :**
- `$configLogDir = getConfigValue('LOG_DIR')`
- `$LOG_DIR = ($configLogDir && is_writable($configLogDir)) ? $configLogDir : '/tmp/backup_logs'`

**Fonctions :**

*getLogFiles() :*
- Pattern : `glob($LOG_DIR . '/sauvegarde_*.log')`
- Tri : `rsort($files)` (plus récents en premier)
- Métadonnées : taille, date modification

*calculateLogStats() :*
- Période : 7 jours (`$cutoff = time() - (7 * 24 * 60 * 60)`)
- Patterns : `Sauvegardes réussies: (\d+)`, `Sauvegardes échouées: (\d+)`
- Retour : array success, error, total_size

#### terminal.php - Terminal Interactif (400+ lignes)

**Session Persistante :**
- `$_SESSION['terminal_cwd']` : répertoire de travail
- Initialisation : `dirname(__DIR__)`

**Streaming Temps Réel :**
- Headers : `text/event-stream`, `Cache-Control: no-cache`
- Format : `data: {"type":"output","data":"ligne","cwd":"path"}\n\n`

**Commandes Intégrées :**
- `cd` : change `$_SESSION['terminal_cwd']` avec `realpath()`
- `clear` : envoie `{"type":"clear"}`
- `help` : affiche aide intégrée

**Exécution Commandes :**
- `popen($command . ' 2>&1', 'r')` pour streaming
- `exec($command . ' 2>&1', $output, $code)` pour fallback
- Lecture ligne par ligne avec `fgets()`

#### status.php - API Statut (50 lignes)

**Fichiers de Statut :**
| Fichier | Contenu | Usage |
|---------|---------|-------|
| `/tmp/backup_running.flag` | Existence | Sauvegarde active |
| `/tmp/current_backup.txt` | Nom sauvegarde | Sauvegarde courante |
| `/tmp/backup_progress.txt` | 0-100 | Progression |
| `/tmp/backup_start_time.txt` | Timestamp | Heure début |
| `/tmp/last_success.txt` | Date + message | Dernier succès |
| `/tmp/last_error.txt` | Date + message | Dernière erreur |

**Format JSON :**
```json
{
  "running": boolean,
  "current_backup": string,
  "progress": string,
  "start_time": string,
  "last_log": array,
  "stats": {
    "total_backups": 5,
    "last_success": string,
    "last_error": string
  }
}
```

#### app.js - Client JavaScript (500+ lignes)

**Variables Globales :**
| Variable | Type | Usage |
|----------|------|-------|
| `SCRIPT_PATH` | String | '../sauvegarde.sh' |
| `isRunning` | Boolean | État exécution |
| `customBackups` | Array | Sauvegardes custom |
| `statusInterval` | Integer | Intervalle polling |

**Fonctions Principales :**

*executeRealBackup(selection, dryRun) :*
- FormData avec action='run_backup'
- Fetch vers `index.php`
- Parsing HTML avec DOMParser
- Extraction résultat depuis `.alert.alert-success pre`

*updateStatus() :*
- Fetch `status.php` toutes les 2 secondes
- Mise à jour métriques dashboard
- Gestion logs temps réel

*updateDashboard(status) :*
- Statut : 🟢 (inactif), 🟡 (en cours)
- Progression : barre + pourcentage
- Durée : calcul minutes/secondes depuis start_time
- Logs : coloration selon niveau (INFO/ATTENTION/ERREUR)

#### style.css - Interface Moderne (800+ lignes)

**Architecture CSS :**
- Layout : `.app-layout` (flex), `.sidebar` (260px), `.main-content` (flex: 1)
- Composants : `.card`, `.metric-card`, `.table`, `.btn`, `.alert`
- Terminal : `.terminal-container`, thème sombre, police Courier New
- Responsive : media query `@media (max-width: 768px)`

### Configuration - Couche Données

#### default_backups.conf

**Format :**
```
docs_eric=1
docs_fanou=1
photos_vm=1
projets_serveur=1
docs_portable=1
```

#### sauvegardes_custom.conf

**Format :**
```
# SAUVEGARDE: nom_sauvegarde
SOURCE_LOCALE_NOM_SAUVEGARDE="/chemin/source"
DEST_MAIN_NOM_SAUVEGARDE="$DEST_BASE_SAUVEGARDES/Destination/"
DEST_INCR_BASE_NOM_SAUVEGARDE="$DEST_BASE_SAUVEGARDES/incremental-Destination/"
JOURS_RETENTION_NOM_SAUVEGARDE_QUOTIDIEN=7
JOURS_RETENTION_NOM_SAUVEGARDE_HEBDO=4
JOURS_RETENTION_NOM_SAUVEGARDE_MENSUEL=12
```

## Interactions et Flux de Données

### Flux Principal d'Exécution

1. **Interface Web** → `index.php::executerSauvegarde()`
2. **PHP** → `shell_exec("cd $SCRIPT_DIR && ./sauvegarde.sh $selection")`
3. **sauvegarde.sh** → Source `config.sh` + `fonctions_erreur.sh`
4. **Validation** → `valider_variable()` pour tous paramètres
5. **Exécution** → `effectuer_sauvegarde()` avec 8 paramètres
6. **Rsync** → Commande construite dynamiquement selon type
7. **Statut** → Écriture `/tmp/backup_*.txt`
8. **JavaScript** → Polling `status.php` → Mise à jour interface

### Communication Inter-Modules

**Fichiers Temporaires :**
- `/tmp/backup_running.flag` : Flag d'exécution
- `/tmp/current_backup.txt` : Sauvegarde en cours
- `/tmp/backup_progress.txt` : Progression 0-100
- `/tmp/backup_start_time.txt` : Timestamp début

**Variables Partagées :**
- `DEST_BASE_SAUVEGARDES` : Utilisée par toutes destinations
- `LOG_DIR` : Partagée Bash ↔ PHP
- Noms sauvegardes : Cohérence config.sh ↔ default_backups.conf

### Dépendances Critiques

**Variables Calculées :**
- `DEST_MAIN_*="$DEST_BASE_SAUVEGARDES/Nom/"`
- `DEST_INCR_BASE_*="$DEST_BASE_SAUVEGARDES/incremental-Nom/"`
- `LOG_FILE="${LOG_DIR}/sauvegarde_$(date '+%Y%m%d').log"`

**Patterns de Nommage :**
- Variables SSH : `SSH_{USER|IP|PORT}_SAUVEGARDE`
- Variables rétention : `JOURS_RETENTION_SAUVEGARDE_{QUOTIDIEN|HEBDO|MENSUEL}`
- Variables montage : `MONTAGE_SSHFS_SAUVEGARDE`
