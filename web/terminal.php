<?php
session_start();

// Terminal interactif avec session persistante
if (!isset($_SESSION['terminal_cwd'])) {
    $_SESSION['terminal_cwd'] = dirname(__DIR__);
}

// WebSocket simulation pour terminal temps réel
if (isset($_GET['stream'])) {
    header('Content-Type: text/event-stream');
    header('Cache-Control: no-cache');
    
    if (isset($_POST['cmd'])) {
        $command = trim($_POST['cmd']);
        
        if ($command === 'clear') {
            echo "data: {\"type\":\"clear\"}\n\n";
            exit;
        }
        
        // Gestion cd avec session
        if (strpos($command, 'cd ') === 0) {
            $newDir = trim(substr($command, 3)) ?: getenv('HOME');
            $fullPath = realpath($_SESSION['terminal_cwd'] . '/' . $newDir);
            if ($fullPath && is_dir($fullPath)) {
                $_SESSION['terminal_cwd'] = $fullPath;
            }
            echo "data: {\"type\":\"output\",\"data\":\"" . $_SESSION['terminal_cwd'] . "\n\",\"cwd\":\"" . $_SESSION['terminal_cwd'] . "\"}\n\n";
            exit;
        }
        
        // Exécution avec bash interactif
        chdir($_SESSION['terminal_cwd']);
        $process = popen($command . ' 2>&1', 'r');
        
        while (!feof($process)) {
            $line = fgets($process);
            if ($line) {
                echo "data: " . json_encode([
                    'type' => 'output',
                    'data' => $line,
                    'cwd' => $_SESSION['terminal_cwd']
                ]) . "\n\n";
                flush();
            }
        }
        pclose($process);
    }
    exit;
}

