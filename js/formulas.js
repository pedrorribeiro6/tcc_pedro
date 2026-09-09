/**
 * THE MATHEMATICAL GAME - CENTRAL DE FÓRMULAS E DICAS CATEGORIZADA
 * Alternância de Abas (Pitágoras, Radiciação, Geometria, Plano Cartesiano, Frações, Equações)
 */

document.addEventListener('DOMContentLoaded', () => {
    initFormulasTabs();
    initGeometryImages();
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

function initGeometryImages() {
    const typeSelect = document.getElementById('geometryImageType');
    const searchButton = document.getElementById('searchGeometryImages');
    const status = document.getElementById('geometryImagesStatus');
    const grid = document.getElementById('geometryImagesGrid');

    if (!typeSelect || !searchButton || !status || !grid) return;

    const search = async () => {
        searchButton.disabled = true;
        status.textContent = 'Buscando imagens no Google...';
        grid.innerHTML = '';

        try {
            const response = await fetch(`google_imagens.php?tipo=${encodeURIComponent(typeSelect.value)}`);
            const data = await response.json();
            if (!response.ok) throw new Error(data.erro || 'Não foi possível carregar as imagens.');

            if (!data.imagens.length) {
                status.textContent = 'Nenhuma imagem encontrada para este tipo.';
                return;
            }

            data.imagens.forEach((item) => {
                const card = document.createElement('article');
                card.className = 'geometry-image-card';
                card.innerHTML = `
                    <a href="${escapeGeometryAttribute(item.fonte)}" target="_blank" rel="noopener noreferrer">
                        <img src="${escapeGeometryAttribute(item.imagem)}" alt="${escapeGeometryAttribute(item.titulo)}" loading="lazy">
                    </a>
                    <a class="geometry-image-title" href="${escapeGeometryAttribute(item.fonte)}" target="_blank" rel="noopener noreferrer">${escapeGeometryText(item.titulo)}</a>
                `;
                grid.appendChild(card);
            });
            status.textContent = `${data.imagens.length} imagens encontradas. Clique em uma imagem para abrir a fonte.`;
        } catch (error) {
            status.textContent = error.message;
        } finally {
            searchButton.disabled = false;
        }
    };

    searchButton.addEventListener('click', search);
    typeSelect.addEventListener('change', search);
}

function escapeGeometryText(value) {
    const element = document.createElement('span');
    element.textContent = value || 'Figura geométrica';
    return element.innerHTML;
}

function escapeGeometryAttribute(value) {
    return escapeGeometryText(value).replace(/"/g, '&quot;');
}
