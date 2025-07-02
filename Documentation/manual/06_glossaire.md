# Glossaire

## A

**ACTIVERLOCK**
Variable de contrôle du mécanisme de verrouillage (`ACTIVERLOCK=1`). Empêche l'exécution simultanée de plusieurs instances du script via fichier PID.

**API (Application Programming Interface)**
Interface de programmation permettant la communication entre différents composants logiciels. Dans le projet, status.php fournit une API JSON pour le statut des sauvegardes avec structure : `{running, current_backup, progress, start_time, last_log, stats}`.

**Apache**
Serveur web open source utilisé pour héberger l'interface web du système. Configuration automatique via setup-web.sh avec modules PHP activés.

**App.js**
Fichier JavaScript client (interface web) gérant l'interactivité. Fonctions principales : `executeRealBackup()`, `updateStatus()`, `updateDashboard()`, `runBackup()`, polling automatique toutes les 2 secondes.

**Authentification SSH**
Processus de vérification d'identité pour les connexions SSH, utilisant des clés cryptographiques dédiées `/var/www/.ssh/backup_key` générées automatiquement par setup-web.sh.

## B

**Backup**
Terme anglais pour sauvegarde. Copie de sécurité des données stockée séparément des données originales.

**Bash**
Shell Unix utilisé pour l'exécution des scripts de sauvegarde. Langage de script principal du projet avec options sécurisées `set -o errexit -o nounset -o pipefail`.

**Batch Mode**
Mode non interactif d'exécution SSH (`-o BatchMode=yes`) pour éviter les demandes de mot de passe.

## C

**Clé SSH**
Paire de clés cryptographiques (publique/privée) utilisée pour l'authentification SSH sans mot de passe. Clé dédiée du projet : `/var/www/.ssh/backup_key`.

**Code d'Erreur**
Numéro identifiant un type d'erreur spécifique. Le système utilise 18 codes d'erreur (1-17 + 127), gérés par `diagnostiquer_et_logger_erreur()` avec actions suggérées automatiques et messages détaillés.

**Config.sh**
Fichier de configuration principal (version 2.5, 200+ lignes) contenant 75+ variables organisées en 9 catégories. Sourcé en premier dans sauvegarde.sh. Inclut chemins d'exécutables configurables (`CHEMIN_RSYNC`, `CHEMIN_SSH`, etc.).

**Configure_web_environment()**
Fonction d'adaptation automatique pour l'exécution web. Détecte l'utilisateur www-data, adapte les répertoires (`LOG_DIR`, `DEST_BASE_SAUVEGARDES`), crée les points de montage SSHFS.

**Cron/Crontab**
Système de planification de tâches Unix/Linux permettant l'exécution automatique des sauvegardes.

## D

**Dashboard**
Tableau de bord principal (index.php, 300+ lignes) affichant l'état du système et les métriques temps réel avec polling automatique toutes les 2 secondes. Métriques : statut, progression, durée, nombre de sauvegardes.

**Default_backups.conf**
Fichier de configuration des sauvegardes par défaut. Format : `nom_sauvegarde=1` (activée), `=0` (désactivée). Lu par `is_default_backup_enabled()`. Contrôle 5 sauvegardes : docs_eric, docs_fanou, photos_vm, projets_serveur, docs_portable.

**DEFAULT_RSYNC_OPTIONS**
Variable définissant les options rsync par défaut : `"-avh --partial --progress --info=progress2,misc0,name0"`. Complétée par `RSYNC_DELETE` pour contrôler l'option --delete.

**DEFAULT_TYPE_CONNEXION_DISTANTE**
Variable de configuration du type de connexion distante : `0` = SSHFS (recommandé), `1` = SSH direct (rsync via SSH).

**Destination**
Répertoire de stockage des sauvegardes. Variable racine : `DEST_BASE_SAUVEGARDES="/mnt/backup_nas"`. Adaptable en `/tmp/backups` en mode web.

**Diagnostiquer_et_logger_erreur()**
Fonction centrale de gestion des erreurs avec 18 codes spécifiques. Chaque erreur inclut diagnostic automatique, action suggérée et exit avec code approprié.

