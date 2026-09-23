<?php
session_start();
require_once 'conexao.php';

$mensagem_erro = '';

// Se já estiver logado, redireciona para a tela inicial (home.php)
if (isset($_SESSION['usuario_nome'])) {
    header('Location: home.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['login_email']) && !empty($_POST['login_senha'])) {
        $loginInput = trim($_POST['login_email']);
        $senhaInput = trim($_POST['login_senha']);

        if ($pdo) {
            try {
                $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email LIMIT 1");
                $stmt->execute(['email' => $loginInput]);
                $usuario = $stmt->fetch();

                if ($usuario && !empty($usuario['senha']) && password_verify($senhaInput, $usuario['senha'])) {
                    $_SESSION['usuario_id']     = $usuario['id'];
                    $_SESSION['usuario_nome']   = $usuario['nome'];
                    $_SESSION['usuario_email']  = $usuario['email'];
                    $_SESSION['usuario_pontos'] = $usuario['pontos'] ?? 0;
                    $_SESSION['usuario_nivel']  = floor($_SESSION['usuario_pontos'] / 1000);
                    $_SESSION['usuario_foto']   = $usuario['foto_perfil'] ?: '../imagens/logo_matematico.png';
                    header('Location: home.php');
                    exit;
                } else {
                    $mensagem_erro = 'E-mail ou senha incorretos. Tente novamente.';
                }
            } catch (PDOException $e) {
                error_log('Erro no login: ' . $e->getMessage());
                $mensagem_erro = 'Erro no servidor. Tente novamente em instantes.';
            }
        } else {
            $mensagem_erro = 'Não foi possível conectar ao banco de dados. Verifique o servidor.';
        }
    }
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
    
  
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/style3.css">


   
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

    
    <script src="../js/theme.js"></script>
    <script src="../js/index.js"></script>
</body>
</html>