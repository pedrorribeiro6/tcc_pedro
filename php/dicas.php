<?php
session_start();

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
    <title>Fórmulas e Dicas de Estudo - The Mathematical Game</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Estilos Modulares do Projeto -->
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/style2.css">
    <link rel="stylesheet" href="../css/dicas.css">
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
                        <a href="historico.php" class="nav-link">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
                            <span>Histórico</span>
                        </a>
                    </li>
                    <li>
                        <a href="dicas.php" class="nav-link active">
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
            <li><a href="historico.php" class="nav-link"><span>Histórico</span></a></li>
            <li><a href="dicas.php" class="nav-link active"><span>Fórmulas & Dicas</span></a></li>
        </ul>
    </div>

    <!-- CONTEÚDO PRINCIPAL DE DICAS E FÓRMULAS -->
    <main class="dashboard-container">
        
        <div class="section-header-block" style="margin-bottom: 30px;">
            <div>
                <h1 class="block-title" style="font-size: 2.1rem;">
                    Central de Fórmulas e Guia Completo de Estudos
                </h1>
                <p class="block-subtitle">Aprenda a teoria, revise propriedades matemáticas e veja exemplos resolvidos passo a passo para dominar cada matéria!</p>
            </div>
        </div>

        <section class="formulas-card-container" style="margin-top: 0;">
            
            <!-- MATÉRIA 1: TEOREMA DE PITÁGORAS -->
            <div class="formula-tab-content active" id="tab-pitagoras">
                <h2 class="formula-category-title">Teorema de Pitágoras e Relações Métricas</h2>
                <p style="color: var(--color-text-muted); font-size: 0.95rem; margin-bottom: 24px; line-height: 1.6;">
                    O Teorema de Pitágoras relaciona os comprimentos dos lados de um triângulo retângulo (que possui um ângulo reto de 90°).
                </p>

                <div class="formula-cards-grid">
                    <div class="formula-detail-card">
                        <div class="formula-card-header">
                            Teorema de Pitágoras
                        </div>
                        <div class="math-expr-box">a² + b² = c²</div>
                        <p class="formula-desc-text">
                            A soma dos quadrados dos catetos (<strong>a</strong> e <strong>b</strong>) é exatamente igual ao quadrado da hipotenusa (<strong>c</strong>).
                        </p>
                        <div class="formula-example-box">
                            <strong>Exemplo Passo a Passo:</strong><br>
                            Dados os catetos a = 6 cm e b = 8 cm:<br>
                            c² = 6² + 8² = 36 + 64 = 100 ➔ c = √100 = <strong>10 cm</strong>.
                        </div>
                    </div>

                    <div class="formula-detail-card">
                        <div class="formula-card-header">
                            Triângulos Pitagóricos Famosos
                        </div>
                        <div class="math-expr-box">3 - 4 - 5 | 5 - 12 - 13</div>
                        <p class="formula-desc-text">
                            Ternos pitagóricos são triângulos cujas medidas dos lados são números inteiros. Seus múltiplos também formam triângulos retângulos!
                        </p>
                        <div class="formula-example-box">
                            <strong>Dica Rápida:</strong> Se os catetos medem 6 e 8 (dobro de 3 e 4), a hipotenusa será 10 (dobro de 5)!
                        </div>
                    </div>

                    <div class="formula-detail-card">
                        <div class="formula-card-header">
                            Encontrando um Cateto
                        </div>
                        <div class="math-expr-box">a = √(c² - b²)</div>
                        <p class="formula-desc-text">
                            Para encontrar a medida de um cateto desconhecido, subtraia o quadrado do outro cateto do quadrado da hipotenusa.
                        </p>
                        <div class="formula-example-box">
                            <strong>Exemplo:</strong> Hipotenusa c = 13 e b = 12:<br>
                            a² = 13² - 12² = 169 - 144 = 25 ➔ <strong>a = 5</strong>.
                        </div>
                    </div>
                </div>
            </div>

            <!-- MATÉRIA 2: RADICIAÇÃO & POTENCIAÇÃO -->
            <div class="formula-tab-content" id="tab-radiciacao">
                <h2 class="formula-category-title">Radiciação e Propriedades de Potências</h2>
                <p style="color: var(--color-text-muted); font-size: 0.95rem; margin-bottom: 24px; line-height: 1.6;">
                    Radiciação é a operação inversa da potenciação. Aprenda as propriedades fundamentais para simplificar radicais complexos.
                </p>

                <div class="formula-cards-grid">
                    <div class="formula-detail-card">
                        <div class="formula-card-header">
                            Produto de Radicais
                        </div>
                        <div class="math-expr-box">√(a · b) = √a · √b</div>
                        <p class="formula-desc-text">
                            A raiz de uma multiplicação pode ser separada na multiplicação de duas raízes independentes.
                        </p>
                        <div class="formula-example-box">
                            <strong>Exemplo:</strong> √50 = √(25 · 2) = √25 · √2 = <strong>5√2</strong>.
                        </div>
                    </div>

                    <div class="formula-detail-card">
                        <div class="formula-card-header">
                            Divisão de Radicais
                        </div>
                        <div class="math-expr-box">√(a / b) = √a / √b</div>
                        <p class="formula-desc-text">
                            A raiz de uma fração é igual à divisão da raiz do numerador pela raiz do denominador.
                        </p>
                        <div class="formula-example-box">
                            <strong>Exemplo:</strong> √(36 / 4) = √36 / √4 = 6 / 2 = <strong>3</strong>.
                        </div>
                    </div>

                    <div class="formula-detail-card">
                        <div class="formula-card-header">
                            Expoentes Fracionários
                        </div>
                        <div class="math-expr-box">a^(m/n) = ⁿ√(aᵐ)</div>
                        <p class="formula-desc-text">
                            "Quem está ao sol vai para a sombra, quem está na sombra vai para o sol". O denominador vira o índice da raiz!
                        </p>
                        <div class="formula-example-box">
                            <strong>Exemplo:</strong> 8^(2/3) = ³√(8²) = ³√64 = <strong>4</strong>.
                        </div>
                    </div>
                </div>
            </div>

            <!-- MATÉRIA 3: GEOMETRIA 2D E 3D -->
            <div class="formula-tab-content" id="tab-geometria">
                <h2 class="formula-category-title">Geometria Plana (Áreas 2D) & Espacial (Volumes 3D)</h2>
                <p style="color: var(--color-text-muted); font-size: 0.95rem; margin-bottom: 24px; line-height: 1.6;">
                    Fórmulas fundamentais para determinar a superfície de figuras no plano e a capacidade volumétrica de sólidos tridimensionais.
                </p>



                <div class="formula-cards-grid">
                    <div class="formula-detail-card">
                        <div class="formula-card-header">Área do Quadrado</div>
                        <div class="math-expr-box">A = l²</div>
                        <p class="formula-desc-text">Eleve a medida do lado (l) ao quadrado.</p>
                        <div class="formula-example-box"><strong>Exemplo:</strong> l = 6 cm ➔ A = 6² = <strong>36 cm²</strong>.</div>
                    </div>

                    <div class="formula-detail-card">
                        <div class="formula-card-header">Área do Retângulo</div>
                        <div class="math-expr-box">A = b · h</div>
                        <p class="formula-desc-text">Multiplique a base (b) pela altura (h).</p>
                        <div class="formula-example-box"><strong>Exemplo:</strong> b = 8 cm e h = 4 cm ➔ A = <strong>32 cm²</strong>.</div>
                    </div>

                    <div class="formula-detail-card">
                        <div class="formula-card-header">Área do Círculo</div>
                        <div class="math-expr-box">A = π · r²</div>
                        <p class="formula-desc-text">Use o raio (r) ao quadrado multiplicado por π (aproximadamente 3,14).</p>
                        <div class="formula-example-box"><strong>Exemplo:</strong> r = 3 cm ➔ A ≈ 3,14 · 9 = <strong>28,26 cm²</strong>.</div>
                    </div>

                    <div class="formula-detail-card">
                        <div class="formula-card-header">Área do Triângulo</div>
                        <div class="math-expr-box">A = (b · h) / 2</div>
                        <p class="formula-desc-text">Multiplique a base (b) pela altura (h) e divida o resultado por 2.</p>
                        <div class="formula-example-box"><strong>Exemplo:</strong> b = 10 cm e h = 6 cm ➔ A = <strong>30 cm²</strong>.</div>
                    </div>

                    <div class="formula-detail-card">
                        <div class="formula-card-header">Área do Pentágono</div>
                        <div class="math-expr-box">A = (P · a) / 2</div>
                        <p class="formula-desc-text">Para um pentágono regular, multiplique o perímetro (P) pelo apótema (a) e divida por 2.</p>
                        <div class="formula-example-box"><strong>Exemplo:</strong> P = 25 cm e a = 3,44 cm ➔ A = <strong>43 cm²</strong>.</div>
                    </div>

                    <div class="formula-detail-card">
                        <div class="formula-card-header">Área do Hexágono</div>
                        <div class="math-expr-box">A = (P · a) / 2</div>
                        <p class="formula-desc-text">No hexágono regular, P é o perímetro e a é o apótema.</p>
                        <div class="formula-example-box"><strong>Exemplo:</strong> P = 36 cm e a = 5,2 cm ➔ A = <strong>93,6 cm²</strong>.</div>
                    </div>

                    <div class="formula-detail-card">
                        <div class="formula-card-header">Área do Heptágono</div>
                        <div class="math-expr-box">A = (P · a) / 2</div>
                        <p class="formula-desc-text">Para o heptágono regular, use o perímetro (P) e o apótema (a).</p>
                        <div class="formula-example-box"><strong>Exemplo:</strong> P = 35 cm e a = 5 cm ➔ A = <strong>87,5 cm²</strong>.</div>
                    </div>

                    <div class="formula-detail-card">
                        <div class="formula-card-header">Área do Octógono</div>
                        <div class="math-expr-box">A = (P · a) / 2</div>
                        <p class="formula-desc-text">No octógono regular, multiplique o perímetro (P) pelo apótema (a) e divida por 2.</p>
                        <div class="formula-example-box"><strong>Exemplo:</strong> P = 40 cm e a = 4,83 cm ➔ A = <strong>96,6 cm²</strong>.</div>
                    </div>

                    <div class="formula-detail-card">
                        <div class="formula-card-header">Área do Losango</div>
                        <div class="math-expr-box">A = (D · d) / 2</div>
                        <p class="formula-desc-text">Multiplique as diagonais maior (D) e menor (d) e divida por 2.</p>
                        <div class="formula-example-box"><strong>Exemplo:</strong> D = 10 cm e d = 6 cm ➔ A = <strong>30 cm²</strong>.</div>
                    </div>

                    <div class="formula-detail-card">
                        <div class="formula-card-header">Área do Trapézio</div>
                        <div class="math-expr-box">A = ((B + b) · h) / 2</div>
                        <p class="formula-desc-text">Some as bases maior (B) e menor (b), multiplique pela altura (h) e divida por 2.</p>
                        <div class="formula-example-box"><strong>Exemplo:</strong> B = 10 cm, b = 6 cm e h = 4 cm ➔ A = <strong>32 cm²</strong>.</div>
                    </div>

                    <div class="formula-detail-card">
                        <div class="formula-card-header">Volume do Cubo</div>
                        <div class="math-expr-box">V = a³</div>
                        <p class="formula-desc-text">Eleve a medida da aresta (a) ao cubo.</p>
                        <div class="formula-example-box"><strong>Exemplo:</strong> a = 5 cm ➔ V = 5³ = <strong>125 cm³</strong>.</div>
                    </div>

                    <div class="formula-detail-card">
                        <div class="formula-card-header">Volume do Cilindro</div>
                        <div class="math-expr-box">V = π · r² · h</div>
                        <p class="formula-desc-text">Multiplique a área da base circular pela altura (h).</p>
                        <div class="formula-example-box"><strong>Exemplo:</strong> r = 2 cm e h = 10 cm ➔ V ≈ <strong>125,6 cm³</strong>.</div>
                    </div>

                    <div class="formula-detail-card">
                        <div class="formula-card-header">Volume da Esfera</div>
                        <div class="math-expr-box">V = (4 · π · r³) / 3</div>
                        <p class="formula-desc-text">Eleve o raio (r) ao cubo, multiplique por 4π e divida por 3.</p>
                        <div class="formula-example-box"><strong>Exemplo:</strong> r = 3 cm ➔ V = 36π ≈ <strong>113,04 cm³</strong>.</div>
                    </div>

                    <div class="formula-detail-card">
                        <div class="formula-card-header">Volume do Cone</div>
                        <div class="math-expr-box">V = (π · r² · h) / 3</div>
                        <p class="formula-desc-text">Calcule o volume do cilindro com a mesma base e altura e divida por 3.</p>
                        <div class="formula-example-box"><strong>Exemplo:</strong> r = 3 cm e h = 4 cm ➔ V ≈ <strong>37,68 cm³</strong>.</div>
                    </div>

                    <div class="formula-detail-card">
                        <div class="formula-card-header">Volume da Pirâmide</div>
                        <div class="math-expr-box">V = (Aᵦ · h) / 3</div>
                        <p class="formula-desc-text">Multiplique a área da base (Aᵦ) pela altura (h) e divida por 3.</p>
                        <div class="formula-example-box"><strong>Exemplo:</strong> Aᵦ = 30 cm² e h = 9 cm ➔ V = <strong>90 cm³</strong>.</div>
                    </div>

                    <div class="formula-detail-card">
                        <div class="formula-card-header">Volume da Pirâmide Triangular</div>
                        <div class="math-expr-box">V = (Aᵦ · h) / 3</div>
                        <p class="formula-desc-text">Multiplique a área da base triangular (Aᵦ) pela altura e divida por 3.</p>
                        <div class="formula-example-box"><strong>Exemplo:</strong> Aᵦ = 12 cm² e h = 9 cm ➔ V = <strong>36 cm³</strong>.</div>
                    </div>

                    <div class="formula-detail-card">
                        <div class="formula-card-header">Volume do Paralelepípedo</div>
                        <div class="math-expr-box">V = c · l · h</div>
                        <p class="formula-desc-text">Multiplique o comprimento (c), a largura (l) e a altura (h).</p>
                        <div class="formula-example-box"><strong>Exemplo:</strong> 5 cm · 3 cm · 2 cm = <strong>30 cm³</strong>.</div>
                    </div>

                    <div class="formula-detail-card">
                        <div class="formula-card-header">Volume do Prisma Pentagonal</div>
                        <div class="math-expr-box">V = Aᵦ · h</div>
                        <p class="formula-desc-text">Calcule a área da base pentagonal (Aᵦ) e multiplique pela altura (h).</p>
                        <div class="formula-example-box"><strong>Exemplo:</strong> Aᵦ = 20 cm² e h = 8 cm ➔ V = <strong>160 cm³</strong>.</div>
                    </div>

                    <div class="formula-detail-card">
                        <div class="formula-card-header">Volume do Prisma Hexagonal</div>
                        <div class="math-expr-box">V = Aᵦ · h</div>
                        <p class="formula-desc-text">Calcule a área da base hexagonal (Aᵦ) e multiplique pela altura (h).</p>
                        <div class="formula-example-box"><strong>Exemplo:</strong> Aᵦ = 30 cm² e h = 6 cm ➔ V = <strong>180 cm³</strong>.</div>
                    </div>

                    <div class="formula-detail-card">
                        <div class="formula-card-header">Volume do Prisma Heptagonal</div>
                        <div class="math-expr-box">V = Aᵦ · h</div>
                        <p class="formula-desc-text">Calcule a área da base heptagonal (Aᵦ) e multiplique pela altura (h).</p>
                        <div class="formula-example-box"><strong>Exemplo:</strong> Aᵦ = 35 cm² e h = 4 cm ➔ V = <strong>140 cm³</strong>.</div>
                    </div>

                    <div class="formula-detail-card">
                        <div class="formula-card-header">Volume do Prisma Octagonal</div>
                        <div class="math-expr-box">V = Aᵦ · h</div>
                        <p class="formula-desc-text">Calcule a área da base octagonal (Aᵦ) e multiplique pela altura (h).</p>
                        <div class="formula-example-box"><strong>Exemplo:</strong> Aᵦ = 40 cm² e h = 3 cm ➔ V = <strong>120 cm³</strong>.</div>
                    </div>
                </div>
            </div>

            <!-- MATÉRIA 4: PLANO CARTESIANO -->
            <div class="formula-tab-content" id="tab-cartesiano">
                <h2 class="formula-category-title">Plano Cartesiano e Geometria Analítica</h2>
                <p style="color: var(--color-text-muted); font-size: 0.95rem; margin-bottom: 24px; line-height: 1.6;">
                    O plano cartesiano consiste em duas retas numéricas perpendiculares que se cruzam na Origem (0,0).
                </p>

                <div class="formula-cards-grid">
                    <div class="formula-detail-card">
                        <div class="formula-card-header">
                            <span>📍</span> Pares Ordenados (x, y)
                        </div>
                        <div class="math-expr-box">Ponto P = (x, y)</div>
                        <p class="formula-desc-text">
                            O valor de <strong>x</strong> (Eixo das Abscissas - Horizontal) sempre vem em primeiro lugar. O valor de <strong>y</strong> (Eixo das Ordenadas - Vertical) vem em segundo.
                        </p>
                        <div class="formula-example-box">
                            <strong>Quadrantes:</strong><br>
                            1º Quad: (+, +) | 2º Quad: (-, +)<br>
                            3º Quad: (-, -) | 4º Quad: (+, -)
                        </div>
                    </div>

                    <div class="formula-detail-card">
                        <div class="formula-card-header">
                            <span>📏</span> Distância Entre Dois Pontos
                        </div>
                        <div class="math-expr-box">d = √[(x₂ - x₁)² + (y₂ - y₁)²]</div>
                        <p class="formula-desc-text">
                            Fórmula para calcular a distância em linha reta entre dois pontos quaisquer A(x₁, y₁) e B(x₂, y₂).
                        </p>
                        <div class="formula-example-box">
                            <strong>Exemplo:</strong> A(1, 2) e B(4, 6):<br>
                            d = √[(4-1)² + (6-2)²] = √[3² + 4²] = √25 = <strong>5 unidades</strong>.
                        </div>
                    </div>
                </div>
            </div>

            <!-- MATÉRIA 5: OPERAÇÕES COM FRAÇÕES -->
            <div class="formula-tab-content" id="tab-fracoes">
                <h2 class="formula-category-title">Operações com Frações</h2>
                <p style="color: var(--color-text-muted); font-size: 0.95rem; margin-bottom: 24px; line-height: 1.6;">
                    Aprenda a somar, subtrair, multiplicar e dividir frações com denominadores iguais e diferentes. Dominar frações é a chave para avançar na matemática!
                </p>

                <div class="formula-cards-grid">

                    <!-- ADIÇÃO -->
                    <div class="formula-detail-card">
                        <div class="formula-card-header">➕ Adição de Frações</div>
                        <div class="math-expr-box">a/b + c/b = (a + c) / b</div>
                        <p class="formula-desc-text">
                            <strong>Denominadores iguais:</strong> Mantenha o denominador e some apenas os numeradores.<br><br>
                            <strong>Denominadores diferentes:</strong> Calcule o MMC dos denominadores, transforme as frações equivalentes e então some os numeradores.
                        </p>
                        <div class="formula-example-box">
                            <strong>Exemplo (iguais):</strong> 2/7 + 3/7 = <strong>5/7</strong>.<br><br>
                            <strong>Exemplo (diferentes):</strong> 1/4 + 1/6 ➔ MMC(4, 6) = 12<br>
                            (3/12) + (2/12) = <strong>5/12</strong>.
                        </div>
                    </div>

                    <!-- SUBTRAÇÃO -->
                    <div class="formula-detail-card">
                        <div class="formula-card-header">➖ Subtração de Frações</div>
                        <div class="math-expr-box">a/b - c/b = (a - c) / b</div>
                        <p class="formula-desc-text">
                            <strong>Denominadores iguais:</strong> Mantenha o denominador e subtraia os numeradores.<br><br>
                            <strong>Denominadores diferentes:</strong> Iguale os denominadores usando o MMC e depois subtraia os numeradores.
                        </p>
                        <div class="formula-example-box">
                            <strong>Exemplo (iguais):</strong> 5/9 - 2/9 = <strong>3/9 = 1/3</strong>.<br><br>
                            <strong>Exemplo (diferentes):</strong> 3/4 - 1/3 ➔ MMC(4, 3) = 12<br>
                            (9/12) - (4/12) = <strong>5/12</strong>.
                        </div>
                    </div>

                    <!-- MULTIPLICAÇÃO -->
                    <div class="formula-detail-card">
                        <div class="formula-card-header">✖️ Multiplicação de Frações</div>
                        <div class="math-expr-box">(a/b) · (c/d) = (a · c) / (b · d)</div>
                        <p class="formula-desc-text">
                            Multiplique numerador com numerador e denominador com denominador. Não é necessário igualar os denominadores!
                        </p>
                        <div class="formula-example-box">
                            <strong>Exemplo:</strong> (2/3) · (3/4) = (2 · 3) / (3 · 4) = 6/12 = <strong>1/2</strong>.<br><br>
                            <strong>Dica:</strong> Simplifique em cruz antes de multiplicar para evitar números grandes!<br>
                            2/3 · 3/4 ➔ cancela o 3 ➔ 2/1 · 1/4 = <strong>2/4 = 1/2</strong>.
                        </div>
                    </div>

                    <!-- DIVISÃO -->
                    <div class="formula-detail-card">
                        <div class="formula-card-header">➗ Divisão de Frações</div>
                        <div class="math-expr-box">(a/b) ÷ (c/d) = (a/b) · (d/c)</div>
                        <p class="formula-desc-text">
                            Mantenha a primeira fração, troque a operação por multiplicação e inverta (vire) a segunda fração (numerador e denominador trocam de lugar).
                        </p>
                        <div class="formula-example-box">
                            <strong>Exemplo:</strong> (3/4) ÷ (2/5) = (3/4) · (5/2) = 15/8 = <strong>1 e 7/8</strong>.<br><br>
                            <strong>Macete:</strong> "Mantenha, Mude, Inverta" — Keep, Change, Flip!
                        </div>
                    </div>

                    <!-- SIMPLIFICAÇÃO -->
                    <div class="formula-detail-card">
                        <div class="formula-card-header">🔢 Simplificação de Frações</div>
                        <div class="math-expr-box">a/b ➔ (a ÷ MDC) / (b ÷ MDC)</div>
                        <p class="formula-desc-text">
                            Encontre o Máximo Divisor Comum (MDC) entre numerador e denominador e divida ambos por ele até obter a fração irredutível.
                        </p>
                        <div class="formula-example-box">
                            <strong>Exemplo:</strong> 12/18 ➔ MDC(12, 18) = 6<br>
                            12 ÷ 6 = 2 e 18 ÷ 6 = 3 ➔ fração simplificada: <strong>2/3</strong>.
                        </div>
                    </div>

                    <!-- FRAÇÕES EQUIVALENTES -->
                    <div class="formula-detail-card">
                        <div class="formula-card-header">🔄 Frações Equivalentes</div>
                        <div class="math-expr-box">a/b = (a · k) / (b · k)</div>
                        <p class="formula-desc-text">
                            Multiplique ou divida numerador e denominador pelo mesmo número (k ≠ 0) para obter uma fração equivalente com o mesmo valor.
                        </p>
                        <div class="formula-example-box">
                            <strong>Exemplo:</strong> 1/2 = 2/4 = 3/6 = 4/8 — todas têm o mesmo valor!<br><br>
                            <strong>Dica:</strong> Essa técnica é fundamental para igualar denominadores na adição e subtração.
                        </div>
                    </div>

                </div>
            </div>

            <!-- MATÉRIA 6: EQUAÇÕES DO 1º E 2º GRAU -->
            <div class="formula-tab-content" id="tab-equacoes">
                <h2 class="formula-category-title">Equações do 1º e 2º Grau (Fórmula de Bhaskara)</h2>
                <p style="color: var(--color-text-muted); font-size: 0.95rem; margin-bottom: 24px; line-height: 1.6;">
                    Aprenda a encontrar os valores desconhecidos (raízes) que tornam uma igualdade verdadeira.
                </p>

                <div class="formula-cards-grid">
                    <div class="formula-detail-card">
                        <div class="formula-card-header">
                            <span>⚡</span> Equação do 1º Grau
                        </div>
                        <div class="math-expr-box">ax + b = c ➔ x = (c - b) / a</div>
                        <p class="formula-desc-text">
                            Isola-se o termo com a incógnita <strong>x</strong> em um dos lados da igualdade.
                        </p>
                        <div class="formula-example-box">
                            <strong>Exemplo:</strong> 3x - 7 = 11 ➔ 3x = 18 ➔ <strong>x = 6</strong>.
                        </div>
                    </div>

                    <div class="formula-detail-card">
                        <div class="formula-card-header">
                            <span>🎯</span> Fórmula de Bhaskara (2º Grau)
                        </div>
                        <div class="math-expr-box">Δ = b² - 4ac | x = (-b ± √Δ) / 2a</div>
                        <p class="formula-desc-text">
                            Fórmula para calcular as raízes de equações quadráticas da forma ax² + bx + c = 0.
                        </p>
                        <div class="formula-example-box">
                            <strong>Análise do Delta (Δ):</strong><br>
                            • Δ > 0: Duas raízes reais diferentes.<br>
                            • Δ = 0: Uma única raiz real.<br>
                            • Δ < 0: Nenhuma raiz real.
                        </div>
                    </div>
                </div>
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
    <script src="../js/formulas.js"></script>
</body>
</html>