**Dry-run**
Mode de simulation (option `--dry-run`, variable `DRY_RUN=1`) permettant de tester sans modifications réelles. Supporté dans l'interface web et en ligne de commande.

## E

**Échappement Shell**
Technique de sécurisation via `escapeshellarg()` pour éviter l'injection de code dans les commandes shell. Utilisé dans toutes les fonctions PHP d'exécution.

**Effectuer_sauvegarde()**
Fonction centrale du script principal avec 8 paramètres : type, source, dest_main, dest_incr_base, ssh_user, ssh_ip, ssh_port, montage_sshfs. Gère les sauvegardes locales et distantes.

**EMAIL_NOTIFICATION**
Variable de configuration pour l'envoi de rapports par email. Fonction `envoyer_rapport_email()` avec détection automatique de la commande mail (mailx/mail).

**Espace Disque**
Capacité de stockage surveillée par `verifier_espace_disque()`. Seuil minimum : `ESPACE_DISQUE_MIN_GO=5`. Vérification avant chaque sauvegarde.

## F

**Fichier de Verrouillage**
Fichier PID (`$PID_FILE="/var/run/sauvegarde.pid"`) empêchant l'exécution simultanée. Géré par `gerer_verrouillage()` avec trap automatique.

**Fonctions_erreur.sh**
Module de gestion centralisée des erreurs (version 6.6 Beta, 400+ lignes). Fonctions principales : `log_info()`, `log_warning()`, `log_error()`, `diagnostiquer_et_logger_erreur()`, `valider_variable()`, `monter_sshfs()`, `demonter_sshfs()`, `verifier_connexion_ssh()`, `verifier_chemin_distant_ssh()`.

**FUSE (Filesystem in Userspace)**
Système permettant les systèmes de fichiers en espace utilisateur. Requis pour SSHFS, utilisateur www-data doit être membre du groupe fuse.

## G

**Gestion d'Erreurs**
Mécanisme centralisé avec 18 codes spécifiques (1-17 + 127). Chaque erreur inclut diagnostic automatique et action suggérée. Logs de secours dans `/tmp/backup_fallback_errors.log`.

**Gerer_verrouillage()**
Fonction de gestion du verrouillage anti-concurrence. Crée fichier PID, vérifie processus existant, configure trap pour nettoyage automatique.

## H

**Hardlink**
Lien physique vers un fichier, utilisé par rsync avec `--link-dest=../current` pour optimiser l'espace disque des sauvegardes incrémentales.

**Hook**
Scripts personnalisés exécutés à des moments spécifiques : `SCRIPT_PRE_SAUVEGARDE_GLOBAL` (avant), `SCRIPT_POST_SAUVEGARDE_GLOBAL` (après). Vérification d'existence et d'exécutabilité automatique.

## I

**Incrémentale (Sauvegarde)**
Type de sauvegarde ne copiant que les fichiers modifiés. Structure : `daily-YYYY-MM-DD_HHMMSS`, lien `current` vers dernière sauvegarde réussie.

**Index.php**
Fichier principal du dashboard web (300+ lignes). Fonctions : `readConfigValue()`, `getSauvegardes()`, `isDefaultBackupEnabled()`, `toggleDefaultBackup()`, `executerSauvegarde()`. Interface complète de gestion.

**Interface Web**
7 fichiers PHP/HTML/JS/CSS : index.php (dashboard), manage.php (gestion), logs.php (journaux), terminal.php (terminal), status.php (API), app.js (client), style.css (styles). Architecture MVC avec session persistante.

**Is_default_backup_enabled()**
Fonction de vérification de l'état d'une sauvegarde par défaut. Lit default_backups.conf, retourne true si activée (=1) ou si fichier absent.

## J

**JavaScript**
Langage client pour l'interactivité web. Fonctions principales : `executeRealBackup()`, `updateStatus()`, `updateDashboard()`, `runBackup()`.

**JSON**
Format d'échange de données utilisé par status.php. Structure : `{running, current_backup, progress, start_time, last_log, stats}`.

