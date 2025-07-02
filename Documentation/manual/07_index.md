# Index Thématique

## A

**Administration Système**
- Configuration Apache/Nginx → [Manuel Utilisateur](02_manuel_utilisateur.md#processus-dinstallation)
- Configuration PHP (disable_functions, max_execution_time) → [Manuel Utilisateur](02_manuel_utilisateur.md#processus-dinstallation)
- Gestion des permissions www-data → [Diagnostics](04_diagnostics_bugs.md#bug-009--interface-web-inaccessible)
- Installation des dépendances multi-distributions → [Introduction](01_introduction.md#dépendances-système)
- Surveillance système automatique → [Diagnostics](04_diagnostics_bugs.md#script-de-surveillance)
- Utilisateur www-data (groupe fuse, shell bash, sudo) → [Glossaire](05_glossaire.md#www-data)
- Détection automatique de distribution (apt-get, yum, dnf) → [Description Fonctionnelle](03_description_fonctionnelle.md#setup-websh)
- Configuration automatique serveur web → [Description Fonctionnelle](03_description_fonctionnelle.md#configuration-apache)
- Génération clé SSH dédiée (/var/www/.ssh/backup_key) → [Description Fonctionnelle](03_description_fonctionnelle.md#configuration-ssh-web)

**ACTIVERLOCK**
- Variable de verrouillage → [Glossaire](05_glossaire.md#activerlock)
- Mécanisme de verrouillage → [Description Fonctionnelle](03_description_fonctionnelle.md#mécanisme-de-verrouillage)
- Fonction gerer_verrouillage() → [Description Fonctionnelle](03_description_fonctionnelle.md#gerer_verrouillage)

**API et Interfaces**
- API Status (status.php) - JSON temps réel → [Description Fonctionnelle](03_description_fonctionnelle.md#statusphp---api-de-statut)
- Interface web (7 fichiers PHP/HTML/JS/CSS) → [Introduction](01_introduction.md#interface-web-phphtml-js)
- Terminal interactif (Server-Sent Events) → [Manuel Utilisateur](02_manuel_utilisateur.md#streaming-temps-réel)
- Polling automatique (2 secondes) → [Glossaire](05_glossaire.md#polling)
- API REST pour statut sauvegardes (fichiers /tmp/backup_*.txt) → [Description Fonctionnelle](03_description_fonctionnelle.md#api-statut-temps-reel)
- Session persistante terminal web ($_SESSION['terminal_cwd']) → [Description Fonctionnelle](03_description_fonctionnelle.md#terminal-session-persistante)
- Streaming temps réel via EventSource → [Description Fonctionnelle](03_description_fonctionnelle.md#streaming-server-sent-events)
- Interface responsive (mobile/desktop) → [Description Fonctionnelle](03_description_fonctionnelle.md#design-responsive)

**app.js - Logique Client**
- Variables globales JavaScript → [Description Fonctionnelle](03_description_fonctionnelle.md#variables-globales-javascript)
- Fonctions executeRealBackup(), updateStatus() → [Description Fonctionnelle](03_description_fonctionnelle.md#fonctions-principales)
- Polling automatique du statut → [Description Fonctionnelle](03_description_fonctionnelle.md#fonctionnalités-temps-réel)
- Exécution réelle via PHP (executeRealBackup) → [Description Fonctionnelle](03_description_fonctionnelle.md#execution-reelle-php)
- Gestion d'erreurs async/await → [Description Fonctionnelle](03_description_fonctionnelle.md#gestion-erreurs-javascript)
- Simulation d'exécution avec étapes réalistes → [Description Fonctionnelle](03_description_fonctionnelle.md#simulation-backup)
- Raccourcis clavier (Ctrl+R, Ctrl+L) → [Description Fonctionnelle](03_description_fonctionnelle.md#raccourcis-clavier)
- Mise à jour dashboard temps réel → [Description Fonctionnelle](03_description_fonctionnelle.md#dashboard-temps-reel)

**Architecture**
- Architecture hybride modulaire → [Description Fonctionnelle](03_description_fonctionnelle.md#vue-densemble)
- Diagramme d'architecture (4 couches) → [Description Fonctionnelle](03_description_fonctionnelle.md#diagramme-darchitecture)
- Modules et composants (Bash + Web + Config) → [Description Fonctionnelle](03_description_fonctionnelle.md#modules-et-composants)
- Flux de données principal → [Description Fonctionnelle](03_description_fonctionnelle.md#flux-de-données-principal)
- Communication inter-modules → [Description Fonctionnelle](03_description_fonctionnelle.md#communication-inter-modules)

**Authentification**
- Clés SSH dédiées (/var/www/.ssh/backup_key) → [Manuel Utilisateur](02_manuel_utilisateur.md#génération-des-clés-ssh)
- Configuration SSH avancée (timeout, options) → [Description Fonctionnelle](03_description_fonctionnelle.md#variables-ssh-avancées)
- Problèmes d'authentification (codes erreur 5, 17) → [Diagnostics](04_diagnostics_bugs.md#bug-003--authentification-ssh-échouée)
- StrictHostKeyChecking configurable → [Glossaire](05_glossaire.md#stricthostkeychecking_ssh)
- Fonction verifier_connexion_ssh() → [Description Fonctionnelle](03_description_fonctionnelle.md#verifier_connexion_ssh)

## B

**Backup (Sauvegarde)**
- Définition et concept → [Glossaire](05_glossaire.md#backup)
- Types de sauvegardes (locale/distante, incrémentale) → [Introduction](01_introduction.md#sauvegardes-prédéfinies)
- Sauvegardes par défaut (5 préconfigurées) → [Manuel Utilisateur](02_manuel_utilisateur.md#activation-désactivation)
- Sauvegardes personnalisées (génération automatique) → [Manuel Utilisateur](02_manuel_utilisateur.md#génération-automatique-des-variables)
- Fichiers de statut temporaires → [Glossaire](05_glossaire.md#fichiers-de-statut-temporaires)

**Bash Scripts (4 fichiers)**
- config.sh (version 2.5, 75+ variables, 9 catégories) → [Description Fonctionnelle](03_description_fonctionnelle.md#variables-globales-de-configuration)
- fonctions_erreur.sh (version 6.6 Beta, 17 codes erreur, 400+ lignes) → [Description Fonctionnelle](03_description_fonctionnelle.md#système-de-codes-derreur)
- sauvegarde.sh (version 6.5, 800+ lignes, script principal) → [Description Fonctionnelle](03_description_fonctionnelle.md#variables-globales-du-script)
- setup-web.sh (installation multi-distributions, 100+ lignes) → [Manuel Utilisateur](02_manuel_utilisateur.md#processus-dinstallation)
- Chemins d'exécutables configurables (CHEMIN_RSYNC, CHEMIN_SSH, etc.) → [Description Fonctionnelle](03_description_fonctionnelle.md#chemins-executables-configurables)
- Hooks personnalisés (PRE/POST_SAUVEGARDE_GLOBAL) → [Description Fonctionnelle](03_description_fonctionnelle.md#hooks-personnalises)
- Gestion avanced des logs (rotation, compression) → [Description Fonctionnelle](03_description_fonctionnelle.md#gestion-avancee-logs)
- Options SSH avancées (StrictHostKeyChecking, timeout) → [Description Fonctionnelle](03_description_fonctionnelle.md#options-ssh-avancees)

**Bash Scripts (4 fichiers)**
- config.sh (version 2.5, 75+ variables, 9 catégories) → [Description Fonctionnelle](03_description_fonctionnelle.md#variables-globales-de-configuration)
- fonctions_erreur.sh (version 6.6 Beta, 17 codes erreur, gestion SSHFS) → [Description Fonctionnelle](03_description_fonctionnelle.md#fonctions-de-validation)
- sauvegarde.sh (version 6.5, 800+ lignes, fonction centrale) → [Description Fonctionnelle](03_description_fonctionnelle.md#variables-globales-du-script)
- setup-web.sh (installation multi-distributions, configuration automatique) → [Manuel Utilisateur](02_manuel_utilisateur.md#processus-dinstallation)

**Bugs et Erreurs**
- Bugs connus (12 bugs documentés) → [Diagnostics](04_diagnostics_bugs.md#bugs-connus-et-solutions)
- Codes d'erreur (17 codes spécifiques + 127) → [Diagnostics](04_diagnostics_bugs.md#codes-derreur-système-17-codes-spécifiques)
- Diagnostic automatique (script diagnostic.sh) → [Diagnostics](04_diagnostics_bugs.md#script-de-diagnostic-automatique)
- Résolution de problèmes (méthodologie systématique) → [Diagnostics](04_diagnostics_bugs.md#approche-systématique)
- Fonction diagnostiquer_et_logger_erreur() → [Glossaire](05_glossaire.md#codes-derreur-spécifiques)

## C

**Configuration**
- Configuration centralisée (config.sh, 75+ variables) → [Description Fonctionnelle](03_description_fonctionnelle.md#variables-globales-de-configuration)
- Configuration initiale (setup-web.sh) → [Manuel Utilisateur](02_manuel_utilisateur.md#processus-dinstallation)
- Fichiers de configuration (2 fichiers .conf) → [Introduction](01_introduction.md#configuration-et-données)
- Variables de configuration (9 catégories) → [Glossaire](05_glossaire.md#variables-de-configuration-globales)
- Chemins d'exécutables configurables → [Description Fonctionnelle](03_description_fonctionnelle.md#variables-de-chemins-dexécutables)
- Configuration adaptative environnement web (configure_web_environment) → [Description Fonctionnelle](03_description_fonctionnelle.md#configuration-adaptative)
- Variables EMAIL_NOTIFICATION, ESPACE_DISQUE_MIN_GO → [Description Fonctionnelle](03_description_fonctionnelle.md#variables-systeme-principales)
- Configuration RSYNC_DELETE, ACTIVERLOCK → [Description Fonctionnelle](03_description_fonctionnelle.md#variables-securite-controle)
- Politiques de rétention par sauvegarde → [Description Fonctionnelle](03_description_fonctionnelle.md#politiques-retention)

**config.sh - Fichier Principal**
- Variables système principales → [Description Fonctionnelle](03_description_fonctionnelle.md#variables-système-principales)
- Variables de comportement rsync → [Description Fonctionnelle](03_description_fonctionnelle.md#variables-de-comportement-rsync)
- Variables de sécurité et contrôle → [Description Fonctionnelle](03_description_fonctionnelle.md#variables-de-sécurité-et-contrôle)
- Variables de sauvegardes prédéfinies → [Description Fonctionnelle](03_description_fonctionnelle.md#variables-de-sauvegardes-prédéfinies)

**Connexions Distantes**
- Configuration SSH (pattern par sauvegarde) → [Manuel Utilisateur](02_manuel_utilisateur.md#configuration-des-sauvegardes-distantes)
- Problèmes de connexion (codes erreur 5, 6, 7, 8) → [Diagnostics](04_diagnostics_bugs.md#problèmes-de-connexion-ssh)
- SSHFS (mode par défaut, DEFAULT_TYPE_CONNEXION_DISTANTE=0) → [Glossaire](05_glossaire.md#sshfs-ssh-filesystem)
- Test de connectivité (script test_connectivity.sh) → [Diagnostics](04_diagnostics_bugs.md#test-de-connectivité)
- Fonctions monter_sshfs() et demonter_sshfs() → [Description Fonctionnelle](03_description_fonctionnelle.md#gestion-sshfs)
- Variables SSH par sauvegarde → [Description Fonctionnelle](03_description_fonctionnelle.md#variables-ssh-avancées)

**Cron et Programmation**
- Configuration cron (installation tâche) → [Manuel Utilisateur](02_manuel_utilisateur.md#installation-de-la-tâche-cron)
- Exemples de programmation (4 patterns) → [Manuel Utilisateur](02_manuel_utilisateur.md#exemples-de-programmation)
- Surveillance des exécutions (logs cron + script) → [Manuel Utilisateur](02_manuel_utilisateur.md#surveillance-temps-réel)
- Notifications email automatiques → [Manuel Utilisateur](02_manuel_utilisateur.md#types-de-notifications-automatiques)

## D

**Dashboard (index.php)**
- Accès au dashboard (interface web principale) → [Manuel Utilisateur](02_manuel_utilisateur.md#accès-au-dashboard)
- Métriques principales (5 métriques temps réel) → [Manuel Utilisateur](02_manuel_utilisateur.md#métriques-principales)
- Vue d'ensemble (statut, progression, durée) → [Manuel Utilisateur](02_manuel_utilisateur.md#vue-densemble)
- Variables globales PHP → [Description Fonctionnelle](03_description_fonctionnelle.md#variables-globales-php)
- Fonctions getSauvegardes(), executerSauvegarde() → [Description Fonctionnelle](03_description_fonctionnelle.md#fonctions-php)
- Lecture configuration depuis config.sh (readConfigValue) → [Description Fonctionnelle](03_description_fonctionnelle.md#lecture-configuration-php)
- Gestion sauvegardes par défaut et personnalisées → [Description Fonctionnelle](03_description_fonctionnelle.md#gestion-sauvegardes-mixtes)
- Interface responsive avec sidebar navigation → [Description Fonctionnelle](03_description_fonctionnelle.md#interface-responsive)
- Actions toggle pour activation/désactivation → [Description Fonctionnelle](03_description_fonctionnelle.md#actions-toggle)

**default_backups.conf**
- Format de configuration (nom=1/0) → [Glossaire](05_glossaire.md#default_backupsconf)
- Fonction isDefaultBackupEnabled() → [Description Fonctionnelle](03_description_fonctionnelle.md#isdefaultbackupenabled)
- Fonction toggleDefaultBackup() → [Description Fonctionnelle](03_description_fonctionnelle.md#toggledefaultbackup)

**Diagnostic**
- Méthodologie (approche systématique 3 étapes) → [Diagnostics](04_diagnostics_bugs.md#approche-systématique)
- Outils de diagnostic (commandes de base + avancées) → [Diagnostics](04_diagnostics_bugs.md#commandes-de-diagnostic-avancé)
- Procédures avancées (diagnostic complet) → [Diagnostics](04_diagnostics_bugs.md#diagnostic-complet-du-système)
- Scripts de diagnostic (diagnostic.sh, test_config.sh) → [Diagnostics](04_diagnostics_bugs.md#tests-de-validation)
- Codes d'erreur avec actions suggérées → [Diagnostics](04_diagnostics_bugs.md#codes-derreur-système-17-codes-spécifiques)

**Dry-run**
- Définition (mode simulation, variable DRY_RUN=1) → [Glossaire](05_glossaire.md#dry-run)
- Utilisation (option --dry-run) → [Manuel Utilisateur](02_manuel_utilisateur.md#exécution-en-mode-test)
- Implémentation dans effectuer_sauvegarde() → [Description Fonctionnelle](03_description_fonctionnelle.md#effectuer_sauvegarde---fonction-centrale)
- Variable globale DRY_RUN → [Description Fonctionnelle](03_description_fonctionnelle.md#variables-détat-et-de-contrôle)

## E

**Erreurs**
- Codes d'erreur (17 codes + 127, avec actions) → [Diagnostics](04_diagnostics_bugs.md#codes-derreur-système-17-codes-spécifiques)
- Gestion d'erreurs (fonctions_erreur.sh) → [Description Fonctionnelle](03_description_fonctionnelle.md#fonctions-de-validation)
- Messages d'erreur (format [ERREUR] avec timestamp) → [Diagnostics](04_diagnostics_bugs.md#bugs-connus-et-solutions)
- Fonction diagnostiquer_et_logger_erreur() → [Description Fonctionnelle](03_description_fonctionnelle.md#diagnostiquer_et_logger_erreur)
- Logs de secours (/tmp/backup_fallback_errors.log) → [Glossaire](05_glossaire.md#journalisation-logging)

**EMAIL_NOTIFICATION**
- Variable de notification → [Description Fonctionnelle](03_description_fonctionnelle.md#variables-système-principales)
- Configuration email avancée → [Manuel Utilisateur](02_manuel_utilisateur.md#configuration-email-avancée)
- Fonction envoyer_rapport_email() → [Description Fonctionnelle](03_description_fonctionnelle.md#envoyer_rapport_email)

**Espace Disque**
- Vérification (fonction verifier_espace_disque()) → [Description Fonctionnelle](03_description_fonctionnelle.md#verifier_espace_disque)
- Configuration minimum (ESPACE_DISQUE_MIN_GO=5) → [Description Fonctionnelle](03_description_fonctionnelle.md#variables-système-principales)
- Problèmes d'espace (code erreur 4, 11) → [Diagnostics](04_diagnostics_bugs.md#codes-derreur-système-17-codes-spécifiques)
- Surveillance automatique (script monitor.sh) → [Diagnostics](04_diagnostics_bugs.md#script-de-surveillance)

**Exécution**
- Ligne de commande (syntaxe, options, sélections) → [Manuel Utilisateur](02_manuel_utilisateur.md#syntaxe-de-base)
- Interface web (exécution via PHP shell_exec) → [Manuel Utilisateur](02_manuel_utilisateur.md#actions-disponibles)
- Programmée (cron, 4 patterns d'exemple) → [Manuel Utilisateur](02_manuel_utilisateur.md#exemples-de-programmation)
- Fonction effectuer_sauvegarde() (8 paramètres) → [Description Fonctionnelle](03_description_fonctionnelle.md#effectuer_sauvegarde---fonction-centrale)
- Flux d'exécution (6 étapes) → [Description Fonctionnelle](03_description_fonctionnelle.md#flux-dexécution)

## F

**Fichiers de Configuration**
- config.sh → [Description Fonctionnelle](03_description_fonctionnelle.md#configsh---configuration-centralisée)
- default_backups.conf → [Manuel Utilisateur](02_manuel_utilisateur.md#gestion-des-sauvegardes-par-défaut)
- sauvegardes_custom.conf → [Manuel Utilisateur](02_manuel_utilisateur.md#création-dune-sauvegarde-personnalisée)

**Fonctions**
- Fonctions bash → [Description Fonctionnelle](03_description_fonctionnelle.md#fonctions-principales)
- Fonctions PHP → [Description Fonctionnelle](03_description_fonctionnelle.md#fonctions-php)
- Fonctions JavaScript → [Description Fonctionnelle](03_description_fonctionnelle.md#fonctions-principales-1)
- effectuer_sauvegarde() - fonction centrale (8 paramètres) → [Description Fonctionnelle](03_description_fonctionnelle.md#effectuer-sauvegarde)
- configure_web_environment() - adaptation www-data → [Description Fonctionnelle](03_description_fonctionnelle.md#configure-web-environment)
- diagnostiquer_et_logger_erreur() - 17 codes erreur → [Description Fonctionnelle](03_description_fonctionnelle.md#diagnostiquer-logger-erreur)
- valider_variable() - 6 types validation → [Description Fonctionnelle](03_description_fonctionnelle.md#valider-variable)
- ajouterSauvegarde() - génération variables → [Description Fonctionnelle](03_description_fonctionnelle.md#ajouter-sauvegarde)
- executeRealBackup() - exécution asynchrone → [Description Fonctionnelle](03_description_fonctionnelle.md#execute-real-backup)

## G

**Gestion**
- Gestion des erreurs → [Description Fonctionnelle](03_description_fonctionnelle.md#fonctions_erreursh---gestion-des-erreurs)
- Gestion des sauvegardes → [Manuel Utilisateur](02_manuel_utilisateur.md#gestion-des-sauvegardes)
- Gestion des états → [Description Fonctionnelle](03_description_fonctionnelle.md#gestion-des-états-et-processus)

## H

**Hooks**
- Configuration → [Description Fonctionnelle](03_description_fonctionnelle.md#hooks-système)
- Extensibilité → [Description Fonctionnelle](03_description_fonctionnelle.md#extensibilité-et-hooks)

## I

**Installation**
- Installation automatique → [Manuel Utilisateur](02_manuel_utilisateur.md#installation-automatique)
- Installation manuelle → [Manuel Utilisateur](02_manuel_utilisateur.md#configuration-manuelle)
- Prérequis → [Introduction](01_introduction.md#prérequis)

**Interface Web**
- Accès → [Manuel Utilisateur](02_manuel_utilisateur.md#interface-web)
- Composants → [Description Fonctionnelle](03_description_fonctionnelle.md#module-web---interface-utilisateur)
- Problèmes → [Diagnostics](04_diagnostics_bugs.md#problèmes-dinterface-web)

## J

**JavaScript**
- app.js → [Description Fonctionnelle](03_description_fonctionnelle.md#appjs---logique-client)
- Fonctionnalités temps réel → [Description Fonctionnelle](03_description_fonctionnelle.md#fonctionnalités-temps-réel)

**Journalisation**
- Configuration → [Description Fonctionnelle](03_description_fonctionnelle.md#variables-globales)
- Consultation → [Manuel Utilisateur](02_manuel_utilisateur.md#consultation-des-logs)
- Fichiers de logs → [Description Fonctionnelle](03_description_fonctionnelle.md#fichiers-de-logs)

## L

**Ligne de Commande**
- Syntaxe → [Manuel Utilisateur](02_manuel_utilisateur.md#syntaxe-de-base)
- Options → [Manuel Utilisateur](02_manuel_utilisateur.md#options-disponibles)
- Exemples → [Manuel Utilisateur](02_manuel_utilisateur.md#exemples-dutilisation)

**Logs**
- Consultation → [Manuel Utilisateur](02_manuel_utilisateur.md#consultation-des-logs)
- Fichiers → [Description Fonctionnelle](03_description_fonctionnelle.md#fichiers-de-logs)
- Surveillance → [Diagnostics](04_diagnostics_bugs.md#vérifications-régulières)

## M

**Maintenance**
- Maintenance préventive → [Diagnostics](04_diagnostics_bugs.md#maintenance-préventive)
- Surveillance → [Diagnostics](04_diagnostics_bugs.md#surveillance-automatique)
- Vérifications → [Diagnostics](04_diagnostics_bugs.md#vérifications-régulières)

**Montage SSHFS**
- Configuration → [Description Fonctionnelle](03_description_fonctionnelle.md#gestion-sshfs)
- Problèmes → [Diagnostics](04_diagnostics_bugs.md#problèmes-de-montage-sshfs)

## N

**Notifications**
- Configuration → [Description Fonctionnelle](03_description_fonctionnelle.md#variables-globales)
- Types → [Manuel Utilisateur](02_manuel_utilisateur.md#notifications-email)

## O

**Options**
- Options rsync → [Description Fonctionnelle](03_description_fonctionnelle.md#variables-globales)
- Options du script → [Manuel Utilisateur](02_manuel_utilisateur.md#options-disponibles)

## P

**Performance**
- Optimisation → [Diagnostics](04_diagnostics_bugs.md#problèmes-de-performance)
- Surveillance → [Manuel Utilisateur](02_manuel_utilisateur.md#bonnes-pratiques)

**Permissions**
- Configuration → [Manuel Utilisateur](02_manuel_utilisateur.md#installation-automatique)
- Problèmes → [Diagnostics](04_diagnostics_bugs.md#problèmes-de-permissions)

**PHP**
- Fichiers PHP → [Description Fonctionnelle](03_description_fonctionnelle.md#module-web---interface-utilisateur)
- Configuration → [Manuel Utilisateur](02_manuel_utilisateur.md#installation-automatique)

**Programmation**
- Cron → [Manuel Utilisateur](02_manuel_utilisateur.md#programmation-des-sauvegardes)
- Exemples → [Manuel Utilisateur](02_manuel_utilisateur.md#exemples-de-programmation)

## R

**Rsync**
- Configuration → [Description Fonctionnelle](03_description_fonctionnelle.md#variables-globales)
- Options → [Glossaire](05_glossaire.md#options-rsync)
- Problèmes → [Diagnostics](04_diagnostics_bugs.md#problèmes-rsync)

**Rétention**
- Configuration → [Description Fonctionnelle](03_description_fonctionnelle.md#politiques-de-rétention)
- Politiques → [Manuel Utilisateur](02_manuel_utilisateur.md#politiques-de-rétention)

## S

**Sauvegardes**
- Types → [Introduction](01_introduction.md#types-de-sauvegardes-supportées)
- Configuration → [Manuel Utilisateur](02_manuel_utilisateur.md#configuration-des-sauvegardes-distantes)
- Exécution → [Manuel Utilisateur](02_manuel_utilisateur.md#exécution-réelle)
- Gestion → [Manuel Utilisateur](02_manuel_utilisateur.md#gestion-des-sauvegardes)

**Scripts**
- Scripts bash → [Description Fonctionnelle](03_description_fonctionnelle.md#module-core---scripts-bash)
- Script principal → [Description Fonctionnelle](03_description_fonctionnelle.md#sauvegardesh---script-principal)

**Sécurité**
- Bonnes pratiques → [Manuel Utilisateur](02_manuel_utilisateur.md#bonnes-pratiques)
- Configuration SSH → [Manuel Utilisateur](02_manuel_utilisateur.md#configuration-des-clés-ssh)
- Validation → [Description Fonctionnelle](03_description_fonctionnelle.md#fonctions-de-validation)

**SSH**
- Configuration → [Manuel Utilisateur](02_manuel_utilisateur.md#configuration-des-clés-ssh)
- Problèmes → [Diagnostics](04_diagnostics_bugs.md#problèmes-de-connexion-ssh)
- Test → [Manuel Utilisateur](02_manuel_utilisateur.md#test-des-connexions)

**SSHFS**
- Configuration → [Description Fonctionnelle](03_description_fonctionnelle.md#gestion-sshfs)
- Montage → [Description Fonctionnelle](03_description_fonctionnelle.md#monter_sshfs)
- Problèmes → [Diagnostics](04_diagnostics_bugs.md#problèmes-de-montage-sshfs)

## T

**Terminal**
- Terminal web → [Manuel Utilisateur](02_manuel_utilisateur.md#terminal-web-interactif)
- Fonctionnalités → [Description Fonctionnelle](03_description_fonctionnelle.md#terminalphp---terminal-interactif)
- Problèmes → [Diagnostics](04_diagnostics_bugs.md#terminal-web-non-fonctionnel)
- Session persistante ($_SESSION['terminal_cwd']) → [Description Fonctionnelle](03_description_fonctionnelle.md#session-persistante-terminal)
- Streaming temps réel via Server-Sent Events → [Description Fonctionnelle](03_description_fonctionnelle.md#streaming-sse)
- Interface terminal avancée → [Description Fonctionnelle](03_description_fonctionnelle.md#interface-terminal-avancee)
- Gestion commandes cd avec session → [Description Fonctionnelle](03_description_fonctionnelle.md#gestion-cd-session)
- Historique commandes et navigation flèches → [Description Fonctionnelle](03_description_fonctionnelle.md#historique-commandes)
- Auto-complétion et raccourcis (Tab, Ctrl+C) → [Description Fonctionnelle](03_description_fonctionnelle.md#auto-completion)

**Tests**
- Mode dry-run → [Manuel Utilisateur](02_manuel_utilisateur.md#exécution-en-mode-test)
- Tests de validation → [Diagnostics](04_diagnostics_bugs.md#tests-de-validation)
- Test de configuration → [Diagnostics](04_diagnostics_bugs.md#test-de-configuration)

## U

**Utilisation**
- Ligne de commande → [Manuel Utilisateur](02_manuel_utilisateur.md#utilisation-en-ligne-de-commande)
- Interface web → [Manuel Utilisateur](02_manuel_utilisateur.md#interface-web)

## V

**Validation**
- Fonctions de validation → [Description Fonctionnelle](03_description_fonctionnelle.md#fonctions-de-validation)
- Tests de validation → [Diagnostics](04_diagnostics_bugs.md#tests-de-validation)

**Variables**
- Variables de configuration → [Description Fonctionnelle](03_description_fonctionnelle.md#variables-globales)
- Conventions → [Glossaire](05_glossaire.md#conventions-de-nommage)

**Verrouillage**
- Mécanisme → [Description Fonctionnelle](03_description_fonctionnelle.md#mécanisme-de-verrouillage)
- Configuration → [Description Fonctionnelle](03_description_fonctionnelle.md#variables-globales)

## W

**Web Interface**
- Composants → [Description Fonctionnelle](03_description_fonctionnelle.md#module-web---interface-utilisateur)
- Utilisation → [Manuel Utilisateur](02_manuel_utilisateur.md#interface-web)

## Index par Fichiers

### Scripts Bash (4 fichiers)
- **config.sh** (version 2.5, 200+ lignes, 75+ variables) → [Description Fonctionnelle](03_description_fonctionnelle.md#variables-globales-de-configuration)
- **fonctions_erreur.sh** (version 6.6 Beta, 400+ lignes, 17 codes erreur) → [Description Fonctionnelle](03_description_fonctionnelle.md#fonctions-de-validation)
- **sauvegarde.sh** (version 6.5, 800+ lignes, script principal) → [Description Fonctionnelle](03_description_fonctionnelle.md#variables-globales-du-script)
- **setup-web.sh** (100+ lignes, installation multi-distributions) → [Manuel Utilisateur](02_manuel_utilisateur.md#processus-dinstallation)

### Interface Web (7 fichiers)
- **index.php** (300+ lignes, dashboard principal, métriques temps réel) → [Description Fonctionnelle](03_description_fonctionnelle.md#variables-globales-php)
- **manage.php** (250+ lignes, gestion sauvegardes personnalisées) → [Description Fonctionnelle](03_description_fonctionnelle.md#fonctions-de-gestion)
- **logs.php** (200+ lignes, visualisation logs avec statistiques) → [Description Fonctionnelle](03_description_fonctionnelle.md#fonctions-de-lecture)
- **terminal.php** (400+ lignes, terminal interactif, Server-Sent Events) → [Description Fonctionnelle](03_description_fonctionnelle.md#variables-de-session-php)
- **status.php** (50 lignes, API JSON temps réel) → [Description Fonctionnelle](03_description_fonctionnelle.md#variables-de-statut)
- **app.js** (500+ lignes, logique client, polling automatique) → [Description Fonctionnelle](03_description_fonctionnelle.md#variables-globales-javascript)
- **style.css** (800+ lignes, design responsive professionnel) → [Introduction](01_introduction.md#interface-web-phphtml-js)
- Architecture modulaire avec sidebar navigation → [Description Fonctionnelle](03_description_fonctionnelle.md#architecture-modulaire-web)
- Design moderne avec animations CSS → [Description Fonctionnelle](03_description_fonctionnelle.md#design-moderne-css)
- Gestion d'états temps réel (running, progress, duration) → [Description Fonctionnelle](03_description_fonctionnelle.md#etats-temps-reel)

### Configuration (2 fichiers)
- **default_backups.conf** (format nom=1/0, 5 sauvegardes par défaut) → [Manuel Utilisateur](02_manuel_utilisateur.md#activation-désactivation)
- **sauvegardes_custom.conf** (format # SAUVEGARDE: nom, génération automatique) → [Manuel Utilisateur](02_manuel_utilisateur.md#génération-automatique-des-variables)

### Fichiers de Statut Temporaires (7 fichiers)
- **/tmp/backup_running.flag** (indicateur d'exécution active) → [Glossaire](05_glossaire.md#fichiers-de-statut-temporaires)
- **/tmp/current_backup.txt** (nom sauvegarde en cours) → [Description Fonctionnelle](03_description_fonctionnelle.md#variables-de-statut)
- **/tmp/backup_progress.txt** (progression 0-100%) → [Description Fonctionnelle](03_description_fonctionnelle.md#variables-de-statut)
- **/tmp/backup_start_time.txt** (heure de début) → [Description Fonctionnelle](03_description_fonctionnelle.md#variables-de-statut)
- **/tmp/last_success.txt** et **/tmp/last_error.txt** (dernières exécutions) → [Description Fonctionnelle](03_description_fonctionnelle.md#variables-de-statut)
- **/tmp/backup_fallback_errors.log** (logs d'erreurs critiques) → [Description Fonctionnelle](03_description_fonctionnelle.md#logs-fallback)
- **/tmp/sauvegardes_active.conf** (sauvegardes personnalisées actives) → [Description Fonctionnelle](03_description_fonctionnelle.md#sauvegardes-actives)
- API temps réel via status.php pour lecture de ces fichiers → [Description Fonctionnelle](03_description_fonctionnelle.md#api-lecture-statut)

## Index par Fonctionnalités

### Fonctionnalités Principales
- **Sauvegarde incrémentale** (rsync --link-dest, hardlinks) → [Introduction](01_introduction.md#automatisation-des-sauvegardes)
- **Interface web moderne** (7 fichiers, design responsive) → [Introduction](01_introduction.md#interface-web-phphtml-js)
- **Terminal interactif** (Server-Sent Events, session persistante) → [Manuel Utilisateur](02_manuel_utilisateur.md#streaming-temps-réel)
- **Gestion d'erreurs avancée** (17 codes + actions suggérées) → [Diagnostics](04_diagnostics_bugs.md#codes-derreur-système-17-codes-spécifiques)

### Fonctionnalités Avancées
- **Mode dry-run** (simulation sans écriture, variable DRY_RUN) → [Manuel Utilisateur](02_manuel_utilisateur.md#exécution-en-mode-test)
- **Hooks personnalisés** (PRE/POST_SAUVEGARDE_GLOBAL) → [Description Fonctionnelle](03_description_fonctionnelle.md#variables-de-hooks)
- **Surveillance temps réel** (polling 2s, 5 métriques) → [Manuel Utilisateur](02_manuel_utilisateur.md#métriques-principales)
- **Configuration adaptative** (détection www-data, chemins configurables) → [Description Fonctionnelle](03_description_fonctionnelle.md#configure_web_environment)
- **Sauvegardes personnalisées** (génération automatique variables) → [Manuel Utilisateur](02_manuel_utilisateur.md#génération-automatique-des-variables)
- **Multi-distributions** (Debian/Ubuntu/RHEL/CentOS/Fedora) → [Manuel Utilisateur](02_manuel_utilisateur.md#processus-dinstallation)
- **Terminal web complet** (session persistante, historique, auto-complétion) → [Description Fonctionnelle](03_description_fonctionnelle.md#terminal-web-avance)
- **Gestion d'erreurs avancée** (17 codes + actions suggérées) → [Description Fonctionnelle](03_description_fonctionnelle.md#gestion-erreurs-avancee)
- **Interface responsive moderne** (design professionnel) → [Description Fonctionnelle](03_description_fonctionnelle.md#interface-moderne)
- **Exécution réelle via interface web** (pas seulement simulation) → [Description Fonctionnelle](03_description_fonctionnelle.md#execution-reelle-web)
- **Validation robuste** (6 types de validation, chemins, IP, ports) → [Description Fonctionnelle](03_description_fonctionnelle.md#validation-robuste)
- **Logs avancés** (rotation, compression, fallback) → [Description Fonctionnelle](03_description_fonctionnelle.md#logs-avances)

## Index par Problèmes Courants

### Problèmes d'Installation
- **Dépendances manquantes** (code erreur 127) → [Diagnostics](04_diagnostics_bugs.md#bug-001--variables-non-définies-code-derreur-2)
- **Permissions incorrectes** (www-data, groupe fuse) → [Diagnostics](04_diagnostics_bugs.md#bug-009--interface-web-inaccessible)
- **Configuration Apache** (PHP disable_functions) → [Diagnostics](04_diagnostics_bugs.md#bug-010--terminal-web-non-fonctionnel)
- **Variables non définies** (config.sh, ordre de chargement) → [Diagnostics](04_diagnostics_bugs.md#bug-001--variables-non-définies-code-derreur-2)

### Problèmes d'Exécution
- **Connexion SSH** (codes erreur 5, 17, timeout, authentification) → [Diagnostics](04_diagnostics_bugs.md#bug-003--authentification-ssh-échouée)
- **Montage SSHFS** (codes erreur 6, 7, 8, groupe fuse, points montage) → [Diagnostics](04_diagnostics_bugs.md#bug-005--échec-de-montage-sshfs-code-derreur-7)
- **Erreurs rsync** (code erreur 9, codes rsync 23, timeout) → [Diagnostics](04_diagnostics_bugs.md#problèmes-rsync)
- **Performance** (sauvegardes lentes, consommation mémoire) → [Diagnostics](04_diagnostics_bugs.md#problèmes-de-performance)
- **Espace disque** (codes erreur 4, 11, surveillance) → [Diagnostics](04_diagnostics_bugs.md#codes-derreur-système-17-codes-spécifiques)
- **Verrouillage** (code erreur 10, fichier PID, processus actif) → [Diagnostics](04_diagnostics_bugs.md#codes-derreur-système-17-codes-spécifiques)

## Index par Variables Importantes

### Variables de Configuration Critiques
- **DEST_BASE_SAUVEGARDES** (répertoire racine) → [Description Fonctionnelle](03_description_fonctionnelle.md#variables-système-principales)
- **EMAIL_NOTIFICATION** (rapports automatiques) → [Description Fonctionnelle](03_description_fonctionnelle.md#variables-système-principales)
- **LOG_DIR** (répertoire logs) → [Description Fonctionnelle](03_description_fonctionnelle.md#variables-système-principales)
- **DEFAULT_RSYNC_OPTIONS** (options par défaut) → [Description Fonctionnelle](03_description_fonctionnelle.md#variables-de-comportement-rsync)
- **DEFAULT_TYPE_CONNEXION_DISTANTE** (mode SSHFS/SSH) → [Description Fonctionnelle](03_description_fonctionnelle.md#variables-de-connexion-distante)
- **CHEMIN_RSYNC, CHEMIN_SSH, CHEMIN_SSHFS** (chemins exécutables) → [Description Fonctionnelle](03_description_fonctionnelle.md#chemins-executables)
- **SCRIPT_PRE_SAUVEGARDE_GLOBAL, SCRIPT_POST_SAUVEGARDE_GLOBAL** (hooks) → [Description Fonctionnelle](03_description_fonctionnelle.md#hooks-globaux)
- **TAILLE_MAX_LOG_MO, JOURS_RETENTION_LOGS** (gestion logs) → [Description Fonctionnelle](03_description_fonctionnelle.md#gestion-logs-avancee)
- **OPTIONS_COMMUNES_SSH, StrictHostKeyChecking_SSH** (SSH avancé) → [Description Fonctionnelle](03_description_fonctionnelle.md#ssh-avance)

### Variables de Timeout et Performance
- **DELAI_CONNEXION_SSH_SECONDES** (timeout SSH) → [Description Fonctionnelle](03_description_fonctionnelle.md#variables-de-timeout-et-connexion)
- **DELAI_OPERATION_RSYNC_SECONDES** (timeout rsync) → [Description Fonctionnelle](03_description_fonctionnelle.md#variables-de-timeout-et-connexion)
- **ESPACE_DISQUE_MIN_GO** (espace minimum) → [Description Fonctionnelle](03_description_fonctionnelle.md#variables-système-principales)

### Variables de Sécurité et Contrôle
- **ACTIVERLOCK** (verrouillage) → [Description Fonctionnelle](03_description_fonctionnelle.md#variables-de-sécurité-et-contrôle)
- **RSYNC_DELETE** (option --delete) → [Description Fonctionnelle](03_description_fonctionnelle.md#variables-de-comportement-rsync)
- **StrictHostKeyChecking_SSH** (vérification clés) → [Description Fonctionnelle](03_description_fonctionnelle.md#variables-ssh-avancées)

## Index par Fonctions Importantes

### Fonctions Bash Principales
- **effectuer_sauvegarde()** (fonction centrale, 8 paramètres) → [Description Fonctionnelle](03_description_fonctionnelle.md#effectuer_sauvegarde---fonction-centrale)
- **configure_web_environment()** (adaptation www-data) → [Description Fonctionnelle](03_description_fonctionnelle.md#configure_web_environment)
- **diagnostiquer_et_logger_erreur()** (17 codes erreur) → [Description Fonctionnelle](03_description_fonctionnelle.md#diagnostiquer_et_logger_erreur)
- **monter_sshfs()** et **demonter_sshfs()** (gestion SSHFS) → [Description Fonctionnelle](03_description_fonctionnelle.md#gestion-sshfs)
- **valider_variable()** (6 types de validation) → [Description Fonctionnelle](03_description_fonctionnelle.md#valider_variable)

### Fonctions PHP Principales
- **getSauvegardes()** (lecture configurations) → [Description Fonctionnelle](03_description_fonctionnelle.md#getsauvegardes)
- **executerSauvegarde()** (exécution via shell_exec) → [Description Fonctionnelle](03_description_fonctionnelle.md#executersauvegarde)
- **ajouterSauvegarde()** (génération variables) → [Description Fonctionnelle](03_description_fonctionnelle.md#ajoutersauvegarde)
- **readConfigValue()** (lecture config.sh depuis PHP) → [Description Fonctionnelle](03_description_fonctionnelle.md#read-config-value)
- **isDefaultBackupEnabled()** (vérification activation) → [Description Fonctionnelle](03_description_fonctionnelle.md#is-default-backup-enabled)
- **toggleDefaultBackup()** (activation/désactivation) → [Description Fonctionnelle](03_description_fonctionnelle.md#toggle-default-backup)
- **getSauvegardesCustom()** (sauvegardes personnalisées) → [Description Fonctionnelle](03_description_fonctionnelle.md#get-sauvegardes-custom)
- **supprimerSauvegarde()** (suppression sauvegarde) → [Description Fonctionnelle](03_description_fonctionnelle.md#supprimer-sauvegarde)
- **calculateLogStats()** (statistiques logs) → [Description Fonctionnelle](03_description_fonctionnelle.md#calculate-log-stats)

### Fonctions JavaScript Principales
- **executeRealBackup()** (exécution asynchrone) → [Description Fonctionnelle](03_description_fonctionnelle.md#executerealbackup)
- **updateStatus()** (polling temps réel) → [Description Fonctionnelle](03_description_fonctionnelle.md#updatestatus)
- **updateDashboard()** (mise à jour interface) → [Description Fonctionnelle](03_description_fonctionnelle.md#updatedashboard)
- **runBackup()** (fonction principale exécution) → [Description Fonctionnelle](03_description_fonctionnelle.md#run-backup)
- **simulateBackupExecution()** (simulation réaliste) → [Description Fonctionnelle](03_description_fonctionnelle.md#simulate-backup-execution)
- **showMessage()** (affichage messages utilisateur) → [Description Fonctionnelle](03_description_fonctionnelle.md#show-message)
- **addToConsole()** (ajout logs console) → [Description Fonctionnelle](03_description_fonctionnelle.md#add-to-console)
- **startStatusPolling()** (démarrage polling) → [Description Fonctionnelle](03_description_fonctionnelle.md#start-status-polling)
- **loadCustomBackups()** (chargement sauvegardes custom) → [Description Fonctionnelle](03_description_fonctionnelle.md#load-custom-backups)
- **downloadLogs()** et **exportConfig()** (export données) → [Description Fonctionnelle](03_description_fonctionnelle.md#export-functions)


## Index par Innovations Techniques

### Innovations Majeures du Projet

#### Architecture Hybride Moderne
- **Monolithe modulaire** avec séparation claire Bash/PHP/JS → [Description Fonctionnelle](03_description_fonctionnelle.md#architecture-hybride)
- **Configuration adaptative** selon environnement (CLI/Web) → [Description Fonctionnelle](03_description_fonctionnelle.md#configuration-adaptative)
- **Exécution réelle** via interface web (pas simulation) → [Description Fonctionnelle](03_description_fonctionnelle.md#execution-reelle-web)

#### Terminal Web Avancé
- **Session persistante** avec navigation répertoires → [Description Fonctionnelle](03_description_fonctionnelle.md#session-persistante)
- **Streaming temps réel** via Server-Sent Events → [Description Fonctionnelle](03_description_fonctionnelle.md#streaming-sse)
- **Interface terminal avancée** avec historique et auto-complétion → [Description Fonctionnelle](03_description_fonctionnelle.md#interface-terminal-avancee)

#### Gestion d'Erreurs Professionnelle
- **17 codes d'erreur spécifiques** avec actions suggérées → [Description Fonctionnelle](03_description_fonctionnelle.md#codes-erreur-specifiques)
- **Logs de fallback** même si logs désactivés → [Description Fonctionnelle](03_description_fonctionnelle.md#logs-fallback)
- **Diagnostic automatique** intégré → [Description Fonctionnelle](03_description_fonctionnelle.md#diagnostic-automatique)

#### Extensibilité et Hooks
- **Hooks PRE/POST globaux** pour scripts personnalisés → [Description Fonctionnelle](03_description_fonctionnelle.md#hooks-globaux)
- **Chemins exécutables configurables** pour portabilité → [Description Fonctionnelle](03_description_fonctionnelle.md#chemins-configurables)
- **Sauvegardes personnalisées** avec génération automatique → [Description Fonctionnelle](03_description_fonctionnelle.md#sauvegardes-personnalisees)

#### Interface Utilisateur Moderne
- **Design responsive** type dashboard professionnel → [Description Fonctionnelle](03_description_fonctionnelle.md#design-responsive)
- **Métriques temps réel** avec polling automatique → [Description Fonctionnelle](03_description_fonctionnelle.md#metriques-temps-reel)
- **Actions en un clic** (activation/désactivation, test, exécution) → [Description Fonctionnelle](03_description_fonctionnelle.md#actions-un-clic)

### Comparaison avec Solutions Existantes

#### Avantages Uniques
- **Pas de base de données** requise (fichiers plats) → [Description Fonctionnelle](03_description_fonctionnelle.md#sans-bdd)
- **Installation en une commande** multi-distributions → [Description Fonctionnelle](03_description_fonctionnelle.md#installation-simple)
- **Double interface** (CLI + Web) avec même backend → [Description Fonctionnelle](03_description_fonctionnelle.md#double-interface)
- **Documentation exhaustive** générée automatiquement → [Description Fonctionnelle](03_description_fonctionnelle.md#documentation-auto)

#### Technologies Innovantes Utilisées
- **Server-Sent Events** pour streaming temps réel → [Description Fonctionnelle](03_description_fonctionnelle.md#sse-innovation)
- **Bash moderne** avec gestion d'erreurs avancée → [Description Fonctionnelle](03_description_fonctionnelle.md#bash-moderne)
- **CSS Grid/Flexbox** pour interface responsive → [Description Fonctionnelle](03_description_fonctionnelle.md#css-moderne)
- **JavaScript async/await** pour exécution asynchrone → [Description Fonctionnelle](03_description_fonctionnelle.md#js-moderne)

## Index par Complexité Technique

### Niveau Débutant
- **Installation automatique** (setup-web.sh) → [Manuel Utilisateur](02_manuel_utilisateur.md#installation-automatique)
- **Sauvegardes par défaut** (5 préconfigurées) → [Manuel Utilisateur](02_manuel_utilisateur.md#sauvegardes-par-defaut)
- **Interface web simple** (point-and-click) → [Manuel Utilisateur](02_manuel_utilisateur.md#interface-web)

### Niveau Intermédiaire
- **Configuration personnalisée** (config.sh) → [Description Fonctionnelle](03_description_fonctionnelle.md#configuration-personnalisee)
- **Sauvegardes personnalisées** (via interface) → [Manuel Utilisateur](02_manuel_utilisateur.md#sauvegardes-personnalisees)
- **Gestion des logs** (consultation, statistiques) → [Manuel Utilisateur](02_manuel_utilisateur.md#gestion-logs)

### Niveau Avancé
- **Hooks personnalisés** (scripts PRE/POST) → [Description Fonctionnelle](03_description_fonctionnelle.md#hooks-personnalises)
- **Modification du code source** → [Description Fonctionnelle](03_description_fonctionnelle.md#modification-code)
- **Intégration avec autres systèmes** → [Description Fonctionnelle](03_description_fonctionnelle.md#integration-systemes)

### Niveau Expert
- **Développement de nouvelles fonctionnalités** → [Description Fonctionnelle](03_description_fonctionnelle.md#developpement-fonctionnalites)
- **Optimisation des performances** → [Diagnostics](04_diagnostics_bugs.md#optimisation-performances)
- **Sécurisation avancée** → [Diagnostics](04_diagnostics_bugs.md#securisation-avancee)

## Index par Cas d'Usage

### Particuliers
- **Sauvegarde documents personnels** → [Manuel Utilisateur](02_manuel_utilisateur.md#documents-personnels)
- **Sauvegarde photos/vidéos** → [Manuel Utilisateur](02_manuel_utilisateur.md#photos-videos)
- **Sauvegarde vers NAS domestique** → [Manuel Utilisateur](02_manuel_utilisateur.md#nas-domestique)

### Petites Entreprises
- **Sauvegarde serveurs multiples** → [Manuel Utilisateur](02_manuel_utilisateur.md#serveurs-multiples)
- **Sauvegarde bases de données** → [Manuel Utilisateur](02_manuel_utilisateur.md#bases-donnees)
- **Rapports automatiques par email** → [Manuel Utilisateur](02_manuel_utilisateur.md#rapports-email)

### Entreprises
- **Intégration avec infrastructure existante** → [Description Fonctionnelle](03_description_fonctionnelle.md#integration-infrastructure)
- **Surveillance et monitoring** → [Diagnostics](04_diagnostics_bugs.md#surveillance-monitoring)
- **Conformité et audit** → [Diagnostics](04_diagnostics_bugs.md#conformite-audit)

### Développeurs
- **Sauvegarde projets de développement** → [Manuel Utilisateur](02_manuel_utilisateur.md#projets-developpement)
- **Intégration CI/CD** → [Description Fonctionnelle](03_description_fonctionnelle.md#integration-cicd)
- **Personnalisation et extension** → [Description Fonctionnelle](03_description_fonctionnelle.md#personnalisation-extension)

## Index Alphabétique Complet

### A-C
- ACTIVERLOCK → [Glossaire](05_glossaire.md#activerlock)
- Administration système → [Index](#administration-système)
- API et interfaces → [Index](#api-et-interfaces)
- app.js → [Index](#appjs---logique-client)
- Architecture → [Index](#architecture)
- Authentification → [Index](#authentification)
- Backup → [Index](#backup-sauvegarde)
- Bash scripts → [Index](#bash-scripts-4-fichiers)
- Bugs et erreurs → [Index](#bugs-et-erreurs)
- Configuration → [Index](#configuration)
- Connexions distantes → [Index](#connexions-distantes)
- Cron et programmation → [Index](#cron-et-programmation)

### D-F
- Dashboard → [Index](#dashboard-indexphp)
- default_backups.conf → [Index](#default_backupsconf)
- Diagnostic → [Index](#diagnostic)
- Dry-run → [Index](#dry-run)
- Erreurs → [Index](#erreurs)
- EMAIL_NOTIFICATION → [Index](#email_notification)
- Espace disque → [Index](#espace-disque)
- Exécution → [Index](#exécution)
- Fichiers de configuration → [Index](#fichiers-de-configuration)
- Fonctions → [Index](#fonctions)

### G-L
- Gestion → [Index](#gestion)
- Hooks → [Index](#hooks)
- Installation → [Index](#installation)
- Interface web → [Index](#interface-web)
- JavaScript → [Index](#javascript)
- Journalisation → [Index](#journalisation)
- Ligne de commande → [Index](#ligne-de-commande)
- Logs → [Index](#logs)

### M-R
- Maintenance → [Index](#maintenance)
- Montage SSHFS → [Index](#montage-sshfs)
- Notifications → [Index](#notifications)
- Options → [Index](#options)
- Performance → [Index](#performance)
- Permissions → [Index](#permissions)
- PHP → [Index](#php)
- Programmation → [Index](#programmation)
- Rsync → [Index](#rsync)
- Rétention → [Index](#rétention)

### S-Z
- Sauvegardes → [Index](#sauvegardes)
- Scripts → [Index](#scripts)
- Sécurité → [Index](#sécurité)
- SSH → [Index](#ssh)
- SSHFS → [Index](#sshfs)
- Terminal → [Index](#terminal)
- Tests → [Index](#tests)
- Utilisation → [Index](#utilisation)
- Validation → [Index](#validation)
- Variables → [Index](#variables)
- Verrouillage → [Index](#verrouillage)
- Web Interface → [Index](#web-interface)