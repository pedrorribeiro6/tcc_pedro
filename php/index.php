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
    <title>The Mathematical Game - Login</title>
    
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
  
    <link rel="stylesheet" href="../css/style.css">

   
    <script src="https://accounts.google.com/gsi/client" async defer></script>
</head>
<body>

    <!-- Fundo Matemático com Símbolos Flutuantes -->
    <div class="math-background">
        <div class="math-symbol">&pi;</div>
        <div class="math-symbol">&sum;</div>
        <div class="math-symbol">&int;</div>
        <div class="math-symbol">&radic;</div>
        <div class="math-symbol">&infin;</div>
        <div class="math-symbol">&times;</div>
    </div>

   
    <main class="auth-container">
        <div class="auth-card">
            
            
            <div class="auth-header">
                <div class="logo-wrapper">
                    <img src="../imagens/logo_matematico.png" alt="The Mathematical Game Logo" class="logo-img">
                </div>
                <h1 class="auth-title">Bem-vindo ao <span class="highlight">The Mathematical Game</span></h1>
                <p class="auth-subtitle">Entre para explorar desafios, fórmulas e conquistas matemáticas.</p>
            </div>

            
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

           
            <form action="index.php" method="POST" class="auth-form" id="formLogin">
                
                
                <div class="form-group">
                    <label for="login_email" class="form-label">E-mail ou Usuário</label>
                    <div class="input-container">
                        <span class="input-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                <polyline points="22,6 12,13 2,6"></polyline>
                            </svg>
                        </span>
                        <input type="text" id="login_email" name="login_email" class="form-input" placeholder="seu.email@exemplo.com" required autocomplete="username">
                    </div>
                </div>

                
                <div class="form-group">
                    <div class="form-label">
                        <label for="login_senha">Senha</label>
                    </div>
                    <div class="input-container">
                        <span class="input-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                        </span>
                        <input type="password" id="login_senha" name="login_senha" class="form-input" placeholder="••••••••" required autocomplete="current-password">
                        <button type="button" class="btn-toggle-password" data-target="login_senha" aria-label="Mostrar ou ocultar senha">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="form-extras">
                    <label class="checkbox-label">
                        <input type="checkbox" name="lembrar" id="lembrar">
                        <span>Lembrar de mim</span>
                    </label>
                    <a href="#" class="link-forgot">Esqueceu a senha?</a>
                </div>

                
                <button type="submit" class="btn-primary">
                    <span>Acessar Plataforma</span>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </button>
            </form>

            
            <div class="auth-divider">
                <span>ou continue com</span>
            </div>

            
            <div class="google-auth-wrapper">
                <!-- Div Oficial do Google GIS -->
                <div id="g_id_onload"
                     data-client_id="SEU_GOOGLE_CLIENT_ID.apps.googleusercontent.com"
                     data-context="signin"
                     data-ux_mode="popup"
                     data-callback="handleGoogleCredentialResponse"
                     data-auto_prompt="false">
                </div>

                <div class="g_id_signin"
                     data-type="standard"
                     data-shape="rectangular"
                     data-theme="outline"
                     data-text="signin_with"
                     data-size="large"
                     data-logo_alignment="left"
                     data-width="390">
                </div>
            </div>

           
            <div class="auth-footer">
                Não tem uma conta matemática? <a href="cadastro.php">Crie uma agora</a>
            </div>

        </div>
    </main>

    
    <script src="../js/index.js"></script>
</body>
</html>