**Journalisation**
Enregistrement des événements dans des logs quotidiens `sauvegarde_YYYYMMDD.log`. Trois niveaux : `[INFO]`, `[ATTENTION]`, `[ERREUR]`. Logs de secours : `/tmp/backup_fallback_errors.log`.

## L

**Link-dest**
Option rsync `--link-dest=../current` créant des liens physiques vers la sauvegarde précédente pour économiser l'espace.

**LOG_DIR**
Variable définissant le répertoire des logs (`"/var/log/sauvegardes"`). Adaptatif : `/tmp/backup_logs` en mode web si inaccessible. Vérification des permissions par `verifier_permissions_log_dir()`.

**Logs.php**
Interface web de consultation des logs. Fonctions : `getConfigValue()`, `getLogFiles()`, `readLogFile()`, `calculateLogStats()`. Affichage sécurisé avec protection path traversal.

## M

**Manage.php**
Interface web de gestion des sauvegardes personnalisées. Fonctions : `ajouterSauvegarde()`, `supprimerSauvegarde()`, `toggleSauvegarde()`, `getSauvegardesCustom()`. Validation complète des données.

**Montage SSHFS**
Processus géré par `monter_sshfs()` avec 3 tentatives, délai 5 secondes. Options : `reconnect,no_readahead,default_permissions,allow_other`. Points de montage : `/tmp/sshfs_mounts/{photos_vm,projets_serveur,docs_portable}`. Mode web avec clé dédiée.

**Métrique**
Mesures quantitatives affichées dans le dashboard : statut, progression, durée, nombre de sauvegardes. Mise à jour temps réel via polling JavaScript.

## N

**Nettoyer_anciennes_sauvegardes()**
Fonction de nettoyage automatique selon politiques de rétention. Gère quotidien, hebdomadaire, mensuel. Supprime liens symboliques brisés, applique rétention par âge et nombre.

## O

**OPTIONS_COMMUNES_SSH**
Variable définissant les options SSH communes : `"-o BatchMode=yes -o ConnectTimeout=10"`. Complétée par `StrictHostKeyChecking_SSH` si défini.

**OPTIONS_RSYNC_INCREMENTALE**
Variable pour options rsync incrémentales : `"--link-dest=../current"`. Cruciale pour l'efficacité des sauvegardes avec hardlinks.

## P

**PHP**
Langage serveur pour l'interface web. Configuration requise : `max_execution_time=300`, `disable_functions=""`. Fonctions critiques : `shell_exec()`, `popen()` pour exécution de commandes. Configuration automatique par setup-web.sh.

**PID (Process Identifier)**
Identifiant numérique unique d'un processus, utilisé pour le verrouillage via fichier `$PID_FILE`. Vérification avec `kill -0` pour détecter processus actif.

**Polling**
Vérification périodique de l'état (toutes les 2 secondes) via `setInterval(updateStatus, 2000)`. Lit `/tmp/backup_*.txt` et met à jour le dashboard en temps réel.

## R

**Rétention**
Politique de conservation des sauvegardes. Variables : `JOURS_RETENTION_*_QUOTIDIEN/HEBDO/MENSUEL`. Nettoyage par `nettoyer_anciennes_sauvegardes()`. Configurable par sauvegarde.

**Rsync**
Outil de synchronisation. Options par défaut : `-avh --partial --progress --info=progress2,misc0,name0`. Support `--link-dest`, timeout configurable via `DELAI_OPERATION_RSYNC_SECONDES`. Chemin configurable via `CHEMIN_RSYNC`.

**RSYNC_DELETE**
Variable de contrôle de l'option rsync --delete : `0` = désactivé (sécurisé), `1` = activé (supprime fichiers supprimés de la source).

## S

**Sauvegarde Distante**
Sauvegarde via SSH/SSHFS. Variables : `SSH_USER_*`, `SSH_IP_*`, `SSH_PORT_*`, `SOURCE_DIST_*`, `MONTAGE_SSHFS_*`. Vérification connexion et chemin distant automatique.

