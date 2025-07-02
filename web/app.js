// Application JavaScript pour l'interface de gestion des sauvegardes

// Configuration
const SCRIPT_PATH = '../sauvegarde.sh';

// État de l'application
let isRunning = false;
let customBackups = [];

// NOUVELLE FONCTION: Exécution réelle via PHP
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
            // Extraire le résultat de la réponse HTML
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

// Fonctions utilitaires
function showMessage(text, type = 'info') {
    const messageArea = document.getElementById('message-area');
    const alertClass = type === 'error' ? 'error' : 'success';
    messageArea.innerHTML = `<div class="alert ${alertClass}">${text}</div>`;
    
    // Auto-hide après 5 secondes
    setTimeout(() => {
        messageArea.innerHTML = '';
    }, 5000);
}

function addToConsole(text) {
    const console = document.getElementById('console');
    if (!console) return; // Élément peut ne pas exister sur toutes les pages
    
    const timestamp = new Date().toLocaleTimeString('fr-FR');
    console.innerHTML += `<div class="console-line"><span class="timestamp">[${timestamp}]</span> ${text}</div>`;
    console.scrollTop = console.scrollHeight;
}

function clearConsole() {
    document.getElementById('console').innerHTML = '<p>Console effacée...</p>';
}

// Fonction principale pour exécuter une sauvegarde
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
        // EXÉCUTION RÉELLE via PHP
        addToConsole(`⏳ Exécution du script de sauvegarde...`);
        const result = await executeRealBackup(selection, dryRun);
        
        // Afficher le résultat ligne par ligne
        const lines = result.split('\n');
        lines.forEach(line => {
            if (line.trim()) {
                addToConsole(line);
            }
        });
        
        addToConsole(`✅ Sauvegarde "${selection}" terminée avec succès`);
        showMessage(`Sauvegarde "${selection}" terminée avec succès !`, 'success');
        
        // Mettre à jour la dernière exécution si l'élément existe
        const lastRunElement = document.getElementById('last-run');
        if (lastRunElement) {
            lastRunElement.textContent = new Date().toLocaleString('fr-FR');
        }
        
    } catch (error) {
        addToConsole(`❌ Erreur lors de la sauvegarde "${selection}": ${error.message}`);
        showMessage(`Erreur lors de la sauvegarde "${selection}": ${error.message}`, 'error');
    } finally {
        isRunning = false;
    }
}

// Simulation de l'exécution d'une sauvegarde
async function simulateBackupExecution(selection, dryRun) {
    const steps = [
        'Vérification des permissions...',
        'Chargement de la configuration...',
        'Validation des chemins source et destination...',
    ];
    
    if (selection.includes('vm') || selection.includes('serveur') || selection.includes('portable')) {
        steps.push('Vérification de la connexion SSH...');
        steps.push('Montage SSHFS...');
    }
    
    steps.push('Exécution de rsync...');
    
    if (!dryRun) {
        steps.push('Mise à jour des liens symboliques...');
        steps.push('Nettoyage des anciennes sauvegardes...');
    }
    
    steps.push('Génération du rapport...');
    
    for (let i = 0; i < steps.length; i++) {
        addToConsole(`⏳ ${steps[i]}`);
        await new Promise(resolve => setTimeout(resolve, 500 + Math.random() * 1000));
        
        // Simulation d'une erreur occasionnelle
        if (Math.random() < 0.05) {
            throw new Error('Erreur simulée de connexion réseau');
        }
    }
    
    if (selection === 'all') {
        addToConsole('📊 Résumé: 5 sauvegardes réussies, 0 échouée');
    } else {
        addToConsole(`📊 Sauvegarde "${selection}" : ${dryRun ? 'simulation' : 'exécution'} réussie`);
    }
}

// Gestion des sauvegardes personnalisées
function loadCustomBackups() {
    // Dans un environnement réel, ceci ferait un appel AJAX
    // Pour la démo, on simule quelques sauvegardes personnalisées
    customBackups = [
        { name: 'photos_perso', type: 'locale' },
        { name: 'backup_serveur2', type: 'distante' }
    ];
    
    updateBackupList();
}

function updateBackupList() {
    const backupList = document.getElementById('backup-list');
    if (!backupList) return;
    
    // Ajouter les sauvegardes personnalisées à la liste
    customBackups.forEach(backup => {
        const backupItem = document.createElement('div');
        backupItem.className = 'backup-item';
        backupItem.innerHTML = `
            <span class="backup-name">${backup.name}</span>
            <span class="backup-type">Custom</span>
            <button onclick="runBackup('${backup.name}')" class="btn btn-primary">Exécuter</button>
            <button onclick="runBackup('${backup.name}', true)" class="btn btn-secondary">Test</button>
        `;
        backupList.appendChild(backupItem);
    });
    
    // Mettre à jour le compteur
    const totalBackups = 5 + customBackups.length;
    const countElement = document.getElementById('backup-count');
    if (countElement) {
        countElement.textContent = totalBackups;
    }
}

// Fonction pour télécharger les logs
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

// Fonction pour exporter la configuration
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

// Polling temps réel du statut
let statusInterval;

function startStatusPolling() {
    statusInterval = setInterval(updateStatus, 2000); // Toutes les 2 secondes
    updateStatus(); // Première mise à jour immédiate
}

function stopStatusPolling() {
    if (statusInterval) {
        clearInterval(statusInterval);
    }
}

async function updateStatus() {
    try {
        const response = await fetch('status.php');
        const status = await response.json();
        
        // Mettre à jour le dashboard
        updateDashboard(status);
        
    } catch (error) {
        console.error('Erreur lors de la mise à jour du statut:', error);
    }
}

function updateDashboard(status) {
    // Statut principal
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
    
    // Sauvegarde en cours
    const currentElement = document.getElementById('current-backup');
    if (currentElement) {
        currentElement.textContent = status.current_backup || 'Aucune sauvegarde';
    }
    
    // Progression
    const progressFill = document.getElementById('progress-fill');
    const progressMetric = document.getElementById('progress-metric');
    if (progressFill && progressMetric) {
        const progress = parseInt(status.progress) || 0;
        progressFill.style.width = progress + '%';
        progressMetric.textContent = progress + '%';
    }
    
    // Durée
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
    
    // Logs en direct
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

// Initialisation de l'application
document.addEventListener('DOMContentLoaded', function() {
    // Charger les sauvegardes personnalisées
    loadCustomBackups();
    
    // Démarrer le polling de statut
    startStatusPolling();
    
    // Ajouter des raccourcis clavier
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey) {
            switch(e.key) {
                case 'r':
                    e.preventDefault();
                    runBackup('all');
                    break;
                case 'l':
                    e.preventDefault();
                    clearConsole();
                    break;
            }
        }
    });
    
    // Message de bienvenue
    addToConsole('🎯 Interface de gestion des sauvegardes initialisée');
    addToConsole('💡 Raccourcis: Ctrl+R (exécuter tout), Ctrl+L (effacer console)');
});

// Arrêter le polling quand on quitte la page
window.addEventListener('beforeunload', stopStatusPolling);

// Fonctions pour la gestion des erreurs
window.addEventListener('error', function(e) {
    addToConsole(`❌ Erreur JavaScript: ${e.message}`);
    showMessage('Une erreur inattendue s\'est produite', 'error');
});

// Export des fonctions pour utilisation globale
window.runBackup = runBackup;
window.clearConsole = clearConsole;
window.loadCustomBackups = loadCustomBackups;
window.downloadLogs = downloadLogs;
window.exportConfig = exportConfig;