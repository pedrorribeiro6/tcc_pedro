<?php
session_start();

$usuario_nome = $_SESSION['usuario_nome'] ?? 'Jovem Arquimedes';
$pontos = $_SESSION['usuario_pontos'] ?? 2450;
$nivel = floor($pontos / 1000);
$foto_perfil = $_SESSION['usuario_foto'] ?? '../imagens/logo_matematico.png';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Mathematical Game - Arena do Jogo</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Estilos do Projeto -->
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/games.css?v=<?= time(); ?>">
</head>
<body class="app-body">

    <!-- Fundo Matemático Animado -->
    <div class="math-background">
        <div class="math-symbol">&pi;</div>
        <div class="math-symbol">&sum;</div>
        <div class="math-symbol">&int;</div>
        <div class="math-symbol">&radic;</div>
        <div class="math-symbol">&infin;</div>
        <div class="math-symbol">&times;</div>
    </div>

    <!-- Header Fixo da Arena -->
    <header class="app-header">
        <div class="header-container">
            <a href="home.php" class="header-brand" title="Voltar para o Painel Inicial">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#fbbf24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                <img src="../imagens/logo_matematico.png" alt="The Mathematical Game Logo" class="header-brand-img">
                <span class="header-brand-text">Mathematical <span class="highlight">Game</span></span>
            </a>

            <div class="header-user-actions">
                <button class="theme-toggle" id="themeToggle" type="button" aria-label="Ativar tema claro" title="Ativar tema claro" style="display:inline-flex;width:96px;min-width:96px;height:40px;flex:0 0 96px;align-items:center;justify-content:center;gap:7px;padding:0 10px;border:1px solid #00a8ff;border-radius:14px;background:rgba(0,168,255,.18);color:#fbbf24;cursor:pointer;"><svg viewBox="0 0 24 24" aria-hidden="true" style="width:20px;height:20px;fill:none;stroke:currentColor;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round;"><circle cx="12" cy="12" r="4"></circle><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"></path></svg><span>Claro</span></button>
                <div class="user-stats-pill">
                    <div class="stat-item stat-level">
                        <span>Nível <?= $nivel ?></span>
                    </div>
                </div>

                <div class="user-profile-dropdown">
                    <button class="btn-profile-trigger" id="btnProfileTrigger">
                        <img src="<?= htmlspecialchars($foto_perfil) ?>" alt="Avatar" class="user-avatar-img">
                    </button>
                    <div class="profile-menu-popover" id="profileMenuPopover">
                        <div class="profile-menu-header">
                            <div class="user-display-name"><?= htmlspecialchars($usuario_nome) ?></div>
                        </div>
                        <a href="historico.php" class="profile-menu-item"><span>Meu Histórico</span></a>
                        <a href="ranking.php" class="profile-menu-item"><span>Ranking</span></a>
                        <a href="dicas.php" class="profile-menu-item"><span>Fórmulas & Dicas</span></a>
                        <a href="logout.php" class="profile-menu-item logout"><span>Sair</span></a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Arena do Jogo -->
    <main class="game-arena-container">
        
        <!-- Placar e Status de Vidas / Tempo / Pontos -->
        <div class="game-status-header">
            <div class="game-mode-badge">
                <span class="game-mode-badge-icon" id="gameModeIcon">⚡</span>
                <span id="gameModeTitle">Carregando Jogo...</span>
            </div>

            <div class="game-live-stats">
                <!-- Vidas -->
                <div class="stat-hearts" id="statHearts">
                    <span class="heart-icon">❤️</span>
                    <span class="heart-icon">❤️</span>
                    <span class="heart-icon">❤️</span>
                </div>

                <!-- Tempo -->
                <div class="stat-timer-pill" id="statTimerPill">
                    ⏱️ 30s
                </div>

                <!-- Pontuação -->
                <div class="stat-score-pill" id="statScorePill">
                    ⭐ 0 XP
                </div>
            </div>
        </div>

        <!-- Barra de Progresso do Nível / Perguntas -->
        <div class="game-progress-bar-bg">
            <div class="game-progress-bar-fill" id="gameProgressBar"></div>
        </div>

        <!-- Card de Desafio Principal (Renderizado pelo JS) -->
        <div class="game-challenge-card" id="gameChallengeCard">
            <!-- Conteúdo injetado via js/games.js -->
        </div>

    </main>

    <!-- Modal de Vitória / Fim de Jogo -->
    <div class="game-modal-overlay" id="gameModalOverlay">
        <div class="game-modal-card">
            <div class="modal-trophy-icon">🏆</div>
            <h2 class="modal-title" id="modalTitle">Partida Concluída! 🎉</h2>
            <p style="color: var(--color-text-muted); font-size: 0.9rem;">Parabéns! Você completou os desafios desta modalidade.</p>

            <div class="modal-stats-grid">
                <div class="modal-stat-box">
                    <div class="modal-stat-value" id="modalScore">+300 XP</div>
                    <div class="modal-stat-label">Pontuação Ganha</div>
                </div>
                <div class="modal-stat-box">
                    <div class="modal-stat-value" id="modalAccuracy">100%</div>
                    <div class="modal-stat-label">Taxa de Precisão</div>
                </div>
            </div>

            <div class="modal-actions">
                <button onclick="window.location.reload();" class="btn-hero-primary" style="flex: 1; padding: 12px;">
                    <span>Jogar Novamente</span>
                </button>
                <a href="home.php" class="btn-hero-secondary" style="flex: 1; padding: 12px; justify-content: center;">
                    <span>Início</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Script do Motor dos Jogos -->
    <script src="../js/theme.js?v=3"></script>
    <script src="../js/games.js?v=<?= time(); ?>"></script>
</body>
</html>
