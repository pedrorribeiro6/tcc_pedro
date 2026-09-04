<?php
session_start();
require_once 'conexao.php';

// Dados do Jogador
$usuario_nome = $_SESSION['usuario_nome'] ?? 'Jovem Arquimedes';
$usuario_email = $_SESSION['usuario_email'] ?? 'arquimedes@matematica.com';
$pontos = $_SESSION['usuario_pontos'] ?? 2450;
$nivel = floor($pontos / 1000);
$foto_perfil = $_SESSION['usuario_foto'] ?? '../imagens/logo_matematico.png';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo de Jogos - The Mathematical Game</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Estilos Modulares do Projeto -->
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/style2.css">
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
                <div class="user-stats-pill">
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
                        </div>
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
                    <a href="jogos.php?game=quiz" class="btn-play-card">
                        <span>Iniciar Quiz</span>
                    </a>
                </article>

                <!-- Card 2: Geometria 2D & 3D -->
                <article class="game-card">
                    <h3 class="game-card-title">Geometria 2D & 3D</h3>
                    <p class="game-card-desc">Identifique figuras como cubos, pirâmides e cilindros com mecânica Verdadeiro ou Falso.</p>
                    <a href="jogos.php?game=geometria" class="btn-play-card">
                        <span>Adivinhar Figura</span>
                    </a>
                </article>

                <!-- Card 3: Plano Cartesiano -->
                <article class="game-card">
                    <h3 class="game-card-title">Plano Cartesiano</h3>
                    <p class="game-card-desc">Receba coordenadas (X, Y) e marque o ponto exato clicando diretamente na grade.</p>
                    <a href="jogos.php?game=cartesiano" class="btn-play-card">
                        <span>Marcar Ponto</span>
                    </a>
                </article>

                <!-- Card 4: Radiciação -->
                <article class="game-card">
                    <h3 class="game-card-title">Jogo de Radiciação</h3>
                    <p class="game-card-desc">Calcule raízes quadradas, cúbicas e simplificação de radicais sem perguntas repetidas.</p>
                    <a href="jogos.php?game=radiciacao" class="btn-play-card">
                        <span>Calcular Raízes</span>
                    </a>
                </article>

                <!-- Card 5: Equações do 1º Grau -->
                <article class="game-card">
                    <h3 class="game-card-title">Equações do 1º Grau</h3>
                    <p class="game-card-desc">Encontre o valor de X em equações lineares procedurais geradas dinamicamente.</p>
                    <a href="jogos.php?game=equacoes" class="btn-play-card">
                        <span>Resolver Equação</span>
                    </a>
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

    <script src="../js/theme.js?v=3"></script>
    <script src="../js/home.js"></script>
</body>
</html>
