/**
 * THE MATHEMATICAL GAME - INTERATIVIDADE & GOOGLE AUTHENTICATION
 */

document.addEventListener('DOMContentLoaded', () => {
    initPasswordToggles();
    initPasswordStrengthMeter();
    initFormValidations();
    initGoogleAuth();
});

/**
 * Alternar visibilidade da senha (mostrar/ocultar)
 */
function initPasswordToggles() {
    const toggleButtons = document.querySelectorAll('.btn-toggle-password');

    toggleButtons.forEach(button => {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            const targetInputId = button.getAttribute('data-target');
            const input = document.getElementById(targetInputId);
            const icon = button.querySelector('i') || button.querySelector('svg');

            if (!input) return;

            if (input.type === 'password') {
                input.type = 'text';
                button.setAttribute('aria-label', 'Ocultar senha');
                button.innerHTML = `
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                        <line x1="1" y1="1" x2="23" y2="23"></line>
                    </svg>
                `;
            } else {
                input.type = 'password';
                button.setAttribute('aria-label', 'Mostrar senha');
                button.innerHTML = `
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                `;
            }
        });
    });
}

/**
 * Medidor de força da senha em tempo real na tela de cadastro
 */
function initPasswordStrengthMeter() {
    const passwordInput = document.getElementById('cad_senha');
    const strengthProgress = document.getElementById('strengthProgress');
    const strengthLabel = document.getElementById('strengthLabel');

    if (!passwordInput || !strengthProgress || !strengthLabel) return;

    passwordInput.addEventListener('input', () => {
        const val = passwordInput.value;
        let score = 0;

        if (!val) {
            strengthProgress.style.width = '0%';
            strengthProgress.style.backgroundColor = 'transparent';
            strengthLabel.textContent = 'Força da senha';
            return;
        }

        if (val.length >= 6) score += 25;
        if (val.length >= 8) score += 15;
        if (/[A-Z]/.test(val)) score += 20;
        if (/[0-9]/.test(val)) score += 20;
        if (/[^A-Za-z0-9]/.test(val)) score += 20;

        score = Math.min(score, 100);
        strengthProgress.style.width = `${score}%`;

        if (score < 40) {
            strengthProgress.style.backgroundColor = '#ef4444';
            strengthLabel.innerHTML = 'Força: <span style="color: #ef4444; font-weight: 600;">Fraca</span>';
        } else if (score < 75) {
            strengthProgress.style.backgroundColor = '#f59e0b';
            strengthLabel.innerHTML = 'Força: <span style="color: #f59e0b; font-weight: 600;">Média</span>';
        } else {
            strengthProgress.style.backgroundColor = '#10b981';
            strengthLabel.innerHTML = 'Força: <span style="color: #10b981; font-weight: 600;">Forte</span>';
        }
    });
}

/**
 * Validação nos formulários antes do envio
 */
function initFormValidations() {
    const formCadastro = document.getElementById('formCadastro');
    if (formCadastro) {
        formCadastro.addEventListener('submit', (e) => {
            const senha = document.getElementById('cad_senha');
            const confirmaSenha = document.getElementById('cad_confirma_senha');

            if (senha && confirmaSenha && senha.value !== confirmaSenha.value) {
                e.preventDefault();
                alert('As senhas digitadas não coincidem. Por favor, verifique!');
                confirmaSenha.focus();
            }
        });
    }
}

/**
 * Integração e Inicialização da API Google Identity Services
 */
function initGoogleAuth() {
    // Configuração padrão da Google Identity API
    window.handleGoogleCredentialResponse = function(response) {
        if (!response || !response.credential) {
            console.error('Resposta de login do Google inválida.');
            return;
        }

        try {
            // Decodifica o token JWT enviado pelo Google
            const payload = parseJwt(response.credential);
            console.log('Google Auth Payload com sucesso:', payload);

            // Exemplo de redirecionamento ou envio assíncrono para o backend
            // payload contém: sub (Google ID), name (Nome Completo), email (E-mail), picture (Foto)
            const postForm = document.createElement('form');
            postForm.method = 'POST';
            postForm.action = 'index.php'; // Pode ser alterado para o handler de backend (ex: google_auth.php)

            const inputToken = document.createElement('input');
            inputToken.type = 'hidden';
            inputToken.name = 'google_token';
            inputToken.value = response.credential;

            postForm.appendChild(inputToken);
            document.body.appendChild(postForm);
            
            // Notificação visual para o usuário
            const alertBox = document.createElement('div');
            alertBox.className = 'alert-box alert-success';
            alertBox.style.marginTop = '15px';
            alertBox.innerHTML = `Autenticado com sucesso como <strong>${payload.name}</strong>!`;
            
            const card = document.querySelector('.auth-card');
            if (card) {
                card.prepend(alertBox);
            }

            // Submeter se houver endpoint pronto
            // postForm.submit();
        } catch (err) {
            console.error('Erro ao processar dados do Google:', err);
        }
    };
}

/**
 * Função utilitária para decodificar JWT da Google API no frontend
 */
function parseJwt(token) {
    const base64Url = token.split('.')[1];
    const base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
    const jsonPayload = decodeURIComponent(
        atob(base64)
            .split('')
            .map(c => '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2))
            .join('')
    );
    return JSON.parse(jsonPayload);
}
