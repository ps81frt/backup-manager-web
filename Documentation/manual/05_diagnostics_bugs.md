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

