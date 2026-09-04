/**
 * THE MATHEMATICAL GAME - INTERATIVIDADE DA TELA INICIAL (DASHBOARD)
 */

document.addEventListener('DOMContentLoaded', () => {
    initMobileDrawer();
    initProfileDropdown();
    initSmoothNavScroll();
    initGameCardsInteraction();
});

/**
 * Controle do Menu Hambúrguer Responsivo no Mobile
 */
function initMobileDrawer() {
    const hamburgerBtn = document.getElementById('hamburgerBtn');
    const mobileDrawer = document.getElementById('mobileNavDrawer');
    const drawerLinks = document.querySelectorAll('.mobile-nav-links .nav-link');

    if (!hamburgerBtn || !mobileDrawer) return;

    hamburgerBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        const isOpen = mobileDrawer.classList.contains('open');

        if (isOpen) {
            closeMobileDrawer();
        } else {
            openMobileDrawer();
        }
    });

    // Fechar ao clicar em um link do menu
    drawerLinks.forEach(link => {
        link.addEventListener('click', () => {
            closeMobileDrawer();
        });
    });

    // Fechar ao clicar fora do drawer
    document.addEventListener('click', (e) => {
        if (!mobileDrawer.contains(e.target) && !hamburgerBtn.contains(e.target)) {
            closeMobileDrawer();
        }
    });

    function openMobileDrawer() {
        mobileDrawer.classList.add('open');
        hamburgerBtn.setAttribute('aria-expanded', 'true');
        hamburgerBtn.innerHTML = `
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        `;
    }

    function closeMobileDrawer() {
        mobileDrawer.classList.remove('open');
        hamburgerBtn.setAttribute('aria-expanded', 'false');
        hamburgerBtn.innerHTML = `
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="3" y1="12" x2="21" y2="12"></line>
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>
        `;
    }
}

/**
 * Menu Dropdown do Perfil de Usuário
 */
function initProfileDropdown() {
    const btnProfile = document.getElementById('btnProfileTrigger');
    const profileMenu = document.getElementById('profileMenuPopover');

    if (!btnProfile || !profileMenu) return;

    btnProfile.addEventListener('click', (e) => {
        e.stopPropagation();
        profileMenu.classList.toggle('show');
    });

    document.addEventListener('click', (e) => {
        if (!profileMenu.contains(e.target) && !btnProfile.contains(e.target)) {
            profileMenu.classList.remove('show');
        }
    });
}

/**
 * Navegação suave ao clicar nos links do menu e destaque da seção ativa no Scroll
 */
function initSmoothNavScroll() {
    const navLinks = document.querySelectorAll('.nav-link[href^="#"]');
    const sections = document.querySelectorAll('section[id]');

    navLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            const targetId = link.getAttribute('href');
            if (targetId === '#') return;

            const targetSection = document.querySelector(targetId);
            if (targetSection) {
                e.preventDefault();
                const offsetHeader = 80;
                const bodyRect = document.body.getBoundingClientRect().top;
                const elementRect = targetSection.getBoundingClientRect().top;
                const elementPosition = elementRect - bodyRect;
                const offsetPosition = elementPosition - offsetHeader;

                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });

    // Atualiza classe .active nos links durante a rolagem
    window.addEventListener('scroll', () => {
        let currentSectionId = '';
        const scrollPosition = window.scrollY + 120;

        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.offsetHeight;

            if (scrollPosition >= sectionTop && scrollPosition < sectionTop + sectionHeight) {
                currentSectionId = '#' + section.getAttribute('id');
            }
        });

        if (currentSectionId) {
            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === currentSectionId) {
                    link.classList.add('active');
                }
            });
        }
    });
}

/**
 * Efeitos e Feedback Visual ao Clicar em Modos de Jogo
 */
function initGameCardsInteraction() {
    const playButtons = document.querySelectorAll('.btn-play-card, .btn-hero-primary');

    playButtons.forEach(btn => {
        btn.addEventListener('click', (e) => {
            const modeName = btn.getAttribute('data-mode') || 'Desafio Matemático';
            
            // Se for um link direto (href diferente de #), deixa navegar
            if (btn.getAttribute('href') && btn.getAttribute('href') !== '#') {
                return;
            }

            e.preventDefault();
            showQuickNotice(`Iniciando <strong>${modeName}</strong>... Carregando questões!`);
        });
    });
}

/**
 * Toast Notice de Inicialização
 */
function showQuickNotice(message) {
    let existingToast = document.querySelector('.math-toast');
    if (existingToast) existingToast.remove();

    const toast = document.createElement('div');
    toast.className = 'math-toast';
    toast.style.cssText = `
        position: fixed;
        bottom: 24px;
        right: 24px;
        background: rgba(11, 30, 54, 0.95);
        border: 1px solid var(--color-cyan-vibrant);
        color: #ffffff;
        padding: 14px 22px;
        border-radius: 12px;
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 0.9rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.5), 0 0 20px rgba(0, 168, 255, 0.25);
        z-index: 2000;
        animation: fadeInScale 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        display: flex;
        align-items: center;
        gap: 12px;
    `;

    toast.innerHTML = `
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#00a8ff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polygon points="5 3 19 12 5 21 5 3"></polygon>
        </svg>
        <span>${message}</span>
    `;

    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(10px)';
        toast.style.transition = 'all 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}
