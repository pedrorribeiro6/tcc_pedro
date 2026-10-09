/**
 * THE MATHEMATICAL GAME - ACESSIBILIDADE COMPLETA
 * Suporte a: Daltonismo (Deuteranopia, Protanopia, Tritanopia),
 *            Alto Contraste, Modo Calmo (TEA), Fonte OpenDyslexic
 * Persistência: LocalStorage (sem banco de dados)
 */

(function () {
    // ── DALTONISMO ────────────────────────────────────────────────────────────
    const CB_KEY = 'colorblind_mode';
    const CB_MODOS_VALIDOS = ['none', 'deuteranopia', 'protanopia', 'tritanopia', 'high-contrast'];

    // ── TEA / AUTISMO ─────────────────────────────────────────────────────────
    const TEA_KEY = 'tea_mode';
    const TEA_MODOS_VALIDOS = ['none', 'calm', 'dyslexia'];

    // ── SVG FILTERS ───────────────────────────────────────────────────────────
    function injectSvgFilters() {
        if (document.getElementById('colorblind-svg-filters')) return;

        const svgWrapper = document.createElement('div');
        svgWrapper.id = 'colorblind-svg-filters';
        svgWrapper.setAttribute('aria-hidden', 'true');
        svgWrapper.style.cssText = 'position:absolute;width:0;height:0;overflow:hidden;pointer-events:none;';

        svgWrapper.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <!-- Filtro Deuteranopia (Déficit Verde) -->
                    <filter id="deuteranopia-filter" color-interpolation-filters="linearRGB">
                        <feColorMatrix type="matrix" values="
                            0.625, 0.375, 0.000, 0, 0
                            0.700, 0.300, 0.000, 0, 0
                            0.000, 0.300, 0.700, 0, 0
                            0.000, 0.000, 0.000, 1, 0" />
                    </filter>
                    
                    <!-- Filtro Protanopia (Déficit Vermelho) -->
                    <filter id="protanopia-filter" color-interpolation-filters="linearRGB">
                        <feColorMatrix type="matrix" values="
                            0.567, 0.433, 0.000, 0, 0
                            0.558, 0.442, 0.000, 0, 0
                            0.000, 0.242, 0.758, 0, 0
                            0.000, 0.000, 0.000, 1, 0" />
                    </filter>
                    
                    <!-- Filtro Tritanopia (Déficit Azul/Amarelo) -->
                    <filter id="tritanopia-filter" color-interpolation-filters="linearRGB">
                        <feColorMatrix type="matrix" values="
                            0.950, 0.050, 0.000, 0, 0
                            0.000, 0.433, 0.567, 0, 0
                            0.000, 0.475, 0.525, 0, 0
                            0.000, 0.000, 0.000, 1, 0" />
                    </filter>
                </defs>
            </svg>
        `;
        document.body.appendChild(svgWrapper);
    }

    // ── DALTONISMO: aplica modo no <html> ─────────────────────────────────────
    function applyColorblindMode(mode) {
        if (!CB_MODOS_VALIDOS.includes(mode)) mode = 'none';

        if (mode === 'none') {
            document.documentElement.removeAttribute('data-colorblind');
        } else {
            document.documentElement.setAttribute('data-colorblind', mode);
        }

        localStorage.setItem(CB_KEY, mode);
        syncUiColorblind(mode);
    }

    function syncUiColorblind(currentMode) {
        // Botão do Header
        const triggerBtn = document.getElementById('accessibilityTriggerBtn');
        if (triggerBtn) {
            const teaMode = localStorage.getItem(TEA_KEY) || 'none';
            const anyActive = currentMode !== 'none' || teaMode !== 'none';
            triggerBtn.classList.toggle('has-filter', anyActive);
        }

        // Opções no Popover do Header
        document.querySelectorAll('.accessibility-opt-btn[data-mode]').forEach(btn => {
            btn.classList.toggle('active', btn.getAttribute('data-mode') === currentMode);
        });

        // Cards de daltonismo na página de Perfil
        document.querySelectorAll('.colorblind-card').forEach(card => {
            card.classList.toggle('active', card.getAttribute('data-mode') === currentMode);
        });
    }

    // ── TEA: aplica modo no <html> ────────────────────────────────────────────
    function applyTeaMode(mode) {
        if (!TEA_MODOS_VALIDOS.includes(mode)) mode = 'none';

        if (mode === 'none') {
            document.documentElement.removeAttribute('data-tea');
        } else {
            document.documentElement.setAttribute('data-tea', mode);
        }

        localStorage.setItem(TEA_KEY, mode);
        syncUiTea(mode);

        // Atualiza indicador do botão do Header
        const cbMode = localStorage.getItem(CB_KEY) || 'none';
        const anyActive = mode !== 'none' || cbMode !== 'none';
        const triggerBtn = document.getElementById('accessibilityTriggerBtn');
        if (triggerBtn) triggerBtn.classList.toggle('has-filter', anyActive);
    }

    function syncUiTea(currentMode) {
        // Botões TEA no Popover do Header
        document.querySelectorAll('.accessibility-tea-btn[data-tea]').forEach(btn => {
            btn.classList.toggle('active', btn.getAttribute('data-tea') === currentMode);
        });

        // Cards TEA na página de Perfil
        document.querySelectorAll('.tea-card').forEach(card => {
            card.classList.toggle('active', card.getAttribute('data-tea') === currentMode);
        });
    }

    // ── API PÚBLICA ───────────────────────────────────────────────────────────
    window.setColorblindMode = function (mode) { applyColorblindMode(mode); };
    window.setTeaMode = function (mode) { applyTeaMode(mode); };

    // ── INICIALIZAÇÃO ─────────────────────────────────────────────────────────
    function init() {
        // Aplica modos salvos ANTES do render (evita flash)
        const savedCb  = localStorage.getItem(CB_KEY)  || 'none';
        const savedTea = localStorage.getItem(TEA_KEY) || 'none';
        applyColorblindMode(savedCb);
        applyTeaMode(savedTea);
        injectSvgFilters();

        // Popover do Header (toggle abrir/fechar) com posicionamento dinâmico
        const triggerBtn = document.getElementById('accessibilityTriggerBtn');
        const popover    = document.getElementById('accessibilityPopover');

        function positionPopover() {
            if (!triggerBtn || !popover) return;
            const rect = triggerBtn.getBoundingClientRect();
            const popoverWidth = 260;
            const viewportWidth = window.innerWidth;

            // Alinha pela direita do botão; se sair da viewport, alinha pela esquerda
            let left = rect.right - popoverWidth;
            if (left < 8) left = rect.left;
            if (left + popoverWidth > viewportWidth - 8) left = viewportWidth - popoverWidth - 8;

            popover.style.left = left + 'px';
            popover.style.right = 'auto';
        }

        if (triggerBtn && popover) {
            triggerBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                const isShowing = popover.classList.contains('show');
                if (!isShowing) {
                    positionPopover();
                }
                popover.classList.toggle('show');
            });
            document.addEventListener('click', (e) => {
                if (!popover.contains(e.target) && !triggerBtn.contains(e.target)) {
                    popover.classList.remove('show');
                }
            });
            window.addEventListener('resize', () => {
                if (popover.classList.contains('show')) positionPopover();
            });
        }

        // Cliques nos botões de Daltonismo do Header
        document.querySelectorAll('.accessibility-opt-btn[data-mode]').forEach(btn => {
            btn.addEventListener('click', () => {
                applyColorblindMode(btn.getAttribute('data-mode'));
                if (popover) popover.classList.remove('show');
            });
        });

        // Cliques nos botões TEA do Header
        document.querySelectorAll('.accessibility-tea-btn[data-tea]').forEach(btn => {
            btn.addEventListener('click', () => {
                const currentTea = localStorage.getItem(TEA_KEY) || 'none';
                const clickedTea = btn.getAttribute('data-tea');
                // Toggle: clicar no mesmo desativa
                applyTeaMode(currentTea === clickedTea ? 'none' : clickedTea);
                if (popover) popover.classList.remove('show');
            });
        });

        // Cliques nos cards de Daltonismo na página de Perfil
        document.querySelectorAll('.colorblind-card').forEach(card => {
            card.addEventListener('click', () => {
                applyColorblindMode(card.getAttribute('data-mode'));
                if (typeof mostrarToast === 'function') {
                    const titles = {
                        'none':          'Cores padrão restauradas.',
                        'deuteranopia':  'Modo Deuteranopia ativado.',
                        'protanopia':    'Modo Protanopia ativado.',
                        'tritanopia':    'Modo Tritanopia ativado.',
                        'high-contrast': 'Modo Alto Contraste ativado.'
                    };
                    mostrarToast(titles[card.getAttribute('data-mode')] || 'Filtro atualizado!', 'success');
                }
            });
        });

        // Cliques nos cards TEA na página de Perfil
        document.querySelectorAll('.tea-card').forEach(card => {
            card.addEventListener('click', () => {
                const currentTea = localStorage.getItem(TEA_KEY) || 'none';
                const clickedTea = card.getAttribute('data-tea');
                const newMode = currentTea === clickedTea ? 'none' : clickedTea;
                applyTeaMode(newMode);
                if (typeof mostrarToast === 'function') {
                    const titles = {
                        'none':     'Modos de conforto desativados.',
                        'calm':     'Modo Calmo ativado — animações removidas.',
                        'dyslexia': 'Fonte OpenDyslexic ativada.'
                    };
                    mostrarToast(titles[newMode] || 'Preferência atualizada!', 'success');
                }
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
