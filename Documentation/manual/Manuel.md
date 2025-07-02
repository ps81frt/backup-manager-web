
<div align="center">

═══════════════════════════════════════════════════

# **BACKUP MANAGER WEB**
## *Système de Sauvegarde Hybride Nouvelle Génération*

═══════════════════════════════════════════════════

### **MANUEL TECHNIQUE & GUIDE D'ADMINISTRATION**

**VERSION 6.5**  
**JUILLET 2025**

═══════════════════════════════════════════════════

**Auteur Principal :** enRIKO  
**Équipe de Développement :** geole, iznobe, Watael, steph810

═══════════════════════════════════════════════════

### **ARCHITECTURE HYBRIDE**
*Scripts Bash Robustes + Interface Web Moderne*

**Systèmes Supportés :** Debian | Ubuntu | RHEL | CentOS | Fedora  
**Stack Technologique :** Bash, PHP, JavaScript, HTML5, CSS3  
**Protocoles Réseau :** SSH, SSHFS, rsync, REST API

**Fonctionnalités :** Sauvegarde Locale & Distante | Incrémentale | Automatisée

═══════════════════════════════════════════════════

*Documentation Technique Complète*

═══════════════════════════════════════════════════

</div>

# Table des Matières

## 1. [Introduction](#introduction)
- [Contexte du Projet](#contexte-du-projet)
- [Objectifs du Système](#objectifs-du-système)
- [Périmètre du Projet](#périmètre-du-projet)

## 2. [Manuel Utilisateur](#manuel-utilisateur)
- [Installation et Configuration](#installation-et-configuration-initiale)
- [Utilisation en Ligne de Commande](#utilisation-en-ligne-de-commande)
- [Interface Web](#interface-web)
- [Programmation des Sauvegardes](#programmation-des-sauvegardes)
- [Gestion des Erreurs](#gestion-des-erreurs-courantes)
- [Bonnes Pratiques](#bonnes-pratiques)

## 3. [Description Fonctionnelle](#description-fonctionnelle)
- [Architecture Générale du Système](#architecture-générale-du-système)
- [Analyse Exhaustive du Code Source](#analyse-exhaustive-du-code-source)
- [Interactions et Flux de Données](#interactions-et-flux-de-données)

## 4. [Référence des Fonctions](#référence-complète-des-fonctions)
- [Scripts Bash](#scripts-bash)
- [Interface Web PHP](#interface-web-php)
- [JavaScript - app.js](#javascript---appjs)

## 5. [Diagnostics et Dépannage](#diagnostics-et-résolution-de-bugs)
- [Méthodologie de Diagnostic](#méthodologie-de-diagnostic)
- [Bugs Connus et Solutions](#bugs-connus-et-solutions)
- [Procédures de Maintenance](#procédures-de-maintenance)

## 6. [Glossaire](#glossaire)
- [A](#a)
- [Variables de Configuration Principales](#variables-de-configuration-principales)
- [Codes d'Erreur du Système](#codes-derreur-du-système)

## 7. [Index Thématique](#index-thématique)
- [Index par Fichiers](#index-par-fichiers)
- [Index par Fonctionnalités](#index-par-fonctionnalités)
- [Index par Problèmes Courants](#index-par-problèmes-courants)
- [Index Alphabétique Complet](#index-alphabétique-complet)

---

# Introduction

## Contexte du Projet

Backup Manager Web est un système de sauvegarde hybride développé pour répondre aux besoins de sauvegarde automatisée dans des environnements mixtes (local/distant). Le projet combine la robustesse des scripts Bash avec la convivialité d'une interface web moderne.

### Historique et Évolution

Le projet a évolué à travers plusieurs versions :

**Scripts Bash :**
- **Version 6.5 (2025-06-24)** : Script principal sauvegarde.sh 

**Interface Web :**
- 7 fichiers PHP/HTML/JS/CSS pour interface complète
- Dashboard temps réel avec polling automatique
- Terminal web interactif avec session persistante
- Gestion des sauvegardes personnalisées

### Auteurs et Contributeurs

- **Auteur principal** : enRIKO
- **Contributeurs** : geole, iznobe, Watael, steph810

## Objectifs du Système

### Objectifs Principaux

1. **Automatisation des Sauvegardes**
   - Sauvegardes incrémentales avec rsync et --link-dest
   - Politiques de rétention (quotidien/hebdo/mensuel)
   - Mode dry-run pour tests
   - Exécution programmée via cron

2. **Flexibilité de Configuration**
   - Support sauvegardes locales et distantes (SSHFS/SSH)
   - Configuration centralisée dans config.sh
   - Sauvegardes personnalisées via interface web
   - 5 sauvegardes prédéfinies configurables

3. **Interface Utilisateur Moderne**
   - Dashboard web avec métriques temps réel
   - Terminal interactif avec session persistante
   - Gestion visuelle des configurations
   - API REST pour statut en temps réel

4. **Robustesse et Fiabilité**
   - 17 codes d'erreur spécifiques avec actions suggérées
   - Validation stricte des variables (6 types)
   - Mécanisme de verrouillage PID avec trap EXIT
   - Retry automatique pour SSHFS (3 tentatives)

### Objectifs Techniques

1. **Portabilité**
   - Support Debian/Ubuntu, RHEL/CentOS, Fedora
   - Chemins d'exécutables configurables
   - Adaptation automatique environnement web (www-data)

2. **Sécurité**
   - Validation entrées avec regex strictes
   - Échappement commandes shell (escapeshellarg)
   - Clés SSH dédiées pour interface web
   - Options shell sécurisées (errexit, nounset, pipefail)

3. **Maintenabilité**
   - Code modulaire avec fonctions spécialisées
   - Séparation scripts Bash / interface PHP
   - Configuration centralisée avec variables françaises
   - Documentation technique complète

## Périmètre du Projet

### Fonctionnalités Incluses

#### Scripts de Sauvegarde (Bash)
- **sauvegarde.sh** : Script principal (800+ lignes, version 6.5)
  - Options sécurisées : `set -o errexit -o nounset -o pipefail`
  - Arguments : --dry-run, --list, --help
  - Sélections : docs_eric, docs_fanou, photos_vm, projets_serveur, docs_portable, all
  - Fonction `effectuer_sauvegarde()` avec 8 paramètres
  - Fonction `traiter_sauvegarde_personnalisee()` pour sauvegardes custom
  - Configuration web automatique (www-data)
  - Verrouillage PID avec trap EXIT
  - Hooks PRE/POST_SAUVEGARDE_GLOBAL
  - Fichiers statut : /tmp/backup_running.flag, /tmp/current_backup.txt

- **config.sh** : Configuration centralisée (200+ lignes, version 2.5)
  - Variables principales :
    - `DEST_BASE_SAUVEGARDES="/mnt/backup_nas"`
    - `LOG_DIR="/var/log/sauvegardes"`
    - `EMAIL_NOTIFICATION="votre_email@example.com"`
  - Chemins exécutables : CHEMIN_RSYNC, CHEMIN_SSH, CHEMIN_SSHFS, etc.
  - Options rsync : `DEFAULT_RSYNC_OPTIONS`, `RSYNC_DELETE=0`
  - Variables SSH : timeout, options communes, StrictHostKeyChecking
  - 5 sauvegardes prédéfinies avec variables complètes
  - Politiques rétention : JOURS_RETENTION_*_QUOTIDIEN/HEBDO/MENSUEL

- **fonctions_erreur.sh** : Gestion erreurs (400+ lignes, version 6.6 Beta)
  - 17 codes d'erreur avec actions suggérées
  - Fonction `diagnostiquer_et_logger_erreur()` centralisée
  - Validation `valider_variable()` : string, path, int, ip, port, uuid
  - Fonctions SSHFS : `monter_sshfs()`, `demonter_sshfs()`
  - Journalisation : `log_info()`, `log_warning()`, `log_error()`
  - Logs fallback : `/tmp/backup_fallback_errors.log`
  - Adaptation mode web avec détection www-data

- **setup-web.sh** : Installation automatique (100+ lignes)
  - Détection distributions : apt-get, yum, dnf
  - Dépendances : apache2/httpd, php, rsync, openssh-client, sshfs, fuse
  - Configuration PHP : disable_functions, max_execution_time=300
  - Utilisateur www-data : groupe fuse, shell bash, sudo NOPASSWD
  - Clé SSH : `/var/www/.ssh/backup_key`
  - Répertoires : /tmp/backup_logs, /tmp/backups, /tmp/sshfs_mounts

#### Interface Web (PHP/HTML/JS)
- **Dashboard (index.php)** : Vue d'ensemble (300+ lignes)
  - Fonctions : `getSauvegardes()`, `executerSauvegarde()`, `toggleDefaultBackup()`
  - Lecture config : `readConfigValue()` depuis config.sh
  - Métriques temps réel : statut, progression, durée
  - Exécution sécurisée : `escapeshellarg()`, validation liste blanche
  - Gestion default_backups.conf (activation/désactivation)
  - Interface responsive avec sidebar

- **Gestion (manage.php)** : Sauvegardes personnalisées (250+ lignes)
  - Fonctions : `ajouterSauvegarde()`, `supprimerSauvegarde()`, `toggleSauvegarde()`
  - Validation : regex `[a-zA-Z0-9_]{3,50}`, unicité noms
  - Génération variables : SOURCE_LOCALE_*, DEST_MAIN_*, SSH_USER_*, etc.
  - Format sauvegardes_custom.conf : `# SAUVEGARDE: nom`
  - Support local/distant avec champs adaptatifs
  - Politiques rétention automatiques

- **Logs (logs.php)** : Visualisation journaux (200+ lignes)
  - Fonctions : `getLogFiles()`, `readLogFile()`, `calculateLogStats()`
  - Répertoire adaptatif : LOG_DIR ou /tmp/backup_logs
  - Fichiers : sauvegarde_YYYYMMDD.log
  - Statistiques 7 jours : succès/échec
  - Sécurité : `basename()` contre path traversal

- **Terminal (terminal.php)** : Terminal interactif (400+ lignes)
  - Session : `$_SESSION['terminal_cwd']` persistante
  - Streaming : Server-Sent Events (`text/event-stream`)
  - Exécution : `popen()` avec lecture temps réel
  - Commandes intégrées : cd, clear, help
  - Historique 50 commandes côté client
  - Suggestions : ./sauvegarde.sh, systemctl, htop

- **API Status (status.php)** : Statut JSON (50 lignes)
  - Fichiers : /tmp/backup_running.flag, /tmp/current_backup.txt
  - JSON : running, current_backup, progress, start_time
  - Logs récents : 5 dernières lignes
  - Statistiques : last_success, last_error

- **Client JavaScript (app.js)** : Interface dynamique (500+ lignes)
  - Fonction : `executeRealBackup()` via fetch API
  - Polling : `updateStatus()` toutes les 2 secondes
  - Dashboard : `updateDashboard()` métriques temps réel
  - Notifications auto-hide 5 secondes
  - Raccourcis : Ctrl+R (exécuter), Ctrl+L (effacer)

- **Styles (style.css)** : Interface moderne (800+ lignes)
  - Design responsive sidebar + main-content
  - Composants : cards, tables, forms, buttons, alerts
  - Terminal : thème sombre, police Courier New
  - Animations CSS et transitions
  - Classes utilitaires grid/flex

#### Configuration et Données
- **default_backups.conf** : Activation/désactivation sauvegardes par défaut
  - Format : `docs_eric=1` (activée), `docs_eric=0` (désactivée)
- **sauvegardes_custom.conf** : Sauvegardes personnalisées
  - Format : `# SAUVEGARDE: nom` suivi des variables

### Types de Sauvegardes Supportées

#### Sauvegardes Locales
- Répertoires système : /home, /etc, /var
- Données utilisateur : Documents, projets
- Variables : SOURCE_LOCALE_*, DEST_MAIN_*, DEST_INCR_BASE_*

#### Sauvegardes Distantes
- **Via SSHFS** (DEFAULT_TYPE_CONNEXION_DISTANTE=0)
  - Montage transparent : MONTAGE_SSHFS_*
  - Points de montage : /tmp/sshfs_mounts/
- **Via SSH Direct** (DEFAULT_TYPE_CONNEXION_DISTANTE=1)
  - Rsync direct via SSH sans montage
- Variables SSH : SSH_USER_*, SSH_IP_*, SSH_PORT_*

#### Sauvegardes Prédéfinies (5 configurées)

**Sauvegardes Locales :**
1. **docs_eric** : Documents utilisateur Eric
   - Source : `SOURCE_LOCALE_DOCS_ERIC="/home/eric/Documents"`
   - Destination : `DEST_MAIN_DOCS_ERIC="$DEST_BASE_SAUVEGARDES/DocumentsEric/"`
   - Incrémentale : `DEST_INCR_BASE_DOCS_ERIC="$DEST_BASE_SAUVEGARDES/incremental-DocumentsEric/"`
   - Rétention : JOURS_RETENTION_DOCS_ERIC_QUOTIDIEN/HEBDO/MENSUEL

2. **docs_fanou** : Documents utilisateur Fanou
   - Source : `SOURCE_LOCALE_DOCS_FANOU="/home/fanou/Documents"`
   - Destination : `DEST_MAIN_DOCS_FANOU="$DEST_BASE_SAUVEGARDES/DocumentsFanou/"`
   - Incrémentale : `DEST_INCR_BASE_DOCS_FANOU="$DEST_BASE_SAUVEGARDES/incremental-DocumentsFanou/"`

**Sauvegardes Distantes :**
3. **photos_vm** : Photos depuis VM distante
   - SSH : `SSH_USER_PHOTOS`, `SSH_IP_PHOTOS`, `SSH_PORT_PHOTOS=22`
   - Source : `SOURCE_DIST_PHOTOS_VM="/chemin/sur/vm/Photos"`
   - Montage : `MONTAGE_SSHFS_PHOTOS="/tmp/sshfs_mounts/photos_vm"`
   - Destination : `DEST_MAIN_PHOTOS="$DEST_BASE_SAUVEGARDES/PhotosVM/"`

4. **projets_serveur** : Projets depuis serveur
   - SSH : `SSH_USER_PROJETS`, `SSH_IP_PROJETS`, `SSH_PORT_PROJETS=22`
   - Source : `SOURCE_DIST_PROJETS_SERVEUR="/Projets/Serveur/"`
   - Montage : `MONTAGE_SSHFS_PROJETS="/tmp/sshfs_mounts/projets_serveur"`
   - Destination : `DEST_MAIN_PROJETS="$DEST_BASE_SAUVEGARDES/ProjetsServeur/"`

5. **docs_portable** : Documents depuis portable
   - SSH : `SSH_USER_DOCS_PORTABLE`, `SSH_IP_DOCS_PORTABLE`, `SSH_PORT_DOCS_PORTABLE=22`
   - Source : `SOURCE_DIST_DOCS_PORTABLE="/home/utilisateur/Documents/"`
   - Montage : `MONTAGE_SSHFS_DOCS_PORTABLE="/tmp/sshfs_mounts/docs_portable"`
   - Destination : `DEST_MAIN_DOCS_PORTABLE="$DEST_BASE_SAUVEGARDES/DocumentsPortable/"`

### Architecture Technique

#### Structure des Répertoires
```
backup-manager-web/
├── sauvegarde.sh              # Script principal
├── config.sh                  # Configuration centralisée
├── fonctions_erreur.sh        # Gestion erreurs
├── setup-web.sh              # Installation
├── default_backups.conf       # Sauvegardes par défaut
├── sauvegardes_custom.conf    # Sauvegardes personnalisées
└── web/                       # Interface web
    ├── index.php              # Dashboard
    ├── manage.php             # Gestion sauvegardes
    ├── logs.php               # Visualisation logs
    ├── terminal.php           # Terminal interactif
    ├── status.php             # API statut
    ├── app.js                 # JavaScript client
    └── style.css              # Styles CSS
```

#### Flux de Données
1. **Configuration** : config.sh → Scripts Bash + Interface PHP
2. **Exécution** : Interface Web → PHP → shell_exec() → sauvegarde.sh
3. **Statut** : Scripts → /tmp/backup_*.txt → status.php → JavaScript
4. **Logs** : Scripts → LOG_DIR/sauvegarde_YYYYMMDD.log → logs.php

#### Variables Clés
- **Destinations** : `DEST_BASE_SAUVEGARDES="/mnt/backup_nas"`
- **Logs** : `LOG_DIR="/var/log/sauvegardes"`
- **Verrouillage** : `PID_FILE="/var/run/sauvegarde.pid"`
- **Email** : `EMAIL_NOTIFICATION="votre_email@example.com"`
- **Sélections** : `DEFAULT_SELECTIONS_SAUVEGARDES="docs_eric docs_fanou"`
# Manuel Utilisateur

## Installation et Configuration Initiale

### Installation Automatique

Le système fournit un script d'installation automatique qui configure l'ensemble de l'environnement :

```bash
# Cloner ou télécharger le projet
cd /tmp/
git clone <repository> backup-manager-web
cd backup-manager-web

# Exécuter l'installation automatique (copie automatiquement vers /var/www/html/)
sudo ./setup-web.sh
```

#### Processus d'Installation (setup-web.sh)

L'installation automatique effectue les opérations suivantes :

1. **Détection Automatique de la Distribution**
   ```bash
   # Debian/Ubuntu
   if command -v apt-get >/dev/null 2>&1; then
       apt-get update
       apt-get install -y apache2 php libapache2-mod-php php-cli rsync openssh-client sshfs fuse mailutils timeout
       systemctl enable apache2 && systemctl start apache2
   
   # RHEL/CentOS 7
   elif command -v yum >/dev/null 2>&1; then
       yum install -y httpd php php-cli rsync openssh-clients fuse-sshfs fuse mailx timeout
       systemctl enable httpd && systemctl start httpd
   
   # RHEL/CentOS 8+/Fedora
   elif command -v dnf >/dev/null 2>&1; then
       dnf install -y httpd php php-cli rsync openssh-clients fuse-sshfs fuse mailx timeout
       systemctl enable httpd && systemctl start httpd
   fi
   ```

2. **Configuration PHP Automatique**
   ```bash
   # Détection automatique du php.ini
   PHP_INI=$(php --ini | grep "Loaded Configuration File" | cut -d: -f2 | xargs)
   
   # Sauvegarde avec timestamp
   cp "$PHP_INI" "${PHP_INI}.backup-$(date +%Y%m%d)"
   
   # Modifications critiques
   sed -i 's/^disable_functions.*/disable_functions = /' "$PHP_INI"
   sed -i 's/^max_execution_time.*/max_execution_time = 300/' "$PHP_INI"
   ```

3. **Configuration Utilisateur www-data**
   ```bash
   # Permissions critiques pour interface web
   usermod -a -G fuse www-data              # Groupe fuse pour SSHFS
   usermod -s /bin/bash www-data             # Shell interactif
   echo "www-data ALL=(ALL) NOPASSWD: ALL" >> /etc/sudoers  # Sudo complet
   ```

4. **Création Structure de Répertoires**
   ```bash
   # Répertoires essentiels
   mkdir -p /var/www/.ssh /tmp/{backup_logs,backups,sshfs_mounts}
   chown -R www-data:www-data /var/www/.ssh /tmp/backup_logs /tmp/backups /tmp/sshfs_mounts
   ```

5. **Déploiement Interface Web**
   ```bash
   # Copie intelligente du projet
   PROJET_DIR="$(pwd)"
   WEB_DIR="/var/www/html/backup-manager-web"
   
   if [[ "$PROJET_DIR" != "$WEB_DIR" ]]; then
       mkdir -p "$WEB_DIR"
       cp -r . "$WEB_DIR/"
       chown -R www-data:www-data "$WEB_DIR"
       chmod +x "$WEB_DIR"/*.sh
   fi
   ```

6. **Génération Clés SSH Dédiées**
   ```bash
   # Clé RSA spécifique pour sauvegardes
   sudo -u www-data ssh-keygen -t rsa -f /var/www/.ssh/backup_key -N "" -C "backup-web@$(hostname)"
   
   # Permissions sécurisées automatiques
   chmod 600 /var/www/.ssh/backup_key      # Clé privée
   chmod 644 /var/www/.ssh/backup_key.pub   # Clé publique
   ```

### Configuration Manuelle

#### Édition du Fichier de Configuration

Le fichier `config.sh` contient tous les paramètres du système :

```bash
# Éditer la configuration principale (dans le répertoire web)
cd /var/www/html/backup-manager-web
nano config.sh
```

#### Variables de Configuration Principales (config.sh)

**Variables Système Critiques :**
```bash
# Email pour rapports automatiques (obligatoire)
EMAIL_NOTIFICATION="votre_email@example.com"

# Répertoire racine de toutes les sauvegardes
DEST_BASE_SAUVEGARDES="/mnt/backup_nas"

# Répertoire des logs quotidiens (format: sauvegarde_YYYYMMDD.log)
LOG_DIR="/var/log/sauvegardes"

# Espace disque minimum en Go (vérifié par verifier_espace_disque())
ESPACE_DISQUE_MIN_GO=5

# Fichier de verrouillage PID
PID_FILE="/var/run/$DEFAULT_NOM_SCRIPT.pid"
```

**Options Rsync Avancées :**
```bash
# Options par défaut (utilisées par effectuer_sauvegarde())
DEFAULT_RSYNC_OPTIONS="-avh --partial --progress --info=progress2,misc0,name0"

# Contrôle --delete (0=sécurisé, 1=supprime fichiers supprimés)
RSYNC_DELETE=0

# Options incrémentales (hardlinks vers sauvegarde précédente)
OPTIONS_RSYNC_INCREMENTALE="--link-dest=../current"

# Timeout rsync (0=désactivé, utilisé avec commande timeout)
DELAI_OPERATION_RSYNC_SECONDES=0
```

**Contrôles de Sécurité :**
```bash
# Verrouillage anti-exécutions multiples (géré par gerer_verrouillage())
ACTIVERLOCK=1

# Mode de connexion distante (0=SSHFS, 1=SSH direct)
DEFAULT_TYPE_CONNEXION_DISTANTE=0

# Sélections par défaut si aucun argument
DEFAULT_SELECTIONS_SAUVEGARDES="docs_eric docs_fanou"

# Contrôles de débogage
DEFAULT_MODE_DEBOGAGE=0
DEFAULT_JOURNAUX_DESACTIVES=0
```

**Configuration SSH Avancée :**
```bash
# Timeout connexion SSH (utilisé par verifier_connexion_ssh())
DELAI_CONNEXION_SSH_SECONDES=10

# Options communes SSH
OPTIONS_COMMUNES_SSH="-o BatchMode=yes -o ConnectTimeout=${DELAI_CONNEXION_SSH_SECONDES}"

# Vérification clés d'hôte ("no" pour automatisation)
StrictHostKeyChecking_SSH="no"

# Chemins clés SSH (optionnels)
SSH_KEY_PATH=""
SSH_AUTH_SOCK_PATH=""
```

#### Configuration des Sauvegardes Distantes

Pour chaque sauvegarde distante, configurez les paramètres SSH selon le pattern :

```bash
# Exemple pour photos_vm (remplacez PHOTOS par le nom de votre sauvegarde en majuscules)
SSH_USER_PHOTOS="utilisateur_vm"
SSH_IP_PHOTOS="192.168.1.100"
SSH_PORT_PHOTOS=22
SOURCE_DIST_PHOTOS_VM="/chemin/distant/Photos"  # Chemin ABSOLU sur le serveur distant
MONTAGE_SSHFS_PHOTOS="/tmp/sshfs_mounts/photos_vm"  # Point de montage local
DEST_MAIN_PHOTOS="$DEST_BASE_SAUVEGARDES/PhotosVM/"  # Destination principale
DEST_INCR_BASE_PHOTOS="$DEST_BASE_SAUVEGARDES/incremental-PhotosVM/"  # Base incrémentale

# Politiques de rétention (obligatoires)
JOURS_RETENTION_PHOTOS_VM_QUOTIDIEN=7   # Jours de rétention quotidienne
JOURS_RETENTION_PHOTOS_VM_HEBDO=4       # Semaines de rétention hebdomadaire
JOURS_RETENTION_PHOTOS_VM_MENSUEL=12    # Mois de rétention mensuelle
```

#### Configuration des Sauvegardes Locales

```bash
# Exemple pour docs_eric (remplacez DOCS_ERIC par le nom en majuscules)
SOURCE_LOCALE_DOCS_ERIC="/home/eric/Documents"  # Chemin source local
DEST_MAIN_DOCS_ERIC="$DEST_BASE_SAUVEGARDES/DocumentsEric/"  # Destination principale
DEST_INCR_BASE_DOCS_ERIC="$DEST_BASE_SAUVEGARDES/incremental-DocumentsEric/"  # Base incrémentale

# Politiques de rétention (obligatoires)
JOURS_RETENTION_DOCS_ERIC_QUOTIDIEN=7
JOURS_RETENTION_DOCS_ERIC_HEBDO=4
JOURS_RETENTION_DOCS_ERIC_MENSUEL=12
```

### Configuration des Clés SSH

#### Copie des Clés sur les Serveurs Distants

Après l'installation, copiez la clé publique générée sur chaque serveur distant :

```bash
# Afficher la clé publique
cat /var/www/.ssh/backup_key.pub

# Copier sur chaque serveur distant
ssh-copy-id -i /var/www/.ssh/backup_key.pub utilisateur@192.168.1.100
ssh-copy-id -i /var/www/.ssh/backup_key.pub admin@192.168.1.101
```

#### Test des Connexions

Vérifiez que les connexions SSH fonctionnent :

```bash
# Test de connexion
sudo -u www-data ssh -i /var/www/.ssh/backup_key utilisateur@192.168.1.100
```

## Utilisation en Ligne de Commande

### Script Principal

Le script `sauvegarde.sh` est l'outil principal pour exécuter les sauvegardes :

#### Syntaxe de Base

```bash
./sauvegarde.sh [OPTIONS] [SELECTION...]
```

#### Options Disponibles (sauvegarde.sh)

**Options Principales :**
- `--dry-run` : Mode simulation (variable `DRY_RUN=1`, aucune écriture disque)
- `--list` : Affiche sauvegardes avec statut (fonction `is_default_backup_enabled()`)
- `--help` ou `-h` : Aide complète avec exemples (fonction `afficher_aide()`)

**Syntaxe Complète :**
```bash
./sauvegarde.sh [--dry-run] [--list] [selection... | all]
```

#### Sélections de Sauvegarde

**Sauvegardes Par Défaut :**
- `docs_eric` : Documents Eric (locale)
- `docs_fanou` : Documents Fanou (locale) 
- `photos_vm` : Photos VM (distante SSHFS)
- `projets_serveur` : Projets serveur (distante SSHFS)
- `docs_portable` : Documents portable (distante SSHFS)

**Sélections Spéciales :**
- `all` : Toutes les sauvegardes (tableau `SAUVEGARDES_A_TRAITER`)
- **Multiples** : `docs_eric photos_vm` (séparées par espaces)
- **Personnalisées** : Noms définis dans `sauvegardes_custom.conf`
- **Aucune** : Utilise `DEFAULT_SELECTIONS_SAUVEGARDES="docs_eric docs_fanou"`

**Gestion des Sauvegardes Désactivées :**
- Vérification via `is_default_backup_enabled()` avant exécution
- Sauvegardes désactivées ignorées automatiquement
- Message log : "Sauvegarde 'nom' désactivée dans default_backups.conf, ignorée"

### Exemples d'Utilisation Réels

#### Lister les Sauvegardes (--list)

```bash
./sauvegarde.sh --list
```

Sortie réelle (basée sur le code) :
```
Sélections de sauvegarde disponibles :

Sauvegardes par défaut :
  docs_eric (activée)
  docs_fanou (activée)
  photos_vm (activée)
  projets_serveur (activée)
  docs_portable (activée)

Sauvegardes personnalisées :
  test_sauvegarde (personnalisée)
```

#### Mode Simulation (--dry-run)

```bash
# Test simulation d'une sauvegarde
./sauvegarde.sh --dry-run docs_eric
# Log: "Mode 'dry-run' activé : aucune modification ne sera effectuée."
# Log: "DRY-RUN: Simulation de rsync. La commande ne sera pas exécutée."

# Test de toutes les sauvegardes
./sauvegarde.sh --dry-run all
# Exécute effectuer_sauvegarde() avec DRY_RUN=1
```

#### Exécutions Réelles

```bash
# Sauvegarde unique (case "docs_eric")
./sauvegarde.sh docs_eric
# Utilise SOURCE_LOCALE_DOCS_ERIC, DEST_MAIN_DOCS_ERIC, DEST_INCR_BASE_DOCS_ERIC

# Sauvegardes multiples (tableau SAUVEGARDES_A_TRAITER)
./sauvegarde.sh docs_eric photos_vm
# Traite séquentiellement chaque sélection

# Toutes les sauvegardes
./sauvegarde.sh all
# Remplit SAUVEGARDES_A_TRAITER=("docs_eric" "docs_fanou" "photos_vm" "projets_serveur" "docs_portable")

# Aucun argument (utilise DEFAULT_SELECTIONS_SAUVEGARDES)
./sauvegarde.sh
# Équivaut à : ./sauvegarde.sh docs_eric docs_fanou
```

#### Aide Complète (--help)

```bash
./sauvegarde.sh --help
```

Sortie de la fonction `afficher_aide()` :
```
Utilisation : ./sauvegarde.sh [--dry-run] [--list] [selection... | all]

Arguments :
  selection  : Nom d'une sauvegarde à exécuter (ex: docs_eric photos_vm).
  all        : Exécute toutes les sauvegardes définies dans config.sh.

Options :
  --dry-run  : Simule le processus de sauvegarde sans effectuer de modifications réelles.
  --list     : Affiche la liste des sélections de sauvegarde disponibles et quitte.
  --help, -h : Affiche cette aide.

Exemples :
  ./sauvegarde.sh all
  ./sauvegarde.sh docs_eric photos_vm
  ./sauvegarde.sh --dry-run docs_eric
  ./sauvegarde.sh --list
```
```

#### Exécutions Réelles

```bash
# Sauvegarde unique (case "docs_eric")
./sauvegarde.sh docs_eric
# Utilise SOURCE_LOCALE_DOCS_ERIC, DEST_MAIN_DOCS_ERIC, DEST_INCR_BASE_DOCS_ERIC

# Sauvegardes multiples (tableau SAUVEGARDES_A_TRAITER)
./sauvegarde.sh docs_eric photos_vm
# Traite séquentiellement chaque sélection

# Toutes les sauvegardes
./sauvegarde.sh all
# Remplit SAUVEGARDES_A_TRAITER=("docs_eric" "docs_fanou" "photos_vm" "projets_serveur" "docs_portable")

# Aucun argument (utilise DEFAULT_SELECTIONS_SAUVEGARDES)
./sauvegarde.sh
# Équivaut à : ./sauvegarde.sh docs_eric docs_fanou
```

#### Aide Complète (--help)

```bash
./sauvegarde.sh --help
```

Sortie de la fonction `afficher_aide()` :
```
Utilisation : ./sauvegarde.sh [--dry-run] [--list] [selection... | all]

Arguments :
  selection  : Nom d'une sauvegarde à exécuter (ex: docs_eric photos_vm).
  all        : Exécute toutes les sauvegardes définies dans config.sh.

Options :
  --dry-run  : Simule le processus de sauvegarde sans effectuer de modifications réelles.
  --list     : Affiche la liste des sélections de sauvegarde disponibles et quitte.
  --help, -h : Affiche cette aide.

Exemples :
  ./sauvegarde.sh all
  ./sauvegarde.sh docs_eric photos_vm
  ./sauvegarde.sh --dry-run docs_eric
  ./sauvegarde.sh --list
```

### Gestion des Sauvegardes par Défaut

#### Activation/Désactivation

Modifiez le fichier `default_backups.conf` :

```bash
# Éditer la configuration (dans le répertoire web)
cd /var/www/html/backup-manager-web
nano default_backups.conf
```

Contenu du fichier :
```bash
# 1 = Activée, 0 = Désactivée
docs_eric=1
docs_fanou=1
photos_vm=0    # Désactivée
projets_serveur=1
docs_portable=1
```

## Interface Web

### Accès au Dashboard

L'interface web est accessible via navigateur :

```
http://votre-serveur/backup-manager-web/web/
```

### Dashboard Principal

#### Vue d'Ensemble

Le dashboard affiche :
- **Statut Système** : État actuel des sauvegardes
- **Progression** : Avancement des opérations en cours
- **Métriques** : Nombre de sauvegardes configurées
- **Activité Temps Réel** : Logs en direct

#### Métriques Principales

1. **Statut Système** :
   - **Inactif** : Aucune sauvegarde en cours (fichier `/tmp/backup_running.flag` absent)
   - **En cours** : Sauvegarde active (fichier présent)
   - **Erreur** : Problème détecté dans les logs

2. **Progression** :
   - Pourcentage d'avancement (0-100%) lu depuis `/tmp/backup_progress.txt`
   - Mis à jour automatiquement pendant l'exécution
   - Barre de progression visuelle

3. **Durée** :
   - Temps écoulé depuis le début (lu depuis `/tmp/backup_start_time.txt`)
   - Format : minutes et secondes
   - Mise à jour temps réel

4. **Sauvegarde Courante** :
   - Nom de la sauvegarde en cours d'exécution
   - Lu depuis `/tmp/current_backup.txt`
   - "Aucune sauvegarde" si inactif

5. **Nombre de Sauvegardes** :
   - Total des sauvegardes configurées (par défaut + personnalisées)
   - Comptage automatique depuis les fichiers de configuration

#### Actions Disponibles

- **Exécuter Toutes** : Lance toutes les sauvegardes actives
- **Exécuter** : Lance une sauvegarde spécifique
- **Test** : Mode simulation pour une sauvegarde
- **Activer/Désactiver** : Gestion des sauvegardes par défaut

### Gestion des Sauvegardes

#### Accès à la Gestion

Cliquez sur "Sauvegardes" dans le menu latéral ou accédez à :
```
http://votre-serveur/backup-manager-web/web/manage.php
```

#### Création d'une Sauvegarde Personnalisée

1. **Informations de Base**
   - **Nom** : Identifiant unique (regex : `[a-zA-Z0-9_]{3,50}`)
     - Longueur : 3 à 50 caractères
     - Caractères autorisés : lettres, chiffres, underscore uniquement
     - Vérification d'unicité automatique
   - **Type** : Locale ou Distante (SSH/SSHFS)
     - **Locale** : Sauvegarde de répertoires sur le même serveur
     - **Distante** : Sauvegarde via SSH/SSHFS depuis serveur distant
   - **Chemin Source** : Répertoire à sauvegarder (chemin absolu)
   - **Nom Dossier Destination** : Nom du dossier de destination (sans espaces)

2. **Paramètres SSH** (pour sauvegardes distantes uniquement)
   - **Utilisateur SSH** : Nom d'utilisateur sur le serveur distant
   - **IP/Hostname** : Adresse IP ou nom d'hôte du serveur distant
   - **Port SSH** : Port de connexion SSH (défaut : 22, plage : 1-65535)

3. **Politiques de Rétention** (obligatoires)
   - **Quotidien** : Nombre de jours à conserver (défaut : 7)
   - **Hebdomadaire** : Nombre de semaines à conserver (défaut : 4)
   - **Mensuel** : Nombre de mois à conserver (défaut : 12)
   - **Valeur 0** : Désactive ce type de rétention

#### Génération Automatique des Variables

Le système génère automatiquement les variables suivantes dans `sauvegardes_custom.conf` :

```bash
# SAUVEGARDE: nom_sauvegarde
# Variables générées automatiquement selon le type :

# Pour type "locale" :
SOURCE_LOCALE_NOM_SAUVEGARDE="/chemin/source"
DEST_MAIN_NOM_SAUVEGARDE="$DEST_BASE_SAUVEGARDES/DossierDestination/"
DEST_INCR_BASE_NOM_SAUVEGARDE="$DEST_BASE_SAUVEGARDES/incremental-DossierDestination/"

# Pour type "distante" :
SSH_USER_NOM_SAUVEGARDE="utilisateur"
SSH_IP_NOM_SAUVEGARDE="192.168.1.100"
SSH_PORT_NOM_SAUVEGARDE=22
SOURCE_DIST_NOM_SAUVEGARDE="/chemin/source"
MONTAGE_SSHFS_NOM_SAUVEGARDE="/tmp/sshfs_mounts/nom_sauvegarde"
DEST_MAIN_NOM_SAUVEGARDE="$DEST_BASE_SAUVEGARDES/DossierDestination/"
DEST_INCR_BASE_NOM_SAUVEGARDE="$DEST_BASE_SAUVEGARDES/incremental-DossierDestination/"

# Variables de rétention (toujours générées) :
JOURS_RETENTION_NOM_SAUVEGARDE_QUOTIDIEN=7
JOURS_RETENTION_NOM_SAUVEGARDE_HEBDO=4
JOURS_RETENTION_NOM_SAUVEGARDE_MENSUEL=12
```

#### Exemple de Création

```
Nom : backup_photos_perso
Type : Distante (SSH/SSHFS)
Chemin Source : /home/user/Photos
Nom Dossier Destination : PhotosPersonnelles

Paramètres SSH :
- Utilisateur : user
- IP : 192.168.1.50
- Port : 22

Rétention :
- Quotidien : 7 jours
- Hebdomadaire : 4 semaines
- Mensuel : 12 mois
```

#### Gestion des Sauvegardes Existantes

Pour chaque sauvegarde personnalisée :
- **Activer/Désactiver** : Contrôle de l'état
- **Supprimer** : Suppression définitive (avec confirmation)

### Consultation des Logs

#### Accès aux Logs

```
http://votre-serveur/backup-manager-web/web/logs.php
```

#### Fonctionnalités

1. **Liste des Fichiers de Log**
   - Affichage par date (plus récents en premier)
   - Taille de chaque fichier
   - Date de dernière modification

2. **Visualisation**
   - Contenu complet du fichier sélectionné
   - Coloration syntaxique des niveaux de log
   - Recherche dans le contenu

3. **Commandes Utiles**
   - Suivi temps réel : `tail -f`
   - Recherche d'erreurs : `grep "ERREUR"`
   - Comptage des succès : `grep -c "réussie"`

### Terminal Web Interactif

#### Accès au Terminal

```
http://votre-serveur/backup-manager-web/web/terminal.php
```

#### Fonctionnalités

1. **Session Persistante**
   - Maintien du répertoire de travail via `$_SESSION['terminal_cwd']`
   - Historique des commandes (50 commandes max)
   - Variables d'environnement conservées entre les requêtes
   - Répertoire initial : répertoire du projet backup-manager-web

2. **Commandes Intégrées**
   - `clear` : Effacer l'écran du terminal
   - `help` : Afficher l'aide complète avec exemples
   - `cd [répertoire]` : Navigation avec mise à jour de session
   - Toutes les commandes système Linux disponibles

3. **Streaming Temps Réel**
   - Utilisation de Server-Sent Events pour affichage en direct
   - Exécution via `popen()` avec lecture ligne par ligne
   - Gestion des processus longs avec affichage progressif

4. **Raccourcis Clavier**
   - **Flèches Haut/Bas** : Navigation dans l'historique (50 commandes)
   - **Tab** : Auto-complétion des commandes courantes
   - **Ctrl+C** : Interruption de commande (affichage ^C)
   - **Entrée** : Exécution de la commande

5. **Sécurité et Limitations**
   - Exécution dans le contexte utilisateur www-data
   - Permissions sudo configurées par setup-web.sh
   - Répertoire de travail limité au projet
   - Pas de restriction sur les commandes (terminal complet)

#### Commandes Suggérées

Le terminal propose des commandes prêtes à l'emploi :

```bash
# Gestion des sauvegardes
./sauvegarde.sh --list
./sauvegarde.sh --help
./sauvegarde.sh --dry-run all

# Surveillance système
htop
systemctl status apache2
ss -tulpn

# Édition de configuration
vim config.sh
nano config.sh

# Gestion des tâches programmées
crontab -l
```

## Programmation des Sauvegardes

### Configuration Cron

#### Installation de la Tâche Cron

```bash
# Éditer le crontab
crontab -e

# Ajouter une ligne pour exécution quotidienne à 2h00
0 2 * * * /var/www/html/backup-manager-web/sauvegarde.sh all >> /var/log/sauvegardes/cron.log 2>&1
```

#### Exemples de Programmation

```bash
# Tous les jours à 2h00
0 2 * * * /var/www/html/backup-manager-web/sauvegarde.sh all

# Tous les dimanche à 3h00 (sauvegarde complète)
0 3 * * 0 /var/www/html/backup-manager-web/sauvegarde.sh all

# Toutes les 6 heures (sauvegardes critiques)
0 */6 * * * /var/www/html/backup-manager-web/sauvegarde.sh docs_eric docs_fanou

# Sauvegarde différentielle par type
0 1 * * * /var/www/html/backup-manager-web/sauvegarde.sh docs_eric docs_fanou
0 2 * * * /var/www/html/backup-manager-web/sauvegarde.sh photos_vm
0 3 * * * /var/www/html/backup-manager-web/sauvegarde.sh projets_serveur docs_portable
```

### Surveillance des Exécutions

#### Vérification des Logs Cron

```bash
# Logs système cron (selon distribution)
tail -f /var/log/cron          # RHEL/CentOS
tail -f /var/log/cron.log      # Debian/Ubuntu
journalctl -u cron -f          # Systemd

# Logs spécifiques du script (format quotidien)
tail -f /var/log/sauvegardes/sauvegarde_$(date +%Y%m%d).log

# Logs de secours (si LOG_DIR inaccessible)
tail -f /tmp/backup_fallback_errors.log

# Vérification des fichiers de statut
ls -la /tmp/backup_*.txt /tmp/backup_running.flag 2>/dev/null
```

#### Surveillance Temps Réel

```bash
# Statut en cours d'exécution
watch -n 2 'echo "Statut:"; cat /tmp/backup_running.flag 2>/dev/null && echo "ACTIF" || echo "INACTIF"; echo "Progression:"; cat /tmp/backup_progress.txt 2>/dev/null; echo "Sauvegarde courante:"; cat /tmp/current_backup.txt 2>/dev/null'

# Processus rsync actifs
watch -n 5 'ps aux | grep rsync | grep -v grep'

# Montages SSHFS actifs
watch -n 10 'mount | grep sshfs'
```

#### Notifications Email

Le système envoie automatiquement des rapports par email si configuré :

```bash
# Configuration dans config.sh
EMAIL_NOTIFICATION="admin@example.com"
```

Types de notifications automatiques :

1. **Succès Complet** :
   - Sujet : `[Sauvegarde RÉUSSIE] - Toutes les sauvegardes ont été effectuées`
   - Contenu : Résumé avec nombre de sauvegardes réussies
   - Condition : `sauvegardes_echouees == 0 && nombre_sauvegardes > 0`

2. **Échec Partiel ou Total** :
   - Sujet : `[Sauvegarde ÉCHOUÉE] - Des erreurs se sont produites`
   - Contenu : Détail des erreurs avec référence aux logs
   - Condition : `sauvegardes_echouees > 0`

3. **Exécution Incomplète** :
   - Sujet : `[Sauvegarde INCOMPLÈTE] - Aucune sauvegarde n'a été traitée`
   - Contenu : Vérification de la configuration et des arguments
   - Condition : `nombre_sauvegardes == 0`

#### Configuration Email Avancée

```bash
# Dans config.sh
EMAIL_NOTIFICATION="admin@example.com"  # Obligatoire pour notifications
CHEMIN_MAIL="/usr/bin/mailx"          # Commande mail (mailx ou mail)

# Test de configuration email
echo "Test notification" | mailx -s "Test Backup Manager" admin@example.com
```

## Gestion des Erreurs Courantes

### Codes d'Erreur du Système

Le système utilise 17 codes d'erreur spécifiques :

| Code | Description | Action Automatique |
|------|-------------|--------------------|
| 1 | Erreur d'arguments | Vérification syntaxe |
| 2 | Configuration invalide | Examen config.sh |
| 3 | Permissions logs | Vérification LOG_DIR |
| 4 | Espace disque insuffisant | Vérification DEST_BASE_SAUVEGARDES |
| 5 | Connexion SSH échouée | Test réseau/identifiants |
| 6 | Point montage occupé | Démontage manuel requis |
| 7 | Échec montage SSHFS | Vérification permissions SSH |
| 8 | Échec démontage SSHFS | Kill processus utilisant point |
| 9 | Erreur rsync | Analyse logs rsync |
| 10 | Script déjà en cours | Suppression fichier PID |
| 11 | Espace disque insuffisant | Nettoyage destination |
| 12 | Échec création répertoire | Vérification permissions parent |
| 13 | Source inaccessible | Vérification existence/permissions |
| 14 | Configuration sauvegarde | Vérification définitions variables |
| 15 | Échec envoi email | Vérification MTA |
| 16 | Échec nettoyage | Vérification permissions |
| 17 | Chemin distant inaccessible | Vérification SSH et chemin |
| 127 | Commande non trouvée | Installation dépendances |

### Problèmes de Connexion SSH

#### Symptômes
```
[ERREUR] Impossible d'établir une connexion SSH à user@192.168.1.100:22
```

#### Solutions
1. Vérifier la connectivité réseau
2. Contrôler les paramètres SSH dans config.sh
3. Tester la connexion manuellement
4. Vérifier les clés SSH

### Problèmes d'Espace Disque

#### Symptômes
```
[ERREUR] Espace disque insuffisant sur la destination
```

#### Solutions
1. Libérer de l'espace sur la destination
2. Ajuster ESPACE_DISQUE_MIN_GO dans config.sh
3. Nettoyer les anciennes sauvegardes
4. Vérifier les politiques de rétention

### Problèmes de Permissions

#### Symptômes
```
[ERREUR] Le répertoire de log n'est pas accessible en écriture
```

#### Solutions
1. Vérifier les permissions des répertoires
2. Corriger les propriétaires avec chown
3. Ajuster les permissions avec chmod
4. Vérifier la configuration du serveur web

### Problèmes de Montage SSHFS

#### Symptômes
```
[ERREUR] Échec du montage SSHFS après 3 tentatives
```

#### Solutions
1. Vérifier que fuse est installé et configuré
2. Contrôler les permissions du groupe fuse
3. Tester le montage manuel
4. Vérifier les paramètres de connexion SSH

## Bonnes Pratiques

### Sécurité

1. **Accès à l'Interface Web**
   - Restreindre l'accès par IP
   - Utiliser HTTPS obligatoire
   - Surveiller les logs d'accès

2. **Gestion des Clés SSH**
   - Utiliser des clés dédiées pour les sauvegardes
   - Renouveler régulièrement les clés
   - Limiter les permissions sur les serveurs distants

### Performance

1. **Planification des Sauvegardes**
   - Éviter les heures de forte charge
   - Étaler les sauvegardes dans le temps
   - Utiliser le mode dry-run pour les tests

2. **Gestion de l'Espace**
   - Surveiller régulièrement l'espace disponible
   - Ajuster les politiques de rétention
   - Nettoyer les logs anciens

### Maintenance

1. **Surveillance Régulière**
   - Consulter les logs quotidiennement
   - Vérifier les rapports email
   - Tester périodiquement les restaurations

2. **Mises à Jour**
   - Maintenir les dépendances système à jour
   - Sauvegarder la configuration avant modifications
   - Tester les changements en mode dry-run

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
- **sauvegarde.sh** : 8 fonctions principales → [Référence Complète des Fonctions](#scripts-bash)
- **fonctions_erreur.sh** : 7 fonctions de gestion → [Référence Complète des Fonctions](#scripts-bash)

### Interface PHP (16 fonctions)
- **index.php** : 4 fonctions dashboard → [Référence Complète des Fonctions](#interface-web-php)
- **manage.php** : 4 fonctions gestion → [Référence Complète des Fonctions](#interface-web-php)
- **logs.php** : 4 fonctions logs → [Référence Complète des Fonctions](#interface-web-php)
- **terminal.php** : 4 fonctions terminal → [Référence Complète des Fonctions](#interface-web-php)

### JavaScript (10 fonctions)
- **app.js** : 10 fonctions interface client → [Référence Complète des Fonctions](#javascript---appjs)

**Total : 41 fonctions documentées avec code source complet**
# Diagnostics et Résolution de Bugs

## Méthodologie de Diagnostic

### Approche Systématique

1. **Identification du Problème**
   - Collecte des symptômes
   - Reproduction du problème
   - Analyse des logs

2. **Localisation de la Cause**
   - Vérification de la configuration
   - Test des dépendances
   - Analyse des permissions

3. **Résolution et Validation**
   - Application de la solution
   - Test de non-régression
   - Documentation de la résolution

### Outils de Diagnostic

#### Commandes de Base
```bash
# Vérification de l'état du système
./sauvegarde.sh --list                    # Liste toutes les sauvegardes avec statut
./sauvegarde.sh --dry-run all             # Test simulation de toutes les sauvegardes
./sauvegarde.sh --help                    # Aide complète avec exemples

# Analyse des logs (format quotidien)
tail -f /var/log/sauvegardes/sauvegarde_$(date +%Y%m%d).log
grep "\[ERREUR\]" /var/log/sauvegardes/sauvegarde_*.log
grep "\[ATTENTION\]" /var/log/sauvegardes/sauvegarde_*.log
grep "\[INFO\]" /var/log/sauvegardes/sauvegarde_*.log

# Vérification des processus
ps aux | grep sauvegarde | grep -v grep
ps aux | grep rsync | grep -v grep
ps aux | grep sshfs | grep -v grep

# Vérification des fichiers de statut
ls -la /tmp/backup_*.txt /tmp/backup_running.flag 2>/dev/null
cat /tmp/current_backup.txt 2>/dev/null
cat /tmp/backup_progress.txt 2>/dev/null
```

#### Commandes de Diagnostic Avancé
```bash
# Vérification de la configuration
bash -n config.sh                         # Vérification syntaxe
source config.sh && echo "Config OK"       # Test chargement

# Vérification des dépendances avec chemins configurables
for cmd in "$CHEMIN_RSYNC" "$CHEMIN_SSH" "$CHEMIN_SSHFS"; do
    [[ -x "$cmd" ]] && echo "✓ $cmd OK" || echo "✗ $cmd MANQUANT"
done

# Vérification des permissions critiques
ls -ld "$LOG_DIR" "$DEST_BASE_SAUVEGARDES" 2>/dev/null
groups www-data | grep -q fuse && echo "✓ www-data dans groupe fuse" || echo "✗ www-data PAS dans groupe fuse"

# Vérification des montages SSHFS actifs
mount | grep sshfs
mountpoint /tmp/sshfs_mounts/* 2>/dev/null
```

## Bugs Connus et Solutions

### Codes d'Erreur Système (17 codes spécifiques + 127)

Le système utilise des codes d'erreur précis avec actions automatiques suggérées :

| Code | Description | Fonction `diagnostiquer_et_logger_erreur()` |
|------|-------------|---------------------------------------------|
| 1 | Erreur arguments | Vérifiez les arguments passés au script et la syntaxe |
| 2 | Configuration invalide | Examinez le fichier config.sh. Une variable est manquante, vide ou incorrecte |
| 3 | Permissions logs | Assurez-vous que LOG_DIR existe et est accessible en écriture |
| 4 | Espace disque insuffisant | Libérez de l'espace sur DEST_BASE_SAUVEGARDES ou ajustez ESPACE_DISQUE_MIN_GO |
| 5 | Connexion SSH échouée | Vérifiez la connectivité réseau, les identifiants SSH ou l'état du service SSH |
| 6 | Point montage occupé | Le point de montage SSHFS est déjà monté et/ou occupé. Démontez-le manuellement |
| 7 | Échec montage SSHFS | Vérifiez les permissions, la configuration SSH, ou le chemin distant |
| 8 | Échec démontage SSHFS | Le point de montage est peut-être toujours occupé |
| 9 | Erreur rsync | Une erreur rsync s'est produite. Examinez les logs pour les détails |
| 10 | Script déjà en cours | Supprimez manuellement le fichier de verrouillage PID_FILE |
| 11 | Espace disque insuffisant | Espace disque insuffisant sur la destination de sauvegarde |
| 12 | Échec création répertoire | Vérifiez les permissions du répertoire parent |
| 13 | Source inaccessible | La source spécifiée n'existe pas, est vide, ou n'est pas accessible |
| 14 | Configuration sauvegarde | Erreur interne de configuration pour la sélection de sauvegarde |
| 15 | Échec envoi email | Vérifiez la configuration de votre serveur de messagerie (MTA) |
| 16 | Échec nettoyage | La fonction de nettoyage des anciennes sauvegardes a échoué |
| 17 | Chemin distant inaccessible | Échec de la vérification du chemin distant via SSH |
| 127 | Commande non trouvée | Une commande externe requise n'a pas été trouvée dans le PATH |

### Problèmes de Configuration

#### Bug #001 : Variables Non Définies (Code d'erreur 2)
**Symptômes :**
```bash
./sauvegarde.sh: line 45: EMAIL_NOTIFICATION: unbound variable
[ERREUR] Code d'erreur : 2. Configuration invalide
[ERREUR] Action suggérée : Examinez le fichier config.sh. Une variable est manquante, vide ou a une valeur incorrecte.
```

**Cause :**
- Fichier config.sh non sourcé correctement (ordre de chargement)
- Variables obligatoires manquantes dans config.sh
- Options shell `set -o nounset` détecte les variables non définies

**Solution :**
```bash
# Vérifier l'ordre de chargement dans sauvegarde.sh
grep -n "source.*config.sh" sauvegarde.sh
# Doit être avant fonctions_erreur.sh

# Vérifier la syntaxe du fichier
bash -n config.sh

# Vérifier les variables obligatoires
for var in EMAIL_NOTIFICATION DEST_BASE_SAUVEGARDES LOG_DIR ESPACE_DISQUE_MIN_GO; do
    grep -q "^$var=" config.sh && echo "✓ $var définie" || echo "✗ $var MANQUANTE"
done

# Corriger les variables manquantes (valeurs par défaut)
echo 'EMAIL_NOTIFICATION=""' >> config.sh
echo 'DEST_BASE_SAUVEGARDES="/mnt/backup_nas"' >> config.sh
echo 'LOG_DIR="/var/log/sauvegardes"' >> config.sh
echo 'ESPACE_DISQUE_MIN_GO=5' >> config.sh
```

#### Bug #002 : Chemins Incorrects (Code d'erreur 13)
**Symptômes :**
```bash
[ERREUR] Le chemin '/mnt/backup_nas' pour 'DEST_BASE_SAUVEGARDES' n'existe pas ou n'est ni un répertoire ni un fichier.
[ERREUR] Code d'erreur : 13. Source invalide: /mnt/backup_nas
[ERREUR] Action suggérée : La source spécifiée pour la sauvegarde n'existe pas, est vide, ou n'est pas accessible en lecture.
```

**Cause :**
- Répertoire de destination non monté ou inexistant
- Permissions insuffisantes (fonction `valider_variable()` avec type "path")
- Chemin mal configuré dans config.sh
- Vérification automatique par `verifier_permissions_log_dir()`

**Solution :**
```bash
# Vérifier l'existence et les permissions
ls -ld "$DEST_BASE_SAUVEGARDES" 2>/dev/null || echo "Répertoire inexistant"

# Créer le répertoire avec permissions correctes
sudo mkdir -p "$DEST_BASE_SAUVEGARDES"
sudo chown www-data:www-data "$DEST_BASE_SAUVEGARDES"
sudo chmod 755 "$DEST_BASE_SAUVEGARDES"

# Créer les sous-répertoires pour les sauvegardes prédéfinies
for dir in DocumentsEric DocumentsFanou PhotosVM ProjetsServeur DocumentsPortable; do
    sudo -u www-data mkdir -p "$DEST_BASE_SAUVEGARDES/$dir" "$DEST_BASE_SAUVEGARDES/incremental-$dir"
done
```

#### Bug #003 : Permissions Logs (Code d'erreur 3)
**Symptômes :**
```bash
[ERREUR] Le répertoire de log '/var/log/sauvegardes' n'est pas accessible en écriture.
[ERREUR] Code d'erreur : 3. Permissions d'écriture manquantes pour le répertoire de log.
```

**Cause :**
- Répertoire LOG_DIR inexistant
- Permissions insuffisantes pour www-data
- Fonction `verifier_permissions_log_dir()` échoue

**Solution :**
```bash
# Créer le répertoire avec bonnes permissions
sudo mkdir -p "$LOG_DIR"
sudo chown www-data:www-data "$LOG_DIR"
sudo chmod 755 "$LOG_DIR"

# Vérifier l'écriture
sudo -u www-data touch "$LOG_DIR/test.log" && rm "$LOG_DIR/test.log" && echo "OK" || echo "ECHEC"

# Utiliser répertoire de fallback
echo 'LOG_DIR="/tmp/backup_logs"' >> config.sh
mkdir -p /tmp/backup_logs
chown www-data:www-data /tmp/backup_logs
```

#### Bug #004 : Espace Disque Plein (Code d'erreur 4)
**Symptômes :**
```bash
[ERREUR] Espace disque insuffisant sur la destination '/mnt/backup_nas'. Libre: 2G, Requis: 5G.
[ERREUR] Code d'erreur : 4. Espace disque insuffisant sur la destination.
```

**Cause :**
- Seuil ESPACE_DISQUE_MIN_GO trop élevé
- Anciennes sauvegardes non nettoyées
- Politiques de rétention trop permissives

**Solution :**
```bash
# Vérifier l'espace disque
df -h "$DEST_BASE_SAUVEGARDES"

# Nettoyer les anciennes sauvegardes manuellement
find "$DEST_BASE_SAUVEGARDES" -name "daily-*" -mtime +7 -exec rm -rf {} +

# Ajuster le seuil minimum
echo 'ESPACE_DISQUE_MIN_GO=2' >> config.sh

# Configurer des politiques de rétention plus strictes
echo 'JOURS_RETENTION_DOCS_ERIC_QUOTIDIEN=3' >> config.sh
```

### Problèmes de Connexion SSH

#### Bug #005 : Authentification SSH Échouée (Code d'erreur 5)
**Symptômes :**
```bash
[ERREUR] Impossible d'établir une connexion SSH à user@192.168.1.100:22
Permission denied (publickey,password)
[ERREUR] Code d'erreur : 5. Problème de connexion SSH.
```

**Cause :**
- Clé SSH non configurée ou non autorisée sur le serveur distant
- Paramètres SSH incorrects dans config.sh
- Service SSH non démarré sur le serveur distant

**Solution :**
```bash
# Vérifier l'existence de la clé
ls -la /var/www/.ssh/backup_key*

# Tester la connexion manuellement
sudo -u www-data ssh -i /var/www/.ssh/backup_key user@192.168.1.100

# Copier la clé sur le serveur distant
ssh-copy-id -i /var/www/.ssh/backup_key.pub user@192.168.1.100

# Vérifier les permissions de la clé
chmod 600 /var/www/.ssh/backup_key
chmod 644 /var/www/.ssh/backup_key.pub
chown www-data:www-data /var/www/.ssh/backup_key*
```

#### Bug #006 : Montage SSHFS Échoué (Code d'erreur 7)
**Symptômes :**
```bash
[ERREUR] Échec du montage SSHFS de user@192.168.1.100:/path après 3 tentatives.
[ERREUR] Code d'erreur : 7. Échec du montage SSHFS.
```

**Cause :**
- Utilisateur www-data pas dans le groupe fuse
- Point de montage inexistant ou permissions incorrectes
- Options SSHFS incompatibles

**Solution :**
```bash
# Vérifier le groupe fuse
groups www-data | grep fuse

# Ajouter www-data au groupe fuse
sudo usermod -a -G fuse www-data

# Créer les points de montage
sudo mkdir -p /tmp/sshfs_mounts/{photos_vm,projets_serveur,docs_portable}
sudo chown www-data:www-data /tmp/sshfs_mounts/*

# Tester le montage manuellement
sudo -u www-data sshfs -o "IdentityFile=/var/www/.ssh/backup_key,StrictHostKeyChecking=no,port=22" \
    user@192.168.1.100:/path /tmp/sshfs_mounts/test
```

#### Bug #007 : Démontage SSHFS Échoué (Code d'erreur 8)
**Symptômes :**
```bash
[ERREUR] Échec du démontage de /tmp/sshfs_mounts/photos_vm après 3 tentatives.
[ERREUR] Code d'erreur : 8. Échec du démontage SSHFS.
```

**Cause :**
- Processus utilisant encore le point de montage
- Fonction `demonter_sshfs()` avec retry échoue
- Commandes lsof/kill non disponibles

**Solution :**
```bash
# Identifier les processus utilisant le montage
lsof "/tmp/sshfs_mounts/photos_vm" 2>/dev/null

# Forcer le démontage
sudo umount -f "/tmp/sshfs_mounts/photos_vm" 2>/dev/null
sudo fusermount -uz "/tmp/sshfs_mounts/photos_vm" 2>/dev/null

# Tuer les processus récalcitrants
fuser -km "/tmp/sshfs_mounts/photos_vm" 2>/dev/null
```

#### Bug #008 : Chemin Distant Inaccessible (Code d'erreur 17)
**Symptômes :**
```bash
[ERREUR] Le chemin distant '/chemin/sur/vm/Photos' n'existe pas ou n'est pas accessible sur 192.168.1.100.
[ERREUR] Code d'erreur : 17. Chemin distant inaccessible via SSH.
```

**Cause :**
- Chemin SOURCE_DIST_* incorrect dans config.sh
- Permissions insuffisantes sur serveur distant
- Fonction `verifier_chemin_distant_ssh()` échoue

**Solution :**
```bash
# Tester manuellement le chemin distant
ssh user@192.168.1.100 "ls -ld /chemin/sur/vm/Photos"

# Corriger le chemin dans config.sh
echo 'SOURCE_DIST_PHOTOS_VM="/home/user/Photos"' >> config.sh

# Vérifier les permissions sur le serveur distant
ssh user@192.168.1.100 "test -r /chemin/sur/vm/Photos && echo OK || echo INACCESSIBLE"
```

### Problèmes de Sauvegarde

#### Bug #009 : Erreur Rsync (Code d'erreur 9)
**Symptômes :**
```bash
[ERREUR] La sauvegarde de '/home/eric/Documents' a échoué avec le code de sortie rsync: 23.
[ERREUR] Code d'erreur : 9. Erreur rsync lors de la sauvegarde.
```

**Cause :**
- Fichiers en cours d'utilisation (code rsync 23)
- Permissions insuffisantes sur source ou destination
- Interruption réseau pour sauvegardes distantes

**Solution :**
```bash
# Analyser le code de sortie rsync
case $rsync_exit_code in
    23) echo "Erreur partielle - certains fichiers n'ont pas pu être transférés" ;;
    24) echo "Fichiers source ont disparu pendant le transfert" ;;
    25) echo "Limite du nombre maximum d'erreurs atteinte" ;;
esac

# Vérifier les permissions
sudo -u www-data test -r "$SOURCE_LOCALE_DOCS_ERIC" && echo "Source OK" || echo "Source inaccessible"
sudo -u www-data test -w "$DEST_MAIN_DOCS_ERIC" && echo "Dest OK" || echo "Dest inaccessible"

# Réessayer avec options moins strictes
echo 'DEFAULT_RSYNC_OPTIONS="-avh --partial --progress --ignore-errors"' >> config.sh
```

#### Bug #010 : Script Déjà en Cours (Code d'erreur 10)
**Symptômes :**
```bash
[ERREUR] Le script est déjà en cours d'exécution. PID : 12345.
[ERREUR] Code d'erreur : 10. Script déjà en cours d'exécution.
```

**Cause :**
- Fichier PID non supprimé après arrêt brutal
- Processus zombie toujours présent
- Verrouillage ACTIVERLOCK=1 activé

**Solution :**
```bash
# Vérifier si le processus existe vraiment
PID=$(cat "$PID_FILE" 2>/dev/null)
if [[ -n "$PID" ]]; then
    if kill -0 "$PID" 2>/dev/null; then
        echo "Processus $PID actif - attendre ou tuer"
        kill "$PID"
    else
        echo "Processus $PID mort - supprimer PID file"
        rm -f "$PID_FILE"
    fi
fi

# Désactiver temporairement le verrouillage
echo 'ACTIVERLOCK=0' >> config.sh
```

#### Bug #011 : Création Répertoire Échouée (Code d'erreur 12)
**Symptômes :**
```bash
[ERREUR] Impossible de créer le répertoire de destination /mnt/backup_nas/DocumentsEric.
[ERREUR] Code d'erreur : 12. Échec de création du répertoire principal.
```

**Cause :**
- Permissions insuffisantes sur répertoire parent
- Point de montage non accessible
- Espace disque plein

**Solution :**
```bash
# Vérifier le répertoire parent
ls -ld "$(dirname "$DEST_MAIN_DOCS_ERIC")"

# Créer avec sudo si nécessaire
sudo mkdir -p "$DEST_MAIN_DOCS_ERIC"
sudo chown www-data:www-data "$DEST_MAIN_DOCS_ERIC"

# Vérifier le montage
mountpoint "$DEST_BASE_SAUVEGARDES" || echo "Point de montage inactif"
```

#### Bug #012 : Configuration Sauvegarde (Code d'erreur 14)
**Symptômes :**
```bash
[ERREUR] Valeur de sélection inconnue ignorée: backup_inexistant
[ERREUR] Code d'erreur : 14. Sélection de sauvegarde inconnue ou non gérée.
```

**Cause :**
- Sauvegarde personnalisée mal configurée
- Variables SOURCE_LOCALE_* ou SOURCE_DIST_* manquantes
- Fonction `traiter_sauvegarde_personnalisee()` échoue

**Solution :**
```bash
# Vérifier les sauvegardes personnalisées
grep "# SAUVEGARDE:" sauvegardes_custom.conf

# Vérifier les variables associées
for backup in $(grep "# SAUVEGARDE:" sauvegardes_custom.conf | cut -d: -f2); do
    backup_upper=$(echo "$backup" | tr '[:lower:]' '[:upper:]')
    echo "Vérification $backup ($backup_upper):"
    grep "SOURCE_.*$backup_upper" sauvegardes_custom.conf || echo "Variables manquantes"
done
```

#### Bug #013 : Envoi Email Échoué (Code d'erreur 15)
**Symptômes :**
```bash
[ERREUR] Échec de l'envoi de l'e-mail de notification. Vérifiez la configuration du MTA.
[ERREUR] Code d'erreur : 15. Échec de l'envoi d'e-mail.
```

**Cause :**
- MTA (mailx/mail) non installé ou configuré
- Variable EMAIL_NOTIFICATION incorrecte
- Fonction `envoyer_rapport_email()` échoue

**Solution :**
```bash
# Installer MTA
sudo apt-get install mailutils  # Debian/Ubuntu
sudo yum install mailx          # RHEL/CentOS

# Tester l'envoi
echo "Test" | mail -s "Test" "$EMAIL_NOTIFICATION"

# Configurer CHEMIN_MAIL si nécessaire
echo 'CHEMIN_MAIL="/usr/bin/mail"' >> config.sh

# Désactiver les notifications si problème persistant
echo 'EMAIL_NOTIFICATION=""' >> config.sh
```

#### Bug #014 : Nettoyage Sauvegardes Échoué (Code d'erreur 16)
**Symptômes :**
```bash
[ERREUR] La fonction de nettoyage des anciennes sauvegardes a échoué.
[ERREUR] Code d'erreur : 16. Échec de la fonction de nettoyage.
```

**Cause :**
- Permissions insuffisantes sur répertoires incrémentaux
- Fonction `nettoyer_anciennes_sauvegardes()` échoue
- Liens symboliques brisés

**Solution :**
```bash
# Vérifier les permissions sur répertoires incrémentaux
ls -ld "$DEST_INCR_BASE_DOCS_ERIC"
sudo chown -R www-data:www-data "$DEST_INCR_BASE_DOCS_ERIC"

# Nettoyer manuellement les liens brisés
find "$DEST_INCR_BASE_DOCS_ERIC" -type l ! -exec test -e {} \; -delete

# Nettoyer les anciennes sauvegardes manuellement
find "$DEST_INCR_BASE_DOCS_ERIC" -name "daily-*" -mtime +7 -exec rm -rf {} +
```

#### Bug #015 : Commande Non Trouvée (Code d'erreur 127)
**Symptômes :**
```bash
[ERREUR] La commande 'rsync' (ou chemin configuré: '/usr/bin/rsync') n'a pas été trouvée dans le PATH.
[ERREUR] Code d'erreur : 127. Dépendance manquante: rsync.
```

**Cause :**
- Dépendances non installées (rsync, ssh, sshfs, etc.)
- Chemins CHEMIN_* incorrects dans config.sh
- Fonction `verifier_chemin_executables()` échoue

**Solution :**
```bash
# Installer les dépendances manquantes
sudo apt-get install rsync openssh-client sshfs fuse mailutils  # Debian/Ubuntu
sudo yum install rsync openssh-clients fuse-sshfs fuse mailx    # RHEL/CentOS

# Vérifier les chemins configurés
for cmd in CHEMIN_RSYNC CHEMIN_SSH CHEMIN_SSHFS; do
    eval "path=\$$cmd"
    [[ -x "$path" ]] && echo "✓ $cmd OK" || echo "✗ $cmd MANQUANT: $path"
done

# Corriger les chemins dans config.sh
echo 'CHEMIN_RSYNC="$(which rsync)"' >> config.sh
echo 'CHEMIN_SSH="$(which ssh)"' >> config.sh
echo 'CHEMIN_SSHFS="$(which sshfs)"' >> config.sh

# Exécuter setup-web.sh pour installation automatique
./setup-web.sh
```

### Problèmes Interface Web

#### Bug #016 : Interface Web Inaccessible
**Symptômes :**
```
HTTP 500 Internal Server Error
PHP Fatal error: Call to undefined function shell_exec()
```

**Cause :**
- Fonction shell_exec() désactivée dans php.ini
- Permissions PHP insuffisantes
- Configuration Apache/Nginx incorrecte

**Solution :**
```bash
# Vérifier php.ini
php --ini | grep "Loaded Configuration File"
grep disable_functions /etc/php/*/apache2/php.ini

# Corriger disable_functions
sudo sed -i 's/^disable_functions.*/disable_functions = /' /etc/php/*/apache2/php.ini

# Augmenter max_execution_time
sudo sed -i 's/^max_execution_time.*/max_execution_time = 300/' /etc/php/*/apache2/php.ini

# Redémarrer Apache
sudo systemctl restart apache2
```

#### Bug #017 : Terminal Web Non Fonctionnel
**Symptômes :**
- Commandes ne s'exécutent pas
- Session non persistante
- Erreurs JavaScript dans la console

**Cause :**
- Sessions PHP non configurées
- Permissions d'exécution manquantes
- Problème de streaming Server-Sent Events

**Solution :**
```bash
# Vérifier les sessions PHP
ls -la /var/lib/php/sessions/
sudo chown www-data:www-data /var/lib/php/sessions/

# Vérifier les permissions du projet
sudo chown -R www-data:www-data /var/www/html/backup-manager-web/
sudo chmod +x /var/www/html/backup-manager-web/*.sh

# Tester l'exécution des commandes
sudo -u www-data bash -c "cd /var/www/html/backup-manager-web && ./sauvegarde.sh --help"
```

#### Bug #018 : Sauvegardes Personnalisées Non Sauvées
**Symptômes :**
- Formulaire manage.php ne sauvegarde pas
- Variables générées incorrectement
- Fonction `ajouterSauvegarde()` échoue

**Cause :**
- Permissions sur sauvegardes_custom.conf
- Validation regex échoue
- Format de génération incorrect

**Solution :**
```bash
# Vérifier permissions fichier custom
ls -la sauvegardes_custom.conf
sudo chown www-data:www-data sauvegardes_custom.conf
sudo chmod 644 sauvegardes_custom.conf

# Tester la validation regex
php -r "echo preg_match('/^[a-zA-Z0-9_]{3,50}$/', 'test_backup') ? 'OK' : 'ECHEC';"
```

#### Bug #019 : Status API Non Fonctionnel
**Symptômes :**
- status.php retourne données vides
- Polling JavaScript échoue
- Métriques dashboard non mises à jour

**Cause :**
- Fichiers /tmp/backup_*.txt manquants
- Permissions sur fichiers temporaires
- Format JSON incorrect

**Solution :**
```bash
# Créer fichiers de statut par défaut
echo "0" > /tmp/backup_progress.txt
echo "" > /tmp/current_backup.txt
chown www-data:www-data /tmp/backup_*.txt

# Tester l'API
curl http://localhost/backup-manager-web/web/status.php
```

#### Bug #020 : Sauvegardes Lentes
**Symptômes :**
- Timeout des sauvegardes
- Interface web qui ne répond plus
- Processus rsync qui traînent

**Cause :**
- Timeout rsync non configuré
- Options rsync non optimisées
- Bande passante limitée

**Solution :**
```bash
# Configurer le timeout dans config.sh
echo 'DELAI_OPERATION_RSYNC_SECONDES=3600' >> config.sh

# Optimiser les options rsync
echo 'DEFAULT_RSYNC_OPTIONS="-avh --partial --progress --info=progress2 --compress"' >> config.sh

# Tester avec dry-run
./sauvegarde.sh --dry-run docs_eric
```

## Procédures de Maintenance

### Nettoyage Automatique
```bash
# Script de nettoyage des logs anciens
find /var/log/sauvegardes/ -name "sauvegarde_*.log" -mtime +30 -delete

# Nettoyage des fichiers temporaires
rm -f /tmp/backup_*.txt /tmp/backup_running.flag

# Vérification de l'intégrité des liens symboliques
find "$DEST_BASE_SAUVEGARDES" -name "current" -type l ! -exec test -e {} \; -delete
```

### Tests de Validation
```bash
# Test complet du système
./sauvegarde.sh --dry-run all

# Validation de la configuration
bash -n config.sh && echo "Configuration syntaxiquement correcte"
```

### Monitoring et Alertes
```bash
# Vérification quotidienne (à mettre dans cron)
#!/bin/bash
LOG_FILE="/var/log/sauvegardes/sauvegarde_$(date +%Y%m%d).log"
if [[ -f "$LOG_FILE" ]]; then
    ERRORS=$(grep -c "\[ERREUR\]" "$LOG_FILE")
    if [[ $ERRORS -gt 0 ]]; then
        echo "$ERRORS erreurs détectées dans $LOG_FILE" | mail -s "Erreurs Sauvegarde" admin@example.com
    fi
fi
```

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

# Index Thématique

## A

**Administration Système**
- Configuration Apache/Nginx → [Manuel Utilisateur](#installation-automatique)
- Configuration PHP (disable_functions, max_execution_time) → [Manuel Utilisateur](#installation-automatique)
- Gestion des permissions www-data → [Diagnostics et Résolution de Bugs](#bugs-connus-et-solutions)
- Installation des dépendances multi-distributions → [Introduction](#périmètre-du-projet)
- Surveillance système automatique → [Diagnostics et Résolution de Bugs](#procédures-de-maintenance)
- Utilisateur www-data (groupe fuse, shell bash, sudo) → [Glossaire](#w)
- Détection automatique de distribution (apt-get, yum, dnf) → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- Configuration automatique serveur web → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- Génération clé SSH dédiée (/var/www/.ssh/backup_key) → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)

**ACTIVERLOCK**
- Variable de verrouillage → [Glossaire](#a)
- Mécanisme de verrouillage → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- Fonction gerer_verrouillage() → [Référence Complète des Fonctions](#scripts-bash)

**API et Interfaces**
- API Status (status.php) - JSON temps réel → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- Interface web (7 fichiers PHP/HTML/JS/CSS) → [Introduction](#périmètre-du-projet)
- Terminal interactif (Server-Sent Events) → [Manuel Utilisateur](#terminal-web-interactif)
- Polling automatique (2 secondes) → [Glossaire](#p)
- API REST pour statut sauvegardes → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- Session persistante terminal web → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- Streaming temps réel via EventSource → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- Interface responsive → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)

**app.js - Logique Client**
- Variables globales JavaScript → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- Fonctions executeRealBackup(), updateStatus() → [Référence Complète des Fonctions](#javascript---appjs)
- Polling automatique du statut → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- Exécution réelle via PHP → [Référence Complète des Fonctions](#javascript---appjs)
- Gestion d'erreurs async/await → [Référence Complète des Fonctions](#javascript---appjs)
- Simulation d'exécution → [Référence Complète des Fonctions](#javascript---appjs)
- Raccourcis clavier (Ctrl+R, Ctrl+L) → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- Mise à jour dashboard temps réel → [Référence Complète des Fonctions](#javascript---appjs)

**Architecture**
- Architecture hybride modulaire → [Description Fonctionnelle](#architecture-générale-du-système)
- Diagramme d'architecture (4 couches) → [Description Fonctionnelle](#architecture-générale-du-système)
- Modules et composants → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- Flux de données principal → [Description Fonctionnelle](#interactions-et-flux-de-données)
- Communication inter-modules → [Description Fonctionnelle](#interactions-et-flux-de-données)

**Authentification**
- Clés SSH dédiées → [Manuel Utilisateur](#configuration-des-clés-ssh)
- Configuration SSH avancée → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- Problèmes d'authentification → [Diagnostics et Résolution de Bugs](#bugs-connus-et-solutions)
- StrictHostKeyChecking configurable → [Glossaire](#s)
- Fonction verifier_connexion_ssh() → [Référence Complète des Fonctions](#scripts-bash)

## B

**Backup (Sauvegarde)**
- Définition et concept → [Glossaire](#analyse-exhaustive-du-code-source)
- Types de sauvegardes (locale/distante, incrémentale) → [Introduction](#analyse-exhaustive-du-code-source)
- Sauvegardes par défaut (5 préconfigurées) → [Manuel Utilisateur](#analyse-exhaustive-du-code-source)
- Sauvegardes personnalisées (génération automatique) → [Manuel Utilisateur](#analyse-exhaustive-du-code-source)
- Fichiers de statut temporaires → [Glossaire](#analyse-exhaustive-du-code-source)

**Bash Scripts (4 fichiers)**
- config.sh (version 2.5, 75+ variables, 9 catégories) → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- fonctions_erreur.sh (version 6.6 Beta, 17 codes erreur, 400+ lignes) → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- sauvegarde.sh (version 6.5, 800+ lignes, script principal) → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- setup-web.sh (installation multi-distributions, 100+ lignes) → [Manuel Utilisateur](#analyse-exhaustive-du-code-source)
- Chemins d'exécutables configurables (CHEMIN_RSYNC, CHEMIN_SSH, etc.) → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- Hooks personnalisés (PRE/POST_SAUVEGARDE_GLOBAL) → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- Gestion avanced des logs (rotation, compression) → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- Options SSH avancées (StrictHostKeyChecking, timeout) → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)

**Bash Scripts (4 fichiers)**
- config.sh (version 2.5, 75+ variables, 9 catégories) → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- fonctions_erreur.sh (version 6.6 Beta, 17 codes erreur, gestion SSHFS) → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- sauvegarde.sh (version 6.5, 800+ lignes, fonction centrale) → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- setup-web.sh (installation multi-distributions, configuration automatique) → [Manuel Utilisateur](#analyse-exhaustive-du-code-source)

**Bugs et Erreurs**
- Bugs connus (12 bugs documentés) → [Diagnostics](#analyse-exhaustive-du-code-source)
- Codes d'erreur (17 codes spécifiques + 127) → [Diagnostics](#analyse-exhaustive-du-code-source)
- Diagnostic automatique (script diagnostic.sh) → [Diagnostics](#analyse-exhaustive-du-code-source)
- Résolution de problèmes (méthodologie systématique) → [Diagnostics](#analyse-exhaustive-du-code-source)
- Fonction diagnostiquer_et_logger_erreur() → [Glossaire](#analyse-exhaustive-du-code-source)

## C

**Configuration**
- Configuration centralisée (config.sh, 75+ variables) → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- Configuration initiale (setup-web.sh) → [Manuel Utilisateur](#analyse-exhaustive-du-code-source)
- Fichiers de configuration (2 fichiers .conf) → [Introduction](#analyse-exhaustive-du-code-source)
- Variables de configuration (9 catégories) → [Glossaire](#analyse-exhaustive-du-code-source)
- Chemins d'exécutables configurables → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- Configuration adaptative environnement web (configure_web_environment) → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- Variables EMAIL_NOTIFICATION, ESPACE_DISQUE_MIN_GO → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- Configuration RSYNC_DELETE, ACTIVERLOCK → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- Politiques de rétention par sauvegarde → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)

**config.sh - Fichier Principal**
- Variables système principales → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- Variables de comportement rsync → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- Variables de sécurité et contrôle → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- Variables de sauvegardes prédéfinies → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)

**Connexions Distantes**
- Configuration SSH (pattern par sauvegarde) → [Manuel Utilisateur](#analyse-exhaustive-du-code-source)
- Problèmes de connexion (codes erreur 5, 6, 7, 8) → [Diagnostics](#analyse-exhaustive-du-code-source)
- SSHFS (mode par défaut, DEFAULT_TYPE_CONNEXION_DISTANTE=0) → [Glossaire](#analyse-exhaustive-du-code-source)
- Test de connectivité (script test_connectivity.sh) → [Diagnostics](#analyse-exhaustive-du-code-source)
- Fonctions monter_sshfs() et demonter_sshfs() → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- Variables SSH par sauvegarde → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)

**Cron et Programmation**
- Configuration cron (installation tâche) → [Manuel Utilisateur](#analyse-exhaustive-du-code-source)
- Exemples de programmation (4 patterns) → [Manuel Utilisateur](#analyse-exhaustive-du-code-source)
- Surveillance des exécutions (logs cron + script) → [Manuel Utilisateur](#analyse-exhaustive-du-code-source)
- Notifications email automatiques → [Manuel Utilisateur](#analyse-exhaustive-du-code-source)

## D

**Dashboard (index.php)**
- Accès au dashboard (interface web principale) → [Manuel Utilisateur](#analyse-exhaustive-du-code-source)
- Métriques principales (5 métriques temps réel) → [Manuel Utilisateur](#analyse-exhaustive-du-code-source)
- Vue d'ensemble (statut, progression, durée) → [Manuel Utilisateur](#analyse-exhaustive-du-code-source)
- Variables globales PHP → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- Fonctions getSauvegardes(), executerSauvegarde() → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- Lecture configuration depuis config.sh (readConfigValue) → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- Gestion sauvegardes par défaut et personnalisées → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- Interface responsive avec sidebar navigation → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- Actions toggle pour activation/désactivation → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)

**default_backups.conf**
- Format de configuration (nom=1/0) → [Glossaire](#analyse-exhaustive-du-code-source)
- Fonction isDefaultBackupEnabled() → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- Fonction toggleDefaultBackup() → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)

**Diagnostic**
- Méthodologie (approche systématique 3 étapes) → [Diagnostics](#analyse-exhaustive-du-code-source)
- Outils de diagnostic (commandes de base + avancées) → [Diagnostics](#analyse-exhaustive-du-code-source)
- Procédures avancées (diagnostic complet) → [Diagnostics](#analyse-exhaustive-du-code-source)
- Scripts de diagnostic (diagnostic.sh, test_config.sh) → [Diagnostics](#analyse-exhaustive-du-code-source)
- Codes d'erreur avec actions suggérées → [Diagnostics](#analyse-exhaustive-du-code-source)

**Dry-run**
- Définition (mode simulation, variable DRY_RUN=1) → [Glossaire](#analyse-exhaustive-du-code-source)
- Utilisation (option --dry-run) → [Manuel Utilisateur](#analyse-exhaustive-du-code-source)
- Implémentation dans effectuer_sauvegarde() → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- Variable globale DRY_RUN → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)

## E

**Erreurs**
- Codes d'erreur (17 codes + 127, avec actions) → [Diagnostics](#analyse-exhaustive-du-code-source)
- Gestion d'erreurs (fonctions_erreur.sh) → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- Messages d'erreur (format [ERREUR] avec timestamp) → [Diagnostics](#analyse-exhaustive-du-code-source)
- Fonction diagnostiquer_et_logger_erreur() → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- Logs de secours (/tmp/backup_fallback_errors.log) → [Glossaire](#analyse-exhaustive-du-code-source)

**EMAIL_NOTIFICATION**
- Variable de notification → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- Configuration email avancée → [Manuel Utilisateur](#analyse-exhaustive-du-code-source)
- Fonction envoyer_rapport_email() → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)

**Espace Disque**
- Vérification (fonction verifier_espace_disque()) → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- Configuration minimum (ESPACE_DISQUE_MIN_GO=5) → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- Problèmes d'espace (code erreur 4, 11) → [Diagnostics](#analyse-exhaustive-du-code-source)
- Surveillance automatique (script monitor.sh) → [Diagnostics](#analyse-exhaustive-du-code-source)

**Exécution**
- Ligne de commande (syntaxe, options, sélections) → [Manuel Utilisateur](#analyse-exhaustive-du-code-source)
- Interface web (exécution via PHP shell_exec) → [Manuel Utilisateur](#analyse-exhaustive-du-code-source)
- Programmée (cron, 4 patterns d'exemple) → [Manuel Utilisateur](#analyse-exhaustive-du-code-source)
- Fonction effectuer_sauvegarde() (8 paramètres) → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- Flux d'exécution (6 étapes) → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)

## F

**Fichiers de Configuration**
- config.sh → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- default_backups.conf → [Manuel Utilisateur](#analyse-exhaustive-du-code-source)
- sauvegardes_custom.conf → [Manuel Utilisateur](#analyse-exhaustive-du-code-source)

**Fonctions**
- Fonctions bash → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- Fonctions PHP → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- Fonctions JavaScript → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- effectuer_sauvegarde() - fonction centrale (8 paramètres) → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- configure_web_environment() - adaptation www-data → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- diagnostiquer_et_logger_erreur() - 17 codes erreur → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- valider_variable() - 6 types validation → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- ajouterSauvegarde() - génération variables → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- executeRealBackup() - exécution asynchrone → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)

## G

**Gestion**
- Gestion des erreurs → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- Gestion des sauvegardes → [Manuel Utilisateur](#analyse-exhaustive-du-code-source)
- Gestion des états → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)

## H

**Hooks**
- Configuration → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- Extensibilité → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)

## I

**Installation**
- Installation automatique → [Manuel Utilisateur](#analyse-exhaustive-du-code-source)
- Installation manuelle → [Manuel Utilisateur](#analyse-exhaustive-du-code-source)
- Prérequis → [Introduction](#analyse-exhaustive-du-code-source)

**Interface Web**
- Accès → [Manuel Utilisateur](#analyse-exhaustive-du-code-source)
- Composants → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- Problèmes → [Diagnostics](#analyse-exhaustive-du-code-source)

## J

**JavaScript**
- app.js → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- Fonctionnalités temps réel → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)

**Journalisation**
- Configuration → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- Consultation → [Manuel Utilisateur](#analyse-exhaustive-du-code-source)
- Fichiers de logs → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)

## L

**Ligne de Commande**
- Syntaxe → [Manuel Utilisateur](#analyse-exhaustive-du-code-source)
- Options → [Manuel Utilisateur](#analyse-exhaustive-du-code-source)
- Exemples → [Manuel Utilisateur](#analyse-exhaustive-du-code-source)

**Logs**
- Consultation → [Manuel Utilisateur](#analyse-exhaustive-du-code-source)
- Fichiers → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- Surveillance → [Diagnostics](#analyse-exhaustive-du-code-source)

## M

**Maintenance**
- Maintenance préventive → [Diagnostics](#analyse-exhaustive-du-code-source)
- Surveillance → [Diagnostics](#analyse-exhaustive-du-code-source)
- Vérifications → [Diagnostics](#analyse-exhaustive-du-code-source)

**Montage SSHFS**
- Configuration → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- Problèmes → [Diagnostics](#analyse-exhaustive-du-code-source)

## N

**Notifications**
- Configuration → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- Types → [Manuel Utilisateur](#analyse-exhaustive-du-code-source)

## O

**Options**
- Options rsync → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- Options du script → [Manuel Utilisateur](#analyse-exhaustive-du-code-source)

## P

**Performance**
- Optimisation → [Diagnostics](#analyse-exhaustive-du-code-source)
- Surveillance → [Manuel Utilisateur](#analyse-exhaustive-du-code-source)

**Permissions**
- Configuration → [Manuel Utilisateur](#analyse-exhaustive-du-code-source)
- Problèmes → [Diagnostics](#analyse-exhaustive-du-code-source)

**PHP**
- Fichiers PHP → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- Configuration → [Manuel Utilisateur](#analyse-exhaustive-du-code-source)

**Programmation**
- Cron → [Manuel Utilisateur](#analyse-exhaustive-du-code-source)
- Exemples → [Manuel Utilisateur](#analyse-exhaustive-du-code-source)

## R

**Rsync**
- Configuration → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- Options → [Glossaire](#analyse-exhaustive-du-code-source)
- Problèmes → [Diagnostics](#analyse-exhaustive-du-code-source)

**Rétention**
- Configuration → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- Politiques → [Manuel Utilisateur](#analyse-exhaustive-du-code-source)

## S

**Sauvegardes**
- Types → [Introduction](#analyse-exhaustive-du-code-source)
- Configuration → [Manuel Utilisateur](#analyse-exhaustive-du-code-source)
- Exécution → [Manuel Utilisateur](#analyse-exhaustive-du-code-source)
- Gestion → [Manuel Utilisateur](#analyse-exhaustive-du-code-source)

**Scripts**
- Scripts bash → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- Script principal → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)

**Sécurité**
- Bonnes pratiques → [Manuel Utilisateur](#analyse-exhaustive-du-code-source)
- Configuration SSH → [Manuel Utilisateur](#analyse-exhaustive-du-code-source)
- Validation → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)

**SSH**
- Configuration → [Manuel Utilisateur](#analyse-exhaustive-du-code-source)
- Problèmes → [Diagnostics](#analyse-exhaustive-du-code-source)
- Test → [Manuel Utilisateur](#analyse-exhaustive-du-code-source)

**SSHFS**
- Configuration → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- Montage → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- Problèmes → [Diagnostics](#analyse-exhaustive-du-code-source)

## T

**Terminal**
- Terminal web → [Manuel Utilisateur](#analyse-exhaustive-du-code-source)
- Fonctionnalités → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- Problèmes → [Diagnostics](#analyse-exhaustive-du-code-source)
- Session persistante ($_SESSION['terminal_cwd']) → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- Streaming temps réel via Server-Sent Events → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- Interface terminal avancée → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- Gestion commandes cd avec session → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- Historique commandes et navigation flèches → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- Auto-complétion et raccourcis (Tab, Ctrl+C) → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)

**Tests**
- Mode dry-run → [Manuel Utilisateur](#analyse-exhaustive-du-code-source)
- Tests de validation → [Diagnostics](#analyse-exhaustive-du-code-source)
- Test de configuration → [Diagnostics](#analyse-exhaustive-du-code-source)

## U

**Utilisation**
- Ligne de commande → [Manuel Utilisateur](#analyse-exhaustive-du-code-source)
- Interface web → [Manuel Utilisateur](#analyse-exhaustive-du-code-source)

## V

**Validation**
- Fonctions de validation → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- Tests de validation → [Diagnostics](#analyse-exhaustive-du-code-source)

**Variables**
- Variables de configuration → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- Conventions → [Glossaire](#analyse-exhaustive-du-code-source)

**Verrouillage**
- Mécanisme → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- Configuration → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)

## W

**Web Interface**
- Composants → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- Utilisation → [Manuel Utilisateur](#analyse-exhaustive-du-code-source)

## Index par Fichiers

### Scripts Bash (4 fichiers)
- **config.sh** (version 2.5, 200+ lignes, 75+ variables) → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- **fonctions_erreur.sh** (version 6.6 Beta, 400+ lignes, 17 codes erreur) → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- **sauvegarde.sh** (version 6.5, 800+ lignes, script principal) → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- **setup-web.sh** (100+ lignes, installation multi-distributions) → [Manuel Utilisateur](#installation-automatique)

### Interface Web (7 fichiers)
- **index.php** (300+ lignes, dashboard principal) → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- **manage.php** (250+ lignes, gestion sauvegardes personnalisées) → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- **logs.php** (200+ lignes, visualisation logs) → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- **terminal.php** (400+ lignes, terminal interactif) → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- **status.php** (50 lignes, API JSON temps réel) → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- **app.js** (500+ lignes, logique client) → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- **style.css** (800+ lignes, design responsive) → [Introduction](#périmètre-du-projet)
- Architecture modulaire avec sidebar navigation → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- Design moderne avec animations CSS → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- Gestion d'états temps réel (running, progress, duration) → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)

### Configuration (2 fichiers)
- **default_backups.conf** (format nom=1/0, 5 sauvegardes par défaut) → [Manuel Utilisateur](#gestion-des-sauvegardes-par-défaut)
- **sauvegardes_custom.conf** (format # SAUVEGARDE: nom) → [Manuel Utilisateur](#création-dune-sauvegarde-personnalisée)

### Fichiers de Statut Temporaires (7 fichiers)
- **/tmp/backup_running.flag** (indicateur d'exécution active) → [Glossaire](#analyse-exhaustive-du-code-source)
- **/tmp/current_backup.txt** (nom sauvegarde en cours) → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- **/tmp/backup_progress.txt** (progression 0-100%) → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- **/tmp/backup_start_time.txt** (heure de début) → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- **/tmp/last_success.txt** et **/tmp/last_error.txt** (dernières exécutions) → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- **/tmp/backup_fallback_errors.log** (logs d'erreurs critiques) → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- **/tmp/sauvegardes_active.conf** (sauvegardes personnalisées actives) → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- API temps réel via status.php pour lecture de ces fichiers → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)

## Index par Fonctionnalités

### Fonctionnalités Principales
- **Sauvegarde incrémentale** (rsync --link-dest, hardlinks) → [Introduction](#objectifs-du-système)
- **Interface web moderne** (7 fichiers, design responsive) → [Introduction](#périmètre-du-projet)
- **Terminal interactif** (Server-Sent Events, session persistante) → [Manuel Utilisateur](#terminal-web-interactif)
- **Gestion d'erreurs avancée** (17 codes + actions suggérées) → [Diagnostics et Résolution de Bugs](#bugs-connus-et-solutions)

### Fonctionnalités Avancées
- **Mode dry-run** (simulation sans écriture, variable DRY_RUN) → [Manuel Utilisateur](#mode-simulation)
- **Hooks personnalisés** (PRE/POST_SAUVEGARDE_GLOBAL) → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- **Surveillance temps réel** (polling 2s, 5 métriques) → [Manuel Utilisateur](#métriques-principales)
- **Configuration adaptative** (détection www-data, chemins configurables) → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- **Sauvegardes personnalisées** (génération automatique variables) → [Manuel Utilisateur](#génération-automatique-des-variables)
- **Multi-distributions** (Debian/Ubuntu/RHEL/CentOS/Fedora) → [Manuel Utilisateur](#installation-automatique)
- **Terminal web complet** (session persistante, historique, auto-complétion) → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- **Gestion d'erreurs avancée** (17 codes + actions suggérées) → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- **Interface responsive moderne** (design professionnel) → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- **Exécution réelle via interface web** (pas seulement simulation) → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- **Validation robuste** (6 types de validation, chemins, IP, ports) → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- **Logs avancés** (rotation, compression, fallback) → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)

## Index par Problèmes Courants

### Problèmes d'Installation
- **Dépendances manquantes** (code erreur 127) → [Diagnostics et Résolution de Bugs](#bugs-connus-et-solutions)
- **Permissions incorrectes** (www-data, groupe fuse) → [Diagnostics et Résolution de Bugs](#bugs-connus-et-solutions)
- **Configuration Apache** (PHP disable_functions) → [Diagnostics et Résolution de Bugs](#bugs-connus-et-solutions)
- **Variables non définies** (config.sh, ordre de chargement) → [Diagnostics et Résolution de Bugs](#bugs-connus-et-solutions)

### Problèmes d'Exécution
- **Connexion SSH** (codes erreur 5, 17, timeout, authentification) → [Diagnostics et Résolution de Bugs](#bugs-connus-et-solutions)
- **Montage SSHFS** (codes erreur 6, 7, 8, groupe fuse) → [Diagnostics et Résolution de Bugs](#bugs-connus-et-solutions)
- **Erreurs rsync** (code erreur 9, codes rsync 23) → [Diagnostics et Résolution de Bugs](#bugs-connus-et-solutions)
- **Performance** (sauvegardes lentes, consommation mémoire) → [Diagnostics et Résolution de Bugs](#bugs-connus-et-solutions)
- **Espace disque** (codes erreur 4, 11, surveillance) → [Diagnostics et Résolution de Bugs](#bugs-connus-et-solutions)
- **Verrouillage** (code erreur 10, fichier PID) → [Diagnostics et Résolution de Bugs](#bugs-connus-et-solutions)

## Index par Variables Importantes

### Variables de Configuration Critiques
- **DEST_BASE_SAUVEGARDES** (répertoire racine) → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- **EMAIL_NOTIFICATION** (rapports automatiques) → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- **LOG_DIR** (répertoire logs) → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- **DEFAULT_RSYNC_OPTIONS** (options par défaut) → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- **DEFAULT_TYPE_CONNEXION_DISTANTE** (mode SSHFS/SSH) → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- **CHEMIN_RSYNC, CHEMIN_SSH, CHEMIN_SSHFS** (chemins exécutables) → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- **SCRIPT_PRE_SAUVEGARDE_GLOBAL, SCRIPT_POST_SAUVEGARDE_GLOBAL** (hooks) → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- **TAILLE_MAX_LOG_MO, JOURS_RETENTION_LOGS** (gestion logs) → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- **OPTIONS_COMMUNES_SSH, StrictHostKeyChecking_SSH** (SSH avancé) → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)

### Variables de Timeout et Performance
- **DELAI_CONNEXION_SSH_SECONDES** (timeout SSH) → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- **DELAI_OPERATION_RSYNC_SECONDES** (timeout rsync) → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- **ESPACE_DISQUE_MIN_GO** (espace minimum) → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)

### Variables de Sécurité et Contrôle
- **ACTIVERLOCK** (verrouillage) → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- **RSYNC_DELETE** (option --delete) → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- **StrictHostKeyChecking_SSH** (vérification clés) → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)

## Index par Fonctions Importantes

### Fonctions Bash Principales
- **effectuer_sauvegarde()** (fonction centrale, 8 paramètres) → [Référence Complète des Fonctions](#scripts-bash)
- **configure_web_environment()** (adaptation www-data) → [Référence Complète des Fonctions](#scripts-bash)
- **diagnostiquer_et_logger_erreur()** (17 codes erreur) → [Référence Complète des Fonctions](#scripts-bash)
- **monter_sshfs()** et **demonter_sshfs()** (gestion SSHFS) → [Référence Complète des Fonctions](#scripts-bash)
- **valider_variable()** (6 types de validation) → [Référence Complète des Fonctions](#scripts-bash)

### Fonctions PHP Principales
- **getSauvegardes()** (lecture configurations) → [Référence Complète des Fonctions](#interface-web-php)
- **executerSauvegarde()** (exécution via shell_exec) → [Référence Complète des Fonctions](#interface-web-php)
- **ajouterSauvegarde()** (génération variables) → [Référence Complète des Fonctions](#interface-web-php)
- **readConfigValue()** (lecture config.sh depuis PHP) → [Référence Complète des Fonctions](#interface-web-php)
- **isDefaultBackupEnabled()** (vérification activation) → [Référence Complète des Fonctions](#interface-web-php)
- **toggleDefaultBackup()** (activation/désactivation) → [Référence Complète des Fonctions](#interface-web-php)
- **getSauvegardesCustom()** (sauvegardes personnalisées) → [Référence Complète des Fonctions](#interface-web-php)
- **supprimerSauvegarde()** (suppression sauvegarde) → [Référence Complète des Fonctions](#interface-web-php)
- **calculateLogStats()** (statistiques logs) → [Référence Complète des Fonctions](#interface-web-php)

### Fonctions JavaScript Principales
- **executeRealBackup()** (exécution asynchrone) → [Référence Complète des Fonctions](#javascript---appjs)
- **updateStatus()** (polling temps réel) → [Référence Complète des Fonctions](#javascript---appjs)
- **updateDashboard()** (mise à jour interface) → [Référence Complète des Fonctions](#javascript---appjs)
- **runBackup()** (fonction principale) → [Référence Complète des Fonctions](#javascript---appjs)
- **simulateBackupExecution()** (simulation réaliste) → [Référence Complète des Fonctions](#javascript---appjs)
- **showMessage()** (affichage messages utilisateur) → [Référence Complète des Fonctions](#javascript---appjs)
- **addToConsole()** (ajout logs console) → [Référence Complète des Fonctions](#javascript---appjs)
- **startStatusPolling()** (démarrage polling) → [Référence Complète des Fonctions](#javascript---appjs)
- **loadCustomBackups()** (chargement sauvegardes custom) → [Référence Complète des Fonctions](#javascript---appjs)
- **downloadLogs()** et **exportConfig()** (export données) → [Référence Complète des Fonctions](#javascript---appjs)


## Index par Innovations Techniques

### Innovations Majeures du Projet

#### Architecture Hybride Moderne
- **Monolithe modulaire** avec séparation claire Bash/PHP/JS → [Description Fonctionnelle](#architecture-générale-du-système)
- **Configuration adaptative** selon environnement (CLI/Web) → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- **Exécution réelle** via interface web (pas simulation) → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)

#### Terminal Web Avancé
- **Session persistante** avec navigation répertoires → [Manuel Utilisateur](#terminal-web-interactif)
- **Streaming temps réel** via Server-Sent Events → [Manuel Utilisateur](#terminal-web-interactif)
- **Interface terminal avancée** avec historique et auto-complétion → [Manuel Utilisateur](#terminal-web-interactif)

#### Gestion d'Erreurs Professionnelle
- **17 codes d'erreur spécifiques** avec actions suggérées → [Diagnostics et Résolution de Bugs](#bugs-connus-et-solutions)
- **Logs de fallback** même si logs désactivés → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- **Diagnostic automatique** intégré → [Diagnostics et Résolution de Bugs](#méthodologie-de-diagnostic)

#### Extensibilité et Hooks
- **Hooks PRE/POST globaux** pour scripts personnalisés → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- **Chemins exécutables configurables** pour portabilité → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- **Sauvegardes personnalisées** avec génération automatique → [Manuel Utilisateur](#création-dune-sauvegarde-personnalisée)

#### Interface Utilisateur Moderne
- **Design responsive** type dashboard professionnel → [Manuel Utilisateur](#dashboard-principal)
- **Métriques temps réel** avec polling automatique → [Manuel Utilisateur](#métriques-principales)
- **Actions en un clic** (activation/désactivation, test, exécution) → [Manuel Utilisateur](#actions-disponibles)

### Comparaison avec Solutions Existantes

#### Avantages Uniques
- **Pas de base de données** requise (fichiers plats) → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- **Installation en une commande** multi-distributions → [Manuel Utilisateur](#installation-automatique)
- **Double interface** (CLI + Web) avec même backend → [Introduction](#périmètre-du-projet)
- **Documentation exhaustive** générée automatiquement → [Introduction](#contexte-du-projet)

#### Technologies Innovantes Utilisées
- **Server-Sent Events** pour streaming temps réel → [Manuel Utilisateur](#terminal-web-interactif)
- **Bash moderne** avec gestion d'erreurs avancée → [Référence Complète des Fonctions](#scripts-bash)
- **CSS Grid/Flexbox** pour interface responsive → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- **JavaScript async/await** pour exécution asynchrone → [Référence Complète des Fonctions](#javascript---appjs)

## Index par Complexité Technique

### Niveau Débutant
- **Installation automatique** (setup-web.sh) → [Manuel Utilisateur](#installation-automatique)
- **Sauvegardes par défaut** (5 préconfigurées) → [Manuel Utilisateur](#gestion-des-sauvegardes-par-défaut)
- **Interface web simple** (point-and-click) → [Manuel Utilisateur](#interface-web)

### Niveau Intermédiaire
- **Configuration personnalisée** (config.sh) → [Manuel Utilisateur](#configuration-manuelle)
- **Sauvegardes personnalisées** (via interface) → [Manuel Utilisateur](#création-dune-sauvegarde-personnalisée)
- **Gestion des logs** (consultation, statistiques) → [Manuel Utilisateur](#consultation-des-logs)

### Niveau Avancé
- **Hooks personnalisés** (scripts PRE/POST) → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- **Modification du code source** → [Référence Complète des Fonctions](#scripts-bash)
- **Intégration avec autres systèmes** → [Description Fonctionnelle](#interactions-et-flux-de-données)

### Niveau Expert
- **Développement de nouvelles fonctionnalités** → [Référence Complète des Fonctions](#scripts-bash)
- **Optimisation des performances** → [Diagnostics et Résolution de Bugs](#procédures-de-maintenance)
- **Sécurisation avancée** → [Manuel Utilisateur](#bonnes-pratiques)ation-automatique)
- **Sauvegardes par défaut** (5 préconfigurées) → [Manuel Utilisateur](#analyse-exhaustive-du-code-source)
- **Interface web simple** (point-and-click) → [Manuel Utilisateur](#analyse-exhaustive-du-code-source)

### Niveau Intermédiaire
- **Configuration personnalisée** (config.sh) → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- **Sauvegardes personnalisées** (via interface) → [Manuel Utilisateur](#analyse-exhaustive-du-code-source)
- **Gestion des logs** (consultation, statistiques) → [Manuel Utilisateur](#analyse-exhaustive-du-code-source)

### Niveau Avancé
- **Hooks personnalisés** (scripts PRE/POST) → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- **Modification du code source** → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- **Intégration avec autres systèmes** → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)

### Niveau Expert
- **Développement de nouvelles fonctionnalités** → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- **Optimisation des performances** → [Diagnostics](#analyse-exhaustive-du-code-source)
- **Sécurisation avancée** → [Diagnostics](#analyse-exhaustive-du-code-source)

## Index par Cas d'Usage

### Particuliers
- **Sauvegarde documents personnels** → [Manuel Utilisateur](#analyse-exhaustive-du-code-source)
- **Sauvegarde photos/vidéos** → [Manuel Utilisateur](#analyse-exhaustive-du-code-source)
- **Sauvegarde vers NAS domestique** → [Manuel Utilisateur](#analyse-exhaustive-du-code-source)

### Petites Entreprises
- **Sauvegarde serveurs multiples** → [Manuel Utilisateur](#analyse-exhaustive-du-code-source)
- **Sauvegarde bases de données** → [Manuel Utilisateur](#analyse-exhaustive-du-code-source)
- **Rapports automatiques par email** → [Manuel Utilisateur](#analyse-exhaustive-du-code-source)

### Entreprises
- **Intégration avec infrastructure existante** → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- **Surveillance et monitoring** → [Diagnostics](#analyse-exhaustive-du-code-source)
- **Conformité et audit** → [Diagnostics](#analyse-exhaustive-du-code-source)

### Développeurs
- **Sauvegarde projets de développement** → [Manuel Utilisateur](#analyse-exhaustive-du-code-source)
- **Intégration CI/CD** → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- **Personnalisation et extension** → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)

## Index Alphabétique Complet

### A-C
- ACTIVERLOCK → [Glossaire](#a)
- Administration système → [Manuel Utilisateur](#installation-automatique)
- API et interfaces → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- app.js → [Référence Complète des Fonctions](#javascript---appjs)
- Architecture → [Description Fonctionnelle](#architecture-générale-du-système)
- Authentification → [Manuel Utilisateur](#configuration-des-clés-ssh)
- Backup → [Glossaire](#b)
- Bash scripts → [Référence Complète des Fonctions](#scripts-bash)
- Bugs et erreurs → [Diagnostics et Résolution de Bugs](#bugs-connus-et-solutions)
- Configuration → [Manuel Utilisateur](#configuration-manuelle)
- Connexions distantes → [Manuel Utilisateur](#configuration-des-sauvegardes-distantes)
- Cron et programmation → [Manuel Utilisateur](#programmation-des-sauvegardes)

### D-F
- Dashboard → [Manuel Utilisateur](#dashboard-principal)
- default_backups.conf → [Glossaire](#d)
- Diagnostic → [Diagnostics et Résolution de Bugs](#méthodologie-de-diagnostic)
- Dry-run → [Glossaire](#d)
- Erreurs → [Diagnostics et Résolution de Bugs](#bugs-connus-et-solutions)
- EMAIL_NOTIFICATION → [Glossaire](#e)
- Espace disque → [Glossaire](#e)
- Exécution → [Manuel Utilisateur](#utilisation-en-ligne-de-commande)
- Fichiers de configuration → [Description Fonctionnelle](#analyse-exhaustive-du-code-source)
- Fonctions → [Référence Complète des Fonctions](#scripts-bash)

### G-L
- Gestion → [Manuel Utilisateur](#gestion-des-sauvegardes)
- Hooks → [Glossaire](#h)
- Installation → [Manuel Utilisateur](#installation-et-configuration-initiale)
- Interface web → [Manuel Utilisateur](#interface-web)
- JavaScript → [Référence Complète des Fonctions](#javascript---appjs)
- Journalisation → [Glossaire](#j)
- Ligne de commande → [Manuel Utilisateur](#utilisation-en-ligne-de-commande)
- Logs → [Manuel Utilisateur](#consultation-des-logs)

### M-R
- Maintenance → [Diagnostics et Résolution de Bugs](#procédures-de-maintenance)
- Montage SSHFS → [Glossaire](#m)
- Notifications → [Manuel Utilisateur](#notifications-email)
- Options → [Manuel Utilisateur](#options-disponibles)
- Performance → [Manuel Utilisateur](#bonnes-pratiques)
- Permissions → [Manuel Utilisateur](#installation-automatique)
- PHP → [Référence Complète des Fonctions](#interface-web-php)
- Programmation → [Manuel Utilisateur](#programmation-des-sauvegardes)
- Rsync → [Glossaire](#r)
- Rétention → [Glossaire](#r)

### S-Z
- Sauvegardes → [Introduction](#périmètre-du-projet)
- Scripts → [Référence Complète des Fonctions](#scripts-bash)
- Sécurité → [Manuel Utilisateur](#bonnes-pratiques)
- SSH → [Manuel Utilisateur](#configuration-des-clés-ssh)
- SSHFS → [Glossaire](#s)
- Terminal → [Manuel Utilisateur](#terminal-web-interactif)
- Tests → [Manuel Utilisateur](#mode-simulation)
- Utilisation → [Manuel Utilisateur](#utilisation-en-ligne-de-commande)
- Validation → [Référence Complète des Fonctions](#scripts-bash)
- Variables → [Glossaire](#variables-de-configuration-principales)
- Verrouillage → [Glossaire](#v)
- Web Interface → [Manuel Utilisateur](#interface-web)