**Sauvegarde Locale**
Sauvegarde sur le même système. Variables : `SOURCE_LOCALE_*`, `DEST_MAIN_*`, `DEST_INCR_BASE_*`. Plus simple, pas de montage réseau requis.

**Sauvegarde Personnalisée**
Sauvegarde configurée via interface web, stockée dans `sauvegardes_custom.conf`. Format : `# SAUVEGARDE: nom` (active), `# SAUVEGARDE_DISABLED: nom` (inactive). Gestion complète via manage.php.

**Sauvegardes_custom.conf**
Fichier de configuration des sauvegardes personnalisées. Format structuré avec variables cohérentes. Filtrage automatique des sauvegardes désactivées lors du chargement.

**Script Principal**
sauvegarde.sh (version 6.5, 800+ lignes). Fonction centrale : `effectuer_sauvegarde()` avec 8 paramètres. Gestion web automatique via `configure_web_environment()`. Support --dry-run, --list, --help.

**Session PHP**
Maintien d'état pour terminal persistant. Variable clé : `$_SESSION['terminal_cwd']`. Historique 50 commandes côté client. Gestion cd avec persistance.

**Setup-web.sh**
Script d'installation automatique (100+ lignes). Détection multi-distributions (Debian/Ubuntu, RHEL/CentOS, Fedora), installation dépendances, configuration PHP, permissions www-data, génération clés SSH.

**SSH (Secure Shell)**
Protocole sécurisé. Configuration : timeout 10s, `BatchMode=yes`, `StrictHostKeyChecking=no`. Clé dédiée : `/var/www/.ssh/backup_key`. Vérification connexion et chemin distant automatique.

**SSHFS**
Système de fichiers SSH. Mode par défaut (`DEFAULT_TYPE_CONNEXION_DISTANTE=0`). Démontage via `fusermount -uz`. Gestion des montages occupés avec retry et kill des processus.

**Status.php**
API JSON pour le statut des sauvegardes. Lit fichiers temporaires `/tmp/backup_*.txt`, logs récents, statistiques. Structure : `{running, current_backup, progress, start_time, last_log, stats}`.

**Streaming**
Transmission continue via Server-Sent Events (`text/event-stream`) dans terminal.php avec `popen()` et lecture ligne par ligne. Simulation WebSocket pour terminal temps réel.

**Style.css**
Feuille de styles CSS pour l'interface web. Design moderne avec sidebar, dashboard grid, métriques, tables, formulaires. Responsive design avec animations.

## T

**Terminal Web**
Interface terminal (terminal.php, 400+ lignes). Session persistante, streaming temps réel, historique commandes, exécution contexte www-data. Support commandes système complètes avec auto-complétion.

**Timeout**
Délais configurables : `DELAI_CONNEXION_SSH_SECONDES=10` (SSH), `DELAI_OPERATION_RSYNC_SECONDES=0` (rsync, 0=désactivé). Gestion via commande `timeout`.

**Toggle**
Fonctions d'activation/désactivation : `toggleDefaultBackup()` pour sauvegardes par défaut, `toggleSauvegarde()` pour personnalisées. Modification des fichiers de configuration.

**Traiter_sauvegarde_personnalisee()**
Fonction de traitement des sauvegardes personnalisées. Détection automatique du type (locale/distante), construction des variables, exécution avec nettoyage.

**Trap**
Mécanisme bash de capture des signaux pour nettoyage automatique : `trap "rm -f '$PID_FILE'; exit" EXIT SIGINT SIGTERM`. Garantit suppression du fichier de verrouillage.

## V

**Valider_variable()**
Fonction de validation des variables avec types : `string`, `path`, `int`, `ip` (regex IPv4), `port` (1-65535), `uuid`. Paramètre `is_destination_path` pour destinations. Validation stricte avec messages d'erreur.

**Variables de Configuration**
Plus de 75 variables organisées en 9 catégories : options globales, chemins critiques, paramètres avancés, hooks, sauvegardes spécifiques, politiques de rétention. Noms en français cohérents.

**Verifier_chemin_distant_ssh()**
Fonction de vérification de l'existence d'un chemin sur serveur distant via SSH. Utilise `test -d` ou `test -f` pour validation avant montage.