// API fallback
if ($_POST && isset($_POST['command'])) {
    header('Content-Type: application/json');
    $command = trim($_POST['command']);
    chdir($_SESSION['terminal_cwd']);
    
    if (strpos($command, 'cd ') === 0) {
        $newDir = trim(substr($command, 3)) ?: getenv('HOME');
        if (is_dir($newDir)) $_SESSION['terminal_cwd'] = realpath($newDir);
        echo json_encode(['output' => $_SESSION['terminal_cwd'], 'cwd' => $_SESSION['terminal_cwd']]);
        exit;
    }
    
    exec($command . ' 2>&1', $output, $code);
    echo json_encode(['output' => implode("\n", $output), 'cwd' => $_SESSION['terminal_cwd']]);
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Backup Manager - Terminal</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .terminal-container {
            background: #1a1a1a;
            border-radius: 8px;
            padding: 0;
            margin: 20px 0;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }
        
        .terminal-header {
            background: #2d2d2d;
            padding: 10px 15px;
            border-radius: 8px 8px 0 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .terminal-button {
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }
        
        .terminal-button.close { background: #ff5f57; }
        .terminal-button.minimize { background: #ffbd2e; }
        .terminal-button.maximize { background: #28ca42; }
        
        .terminal-title {
            color: #fff;
            font-size: 14px;
            margin-left: 10px;
        }
        
        .terminal-body {
            height: 500px;
            overflow-y: auto;
            padding: 15px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            line-height: 1.4;
        }
        
        .terminal-line {
            margin-bottom: 5px;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        
        .terminal-prompt {
            color: #00ff00;
        }
        
        .terminal-command {
            color: #ffffff;
        }
        
        .terminal-output {
            color: #cccccc;
        }
        
        .terminal-error {
            color: #ff6b6b;
        }
        
        .terminal-input-container {
            display: flex;
            align-items: center;
            padding: 10px 15px;
            background: #1a1a1a;
            border-radius: 0 0 8px 8px;
        }
        
        .terminal-prompt-symbol {
            color: #00ff00;
            margin-right: 8px;
            font-family: 'Courier New', monospace;
        }
        
        .terminal-input {
            flex: 1;
            background: transparent;
            border: none;
            color: #ffffff;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            outline: none;
        }
        
        .terminal-input::placeholder {
            color: #666;
        }
        
        .command-suggestions {
            background: #2d2d2d;
            border-radius: 4px;
            padding: 10px;
            margin: 10px 0;
        }
        
        .command-suggestions h4 {
            color: #ffffff;
            margin: 0 0 10px 0;
            font-size: 14px;
        }
        
        .command-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 5px;
        }
        
        .command-item {
            color: #333333;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            cursor: pointer;
            padding: 2px 5px;
            border-radius: 3px;
            transition: background 0.2s;
        }
        
        .command-item:hover {
            background: #404040;
            color: #ffffff;
        }
        
        .terminal-controls {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="app-layout">
        <nav class="sidebar">
            <div class="sidebar-header">
                <h2>🛡️ Backup Manager</h2>
            </div>
            <ul class="nav-menu">
                <li><a href="index.php"><span class="icon">📊</span>Dashboard</a></li>
                <li><a href="manage.php"><span class="icon">⚙️</span>Sauvegardes</a></li>
                <li><a href="logs.php"><span class="icon">📋</span>Logs</a></li>
                <li><a href="terminal.php" class="active"><span class="icon">💻</span>Terminal</a></li>
            </ul>
        </nav>
        
        <main class="main-content">
            <header class="page-header">
                <h1>Terminal Web</h1>
                <div class="terminal-controls">
                    <button class="btn btn-secondary" onclick="clearTerminal()">Effacer</button>
                    <button class="btn btn-primary" onclick="showHelp()">Aide</button>
                </div>
            </header>
            
            <div class="content-area">
                <div class="card">
                    <div class="card-header">
                        <h3>Terminal Interactif - Session Persistante</h3>
                    </div>
                    <div class="card-body">
                        <div class="command-suggestions">
                            <h4>Terminal Complet - Toutes commandes disponibles :</h4>
                            <div class="command-list">
                                <div class="command-item" onclick="executeQuickCommand('./sauvegarde.sh --list')">./sauvegarde.sh --list</div>
                                <div class="command-item" onclick="executeQuickCommand('./sauvegarde.sh --help')">./sauvegarde.sh --help</div>
                                <div class="command-item" onclick="executeQuickCommand('./sauvegarde.sh all')">./sauvegarde.sh all</div>
                                <div class="command-item" onclick="executeQuickCommand('./sauvegarde.sh --dry-run all')">./sauvegarde.sh --dry-run all</div>


                                <div class="command-item" onclick="executeQuickCommand('ls -la')">ls -la</div>
                                <div class="command-item" onclick="executeQuickCommand('htop')">htop</div>
                                <div class="command-item" onclick="executeQuickCommand('systemctl status apache2')">systemctl status apache2</div>
                                <div class="command-item" onclick="executeQuickCommand('journalctl -u apache2 -f')">journalctl apache2</div>
                                <div class="command-item" onclick="executeQuickCommand('ss -tulpn')">ss -tulpn</div>
                                <div class="command-item" onclick="executeQuickCommand('vim config.sh')">vim config.sh</div>
                                <div class="command-item" onclick="executeQuickCommand('nano config.sh')">nano config.sh</div>
                                <div class="command-item" onclick="executeQuickCommand('crontab -l')">crontab -l</div>
                            </div>
                        </div>
                        
                        <div class="terminal-container">
                            <div class="terminal-header">
                                <div class="terminal-button close"></div>
                                <div class="terminal-button minimize"></div>
                                <div class="terminal-button maximize"></div>
                                <div class="terminal-title">backup-manager-web</div>
                            </div>
                            <div class="terminal-body" id="terminal-output">
                                <div class="terminal-line">
                                    <span class="terminal-output">🚀 Terminal Interactif - Session Persistante</span>
                                </div>

                                <div class="terminal-line">
                                    <span class="terminal-output">💡 Ctrl+C pour interrompre, Tab pour auto-complétion</span>
                                </div>
                                <div class="terminal-line">
                                    <span class="terminal-output">Répertoire: <?= $_SESSION['terminal_cwd'] ?? dirname(__DIR__) ?></span>
                                </div>
                            </div>
                            <div class="terminal-input-container">
                                <span class="terminal-prompt-symbol">www-data@<?= basename(dirname(__DIR__)) ?>:~$</span>
                                <input type="text" class="terminal-input" id="command-input" 
                                       placeholder="" 
                                       autocomplete="off">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        let commandHistory = [];
        let historyIndex = -1;
        let currentCwd = '<?= $_SESSION['terminal_cwd'] ?? dirname(__DIR__) ?>';
        
        const terminalOutput = document.getElementById('terminal-output');
        const commandInput = document.getElementById('command-input');
        
        // Focus automatique sur l'input
        commandInput.focus();
        
        // Gestion des touches
        commandInput.addEventListener('keydown', function(e) {
            switch(e.key) {
                case 'Enter':
                    executeCommand();
                    break;
                case 'ArrowUp':
                    e.preventDefault();
                    navigateHistory(-1);
                    break;
                case 'ArrowDown':
                    e.preventDefault();
                    navigateHistory(1);
                    break;
                case 'Tab':
                    e.preventDefault();
                    autoComplete();
                    break;
                case 'c':
                    if (e.ctrlKey) {
                        e.preventDefault();
                        addToTerminal('^C', 'terminal-command');
                        updatePrompt();
                    }
                    break;
            }
        });
        
        function navigateHistory(direction) {
            if (commandHistory.length === 0) return;
            
            historyIndex += direction;
            
            if (historyIndex < 0) {
                historyIndex = 0;
            } else if (historyIndex >= commandHistory.length) {
                historyIndex = commandHistory.length - 1;
            }
            
            commandInput.value = commandHistory[historyIndex] || '';
        }
        
        async function executeCommand() {
            const command = commandInput.value.trim();
            if (!command) return;
            
            // Ajouter à l'historique
            commandHistory.unshift(command);
            if (commandHistory.length > 50) commandHistory.pop();
            historyIndex = -1;
            
            // Afficher la commande avec prompt réaliste
            const projectName = currentCwd.split('/').pop() || 'backup-manager';
            const shortCwd = currentCwd.replace(currentCwd.split('/').slice(0, -1).join('/'), '~');
            addToTerminal(`www-data@${projectName}:${shortCwd}$ ${command}`, 'terminal-prompt');
            commandInput.value = '';
            
            // Commandes intégrées
            if (command === 'help') { showHelp(); return; }
            if (command === 'clear') { clearTerminal(); return; }
            
            // Exécution temps réel via EventSource
            try {
                const formData = new FormData();
                formData.append('cmd', command);
                
                const response = await fetch('terminal.php?stream=1', {
                    method: 'POST',
                    body: formData
                });
                
                const reader = response.body.getReader();
                const decoder = new TextDecoder();
                
                while (true) {
                    const { done, value } = await reader.read();
                    if (done) break;
                    
                    const chunk = decoder.decode(value);
                    const lines = chunk.split('\n');
                    
                    for (const line of lines) {
                        if (line.startsWith('data: ')) {
                            try {
                                const data = JSON.parse(line.substring(6));
                                if (data.type === 'clear') {
                                    clearTerminal();
                                } else if (data.type === 'output') {
                                    addToTerminal(data.data.replace(/\n$/, ''), 'terminal-output');
                                    if (data.cwd) currentCwd = data.cwd;
                                }
                            } catch (e) {}
                        }
                    }
                }
            } catch (error) {
                addToTerminal(`Erreur: ${error.message}`, 'terminal-error');
            }
            
            updatePrompt();
        }
        
        function executeQuickCommand(command) {
            commandInput.value = command;
            executeCommand();
        }
        
        function addToTerminal(text, className = 'terminal-output') {
            const line = document.createElement('div');
            line.className = `terminal-line ${className}`;
            line.textContent = text;
            terminalOutput.appendChild(line);
            
            // Scroll vers le bas
            terminalOutput.scrollTop = terminalOutput.scrollHeight;
        }
        
        function clearTerminal() {
            terminalOutput.innerHTML = `
                <div class="terminal-line">
                    <span class="terminal-output">Terminal effacé</span>
                </div>
            `;
            commandInput.focus();
        }
        
        function showHelp() {
            const helpText = `
🚀 TERMINAL COMPLET ACTIVÉ - Toutes les commandes système disponibles !

Commandes système complètes:
  Toutes les commandes Linux/Unix standard sont disponibles
  
Exemples utiles:
  ./sauvegarde.sh --list                 - Lister les sauvegardes
  ./sauvegarde.sh --dry-run docs_eric     - Mode test
  systemctl status apache2               - Statut Apache
  journalctl -u apache2 -f               - Logs Apache temps réel
  apt update && apt upgrade              - Mise à jour système
  dpkg -l | grep backup                  - Paquets backup installés
  ss -tulpn                              - Ports réseau (moderne)
  htop                                   - Moniteur processus
  git status                             - Statut Git
  vim/nano [fichier]                     - Éditer fichiers
  crontab -l                             - Tâches programmées
  find / -name "*.conf"                  - Recherche fichiers
  
Commandes intégrées:
  clear                                  - Effacer le terminal
  help                                   - Afficher cette aide
            `;
            
            addToTerminal(helpText.trim(), 'terminal-output');
        }
        
        function updatePrompt() {
            const promptSymbol = document.querySelector('.terminal-prompt-symbol');
            if (promptSymbol) {
                // Détection automatique du nom du projet
                const projectName = currentCwd.split('/').pop() || 'backup-manager';
                const shortCwd = currentCwd.replace(currentCwd.split('/').slice(0, -1).join('/'), '~');
                promptSymbol.textContent = `www-data@${projectName}:${shortCwd}$ `;
            }
        }
        
        function autoComplete() {
            const command = commandInput.value;
            const commonCommands = ['ls', 'cd', 'pwd', 'cat', 'grep', 'find', 'tail', 'head', './sauvegarde.sh'];
            
            for (const cmd of commonCommands) {
                if (cmd.startsWith(command)) {
                    commandInput.value = cmd + ' ';
                    break;
                }
            }
        }
        
        // Maintenir le focus sur l'input
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.terminal-input')) {
                commandInput.focus();
            }
        });
        
        // Initialiser le prompt
        updatePrompt();
    </script>
</body>
</html>