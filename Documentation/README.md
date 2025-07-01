# Backup Manager Web

Système de sauvegarde incrémentale avec interface web de gestion.

## Structure du Projet

```
backup-manager-web/
├── config.sh              # Configuration principale (ORIGINAL)
├── fonctions_erreur.sh     # Gestion des erreurs (ORIGINAL)  
├── sauvegarde.sh          # Script principal (ORIGINAL)
├── sauvegardes_custom.conf # Sauvegardes personnalisées (auto-généré)
├── web/                   # Interface web
│   ├── index.php          # Page d'accueil
│   ├── manage.php         # Gestion des sauvegardes
│   ├── logs.php           # Visualisation des logs
│   └── style.css          # Styles CSS
└── README.md              # Ce fichier
```

## Installation

### 1. Prérequis
- Serveur web (Apache/Nginx) avec PHP 7.4+
- Bash et outils système (rsync, ssh, sshfs)
- Permissions d'écriture sur les répertoires de destination

### 2. Configuration Web
```bash
# Copier le projet dans le répertoire web
sudo cp -r backup-manager-web /var/www/html/

# Ajuster les permissions
sudo chown -R www-data:www-data /var/www/html/backup-manager-web
sudo chmod +x /var/www/html/backup-manager-web/*.sh
```

### 3. Configuration des Scripts
Éditer `config.sh` selon vos besoins :
- Chemins de destination
- Paramètres SSH
- Notifications email
- Politiques de rétention

## Utilisation

### Interface Web
Accédez à `http://votre-serveur/backup-manager-web/web/`

**Fonctionnalités :**
- ✅ Exécution des sauvegardes (normale/test)
- ✅ Ajout de nouvelles sauvegardes
- ✅ Visualisation des logs
- ✅ Gestion des sauvegardes personnalisées

### Ligne de Commande
```bash
# Exécuter une sauvegarde spécifique
./sauvegarde.sh docs_eric

# Exécuter toutes les sauvegardes
./sauvegarde.sh all

# Mode test (dry-run)
./sauvegarde.sh --dry-run docs_eric

# Lister les sauvegardes disponibles
./sauvegarde.sh --list
```

## Sauvegardes par Défaut

Le système inclut 5 sauvegardes préconfigurées :
- `docs_eric` - Documents Eric (locale)
- `docs_fanou` - Documents Fanou (locale)  
- `photos_vm` - Photos VM (distante SSHFS)
- `projets_serveur` - Projets Serveur (distante SSHFS)
- `docs_portable` - Documents Portable (distante SSHFS)

## Ajout de Sauvegardes Personnalisées

### Via Interface Web
1. Aller dans "Gérer" 
2. Remplir le formulaire d'ajout
3. La sauvegarde sera automatiquement disponible

### Manuellement
Les sauvegardes personnalisées sont stockées dans `sauvegardes_custom.conf` :

```bash
# SAUVEGARDE: ma_sauvegarde
SOURCE_LOCALE_MA_SAUVEGARDE="/chemin/source"
DEST_MAIN_MA_SAUVEGARDE="$DEST_BASE_SAUVEGARDES/MonDossier/"
DEST_INCR_BASE_MA_SAUVEGARDE="$DEST_BASE_SAUVEGARDES/incremental-MonDossier/"
JOURS_RETENTION_MA_SAUVEGARDE_QUOTIDIEN=7
JOURS_RETENTION_MA_SAUVEGARDE_HEBDO=4
JOURS_RETENTION_MA_SAUVEGARDE_MENSUEL=12
```

## Sécurité

- L'interface web n'expose pas les mots de passe SSH
- Les scripts originaux restent inchangés
- Validation des entrées utilisateur
- Logs détaillés de toutes les opérations

## Logs

Les logs sont stockés dans `/var/log/sauvegardes/` :
- Format : `sauvegarde_YYYYMMDD.log`
- Accessible via l'interface web
- Rotation automatique

## Dépannage

### Erreurs Communes
1. **Permissions** : Vérifier les droits d'écriture
2. **SSH** : Tester les connexions manuellement
3. **Chemins** : Vérifier l'existence des répertoires source

### Logs d'Erreur
```bash
# Logs de fallback en cas de problème
tail -f /tmp/backup_fallback_errors.log

# Logs principaux
tail -f /var/log/sauvegardes/sauvegarde_$(date +%Y%m%d).log
```

## Architecture

Le projet respecte le principe de **non-modification des scripts originaux** :
- Scripts de base intacts et fonctionnels
- Interface web comme couche supplémentaire
- Sauvegardes personnalisées via fichier externe
- Compatibilité totale avec l'utilisation en ligne de commande

## Support

Pour toute question ou problème :
1. Vérifier les logs
2. Tester en mode dry-run
3. Valider la configuration SSH
4. Contrôler les permissions des fichiers