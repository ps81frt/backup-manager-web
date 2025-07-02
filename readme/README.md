# Backup Manager Web

**Système de Sauvegarde Hybride Nouvelle Génération**

[![Version](https://img.shields.io/badge/version-6.5-blue.svg)](https://github.com/ps81frt/backup-manager-web)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)
[![Platform](https://img.shields.io/badge/platform-Linux-orange.svg)](README.md)

## 📋 Description

Backup Manager Web combine la robustesse des scripts Bash avec la convivialité d'une interface web moderne pour créer un système de sauvegarde automatisé complet.

### ✨ Fonctionnalités Principales

- 🔄 **Sauvegardes incrémentales** avec rsync et hardlinks
- 🌐 **Interface web moderne** avec dashboard temps réel
- 💻 **Terminal interactif** intégré au navigateur
- 📊 **Métriques temps réel** et surveillance automatique
- 🔧 **Configuration centralisée** et sauvegardes personnalisées
- 🛡️ **17 codes d'erreur** avec diagnostic automatique
- 📱 **Design responsive** compatible mobile/desktop

### 🏗️ Architecture

- **Scripts Bash** : Logique de sauvegarde robuste
- **Interface PHP** : Dashboard et gestion web
- **JavaScript** : Interactivité et temps réel
- **Configuration** : Fichiers centralisés (.conf)

## 🚀 Installation Rapide

### Installation Automatique (Recommandée)

```bash
# Cloner le projet
git clone https://github.com/ps81frt/backup-manager-web.git
cd backup-manager-web

# Installation automatique (détecte votre distribution)
sudo ./setup-web.sh
```

### Systèmes Supportés

- ✅ **Debian/Ubuntu** (apt-get)
- ✅ **RHEL/CentOS** (yum/dnf)
- ✅ **Fedora** (dnf)

### Dépendances Installées Automatiquement

- Apache/Nginx + PHP
- rsync, openssh-client, sshfs, fuse
- mailutils (notifications email)

## 🔧 Configuration Rapide

1. **Éditer la configuration principale :**
```bash
nano config.sh
```

2. **Variables essentielles à modifier :**
```bash
EMAIL_NOTIFICATION="votre_email@example.com"
DEST_BASE_SAUVEGARDES="/mnt/backup_nas"
```

3. **Configurer les clés SSH pour sauvegardes distantes :**
```bash
ssh-copy-id -i /var/www/.ssh/backup_key.pub user@serveur-distant
```

## 🎯 Utilisation

### Interface Web
```
http://votre-serveur/backup-manager-web/web/
```

### Ligne de Commande
```bash
# Lister les sauvegardes disponibles
./sauvegarde.sh --list

# Test en mode simulation
./sauvegarde.sh --dry-run docs_eric

# Exécuter toutes les sauvegardes
./sauvegarde.sh all

# Aide complète
./sauvegarde.sh --help
```

### Programmation Automatique (Cron)
```bash
# Sauvegardes quotidiennes à 2h00
0 2 * * * /var/www/html/backup-manager-web/sauvegarde.sh all
```

## 📚 Documentation

- 📖 **[Manuel Complet](../Documentation/manual/Manuel.md)** - Documentation technique exhaustive
- 🔧 **[Guide d'Installation](../Documentation/manual/Manuel.md#installation-et-configuration-initiale)** - Installation détaillée
- 🐛 **[Résolution de Problèmes](../Documentation/manual/Manuel.md#diagnostics-et-résolution-de-bugs)** - Diagnostic et dépannage

## 👥 Équipe de Développement

- **Auteur Principal :** enRIKO
- **Contributeurs :** geole, iznobe, Watael, steph810

## 📄 Licence

Ce projet est sous licence MIT. Voir le fichier [LICENSE](../LICENSE) pour plus de détails.

## 🤝 Contribution

Les contributions sont les bienvenues ! N'hésitez pas à :
- Signaler des bugs
- Proposer des améliorations
- Soumettre des pull requests

---

**Backup Manager Web** - *Sauvegarde Locale & Distante | Incrémentale | Automatisée*