**Verifier_connexion_ssh()**
Fonction de test de connectivité SSH. Validation IP, port, utilisateur. Test de connexion réelle avec options communes. Diagnostic d'erreur détaillé.

**Verifier_espace_disque()**
Fonction de vérification de l'espace disque libre. Utilise `df -BG`, compare au seuil minimum, génère erreur si insuffisant.

**Verrouillage**
Mécanisme anti-concurrence. Contrôlé par `ACTIVERLOCK=1`, fichier PID, vérification processus avec `kill -0`. Trap automatique pour nettoyage.

## W

**www-data**
Utilisateur système du serveur web. Configuration requise : membre groupe fuse, shell `/bin/bash`, sudo `NOPASSWD: ALL`, home `/var/www` avec clés SSH. Configuration automatique par setup-web.sh.

## Variables de Configuration Principales

### Variables Système
- `DEST_BASE_SAUVEGARDES="/mnt/backup_nas"` : Répertoire racine des sauvegardes
- `LOG_DIR="/var/log/sauvegardes"` : Répertoire des logs
- `EMAIL_NOTIFICATION="votre_email@example.com"` : Email pour notifications
- `ESPACE_DISQUE_MIN_GO=5` : Espace disque minimum requis (Go)
- `PID_FILE="/var/run/sauvegarde.pid"` : Fichier de verrouillage
- `ACTIVERLOCK=1` : Activation du verrouillage

### Variables Rsync
- `DEFAULT_RSYNC_OPTIONS="-avh --partial --progress --info=progress2,misc0,name0"` : Options par défaut
- `RSYNC_DELETE=0` : Contrôle option --delete (0=sécurisé)
- `OPTIONS_RSYNC_INCREMENTALE="--link-dest=../current"` : Options incrémentales
- `DELAI_OPERATION_RSYNC_SECONDES=0` : Timeout rsync (0=désactivé)

### Variables SSH
- `DELAI_CONNEXION_SSH_SECONDES=10` : Timeout connexion SSH
- `OPTIONS_COMMUNES_SSH="-o BatchMode=yes -o ConnectTimeout=10"` : Options communes
- `StrictHostKeyChecking_SSH="no"` : Vérification clés d'hôte
- `DEFAULT_TYPE_CONNEXION_DISTANTE=0` : Type connexion (0=SSHFS, 1=SSH direct)

### Variables de Chemins d'Exécutables
- `CHEMIN_RSYNC="/usr/bin/rsync"` : Chemin rsync
- `CHEMIN_SSH="/usr/bin/ssh"` : Chemin ssh
- `CHEMIN_SSHFS="/usr/bin/sshfs"` : Chemin sshfs
- `CHEMIN_FUSEMOUNT="/usr/bin/fusermount"` : Chemin fusermount
- `CHEMIN_MOUNTPOINT="/usr/bin/mountpoint"` : Chemin mountpoint
- `CHEMIN_LSOF="/usr/bin/lsof"` : Chemin lsof
- `CHEMIN_KILL="/usr/bin/kill"` : Chemin kill
- `CHEMIN_MKDIR="/usr/bin/mkdir"` : Chemin mkdir
- `CHEMIN_MAIL="/usr/bin/mailx"` : Chemin mail

### Variables de Journalisation
- `DEFAULT_JOURNAUX_DESACTIVES=0` : Désactivation logs (0=actif)
- `TAILLE_MAX_LOG_MO=10` : Taille max log avant rotation
- `JOURS_RETENTION_LOGS=30` : Rétention logs en jours
- `COMMANDE_COMPRESSION_LOGS="gzip"` : Commande compression logs

### Variables de Sauvegardes Par Défaut
- `SOURCE_LOCALE_DOCS_ERIC="/home/eric/Documents"` : Source docs Eric
- `SOURCE_LOCALE_DOCS_FANOU="/home/fanou/Documents"` : Source docs Fanou
- `SSH_USER_PHOTOS="votre_utilisateur_vm_photos"` : Utilisateur SSH photos VM
- `SSH_IP_PHOTOS="192.168.1.100"` : IP photos VM
- `SOURCE_DIST_PHOTOS_VM="/chemin/sur/vm/Photos"` : Chemin distant photos
- `MONTAGE_SSHFS_PHOTOS="/tmp/sshfs_mounts/photos_vm"` : Point montage photos

