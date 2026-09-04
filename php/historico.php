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

// Carregar Histórico de Partidas
$historicoPartidas = [];
if ($pdo && $usuario_id) {
    try {
        $stmtHist = $pdo->prepare("SELECT * FROM historico_partidas WHERE usuario_id = :uid ORDER BY data_partida DESC LIMIT 50");
        $stmtHist->execute(['uid' => $usuario_id]);
        $historicoPartidas = $stmtHist->fetchAll();
    } catch (PDOException $e) {
        $historicoPartidas = $_SESSION['historico'] ?? [];
    }
} else {
    $historicoPartidas = $_SESSION['historico'] ?? [];
}

// Fallback demonstrativo
if (empty($historicoPartidas)) {
    $historicoPartidas = [
        ['modo_jogo' => 'Quiz das 4 Operações', 'resultado' => 'Vitória', 'pontos_ganhos' => 200, 'precisao_porcentagem' => 95, 'acertos' => 19, 'total_questoes' => 20, 'data_partida' => 'Hoje, 10:42'],
        ['modo_jogo' => 'Geometria 2D & 3D', 'resultado' => 'Vitória', 'pontos_ganhos' => 300, 'precisao_porcentagem' => 100, 'acertos' => 10, 'total_questoes' => 10, 'data_partida' => 'Ontem, 16:15'],
        ['modo_jogo' => 'Plano Cartesiano', 'resultado' => 'Vitória', 'pontos_ganhos' => 250, 'precisao_porcentagem' => 90, 'acertos' => 9, 'total_questoes' => 10, 'data_partida' => '01/09/2026'],
        ['modo_jogo' => 'Jogo de Radiciação', 'resultado' => 'Concluído', 'pontos_ganhos' => 180, 'precisao_porcentagem' => 80, 'acertos' => 8, 'total_questoes' => 10, 'data_partida' => '31/08/2026'],
        ['modo_jogo' => 'Equações do 1º Grau', 'resultado' => 'Vitória', 'pontos_ganhos' => 220, 'precisao_porcentagem' => 90, 'acertos' => 9, 'total_questoes' => 10, 'data_partida' => '30/08/2026']
    ];
}

// Métricas de Resumo
$totalPartidas = count($historicoPartidas);
$somaPrecisao = 0;
$vitoriasCount = 0;
$totalAcertos = 0;
$totalQuestoesRespondidas = 0;

foreach ($historicoPartidas as $p) {
    $somaPrecisao += intval($p['precisao_porcentagem']);
    $totalAcertos += intval($p['acertos'] ?? 0);
    $totalQuestoesRespondidas += intval($p['total_questoes'] ?? 0);
    if (in_array($p['resultado'], ['Vitória', 'Concluído'])) $vitoriasCount++;
}
$mediaPrecisao = $totalPartidas > 0 ? round($somaPrecisao / $totalPartidas) : 0;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico de Partidas - The Mathematical Game</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Estilos Modulares do Projeto -->
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/style2.css">
    <link rel="stylesheet" href="../css/historico.css">
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
                        <a href="jogos_menu.php" class="nav-link">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                            <span>Jogos</span>
                        </a>
                    </li>
                    <li>
                        <a href="historico.php" class="nav-link active">
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
            <li><a href="home.php" class="nav-link"><span>Início</span></a></li>
            <li><a href="jogos_menu.php" class="nav-link"><span>Jogos</span></a></li>
            <li><a href="historico.php" class="nav-link active"><span>Histórico</span></a></li>
            <li><a href="dicas.php" class="nav-link"><span>Fórmulas & Dicas</span></a></li>
        </ul>
    </div>

    <!-- CONTEÚDO PRINCIPAL DO HISTÓRICO -->
    <main class="dashboard-container">
        
        <div class="section-header-block" style="margin-bottom: 30px;">
            <div>
                <h1 class="block-title" style="font-size: 2.1rem;">
                    <span class="block-title-icon" style="width: 44px; height: 44px; font-size: 1.4rem;">📊</span>
                    Histórico de Partidas e Desempenho
                </h1>
                <p class="block-subtitle">Acompanhe detalhadamente seus resultados, pontuações e taxa de precisão (%) em cada jogo.</p>
            </div>
            <a href="jogos_menu.php" class="btn-hero-primary" style="padding: 10px 20px; font-size: 0.95rem; text-decoration: none;">
                <span>Nova Partida</span>
            </a>
        </div>

        <!-- RESUMO DE DESEMPENHO EM CARDS -->
        <div class="history-summary-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); margin-bottom: 35px;">
            <div class="history-summary-card" style="padding: 22px;">
                <div class="summary-val accuracy" style="font-size: 2.4rem;"><?= $mediaPrecisao ?>%</div>
                <div class="summary-lbl" style="font-size: 0.85rem;">Precisão Média do Jogador</div>
            </div>
            <div class="history-summary-card" style="padding: 22px;">
                <div class="summary-val" style="font-size: 2.4rem;"><?= $totalPartidas ?></div>
                <div class="summary-lbl" style="font-size: 0.85rem;">Partidas Concluídas</div>
            </div>
            <div class="history-summary-card" style="padding: 22px;">
                <div class="summary-val" style="color: #86efac; font-size: 2.4rem;"><?= $vitoriasCount ?></div>
                <div class="summary-lbl" style="font-size: 0.85rem;">Total de Vitórias</div>
            </div>
            <div class="history-summary-card" style="padding: 22px;">
                <div class="summary-val" style="color: var(--color-cyan-vibrant); font-size: 2.4rem;"><?= $totalAcertos ?>/<?= $totalQuestoesRespondidas ?></div>
                <div class="summary-lbl" style="font-size: 0.85rem;">Questões Acertadas</div>
            </div>
        </div>

        <!-- TABELA COMPLETA DE HISTÓRICO -->
        <section class="history-card">
            <div class="table-responsive">
                <table class="history-table">
                    <thead>
                        <tr>
                            <th>Modo de Jogo</th>
                            <th>Resultado</th>
                            <th>Pontuação Ganha</th>
                            <th>Percentual do Jogador (%)</th>
                            <th>Data e Hora</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($historicoPartidas as $partida): ?>
                            <?php 
                                $pct = intval($partida['precisao_porcentagem']);
                                $acertos = intval($partida['acertos'] ?? 0);
                                $totalQ = intval($partida['total_questoes'] ?? 0);
                                $resultadoClass = in_array($partida['resultado'], ['Vitória', 'Concluído']) ? 'status-win' : 'status-draw';
                            ?>
                            <tr>
                                <td>
                                    <div class="game-mode-cell">
                                        <div class="mode-icon-mini">🎮</div>
                                        <span><?= htmlspecialchars($partida['modo_jogo']) ?></span>
                                    </div>
                                </td>
                                <td><span class="status-pill <?= $resultadoClass ?>"><?= htmlspecialchars($partida['resultado']) ?></span></td>
                                <td class="score-text">+<?= intval($partida['pontos_ganhos']) ?> XP</td>
                                <td>
                                    <span class="accuracy-badge-pill" style="font-size: 0.95rem; padding: 6px 14px;">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                        <strong><?= $pct ?>%</strong> <?= $totalQ > 0 ? "($acertos/$totalQ acertos)" : '' ?>
                                    </span>
                                </td>
                                <td style="color: var(--color-text-muted);"><?= htmlspecialchars($partida['data_partida']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
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
