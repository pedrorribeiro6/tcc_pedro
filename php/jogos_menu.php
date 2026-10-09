<?php
session_start();
require_once 'conexao.php';
require_once 'rank_helper.php';

// Dados do Jogador
$usuario_nome = $_SESSION['usuario_nome'] ?? 'Jovem Arquimedes';
$usuario_email = $_SESSION['usuario_email'] ?? 'arquimedes@matematica.com';
$pontos = $_SESSION['usuario_pontos'] ?? 2450;
$nivel = calcularNivel($pontos);
$foto_perfil = $_SESSION['usuario_foto'] ?? '../imagens/logo_matematico.png';

$rank_info = obterInfoRank($nivel, $pontos);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo de Jogos - The Mathematical Game</title>
    
    <!-- Google Fonts -->
    <link rel="icon" type="image/png" href="../imagens/favicon_logo.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Estilos Modulares do Projeto -->
    <link rel="stylesheet" href="../css/global.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../css/header.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../css/accessibility.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../css/style2.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../css/games.css?v=<?= time() ?>">
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

    <!-- HEADER DE NAVEGAÇÃO UNIFICADO -->
    <header class="app-header">
        <div class="header-container">
            <a href="home.php" class="header-brand">
                <img src="../imagens/logo_matematico.png" alt="The Mathematical Game Logo" class="header-brand-img">
                <span class="header-brand-text">Mathematical <span class="highlight">Game</span></span>
            </a>

            <nav>
                <ul class="header-nav">
                    <li>
                        <a href="home.php" class="nav-link">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                            <span>Início</span>
                        </a>
                    </li>
                    <li>
                        <a href="jogos_menu.php" class="nav-link active">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                            <span>Jogos</span>
                        </a>
                    </li>
                    <li>
                        <a href="historico.php" class="nav-link">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
                            <span>Histórico</span>
                        </a>
                    </li>
                    <li>
                        <a href="dicas.php" class="nav-link">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                            <span>Fórmulas & Dicas</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="header-user-actions">
                <button class="theme-toggle" id="themeToggle" type="button" aria-label="Ativar tema claro" title="Ativar tema claro" style="display:inline-flex;width:96px;min-width:96px;height:40px;flex:0 0 96px;align-items:center;justify-content:center;gap:7px;padding:0 10px;border:1px solid #00a8ff;border-radius:14px;background:rgba(0,168,255,.18);color:#fbbf24;cursor:pointer;"><svg viewBox="0 0 24 24" aria-hidden="true" style="width:20px;height:20px;fill:none;stroke:currentColor;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round;"><circle cx="12" cy="12" r="4"></circle><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"></path></svg><span>Claro</span></button>

                <!-- SELETOR RÁPIDO DE ACESSIBILIDADE E DALTONISMO -->
                <div class="accessibility-dropdown">
                    <button type="button" class="accessibility-trigger-btn" id="accessibilityTriggerBtn" title="Opções de Acessibilidade e Daltonismo">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"></circle><circle cx="12" cy="12" r="4"></circle><line x1="4.93" y1="4.93" x2="9.17" y2="9.17"></line><line x1="14.83" y1="14.83" x2="19.07" y2="19.07"></line><line x1="14.83" y1="9.17" x2="19.07" y2="4.93"></line><line x1="4.93" y1="19.07" x2="9.17" y2="14.83"></line></svg>
                        <span id="accessibilityTriggerLabel">Acessibilidade</span>
                    </button>
                    <div class="accessibility-popover" id="accessibilityPopover">
                        <div class="accessibility-popover-title">Visão / Daltonismo</div>
                        <button type="button" class="accessibility-opt-btn active" data-mode="none">
                            <span>Padrão</span>
                            <span class="accessibility-opt-badge">Normal</span>
                        </button>
                        <button type="button" class="accessibility-opt-btn" data-mode="deuteranopia">
                            <span>Deuteranopia</span>
                            <span class="accessibility-opt-badge" style="color:#0284c7;">Verde</span>
                        </button>
                        <button type="button" class="accessibility-opt-btn" data-mode="protanopia">
                            <span>Protanopia</span>
                            <span class="accessibility-opt-badge" style="color:#eab308;">Vermelho</span>
                        </button>
                        <button type="button" class="accessibility-opt-btn" data-mode="tritanopia">
                            <span>Tritanopia</span>
                            <span class="accessibility-opt-badge" style="color:#10b981;">Azul</span>
                        </button>
                        <button type="button" class="accessibility-opt-btn" data-mode="high-contrast">
                            <span>Alto Contraste</span>
                            <span class="accessibility-opt-badge">AAA</span>
                        </button>

                        <div class="accessibility-popover-divider"></div>
                        <div class="accessibility-popover-title">Conforto / TEA</div>
                        <button type="button" class="accessibility-tea-btn" data-tea="calm" style="display:flex;align-items:center;justify-content:space-between;width:100%;padding:8px 10px;background:transparent;border:1px solid transparent;border-radius:8px;color:var(--color-text-main,#e2e8f0);font-size:0.84rem;font-weight:600;text-align:left;cursor:pointer;transition:all 0.2s ease;">
                            <span>&#x1F9D8; Modo Calmo</span>
                            <span class="accessibility-opt-badge" style="color:#a78bfa;">TEA</span>
                        </button>
                        <button type="button" class="accessibility-tea-btn" data-tea="dyslexia" style="display:flex;align-items:center;justify-content:space-between;width:100%;padding:8px 10px;background:transparent;border:1px solid transparent;border-radius:8px;color:var(--color-text-main,#e2e8f0);font-size:0.84rem;font-weight:600;text-align:left;cursor:pointer;transition:all 0.2s ease;">
                            <span>&#x1F524; OpenDyslexic</span>
                            <span class="accessibility-opt-badge" style="color:#a78bfa;">Fonte</span>
                        </button>
                    </div>
                </div>

                <div class="user-stats-pill">
                    <div class="stat-item stat-rank rank-badge rank-badge--<?= $rank_info['badge_class'] ?>" title="Elo Ranqueado: <?= htmlspecialchars($rank_info['nome_completo']) ?>">
                        <span class="rank-icon-mini"><?= $rank_info['icone'] ?></span>
                        <span class="rank-name-text"><?= htmlspecialchars($rank_info['nome_completo']) ?></span>
                    </div>
                    <div class="stat-divider"></div>
                    <div class="stat-item stat-level"><span>Nível <?= $nivel ?></span></div>
                    <div class="stat-divider"></div>
                    <div class="stat-item stat-points"><span><?= number_format($pontos, 0, ',', '.') ?> XP</span></div>
                </div>

                <div class="user-profile-dropdown">
                    <button class="btn-profile-trigger" id="btnProfileTrigger">
                        <img src="<?= htmlspecialchars($foto_perfil) ?>" alt="Avatar" class="user-avatar-img">
                    </button>
                    <div class="profile-menu-popover" id="profileMenuPopover">
                        <div class="profile-menu-header">
                            <div class="user-display-name"><?= htmlspecialchars($usuario_nome) ?></div>
                            <div class="user-display-email"><?= htmlspecialchars($usuario_email) ?></div>
                            <div class="profile-rank-tag rank-badge rank-badge--<?= $rank_info['badge_class'] ?>" style="margin-top: 8px; display: inline-flex; align-items: center; gap: 5px; font-size: 0.75rem; padding: 4px 10px; border-radius: 999px;">
                                <span><?= $rank_info['icone'] ?></span>
                                <strong><?= htmlspecialchars($rank_info['nome_completo']) ?></strong>
                            </div>
                        </div>
                        <a href="perfil.php" class="profile-menu-item"><span>Meu Perfil</span></a>
                        <a href="historico.php" class="profile-menu-item"><span>Meu Histórico</span></a>
                        <a href="dicas.php" class="profile-menu-item"><span>Fórmulas & Dicas</span></a>
                        <a href="logout.php" class="profile-menu-item logout"><span>Sair</span></a>
                    </div>
                </div>

                <button class="hamburger-btn" id="hamburgerBtn">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
                </button>
            </div>
        </div>
    </header>

    <!-- DRAWER MOBILE -->
    <div class="mobile-nav-drawer" id="mobileNavDrawer">
        <ul class="mobile-nav-links">
            <li><a href="home.php" class="nav-link"><span>Início</span></a></li>
            <li><a href="jogos_menu.php" class="nav-link active"><span>Jogos</span></a></li>
            <li><a href="historico.php" class="nav-link"><span>Histórico</span></a></li>
            <li><a href="dicas.php" class="nav-link"><span>Fórmulas & Dicas</span></a></li>
        </ul>
    </div>

    <!-- CONTEÚDO PRINCIPAL DO CATÁLOGO DE JOGOS -->
    <main class="dashboard-container">
        
        <div class="section-header-block" style="margin-bottom: 30px;">
            <div>
                <h1 class="block-title" style="font-size: 2.1rem;">
                    Catálogo de Jogos Matemáticos
                </h1>
                <p class="block-subtitle">Escolha uma modalidade e comece seu treino para acumular XP e subir de nível.</p>
            </div>
        </div>

        <section class="games-section">
            <div class="games-grid">
                
                <!-- Card 1: Quiz 4 Operações -->
                <article class="game-card">
                    <h3 class="game-card-title">Quiz das 4 Operações</h3>
                    <p class="game-card-desc">Cálculos mentais de Adição, Subtração, Multiplicação e Divisão sem repetição de questões.</p>
                    <button class="btn-play-card" onclick="openDifficultyModal('quiz', 'Quiz das 4 Operações')">
                        <span>Iniciar Quiz</span>
                    </button>
                </article>

                <!-- Card 2: Geometria 2D & 3D -->
                <article class="game-card">
                    <h3 class="game-card-title">Geometria 2D &amp; 3D</h3>
                    <p class="game-card-desc">Identifique figuras como cubos, pirâmides e cilindros com mecânica Verdadeiro ou Falso.</p>
                    <button class="btn-play-card" onclick="openDifficultyModal('geometria', 'Geometria 2D &amp; 3D')">
                        <span>Adivinhar Figura</span>
                    </button>
                </article>

                <!-- Card 3: Plano Cartesiano -->
                <article class="game-card">
                    <h3 class="game-card-title">Plano Cartesiano</h3>
                    <p class="game-card-desc">Receba coordenadas (X, Y) e marque o ponto exato clicando diretamente na grade.</p>
                    <button class="btn-play-card" onclick="openDifficultyModal('cartesiano', 'Plano Cartesiano')">
                        <span>Marcar Ponto</span>
                    </button>
                </article>

                <!-- Card 4: Radiciação -->
                <article class="game-card">
                    <h3 class="game-card-title">Jogo de Radiciação</h3>
                    <p class="game-card-desc">Calcule raízes quadradas, cúbicas e simplificação de radicais sem perguntas repetidas.</p>
                    <button class="btn-play-card" onclick="openDifficultyModal('radiciacao', 'Jogo de Radiciação')">
                        <span>Calcular Raízes</span>
                    </button>
                </article>

                <!-- Card 5: Equações do 1º Grau -->
                <article class="game-card">
                    <h3 class="game-card-title">Equações do 1º Grau</h3>
                    <p class="game-card-desc">Encontre o valor de X em equações lineares procedurais geradas dinamicamente.</p>
                    <button class="btn-play-card" onclick="openDifficultyModal('equacoes', 'Equações do 1º Grau')">
                        <span>Resolver Equação</span>
                    </button>
                </article>

                <!-- Card 6: Frações -->
                <article class="game-card">
                    <h3 class="game-card-title">Operações com Frações</h3>
                    <p class="game-card-desc">Pratique adição, subtração, multiplicação e divisão de frações com denominadores iguais no formato Verdadeiro ou Falso.</p>
                    <button class="btn-play-card" onclick="openDifficultyModal('fracoes', 'Operações com Frações')">
                        <span>Jogar Frações</span>
                    </button>
                </article>

            </div>
        </section>

    </main>

    <!-- RODAPÉ -->
    <footer class="app-footer">
        <div class="footer-container">
            <p class="footer-quote">"A matemática é a linguagem com a qual o universo foi escrito." — Galileo Galilei</p>
            <p class="footer-copy">&copy; <?= date('Y') ?> The Mathematical Game.</p>
        </div>
    </footer>

    <!-- Modal de Seleção de Dificuldade -->
    <div class="diff-modal-overlay" id="diffModalOverlay" role="dialog" aria-modal="true" aria-labelledby="diffModalTitle">
        <div class="diff-modal-card">
            <button class="diff-modal-close" onclick="closeDifficultyModal()" aria-label="Fechar" title="Fechar modal">&times;</button>

            <div class="diff-modal-header">
                <div class="diff-modal-icon-badge">🎮</div>
                <h2 class="diff-modal-title" id="diffModalTitle">Escolha a Dificuldade</h2>
                <p class="diff-modal-subtitle" id="diffModalGame">Modo de Jogo</p>
            </div>

            <div class="diff-options">

                <button class="diff-btn diff-btn--easy" onclick="startGame('facil')" id="btnFacil" type="button">
                    <div class="diff-btn-icon-box">
                        <span class="diff-btn-emoji">🌱</span>
                    </div>
                    <div class="diff-btn-main">
                        <div class="diff-btn-header-row">
                            <span class="diff-btn-title">Fácil</span>
                            <span class="diff-xp-badge diff-xp--easy">1× XP</span>
                        </div>
                        <div class="diff-tags-row">
                            <span class="diff-tag-pill">❤️ 3 Vidas</span>
                            <span class="diff-tag-pill">⏱️ 45s</span>
                            <span class="diff-tag-pill">Iniciante</span>
                        </div>
                    </div>
                    <div class="diff-btn-chevron">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </div>
                </button>

                <button class="diff-btn diff-btn--medium" onclick="startGame('medio')" id="btnMedio" type="button">
                    <div class="diff-btn-icon-box">
                        <span class="diff-btn-emoji">⚡</span>
                    </div>
                    <div class="diff-btn-main">
                        <div class="diff-btn-header-row">
                            <span class="diff-btn-title">Médio</span>
                            <span class="diff-xp-badge diff-xp--medium">1.5× XP</span>
                        </div>
                        <div class="diff-tags-row">
                            <span class="diff-tag-pill">❤️ 2 Vidas</span>
                            <span class="diff-tag-pill">⏱️ 30s</span>
                            <span class="diff-tag-pill">Equilibrado</span>
                        </div>
                    </div>
                    <div class="diff-btn-chevron">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </div>
                </button>

                <button class="diff-btn diff-btn--hard" onclick="startGame('dificil')" id="btnDificil" type="button">
                    <div class="diff-btn-icon-box">
                        <span class="diff-btn-emoji">🔥</span>
                    </div>
                    <div class="diff-btn-main">
                        <div class="diff-btn-header-row">
                            <span class="diff-btn-title">Difícil</span>
                            <span class="diff-xp-badge diff-xp--hard">2× XP</span>
                        </div>
                        <div class="diff-tags-row">
                            <span class="diff-tag-pill">❤️ 1 Vida</span>
                            <span class="diff-tag-pill">⏱️ 20s</span>
                            <span class="diff-tag-pill">Desafio Extremo</span>
                        </div>
                    </div>
                    <div class="diff-btn-chevron">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </div>
                </button>

            </div>
        </div>
    </div>

    <script src="../js/home.js"></script>
    <script>
        let _currentGame = null;

        function openDifficultyModal(gameMode, gameName) {
            _currentGame = gameMode;
            document.getElementById('diffModalGame').textContent = gameName;
            document.getElementById('diffModalOverlay').classList.add('show');
            document.getElementById('btnFacil').focus();
        }

        function closeDifficultyModal() {
            document.getElementById('diffModalOverlay').classList.remove('show');
            _currentGame = null;
        }

        function startGame(difficulty) {
            if (!_currentGame) return;
            window.location.href = 'jogos.php?game=' + _currentGame + '&difficulty=' + difficulty;
        }

        document.getElementById('diffModalOverlay').addEventListener('click', function(e) {
            if (e.target === this) closeDifficultyModal();
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeDifficultyModal();
        });
    </script>
    <script src="../js/accessibility.js?v=<?= time() ?>"></script>
    <script src="../js/theme.js?v=<?= time() ?>"></script>
</body>
</html>
