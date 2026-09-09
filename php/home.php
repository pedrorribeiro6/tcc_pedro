<?php
session_start();
require_once 'conexao.php';

// Dados do Jogador
$usuario_id = $_SESSION['usuario_id'] ?? null;
$usuario_nome = $_SESSION['usuario_nome'] ?? 'Jovem Arquimedes';
$usuario_email = $_SESSION['usuario_email'] ?? 'arquimedes@matematica.com';
$pontos = $_SESSION['usuario_pontos'] ?? 2450;
$nivel = floor($pontos / 1000);
$foto_perfil = $_SESSION['usuario_foto'] ?? '../imagens/logo_matematico.png';

$xp_atual = $pontos % 1000;
$xp_proximo = 1000;
$porcentagem_xp = round(($xp_atual / $xp_proximo) * 100);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Mathematical Game - Painel Inicial</title>
    
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

    <!-- Fundo Matemático Animado com Símbolos Flutuantes -->
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
                        <a href="home.php" class="nav-link active">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                            <span>Início</span>
                        </a>
                    </li>
                    <li>
                        <a href="jogos_menu.php" class="nav-link">
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
                        <a href="jogos_menu.php" class="profile-menu-item"><span>Catálogo de Jogos</span></a>
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
            <li><a href="home.php" class="nav-link active"><span>Início</span></a></li>
            <li><a href="jogos_menu.php" class="nav-link"><span>Jogos</span></a></li>
            <li><a href="historico.php" class="nav-link"><span>Histórico</span></a></li>
            <li><a href="dicas.php" class="nav-link"><span>Fórmulas & Dicas</span></a></li>
        </ul>
    </div>

    <!-- CONTEÚDO PRINCIPAL DO DASHBOARD -->
    <main class="dashboard-container">

        <!-- Banner de Boas-vindas (Hero Section) -->
        <section class="dashboard-hero">
            <div class="hero-text-content">
                <div class="hero-badge">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                    Plataforma Educacional Ativa
                </div>
                <h1 class="hero-title">Olá, <span class="highlight"><?= htmlspecialchars($usuario_nome) ?></span>!</h1>
                <p class="hero-subtitle">Bem-vindo ao The Mathematical Game! Escolha um dos módulos abaixo para jogar, conferir seu histórico com percentual de acertos ou estudar fórmulas.</p>
                
                <div class="xp-progress-wrapper">
                    <div class="xp-label-row">
                        <span>Progresso para o Nível <?= $nivel + 1 ?></span>
                        <span><?= $xp_atual ?> / <?= $xp_proximo ?> XP (<?= $porcentagem_xp ?>%)</span>
                    </div>
                    <div class="xp-bar-bg">
                        <div class="xp-bar-fill" style="width: <?= $porcentagem_xp ?>%;"></div>
                    </div>
                </div>

                <div class="hero-actions">
                    <a href="jogos_menu.php" class="btn-hero-primary">
                        <span>Ver Todos os Jogos</span>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                    </a>
                    <a href="dicas.php" class="btn-hero-secondary">
                        <span>Central de Fórmulas</span>
                    </a>
                </div>
            </div>

        </section>

        <!-- SEÇÃO: MÓDULOS PRINCIPAIS DA APLICAÇÃO -->
        <section style="margin-top: 20px;">
            <div class="section-header-block">
                <div>
                    <h2 class="block-title">
                        Módulos da Plataforma
                    </h2>
                    <p class="block-subtitle">Acesse as áreas exclusivas de jogos, estatísticas de desempenho e teoria.</p>
                </div>
            </div>

            <div class="games-grid">
                <!-- Atalho Jogos -->
                <article class="game-card" style="background: linear-gradient(135deg, rgba(0, 168, 255, 0.12) 0%, rgba(11, 30, 54, 0.9) 100%);">
                    <h3 class="game-card-title">Aba de Jogos</h3>
                    <p class="game-card-desc">Desafie-se nos 5 mini-jogos: Quiz 4 Operações, Geometria 2D/3D, Plano Cartesiano, Radiciação e Equações.</p>
                    <a href="jogos_menu.php" class="btn-hero-primary" style="justify-content: center; width: 100%; text-decoration: none;">
                        <span>Ir para os Jogos</span>
                    </a>
                </article>

                <!-- Atalho Histórico -->
                <article class="game-card" style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.12) 0%, rgba(11, 30, 54, 0.9) 100%);">
                    <h3 class="game-card-title">Meu Histórico (%)</h3>
                    <p class="game-card-desc">Acompanhe suas partidas anteriores, acertos e consulte seu percentual de precisão exato (%).</p>
                    <a href="historico.php" class="btn-hero-secondary" style="justify-content: center; width: 100%;">
                        <span>Ver Meu Histórico</span>
                    </a>
                </article>

                <!-- Atalho Fórmulas & Dicas -->
                <article class="game-card" style="background: linear-gradient(135deg, rgba(245, 158, 11, 0.12) 0%, rgba(11, 30, 54, 0.9) 100%);">
                    <h3 class="game-card-title">Fórmulas & Dicas</h3>
                    <p class="game-card-desc">Estude o guia completo categorizado com teoria, regras, equações e exemplos passo a passo.</p>
                    <a href="dicas.php" class="btn-hero-secondary" style="justify-content: center; width: 100%;">
                        <span>Estudar Fórmulas</span>
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
