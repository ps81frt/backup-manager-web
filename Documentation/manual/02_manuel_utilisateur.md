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

