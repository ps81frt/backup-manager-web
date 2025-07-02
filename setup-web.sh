#!/bin/bash
#===============================================================
# Script d'installation pour Backup Manager Web
# Rend le système 100% fonctionnel en terminal ET interface web
#===============================================================

echo "Installation de Backup Manager Web..."

# 0. Vérification et installation des dépendances
# Détecter la distribution
if command -v apt-get >/dev/null 2>&1; then
    # Debian/Ubuntu
    apt-get update
    apt-get install -y apache2 php libapache2-mod-php php-cli rsync openssh-client sshfs fuse mailutils timeout
    systemctl enable apache2
    systemctl start apache2
elif command -v yum >/dev/null 2>&1; then
    # RHEL/CentOS 7
    yum install -y httpd php php-cli rsync openssh-clients fuse-sshfs fuse mailx timeout
    systemctl enable httpd
    systemctl start httpd
elif command -v dnf >/dev/null 2>&1; then
    # RHEL/CentOS 8+/Fedora
    dnf install -y httpd php php-cli rsync openssh-clients fuse-sshfs fuse mailx timeout
    systemctl enable httpd
    systemctl start httpd
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

# 7. Générer la clé SSH pour l'interface web
sudo -u www-data ssh-keygen -t rsa -f /var/www/.ssh/backup_key -N "" -C "backup-web@$(hostname)"

echo ""
echo "Installation terminée !"
echo ""
echo "ÉTAPES SUIVANTES :"
echo "1. Modifiez config.sh avec vos vraies valeurs :"
echo "   SSH_USER_PHOTOS=\"votre_utilisateur\""
echo "   SSH_IP_PHOTOS=\"192.168.1.100\""
echo ""
echo "2. Copiez cette clé publique sur vos serveurs distants :"
echo ""
cat /var/www/.ssh/backup_key.pub
echo ""
echo "3. Sur chaque serveur distant, exécutez :"
echo "   ssh-copy-id -i /var/www/.ssh/backup_key.pub utilisateur@serveur"
echo "   Exemples concrets :"
echo "   ssh-copy-id -i /var/www/.ssh/backup_key.pub eric@192.168.1.100"
echo "   ssh-copy-id -i /var/www/.ssh/backup_key.pub admin@192.168.1.101"
echo ""
echo "4. Testez les connexions :"
echo "   sudo -u www-data ssh -i /var/www/.ssh/backup_key utilisateur@serveur"
echo ""
echo "5. Le système est maintenant fonctionnel :"
echo "   - Terminal : ./sauvegarde.sh all"
echo "   - Web : http://localhost/backup-manager-web/web/"
echo ""