/**
 * THE MATHEMATICAL GAME - CENTRAL DE FÓRMULAS E DICAS CATEGORIZADA
 * Alternância de Abas (Pitágoras, Radiciação, Geometria, Plano Cartesiano, Frações, Equações)
 */

document.addEventListener('DOMContentLoaded', () => {
    initFormulasTabs();
});

function initFormulasTabs() {
    const tabButtons = document.querySelectorAll('.formula-tab-btn');
    const tabContents = document.querySelectorAll('.formula-tab-content');

    if (!tabButtons.length || !tabContents.length) return;

    tabButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const targetCategory = btn.getAttribute('data-tab');

            // Remove classe ativa de todas as abas
            tabButtons.forEach(b => b.classList.remove('active'));

            // Mantém todas as matérias visíveis e usa a aba como atalho
            btn.classList.add('active');
            const targetContent = document.getElementById(`tab-${targetCategory}`);
            if (targetContent) {
                targetContent.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    tabContents.forEach(content => {
        content.classList.add('active');
        content.style.display = 'block';
    });
}
