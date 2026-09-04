<?php

session_start();

$mensagem_erro = '';
$mensagem_sucesso = '';

if (isset($_SESSION['sucesso'])) {
    $mensagem_sucesso = $_SESSION['sucesso'];
    unset($_SESSION['sucesso']);
}
if (isset($_SESSION['erro'])) {
    $mensagem_erro = $_SESSION['erro'];
    unset($_SESSION['erro']);
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Mathematical Game - Criar Conta</title>
    
    <!-- Google Fonts & Favicon -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Estilo Personalizado -->
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/style3.css">

    <!-- Google Identity Services API (GIS) -->
    <script src="https://accounts.google.com/gsi/client" async defer></script>
</head>
<body class="auth-body">

    <!-- Fundo Matemático com Símbolos Flutuantes -->
    <div class="math-background">
        <div class="math-symbol">&pi;</div>
        <div class="math-symbol">&sum;</div>
        <div class="math-symbol">&int;</div>
        <div class="math-symbol">&radic;</div>
        <div class="math-symbol">&infin;</div>
        <div class="math-symbol">&times;</div>
    </div>

    <!-- Botão de troca de tema -->
    <div class="auth-theme-toggle-wrapper">
        <button id="themeToggle" class="theme-toggle" aria-label="Ativar tema claro" title="Ativar tema claro" data-theme-label="Claro">
            <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="4"></circle><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"></path></svg><span>Claro</span>
        </button>
    </div>

    <!-- Container Principal -->
    <main class="auth-container">
        <div class="auth-card">
            
            <!-- Cabeçalho com a Logo Oficial -->
            <div class="auth-header">
                <div class="logo-wrapper">
                    <img src="../imagens/logo_matematico.png" alt="The Mathematical Game Logo" class="logo-img">
                </div>
                <h1 class="auth-title">Crie sua Conta <span class="highlight">Matemática</span></h1>
                <p class="auth-subtitle">Junte-se à comunidade e suba no ranking do conhecimento matemático.</p>
            </div>

            <!-- Alertas de Feedback -->
            <?php if (!empty($mensagem_erro)): ?>
                <div class="alert-box alert-error">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <span><?= htmlspecialchars($mensagem_erro); ?></span>
                </div>
            <?php endif; ?>

            <?php if (!empty($mensagem_sucesso)): ?>
                <div class="alert-box alert-success">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                    <span><?= htmlspecialchars($mensagem_sucesso); ?></span>
                </div>
            <?php endif; ?>

            
            <form action="cadastro.php" method="POST" class="auth-form" id="formCadastro">
                
                
                <div class="form-group">
                    <label for="cad_nome" class="form-label">Nome Completo</label>
                    <div class="input-container">
                        <span class="input-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </span>
                        <input type="text" id="cad_nome" name="cad_nome" class="form-input" placeholder="Ex: Arquimedes da Silva" required autocomplete="name">
                    </div>
                </div>

               
                <div class="form-group">
                    <label for="cad_email" class="form-label">E-mail</label>
                    <div class="input-container">
                        <span class="input-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                <polyline points="22,6 12,13 2,6"></polyline>
                            </svg>
                        </span>
                        <input type="email" id="cad_email" name="cad_email" class="form-input" placeholder="seu.email@exemplo.com" required autocomplete="email">
                    </div>
                </div>

                
                <div class="form-group">
                    <label for="cad_senha" class="form-label">Senha</label>
                    <div class="input-container">
                        <span class="input-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                        </span>
                        <input type="password" id="cad_senha" name="cad_senha" class="form-input" placeholder="Mínimo de 6 caracteres" required minlength="6" autocomplete="new-password">
                        <button type="button" class="btn-toggle-password" data-target="cad_senha" aria-label="Mostrar ou ocultar senha">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                    </div>

                    
                    <div class="password-strength-container">
                        <div class="strength-bar">
                            <div class="strength-progress" id="strengthProgress"></div>
                        </div>
                        <div class="strength-text" id="strengthLabel">Força da senha</div>
                    </div>
                </div>

                
                <div class="form-group">
                    <label for="cad_confirma_senha" class="form-label">Confirmar Senha</label>
                    <div class="input-container">
                        <span class="input-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                            </svg>
                        </span>
                        <input type="password" id="cad_confirma_senha" name="cad_confirma_senha" class="form-input" placeholder="Digite a senha novamente" required minlength="6" autocomplete="new-password">
                        <button type="button" class="btn-toggle-password" data-target="cad_confirma_senha" aria-label="Mostrar ou ocultar senha">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                    </div>
                </div>

                
                <div class="form-extras">
                    <label class="checkbox-label">
                        <input type="checkbox" name="termos" id="termos" required>
                        <span>Concordo com os termos e regras de estudo</span>
                    </label>
                </div>

                
                <button type="submit" class="btn-primary">
                    <span>Criar Minha Conta</span>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="8.5" cy="7" r="4"></circle>
                        <line x1="20" y1="8" x2="20" y2="14"></line>
                        <line x1="23" y1="11" x2="17" y2="11"></line>
                    </svg>
                </button>
            </form>

            
            <div class="auth-divider">
                <span>ou cadastre-se com</span>
            </div>

           
            <div class="google-auth-wrapper">
                <div id="g_id_onload"
                     data-client_id="SEU_GOOGLE_CLIENT_ID.apps.googleusercontent.com"
                     data-context="signup"
                     data-ux_mode="popup"
                     data-callback="handleGoogleCredentialResponse"
                     data-auto_prompt="false">
                </div>

                <div class="g_id_signin"
                     data-type="standard"
                     data-shape="rectangular"
                     data-theme="outline"
                     data-text="signup_with"
                     data-size="large"
                     data-logo_alignment="left"
                     data-width="390">
                </div>
            </div>

           
            <div class="auth-footer">
                Já possui uma conta matemática? <a href="index.php">Fazer Login</a>
            </div>

        </div>
    </main>

    
    <script src="../js/theme.js"></script>
    <script src="../js/index.js"></script>
</body>
</html>
