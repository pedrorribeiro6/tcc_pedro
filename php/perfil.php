<?php
session_start();
require_once 'conexao.php';
require_once 'rank_helper.php';

// Redireciona se não estiver logado
if (!isset($_SESSION['usuario_nome'])) {
    header('Location: index.php');
    exit;
}

$usuario_id = $_SESSION['usuario_id'] ?? null;

// Busca dados atualizados do banco se disponível
$nome = $_SESSION['usuario_nome'] ?? 'Jovem Arquimedes';
$email = $_SESSION['usuario_email'] ?? 'arquimedes@matematica.com';
$pontos = $_SESSION['usuario_pontos'] ?? 2450;
$foto_perfil = $_SESSION['usuario_foto'] ?? '../imagens/logo_matematico.png';
$banner_perfil = $_SESSION['usuario_banner'] ?? '../imagens/banners/banner_matematico.svg';

$total_partidas = 0;
$media_precisao = 0;
$total_acertos = 0;

if ($pdo && $usuario_id) {
    try {
        $stmtUser = $pdo->prepare("SELECT nome, email, pontos, foto_perfil, banner_perfil FROM usuarios WHERE id = :id LIMIT 1");
        $stmtUser->execute(['id' => $usuario_id]);
        $userDb = $stmtUser->fetch();
        if ($userDb) {
            $nome = $userDb['nome'];
            $email = $userDb['email'];
            $pontos = $userDb['pontos'] ?? 0;
            if (!empty($userDb['foto_perfil'])) $foto_perfil = $userDb['foto_perfil'];
            if (!empty($userDb['banner_perfil'])) $banner_perfil = $userDb['banner_perfil'];
            $_SESSION['usuario_nome'] = $nome;
            $_SESSION['usuario_email'] = $email;
            $_SESSION['usuario_pontos'] = $pontos;
            $_SESSION['usuario_foto'] = $foto_perfil;
            $_SESSION['usuario_banner'] = $banner_perfil;
        }

        // Estatísticas do histórico
        $stmtStats = $pdo->prepare("
            SELECT COUNT(id) as total_partidas, 
                   COALESCE(ROUND(AVG(precisao_porcentagem), 1), 0) as media_precisao,
                   COALESCE(SUM(acertos), 0) as total_acertos
            FROM historico_partidas 
            WHERE usuario_id = :id
        ");
        $stmtStats->execute(['id' => $usuario_id]);
        $stats = $stmtStats->fetch();
        if ($stats) {
            $total_partidas = intval($stats['total_partidas']);
            $media_precisao = floatval($stats['media_precisao']);
            $total_acertos = intval($stats['total_acertos']);
        }
    } catch (PDOException $e) {
        error_log("Erro ao carregar perfil: " . $e->getMessage());
    }
}

$nivel = calcularNivel($pontos);
$rank_info = obterInfoRank($nivel, $pontos);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Mathematical Game - Meu Perfil</title>
    
    <!-- Google Fonts -->
    <link rel="icon" type="image/png" href="../imagens/favicon_logo.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Estilos -->
    <link rel="stylesheet" href="../css/global.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../css/header.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../css/accessibility.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../css/style2.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../css/perfil.css?v=<?= time() ?>">
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

    <!-- HEADER PRINCIPAL -->
    <header class="app-header">
        <div class="header-container">
            <a href="home.php" class="header-brand" title="Voltar para a Página Inicial">
                <img src="../imagens/logo_matematico.png" alt="Logo The Mathematical Game" class="header-brand-img">
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
                    <li>
                        <a href="perfil.php" class="nav-link active">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                            <span>Meu Perfil</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="header-user-actions">
                <button class="theme-toggle" id="themeToggle" type="button" aria-label="Alternar tema" style="display:inline-flex;width:96px;min-width:96px;height:40px;flex:0 0 96px;align-items:center;justify-content:center;gap:7px;padding:0 10px;border:1px solid #00a8ff;border-radius:14px;background:rgba(0,168,255,.18);color:#fbbf24;cursor:pointer;">
                    <svg viewBox="0 0 24 24" aria-hidden="true" style="width:20px;height:20px;fill:none;stroke:currentColor;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round;"><circle cx="12" cy="12" r="4"></circle><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"></path></svg>
                    <span>Claro</span>
                </button>

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
                                <span>🧘 Modo Calmo</span>
                                <span class="accessibility-opt-badge" style="color:#a78bfa;">TEA</span>
                            </button>
                            <button type="button" class="accessibility-tea-btn" data-tea="dyslexia" style="display:flex;align-items:center;justify-content:space-between;width:100%;padding:8px 10px;background:transparent;border:1px solid transparent;border-radius:8px;color:var(--color-text-main,#e2e8f0);font-size:0.84rem;font-weight:600;text-align:left;cursor:pointer;transition:all 0.2s ease;">
                                <span>🔤 OpenDyslexic</span>
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
                        <img src="<?= htmlspecialchars($foto_perfil) ?>" alt="Avatar" class="user-avatar-img header-avatar-sync">
                    </button>
                    <div class="profile-menu-popover" id="profileMenuPopover">
                        <div class="profile-menu-header">
                            <div class="user-display-name header-name-sync"><?= htmlspecialchars($nome) ?></div>
                            <div class="user-display-email"><?= htmlspecialchars($email) ?></div>
                            <div class="profile-rank-tag rank-badge rank-badge--<?= $rank_info['badge_class'] ?>" style="margin-top: 8px; display: inline-flex; align-items: center; gap: 5px; font-size: 0.75rem; padding: 4px 10px; border-radius: 999px;">
                                <span><?= $rank_info['icone'] ?></span>
                                <strong><?= htmlspecialchars($rank_info['nome_completo']) ?></strong>
                            </div>
                        </div>
                        <a href="perfil.php" class="profile-menu-item active"><span>Meu Perfil</span></a>
                        <a href="jogos_menu.php" class="profile-menu-item"><span>Catálogo de Jogos</span></a>
                        <a href="historico.php" class="profile-menu-item"><span>Meu Histórico</span></a>
                        <a href="dicas.php" class="profile-menu-item"><span>Fórmulas & Dicas</span></a>
                        <a href="logout.php" class="profile-menu-item logout"><span>Sair</span></a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- CONTEÚDO PRINCIPAL: MEU PERFIL -->
    <main class="perfil-container">

        <!-- HERO BANNER DO JOGADOR -->
        <div class="perfil-hero-card">
            <div class="perfil-banner-viewport">
                <img src="<?= htmlspecialchars($banner_perfil) ?>" alt="Banner do Jogador" class="perfil-banner-img" id="currentBannerImg">
                <div class="perfil-banner-overlay"></div>
            </div>

            <div class="perfil-header-content">
                <div class="perfil-user-meta">
                    <div class="perfil-avatar-wrapper">
                        <img src="<?= htmlspecialchars($foto_perfil) ?>" alt="Avatar do Jogador" class="perfil-avatar-img" id="currentAvatarImg">
                        <button type="button" class="btn-avatar-camera" title="Trocar foto de perfil" onclick="document.getElementById('avatarUploadInput').click();">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
                        </button>
                    </div>

                    <div class="perfil-names-block">
                        <h1 class="perfil-display-name" id="currentDisplayName"><?= htmlspecialchars($nome) ?></h1>
                        <p class="perfil-display-email"><?= htmlspecialchars($email) ?></p>
                        <div class="perfil-rank-pill rank-badge rank-badge--<?= $rank_info['badge_class'] ?>">
                            <span><?= $rank_info['icone'] ?></span>
                            <span><?= htmlspecialchars($rank_info['nome_completo']) ?></span>
                            <span style="opacity: 0.65; font-size: 0.78rem;">&bull; Nível <?= $nivel ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- GRID DE ESTATÍSTICAS RÁPIDAS -->
        <div class="perfil-stats-grid">
            <div class="perfil-stat-card">
                <div class="perfil-stat-icon" style="color: #fbbf24;">⚡</div>
                <div class="perfil-stat-info">
                    <span class="perfil-stat-val"><?= number_format($pontos, 0, ',', '.') ?> XP</span>
                    <span class="perfil-stat-label">Experiência Total</span>
                </div>
            </div>

            <div class="perfil-stat-card">
                <div class="perfil-stat-icon" style="color: #38bdf8;">🏆</div>
                <div class="perfil-stat-info">
                    <span class="perfil-stat-val"><?= htmlspecialchars($rank_info['nome_completo']) ?></span>
                    <span class="perfil-stat-label">Patente Ranqueada</span>
                </div>
            </div>

            <div class="perfil-stat-card">
                <div class="perfil-stat-icon" style="color: #a855f7;">🎮</div>
                <div class="perfil-stat-info">
                    <span class="perfil-stat-val"><?= $total_partidas ?></span>
                    <span class="perfil-stat-label">Partidas Concluídas</span>
                </div>
            </div>

            <div class="perfil-stat-card">
                <div class="perfil-stat-icon" style="color: #10b981;">🎯</div>
                <div class="perfil-stat-info">
                    <span class="perfil-stat-val"><?= $media_precisao ?>%</span>
                    <span class="perfil-stat-label">Precisão de Acertos</span>
                </div>
            </div>
        </div>

        <!-- FORMULÁRIO DE EDIÇÃO DO PERFIL -->
        <div class="perfil-form-card">
            <form id="perfilForm" enctype="multipart/form-data">

                <!-- 1. IDENTIDADE BÁSICA -->
                <h2 class="perfil-section-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    <span>Dados de Identidade</span>
                </h2>

                <div class="form-group-custom">
                    <label for="inputNome" class="form-label-custom">Nome de Exibição</label>
                    <input type="text" id="inputNome" name="nome" class="form-input-custom" value="<?= htmlspecialchars($nome) ?>" required maxlength="80" placeholder="Seu nome ou apelido gamer">
                </div>

                <hr style="border: 0; border-top: 1px solid rgba(255,255,255,0.08); margin: 28px 0;">

                <!-- 2. FOTO DE PERFIL / AVATAR -->
                <h2 class="perfil-section-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><circle cx="12" cy="12" r="4"></circle></svg>
                    <span>Foto de Perfil</span>
                </h2>
                <p style="font-size: 0.85rem; color: var(--color-text-muted); margin-top: -12px; margin-bottom: 12px;">Escolha um avatar temático dos grandes matemáticos ou envie uma foto do seu computador/celular.</p>

                <!-- Input Hidden para Preset de Avatar -->
                <input type="hidden" name="avatar_preset" id="avatarPresetInput" value="">
                
                <!-- Input de Upload de Avatar -->
                <input type="file" name="foto_upload" id="avatarUploadInput" class="file-input-hidden" accept="image/png, image/jpeg, image/webp">

                <div class="avatar-selector-grid">
                    <div class="preset-avatar-item <?= ($foto_perfil == '../imagens/logo_matematico.png') ? 'active' : '' ?>" data-src="../imagens/logo_matematico.png" title="Logo Oficial Mathematical Game">
                        <img src="../imagens/logo_matematico.png" alt="Logo Matemático">
                    </div>
                    <div class="preset-avatar-item <?= (strpos($foto_perfil, 'arquimedes') !== false) ? 'active' : '' ?>" data-src="../imagens/avatars/avatar_arquimedes.svg" title="Arquimedes de Siracusa">
                        <img src="../imagens/avatars/avatar_arquimedes.svg" alt="Arquimedes">
                    </div>
                    <div class="preset-avatar-item <?= (strpos($foto_perfil, 'pitagoras') !== false) ? 'active' : '' ?>" data-src="../imagens/avatars/avatar_pitagoras.svg" title="Pitágoras">
                        <img src="../imagens/avatars/avatar_pitagoras.svg" alt="Pitágoras">
                    </div>
                    <div class="preset-avatar-item <?= (strpos($foto_perfil, 'hipatia') !== false) ? 'active' : '' ?>" data-src="../imagens/avatars/avatar_hipatia.svg" title="Hipátia de Alexandria">
                        <img src="../imagens/avatars/avatar_hipatia.svg" alt="Hipátia">
                    </div>
                    <div class="preset-avatar-item <?= (strpos($foto_perfil, 'euler') !== false) ? 'active' : '' ?>" data-src="../imagens/avatars/avatar_euler.svg" title="Leonhard Euler">
                        <img src="../imagens/avatars/avatar_euler.svg" alt="Euler">
                    </div>
                </div>

                <div class="custom-upload-box">
                    <label for="avatarUploadInput" class="btn-upload-label">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                        <span>Fazer Upload de Foto Própria</span>
                    </label>
                    <span class="upload-feedback-text" id="avatarFileFeedback">Nenhum arquivo novo selecionado (PNG, JPG ou WEBP até 5MB)</span>
                </div>

                <hr style="border: 0; border-top: 1px solid rgba(255,255,255,0.08); margin: 28px 0;">

                <!-- 3. BANNER DE PERFIL -->
                <h2 class="perfil-section-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg>
                    <span>Banner de Perfil</span>
                </h2>
                <p style="font-size: 0.85rem; color: var(--color-text-muted); margin-top: -12px; margin-bottom: 16px;">Envie sua própria imagem de banner (PNG, JPG ou WEBP até 5MB).</p>

                <!-- Input Hidden para Preset de Banner e Remoção -->
                <input type="hidden" name="banner_preset" id="bannerPresetInput" value="">
                <input type="hidden" name="remover_banner" id="removerBannerInput" value="0">

                <!-- Input de Upload de Banner -->
                <input type="file" name="banner_upload" id="bannerUploadInput" class="file-input-hidden" accept="image/png, image/jpeg, image/webp">

                <div class="custom-upload-box" style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                    <label for="bannerUploadInput" class="btn-upload-label">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                        <span>Fazer Upload de Banner Próprio</span>
                    </label>

                    <button type="button" class="btn-remove-banner" id="btnRemoverBanner" title="Remover o banner atual e restaurar o banner padrão">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                        <span id="btnRemoverBannerTexto">Remover Banner</span>
                    </button>

                    <span class="upload-feedback-text" id="bannerFileFeedback">Nenhum arquivo selecionado</span>
                </div>

                <hr style="border: 0; border-top: 1px solid rgba(255,255,255,0.08); margin: 28px 0;">

                <!-- 4. ACESSIBILIDADE E DALTONISMO -->
                <h2 class="perfil-section-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><circle cx="12" cy="12" r="4"></circle><line x1="4.93" y1="4.93" x2="9.17" y2="9.17"></line><line x1="14.83" y1="14.83" x2="19.07" y2="19.07"></line><line x1="14.83" y1="9.17" x2="19.07" y2="4.93"></line><line x1="4.93" y1="19.07" x2="9.17" y2="14.83"></line></svg>
                    <span>Acessibilidade Visual & Daltonismo</span>
                </h2>
                <p style="font-size: 0.85rem; color: var(--color-text-muted); margin-top: -12px; margin-bottom: 16px;">Selecione o filtro ideal para sua visão. O modo é aplicado e salvo instantaneamente no navegador para todos os jogos e telas.</p>

                <div class="colorblind-grid">
                    <div class="colorblind-card active" data-mode="none">
                        <div class="colorblind-card-header">
                            <span class="colorblind-card-title">Cores Padrão</span>
                            <span class="accessibility-opt-badge">Normal</span>
                        </div>
                        <p class="colorblind-card-desc">Paleta original e balanceada do The Mathematical Game.</p>
                        <div class="colorblind-swatches">
                            <span class="swatch-item" style="background:#10b981;"></span>
                            <span class="swatch-item" style="background:#00a8ff;"></span>
                            <span class="swatch-item" style="background:#f59e0b;"></span>
                            <span class="swatch-item" style="background:#ef4444;"></span>
                        </div>
                    </div>

                    <div class="colorblind-card" data-mode="deuteranopia">
                        <div class="colorblind-card-header">
                            <span class="colorblind-card-title">Deuteranopia</span>
                            <span class="accessibility-opt-badge" style="color:#0284c7;">Verde</span>
                        </div>
                        <p class="colorblind-card-desc">Compensa o déficit no verde, realçando contrastes em azul e amarelo.</p>
                        <div class="colorblind-swatches">
                            <span class="swatch-item" style="background:#0284c7;"></span>
                            <span class="swatch-item" style="background:#38bdf8;"></span>
                            <span class="swatch-item" style="background:#facc15;"></span>
                            <span class="swatch-item" style="background:#f97316;"></span>
                        </div>
                    </div>

                    <div class="colorblind-card" data-mode="protanopia">
                        <div class="colorblind-card-header">
                            <span class="colorblind-card-title">Protanopia</span>
                            <span class="accessibility-opt-badge" style="color:#eab308;">Vermelho</span>
                        </div>
                        <p class="colorblind-card-desc">Compensa a baixa sensibilidade ao vermelho com azul e âmbar.</p>
                        <div class="colorblind-swatches">
                            <span class="swatch-item" style="background:#0284c7;"></span>
                            <span class="swatch-item" style="background:#38bdf8;"></span>
                            <span class="swatch-item" style="background:#f59e0b;"></span>
                            <span class="swatch-item" style="background:#eab308;"></span>
                        </div>
                    </div>

                    <div class="colorblind-card" data-mode="tritanopia">
                        <div class="colorblind-card-header">
                            <span class="colorblind-card-title">Tritanopia</span>
                            <span class="accessibility-opt-badge" style="color:#10b981;">Azul</span>
                        </div>
                        <p class="colorblind-card-desc">Compensa a baixa sensibilidade ao azul com esmeralda e carmim vivo.</p>
                        <div class="colorblind-swatches">
                            <span class="swatch-item" style="background:#059669;"></span>
                            <span class="swatch-item" style="background:#10b981;"></span>
                            <span class="swatch-item" style="background:#f43f5e;"></span>
                            <span class="swatch-item" style="background:#e11d48;"></span>
                        </div>
                    </div>

                    <div class="colorblind-card" data-mode="high-contrast">
                        <div class="colorblind-card-header">
                            <span class="colorblind-card-title">Alto Contraste</span>
                            <span class="accessibility-opt-badge">AAA</span>
                        </div>
                        <p class="colorblind-card-desc">Bordas reforçadas e nitidez extrema para máxima clareza e foco.</p>
                        <div class="colorblind-swatches">
                            <span class="swatch-item" style="background:#000000; border:1px solid #fff;"></span>
                            <span class="swatch-item" style="background:#ffffff;"></span>
                            <span class="swatch-item" style="background:#38bdf8;"></span>
                            <span class="swatch-item" style="background:#ff4d4d;"></span>
                        </div>
                    </div>
                </div>

                <hr style="border: 0; border-top: 1px solid rgba(255,255,255,0.08); margin: 28px 0;">

                <!-- 5. ACESSIBILIDADE TEA / AUTISMO -->
                <h2 class="perfil-section-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2zm0 18a8 8 0 1 1 8-8 8 8 0 0 1-8 8z"/><path d="M12 6v6l4 2"/></svg>
                    <span>Conforto &amp; Neurodivergência (TEA)</span>
                </h2>
                <p style="font-size: 0.85rem; color: var(--color-text-muted); margin-top: -12px; margin-bottom: 10px;">Recursos projetados para reduzir sobrecarga sensorial e melhorar o conforto. Ative um ou combine com os filtros de visão acima. Preferências salvas automaticamente no navegador.</p>

                <span class="tea-section-badge">🧩 Espectro Autista &amp; Neurodivergência</span>

                <div class="tea-grid">
                    <!-- Modo Calmo -->
                    <div class="tea-card" data-tea="calm">
                        <span class="tea-card-icon">🧘</span>
                        <span class="tea-card-title">Modo Calmo</span>
                        <p class="tea-card-desc">Remove todas as animações, partículas flutuantes, transições e efeitos pulsantes. Reduz a saturação visual para um ambiente mais previsível e silencioso.</p>
                        <span class="tea-card-check">✓ Ativo</span>
                    </div>

                    <!-- OpenDyslexic -->
                    <div class="tea-card" data-tea="dyslexia">
                        <span class="tea-card-icon">🔤</span>
                        <span class="tea-card-title">Fonte OpenDyslexic</span>
                        <p class="tea-card-desc">Substitui toda a tipografia pela OpenDyslexic, fonte projetada para reduzir erros de leitura e confusão de letras espelhadas. Útil para dislexia e TDAH.</p>
                        <span class="tea-card-check">✓ Ativo</span>
                    </div>
                </div>

                <hr style="border: 0; border-top: 1px solid rgba(255,255,255,0.08); margin: 28px 0;">


                <h2 class="perfil-section-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                    <span>Segurança da Conta (Opcional)</span>
                </h2>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 16px;">
                    <div class="form-group-custom">
                        <label for="inputSenhaAtual" class="form-label-custom">Senha Atual</label>
                        <input type="password" id="inputSenhaAtual" name="senha_atual" class="form-input-custom" placeholder="Digite apenas se desejar trocar">
                    </div>
                    <div class="form-group-custom">
                        <label for="inputNovaSenha" class="form-label-custom">Nova Senha</label>
                        <input type="password" id="inputNovaSenha" name="nova_senha" class="form-input-custom" placeholder="Mínimo 6 caracteres">
                    </div>
                    <div class="form-group-custom">
                        <label for="inputConfirmarSenha" class="form-label-custom">Confirmar Nova Senha</label>
                        <input type="password" id="inputConfirmarSenha" name="confirmar_senha" class="form-input-custom" placeholder="Repita a nova senha">
                    </div>
                </div>

                <!-- BOTÃO DE AÇÃO -->
                <div class="perfil-actions-row">
                    <button type="submit" class="btn-save-profile" id="btnSalvarPerfil">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        <span>Salvar Alterações</span>
                    </button>
                </div>

            </form>
        </div>

    </main>

    <!-- TOAST DE NOTIFICAÇÃO -->
    <div id="perfilToast" class="perfil-toast">
        <span id="toastIcon"></span>
        <span id="toastMsg"></span>
    </div>

    <!-- SCRIPTS -->
    <script>
        // Dropdown do perfil no header
        const btnProfileTrigger = document.getElementById('btnProfileTrigger');
        const profileMenuPopover = document.getElementById('profileMenuPopover');
        if (btnProfileTrigger && profileMenuPopover) {
            btnProfileTrigger.addEventListener('click', (e) => {
                e.stopPropagation();
                profileMenuPopover.classList.toggle('active');
            });
            document.addEventListener('click', (e) => {
                if (!profileMenuPopover.contains(e.target) && !btnProfileTrigger.contains(e.target)) {
                    profileMenuPopover.classList.remove('active');
                }
            });
        }

        // Tema: delegado ao theme.js (carregado abaixo)

        // Função de Exibição de Toast
        function mostrarToast(mensagem, tipo = 'success') {
            const toast = document.getElementById('perfilToast');
            const icon = document.getElementById('toastIcon');
            const msg = document.getElementById('toastMsg');
            
            toast.className = 'perfil-toast ' + tipo;
            icon.textContent = (tipo === 'success') ? '✅' : '❌';
            msg.textContent = mensagem;
            toast.classList.add('show');
            
            setTimeout(() => {
                toast.classList.remove('show');
            }, 4000);
        }

        // Seleção de Presets de Avatar
        const avatarItems = document.querySelectorAll('.preset-avatar-item');
        const avatarPresetInput = document.getElementById('avatarPresetInput');
        const currentAvatarImg = document.getElementById('currentAvatarImg');
        const avatarUploadInput = document.getElementById('avatarUploadInput');
        const avatarFileFeedback = document.getElementById('avatarFileFeedback');

        avatarItems.forEach(item => {
            item.addEventListener('click', () => {
                avatarItems.forEach(i => i.classList.remove('active'));
                item.classList.add('active');
                const src = item.getAttribute('data-src');
                avatarPresetInput.value = src;
                currentAvatarImg.src = src;
                avatarUploadInput.value = '';
                avatarFileFeedback.textContent = 'Avatar temático selecionado: ' + item.getAttribute('title');
            });
        });

        // Upload de Avatar Próprio com Preview Imediato
        avatarUploadInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                avatarItems.forEach(i => i.classList.remove('active'));
                avatarPresetInput.value = '';
                avatarFileFeedback.textContent = 'Arquivo selecionado: ' + file.name;
                
                const reader = new FileReader();
                reader.onload = (event) => {
                    currentAvatarImg.src = event.target.result;
                };
                reader.readAsDataURL(file);
            }
        });

        // Seleção de Presets de Banner
        const bannerItems = document.querySelectorAll('.preset-banner-item');
        const bannerPresetInput = document.getElementById('bannerPresetInput');
        const currentBannerImg = document.getElementById('currentBannerImg');
        const bannerUploadInput = document.getElementById('bannerUploadInput');
        const bannerFileFeedback = document.getElementById('bannerFileFeedback');

        bannerItems.forEach(item => {
            item.addEventListener('click', () => {
                bannerItems.forEach(b => b.classList.remove('active'));
                item.classList.add('active');
                const src = item.getAttribute('data-src');
                bannerPresetInput.value = src;
                currentBannerImg.src = src;
                bannerUploadInput.value = '';
                bannerFileFeedback.textContent = 'Banner temático selecionado: ' + item.getAttribute('title');
            });
        });

        // Upload de Banner Próprio com Preview Imediato
        const btnRemoverBanner = document.getElementById('btnRemoverBanner');
        const btnRemoverBannerTexto = document.getElementById('btnRemoverBannerTexto');
        const removerBannerInput = document.getElementById('removerBannerInput');
        const bannerPadrao = '../imagens/banners/banner_matematico.svg';
        let bannerSalvoOriginal = currentBannerImg.src;

        // Ação do Botão Remover Banner
        if (btnRemoverBanner) {
            btnRemoverBanner.addEventListener('click', () => {
                const estaMarcado = removerBannerInput.value === '1';

                if (estaMarcado) {
                    // Desfaz a remoção
                    removerBannerInput.value = '0';
                    btnRemoverBanner.classList.remove('is-removed');
                    btnRemoverBannerTexto.textContent = 'Remover Banner';
                    currentBannerImg.src = bannerSalvoOriginal;
                    bannerFileFeedback.textContent = 'Remoção cancelada. Banner original mantido.';
                    mostrarToast('Remoção do banner desfeita!', 'info');
                } else {
                    // Limpa arquivo selecionado no input
                    bannerUploadInput.value = '';

                    // Se já estiver usando o banner padrão
                    if (bannerSalvoOriginal.includes('banner_matematico.svg')) {
                        mostrarToast('Você já está utilizando o banner padrão do sistema.', 'info');
                        bannerFileFeedback.textContent = 'Banner padrão já em uso.';
                        return;
                    }

                    // Marca para remoção e atualiza o preview imediatamente
                    removerBannerInput.value = '1';
                    btnRemoverBanner.classList.add('is-removed');
                    btnRemoverBannerTexto.textContent = 'Desfazer Remoção';
                    currentBannerImg.src = bannerPadrao;
                    bannerFileFeedback.textContent = 'Banner marcado para remoção. Clique em "Salvar Alterações" para confirmar.';
                    mostrarToast('Banner marcado para remoção! Salve as alterações para confirmar.', 'success');
                }
            });
        }

        bannerUploadInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                if (btnRemoverBanner) {
                    btnRemoverBanner.classList.remove('is-removed');
                    btnRemoverBannerTexto.textContent = 'Remover Banner';
                }
                removerBannerInput.value = '0';
                bannerItems.forEach(b => b.classList.remove('active'));
                bannerPresetInput.value = '';
                bannerFileFeedback.textContent = 'Arquivo selecionado: ' + file.name;
                
                const reader = new FileReader();
                reader.onload = (event) => {
                    currentBannerImg.src = event.target.result;
                };
                reader.readAsDataURL(file);
            }
        });

        // Envio do Formulário de Edição via AJAX
        const perfilForm = document.getElementById('perfilForm');
        const btnSalvar = document.getElementById('btnSalvarPerfil');

        perfilForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            btnSalvar.disabled = true;
            const originalBtnContent = btnSalvar.innerHTML;
            btnSalvar.innerHTML = '<span>Salvando dados...</span>';

            const formData = new FormData(perfilForm);

            try {
                const response = await fetch('salvar_perfil.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.success) {
                    mostrarToast(result.mensagem, 'success');
                    
                    // Sincroniza os nomes, fotos e banners imediatamente
                    if (result.usuario) {
                        document.getElementById('currentDisplayName').textContent = result.usuario.nome;
                        const headerNames = document.querySelectorAll('.header-name-sync');
                        headerNames.forEach(el => el.textContent = result.usuario.nome);
                        
                        const headerAvatars = document.querySelectorAll('.header-avatar-sync');
                        headerAvatars.forEach(el => el.src = result.usuario.foto);

                        if (result.usuario.banner) {
                            currentBannerImg.src = result.usuario.banner;
                            bannerSalvoOriginal = currentBannerImg.src;
                        }
                    }

                    // Redefine estado do botão de remover banner
                    if (btnRemoverBanner) {
                        btnRemoverBanner.classList.remove('is-removed');
                        btnRemoverBannerTexto.textContent = 'Remover Banner';
                    }
                    removerBannerInput.value = '0';
                    bannerUploadInput.value = '';
                    bannerFileFeedback.textContent = 'Nenhum arquivo selecionado';
                    
                    // Limpa campos de senha
                    document.getElementById('inputSenhaAtual').value = '';
                    document.getElementById('inputNovaSenha').value = '';
                    document.getElementById('inputConfirmarSenha').value = '';
                } else {
                    mostrarToast(result.mensagem || 'Erro ao salvar perfil.', 'error');
                }
            } catch (err) {
                console.error(err);
                mostrarToast('Erro de comunicação com o servidor. Tente novamente.', 'error');
            } finally {
                btnSalvar.disabled = false;
                btnSalvar.innerHTML = originalBtnContent;
            }
        });
    </script>
    <script src="../js/accessibility.js?v=<?= time() ?>"></script>
    <script src="../js/theme.js?v=<?= time() ?>"></script>
</body>
</html>
