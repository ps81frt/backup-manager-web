#!/bin/bash
#===============================================================
# Script d'installation pour Backup Manager Web
# Rend le système 100% fonctionnel en terminal ET interface web
#===============================================================

set -e  # Arrêter en cas d'erreur

echo "Installation de Backup Manager Web..."

# Fonction pour vérifier si une commande existe
command_exists() {
    command -v "$1" >/dev/null 2>&1
}

# Fonction pour vérifier si un package est installé
package_installed() {
    local package="$1"
    if command_exists dpkg; then
        dpkg -l "$package" 2>/dev/null | grep -q "^ii"
    elif command_exists rpm; then
        rpm -q "$package" >/dev/null 2>&1
    elif command_exists pacman; then
        pacman -Q "$package" >/dev/null 2>&1
    else
        return 1
    fi
}

# 0. Détection de la distribution et installation des dépendances
echo "Détection de la distribution..."

if command_exists apt-get; then
    echo "Distribution Debian/Ubuntu détectée"
    DISTRO="debian"
    WEB_SERVER="apache2"
    WEB_SERVICE="apache2"
    
    # Détecter la version de fuse disponible
    if apt-cache show fuse3 >/dev/null 2>&1 && ! package_installed fuse; then
        FUSE_PACKAGE="fuse3"
    else
        FUSE_PACKAGE="fuse"
    fi
    
    PACKAGES="apache2 php libapache2-mod-php php-cli rsync openssh-client sshfs $FUSE_PACKAGE mailutils coreutils findutils grep sed gawk"
elif command_exists dnf; then
    echo "Distribution RHEL/CentOS 8+/Fedora détectée"
    DISTRO="rhel_new"
    WEB_SERVER="httpd"
    WEB_SERVICE="httpd"
    PACKAGES="httpd php php-cli rsync openssh-clients fuse-sshfs fuse mailx coreutils findutils grep sed gawk"
elif command_exists yum; then
    echo "Distribution RHEL/CentOS 7 détectée"
    DISTRO="rhel_old"
    WEB_SERVER="httpd"
    WEB_SERVICE="httpd"
    PACKAGES="httpd php php-cli rsync openssh-clients fuse-sshfs fuse mailx coreutils findutils grep sed gawk"
elif command_exists pacman; then
    echo "Distribution Arch Linux détectée"
    DISTRO="arch"
    WEB_SERVER="apache"
    WEB_SERVICE="httpd"
    PACKAGES="apache php php-apache rsync openssh sshfs fuse mailutils coreutils findutils grep sed gawk"
else
    echo "Distribution non supportée. Installation manuelle requise."
    exit 1
fi

# Vérification et installation des packages
echo "Vérification des dépendances..."
MISSING_PACKAGES=()

for package in $PACKAGES; do
    if ! package_installed "$package" && ! command_exists "$package"; then
        MISSING_PACKAGES+=("$package")
    fi
done

# Installation des packages manquants
if [ ${#MISSING_PACKAGES[@]} -gt 0 ]; then
    echo "Installation des packages manquants: ${MISSING_PACKAGES[*]}"
    case "$DISTRO" in
        debian)
            apt-get update
            
            # Résoudre les conflits fuse avant installation
            if echo "${MISSING_PACKAGES[*]}" | grep -q "fuse"; then
                echo "Résolution des conflits fuse..."
                apt-get remove -y fuse fuse3 2>/dev/null || true
                apt-get autoremove -y 2>/dev/null || true
                apt-get install -y --fix-broken
            fi
            
            apt-get install -y "${MISSING_PACKAGES[@]}"
            ;;
        rhel_new)
            dnf install -y "${MISSING_PACKAGES[@]}"
            ;;
        rhel_old)
            yum install -y "${MISSING_PACKAGES[@]}"
            ;;
        arch)
            pacman -S --noconfirm "${MISSING_PACKAGES[@]}"
            ;;
    esac
else
    echo "Toutes les dépendances sont déjà installées."
fi

# Démarrage et activation du serveur web
echo "Configuration du serveur web..."
if ! systemctl is-active --quiet "$WEB_SERVICE"; then
    systemctl enable "$WEB_SERVICE"
    systemctl start "$WEB_SERVICE"
    echo "$WEB_SERVICE démarré et activé"
else
    echo "$WEB_SERVICE déjà en cours d'exécution"
fi

# 1. Configuration PHP pour terminal
PHP_INI=$(php --ini | grep "Loaded Configuration File" | cut -d: -f2 | xargs)
if [[ -z "$PHP_INI" || "$PHP_INI" == "(none)" ]]; then
    PHP_INI="/etc/php/$(php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;')/apache2/php.ini"
fi

cp "$PHP_INI" "${PHP_INI}.backup-$(date +%Y%m%d)"
sed -i 's/^disable_functions.*/disable_functions = /' "$PHP_INI"
sed -i 's/^max_execution_time.*/max_execution_time = 300/' "$PHP_INI"

# 2. Permissions des scripts
chmod +x *.sh

# 3. Ajouter www-data au groupe fuse pour SSHFS
usermod -a -G fuse www-data
usermod -s /bin/bash www-data
echo "www-data ALL=(ALL) NOPASSWD: ALL" >> /etc/sudoers

# 4. Créer les répertoires nécessaires
mkdir -p /var/www/.ssh /tmp/{backup_logs,backups,sshfs_mounts}