### Variables de Rétention
- `JOURS_RETENTION_*_QUOTIDIEN=7` : Rétention quotidienne (jours)
- `JOURS_RETENTION_*_HEBDO=4` : Rétention hebdomadaire (semaines)
- `JOURS_RETENTION_*_MENSUEL=12` : Rétention mensuelle (mois)

### Variables de Hooks
- `SCRIPT_PRE_SAUVEGARDE_GLOBAL=""` : Script pré-sauvegarde
- `SCRIPT_POST_SAUVEGARDE_GLOBAL=""` : Script post-sauvegarde

### Codes d'Erreur du Système

**Code 1** : Erreur d'arguments - Vérifiez les arguments passés au script et la syntaxe
**Code 2** : Erreur de configuration - Variable manquante, vide ou incorrecte dans config.sh
**Code 3** : Erreur de permissions logs - Répertoire de log inaccessible en écriture
**Code 4** : Espace disque insuffisant - Libérez de l'espace sur la destination
**Code 5** : Erreur de connexion SSH - Vérifiez connectivité réseau, identifiants SSH
**Code 6** : Point de montage SSHFS occupé - Démontez manuellement ou vérifiez utilisation
**Code 7** : Échec montage SSHFS - Vérifiez permissions, configuration SSH, chemin distant
**Code 8** : Échec démontage SSHFS - Point de montage toujours occupé
**Code 9** : Erreur rsync - Examinez les logs pour détails de l'erreur rsync spécifique
**Code 10** : Script déjà en cours - Supprimez manuellement le fichier de verrouillage si nécessaire
**Code 11** : Espace disque insuffisant destination - Nettoyez ou libérez de l'espace
**Code 12** : Échec création répertoire - Vérifiez permissions du répertoire parent
**Code 13** : Source inaccessible - Source n'existe pas, vide ou non accessible en lecture
**Code 14** : Erreur configuration sauvegarde - Vérifiez définitions SOURCE/DEST dans config.sh
**Code 15** : Échec envoi email - Vérifiez configuration serveur de messagerie (MTA)
**Code 16** : Échec nettoyage sauvegardes - Vérifiez logique et permissions répertoire
**Code 17** : Chemin distant inaccessible - Chemin n'existe pas sur l'hôte distant
**Code 127** : Commande non trouvée - Dépendance logicielle manquante ou mal configuréeeyChecking_SSH="no"` : Vérification clés d'hôte

### Variables de Contrôle
- `ACTIVERLOCK=1` : Active le verrouillage PID
- `DEFAULT_TYPE_CONNEXION_DISTANTE=0` : Mode connexion (0=SSHFS, 1=SSH direct)
- `DEFAULT_SELECTIONS_SAUVEGARDES="docs_eric docs_fanou"` : Sauvegardes par défaut

### Variables de Chemins Exécutables
- `CHEMIN_RSYNC="/usr/bin/rsync"` : Chemin rsync
- `CHEMIN_SSH="/usr/bin/ssh"` : Chemin ssh
- `CHEMIN_SSHFS="/usr/bin/sshfs"` : Chemin sshfs
- `CHEMIN_FUSEMOUNT="/usr/bin/fusermount"` : Chemin fusermount
- `CHEMIN_MAIL="/usr/bin/mailx"` : Chemin mail

## Sauvegardes Prédéfinies (5 configurées)

### Sauvegardes Locales
1. **docs_eric** : Documents utilisateur Eric
   - `SOURCE_LOCALE_DOCS_ERIC="/home/eric/Documents"`
   - `DEST_MAIN_DOCS_ERIC="$DEST_BASE_SAUVEGARDES/DocumentsEric/"`
   - `DEST_INCR_BASE_DOCS_ERIC="$DEST_BASE_SAUVEGARDES/incremental-DocumentsEric/"`

2. **docs_fanou** : Documents utilisateur Fanou
   - `SOURCE_LOCALE_DOCS_FANOU="/home/fanou/Documents"`
   - `DEST_MAIN_DOCS_FANOU="$DEST_BASE_SAUVEGARDES/DocumentsFanou/"`

### Sauvegardes Distantes
3. **photos_vm** : Photos depuis VM distante
   - `SSH_USER_PHOTOS="votre_utilisateur_vm_photos"`
   - `SSH_IP_PHOTOS="192.168.1.100"`
   - `SOURCE_DIST_PHOTOS_VM="/chemin/sur/vm/Photos"`
   - `MONTAGE_SSHFS_PHOTOS="/tmp/sshfs_mounts/photos_vm"`

4. **projets_serveur** : Projets depuis serveur
   - `SSH_USER_PROJETS="votre_utilisateur_serveur_projets"`
   - `SSH_IP_PROJETS="192.168.1.101"`
   - `SOURCE_DIST_PROJETS_SERVEUR="/Projets/Serveur/"`
   - `MONTAGE_SSHFS_PROJETS="/tmp/sshfs_mounts/projets_serveur"`

5. **docs_portable** : Documents depuis portable
   - `SSH_USER_DOCS_PORTABLE="votre_utilisateur_portable"`
   - `SSH_IP_DOCS_PORTABLE="192.168.1.102"`
   - `SOURCE_DIST_DOCS_PORTABLE="/home/utilisateur/Documents/"`
   - `MONTAGE_SSHFS_DOCS_PORTABLE="/tmp/sshfs_mounts/docs_portable"`

## Fichiers de Statut Temporaires

- `/tmp/backup_running.flag` : Indicateur d'exécution active
- `/tmp/current_backup.txt` : Nom de la sauvegarde en cours
- `/tmp/backup_progress.txt` : Progression 0-100%
- `/tmp/backup_start_time.txt` : Heure de début d'exécution
- `/tmp/last_success.txt` : Dernière exécution réussie
- `/tmp/last_error.txt` : Dernière erreur détectée

## Codes d'Erreur Système

| Code | Description | Action Suggérée |
|------|-------------|-----------------|
| 1 | Erreur arguments | Vérifier syntaxe |
| 2 | Configuration invalide | Examiner config.sh |
| 3 | Permissions logs | Vérifier LOG_DIR |
| 4 | Espace disque insuffisant | Libérer espace |
| 5 | Connexion SSH échouée | Vérifier réseau/SSH |
| 6 | Point montage occupé | Démonter manuellement |
| 7 | Échec montage SSHFS | Vérifier permissions |
| 8 | Échec démontage SSHFS | Point montage occupé |
| 9 | Erreur rsync | Examiner logs rsync |
| 10 | Script déjà en cours | Supprimer PID file |
| 11 | Espace disque insuffisant | Nettoyer destination |
| 12 | Échec création répertoire | Vérifier permissions parent |
| 13 | Source inaccessible | Vérifier chemin source |
| 14 | Configuration sauvegarde | Vérifier définitions |
| 15 | Échec envoi email | Vérifier MTA |
| 16 | Échec nettoyage | Vérifier permissions |
| 17 | Chemin distant inaccessible | Vérifier chemin SSH |
| 127 | Commande non trouvée | Installer dépendances |

## Conventions de Nommage

### Variables
- **MAJUSCULES** : Variables globales (EMAIL_NOTIFICATION)
- **Préfixe + Nom** : Variables spécifiques (SSH_USER_PHOTOS)
- **Suffixe descriptif** : Type de rétention (QUOTIDIEN, HEBDO, MENSUEL)

### Fonctions
- **snake_case** : Fonctions bash (effectuer_sauvegarde)
- **camelCase** : Fonctions JavaScript (executeRealBackup)
- **snake_case** : Fonctions PHP (getSauvegardes)

### Fichiers
- **.sh** : Scripts bash exécutables
- **.php** : Scripts PHP interface web
- **.conf** : Fichiers de configuration
- **.log** : Fichiers de journalisation

