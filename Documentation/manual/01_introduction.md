
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
- [Installation et Configuration](#installation-et-configuration)
- [Utilisation en Ligne de Commande](#utilisation-en-ligne-de-commande)
- [Interface Web](#interface-web)
- [Programmation des Sauvegardes](#programmation-des-sauvegardes)
- [Gestion des Erreurs](#gestion-des-erreurs-courantes)
- [Bonnes Pratiques](#bonnes-pratiques)

## 3. [Description Fonctionnelle](#description-fonctionnelle)
- [Vue d'Ensemble](#vue-densemble)
- [Architecture du Système](#architecture-du-système)
- [Module Core - Scripts Bash](#module-core---scripts-bash)
- [Module Web - Interface Utilisateur](#module-web---interface-utilisateur)
- [Flux de Données](#flux-de-données-principal)

## 4. [Référence des Fonctions](#référence-des-fonctions)
- [Fonctions Bash](#fonctions-bash)
- [Fonctions PHP](#fonctions-php)
- [Fonctions JavaScript](#fonctions-javascript)

## 5. [Diagnostics et Dépannage](#diagnostics-et-dépannage)
- [Codes d'Erreur](#codes-derreur-système)
- [Bugs Connus](#bugs-connus-et-solutions)
- [Outils de Diagnostic](#outils-de-diagnostic)
- [Maintenance Préventive](#maintenance-préventive)

## 6. [Glossaire](#glossaire)
- [Termes Techniques](#termes-techniques)
- [Variables de Configuration](#variables-de-configuration)
- [Codes d'Erreur](#codes-derreur)

## 7. [Index Thématique](#index-thématique)
- [Index Alphabétique](#index-alphabétique)
- [Index par Fonctionnalités](#index-par-fonctionnalités)
- [Index par Problèmes](#index-par-problèmes-courants)

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