# 5. Définir les propriétaires
chown -R www-data:www-data /var/www/.ssh /tmp/backup_logs /tmp/backups /tmp/sshfs_mounts

# 6. Configuration Apache
# Copier le projet dans le répertoire web
PROJET_DIR="$(pwd)"
WEB_DIR="/var/www/html/backup-manager-web"

if [[ "$PROJET_DIR" != "$WEB_DIR" ]]; then
    mkdir -p "$WEB_DIR"
    cp -r . "$WEB_DIR/"
    chown -R www-data:www-data "$WEB_DIR"
    chmod +x "$WEB_DIR"/*.sh
fi

# Activer les modules PHP nécessaires
if command -v a2enmod >/dev/null 2>&1; then
    a2enmod php*
    systemctl reload apache2
fi

# 7. Vérification des outils système critiques
echo "Vérification des outils système..."
CRITICAL_TOOLS="rsync ssh sshfs fusermount mountpoint lsof kill mkdir"
MISSING_TOOLS=()

for tool in $CRITICAL_TOOLS; do
    if ! command_exists "$tool"; then
        MISSING_TOOLS+=("$tool")
    fi
done

if [ ${#MISSING_TOOLS[@]} -gt 0 ]; then
    echo "ERREUR: Outils critiques manquants: ${MISSING_TOOLS[*]}"
    echo "Tentative d'installation..."
    
    case "$DISTRO" in
        debian)
            apt-get install -y util-linux psmisc coreutils
            ;;
        rhel_*)
            if command_exists dnf; then
                dnf install -y util-linux psmisc coreutils
            else
                yum install -y util-linux psmisc coreutils
            fi
            ;;
        arch)
            pacman -S --noconfirm util-linux psmisc coreutils
            ;;
    esac
fi

# 8. Vérification des permissions et groupes
echo "Configuration des permissions..."

# Déterminer le groupe fuse correct
FUSE_GROUP="fuse"
if getent group fuse3 >/dev/null 2>&1; then
    FUSE_GROUP="fuse3"
fi

if ! groups www-data | grep -q "$FUSE_GROUP"; then
    usermod -a -G "$FUSE_GROUP" www-data
    echo "www-data ajouté au groupe $FUSE_GROUP"
fi

if ! grep -q "www-data ALL=(ALL) NOPASSWD: ALL" /etc/sudoers; then
    echo "www-data ALL=(ALL) NOPASSWD: ALL" >> /etc/sudoers
    echo "Permissions sudo accordées à www-data"
fi

# 9. Génération de la clé SSH pour l'interface web
echo "Génération de la clé SSH..."
if [ ! -f /var/www/.ssh/backup_key ]; then
    sudo -u www-data ssh-keygen -t rsa -f /var/www/.ssh/backup_key -N "" -C "backup-web@$(hostname)"
    echo "Clé SSH générée"
else
    echo "Clé SSH déjà existante"
fi

# 10. Vérification finale de l'installation
echo "Vérification finale..."
ERRORS=()

# Vérifier PHP
if ! php -v >/dev/null 2>&1; then
    ERRORS+=("PHP non fonctionnel")
fi

# Vérifier le serveur web
if ! systemctl is-active --quiet "$WEB_SERVICE"; then
    ERRORS+=("Serveur web non démarré")
fi

# Vérifier les répertoires
for dir in "/var/www/.ssh" "/tmp/backup_logs" "/tmp/backups" "/tmp/sshfs_mounts"; do
    if [ ! -d "$dir" ]; then
        ERRORS+=("Répertoire manquant: $dir")
    fi
done

# Vérifier les outils critiques
for tool in rsync ssh sshfs; do
    if ! command_exists "$tool"; then
        ERRORS+=("Outil manquant: $tool")
    fi
done

echo ""
if [ ${#ERRORS[@]} -eq 0 ]; then
    echo "✅ INSTALLATION RÉUSSIE !"
else
    echo "❌ ERREURS DÉTECTÉES :"
    printf '   - %s\n' "${ERRORS[@]}"
    echo ""
fi

echo ""
echo "=== ÉTAPES SUIVANTES ==="
echo "1. Modifiez config.sh avec vos vraies valeurs :"
echo "   SSH_USER_PHOTOS=\"votre_utilisateur\""
echo "   SSH_IP_PHOTOS=\"192.168.1.100\""
echo ""
echo "2. Copiez cette clé publique sur vos serveurs distants :"
echo ""
if [ -f /var/www/.ssh/backup_key.pub ]; then
    cat /var/www/.ssh/backup_key.pub
else
    echo "   [CLÉ SSH NON GÉNÉRÉE - Vérifiez les erreurs ci-dessus]"
fi
echo ""
echo "3. Sur chaque serveur distant, exécutez :"
echo "   ssh-copy-id -i /var/www/.ssh/backup_key.pub utilisateur@serveur"
echo ""
echo "4. Testez les connexions :"
echo "   sudo -u www-data ssh -i /var/www/.ssh/backup_key utilisateur@serveur"
echo ""
echo "5. Le système est maintenant fonctionnel :"
echo "   - Terminal : ./sauvegarde.sh all"
echo "   - Web : http://localhost/backup-manager-web/web/"
echo ""
echo "=== DÉPENDANCES INSTALLÉES ==="
echo "Serveur web: $WEB_SERVER"
echo "Distribution: $DISTRO"
echo "Packages: $PACKAGES"
echo ""
