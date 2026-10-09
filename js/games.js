/**
 * THE MATHEMATICAL GAME - MOTOR DOS 5 MINI-JOGOS MATEMÁTICOS
 * 1. Quiz 4 Operações (Adição, Subtração, Multiplicação, Divisão)
 * 2. Geometria 2D & 3D (Com 2 opções: Verdadeiro / Falso)
 * 3. Plano Cartesiano Interativo (Interação por Canvas com Eixos X e Y)
 * 4. Radiciação (Raízes quadradas, cúbicas e simplificação)
 * 5. Equações do 1º Grau (Resolução para X sem repetição)
 */

document.addEventListener('DOMContentLoaded', () => {
    initGameEngine();
});

// Estado Global do Jogo
const game = {
    mode: 'quiz',
    difficulty: 'medio', // 'facil', 'medio', 'dificil'
    score: 0,
    lives: 3,
    maxLives: 3,
    currentStep: 1,
    totalSteps: 10,
    timer: 30,
    timerDuration: 30,
    xpMultiplier: 1,
    questionUsedExtraTime: false,
    timerInterval: null,
    currentQuestion: null,
    askedQuestions: new Set(),
    isAnswering: false,
    historyAccuracy: { correct: 0, total: 0 }
};

// Config por dificuldade
const DIFFICULTY_CONFIG = {
    facil:   { lives: 3, timer: 45, xpMultiplier: 1,   label: 'Fácil',   emoji: '🌱' },
    medio:   { lives: 2, timer: 30, xpMultiplier: 1.5, label: 'Médio',   emoji: '⚡' },
    dificil: { lives: 1, timer: 20, xpMultiplier: 2,   label: 'Difícil', emoji: '🔥' }
};

/**
 * Inicialização e Leitura da URL (?game=...)
 */
function initGameEngine() {
    const urlParams = new URLSearchParams(window.location.search);
    const modeParam = urlParams.get('game');
    const diffParam = urlParams.get('difficulty');

    if (modeParam && ['quiz', 'geometria', 'cartesiano', 'radiciacao', 'equacoes', 'fracoes'].includes(modeParam)) {
        game.mode = modeParam;
    }
    if (diffParam && DIFFICULTY_CONFIG[diffParam]) {
        game.difficulty = diffParam;
    }

    const cfg = DIFFICULTY_CONFIG[game.difficulty];
    game.maxLives   = cfg.lives;
    game.timerDuration = cfg.timer;
    game.xpMultiplier  = cfg.xpMultiplier;

    setupGameHeader();
    startNewGame();
}

/**
 * Configura o cabeçalho e os títulos de acordo com o modo escolhido
 */
function setupGameHeader() {
    const modeTitleEl = document.getElementById('gameModeTitle');
    const modeIconEl  = document.getElementById('gameModeIcon');

    if (!modeTitleEl || !modeIconEl) return;

    const modesInfo = {
        quiz:       { title: 'Quiz das 4 Operações',    icon: '⚡' },
        geometria:  { title: 'Desafio de Geometria 2D & 3D', icon: '📐' },
        cartesiano: { title: 'Plano Cartesiano',           icon: '🗺️' },
        radiciacao: { title: 'Jogo de Radiciação',        icon: '√' },
        equacoes:   { title: 'Equações do 1º Grau',        icon: '🧮' },
        fracoes:    { title: 'Operações com Frações',      icon: '½' }
    };

    const currentInfo = modesInfo[game.mode] || modesInfo.quiz;
    modeTitleEl.textContent = currentInfo.title;
    modeIconEl.textContent  = currentInfo.icon;

    // Badge de dificuldade no header
    const cfg = DIFFICULTY_CONFIG[game.difficulty];
    const existingBadge = document.getElementById('difficultyBadge');
    if (!existingBadge) {
        const badge = document.createElement('span');
        badge.id = 'difficultyBadge';
        badge.className = `difficulty-badge difficulty-badge--${game.difficulty}`;
        badge.textContent = `${cfg.emoji} ${cfg.label}`;
        modeTitleEl.parentNode.appendChild(badge);
    }
}

/**
 * Reinicia a partida
 */
function startNewGame() {
    const cfg = DIFFICULTY_CONFIG[game.difficulty];
    game.score   = 0;
    game.lives   = cfg.lives;
    game.maxLives = cfg.lives;
    game.timerDuration = cfg.timer;
    game.xpMultiplier  = cfg.xpMultiplier;
    game.questionUsedExtraTime = false;
    game.currentStep   = 1;
    game.askedQuestions.clear();
    game.historyAccuracy = { correct: 0, total: 0 };
    game.isAnswering = false;

    updateHeaderUI();
    loadNextQuestion();
}

/**
 * Carrega a próxima pergunta de acordo com o modo de jogo
 */
function loadNextQuestion() {
    if (game.lives <= 0 || game.currentStep > game.totalSteps) {
        finishGame();
        return;
    }

    game.isAnswering = false;
    game.questionUsedExtraTime = false;
    updateHeaderUI();
    resetTimer();

    const challengeCard = document.getElementById('gameChallengeCard');
    if (!challengeCard) return;

    // Remove contêineres anteriores
    challengeCard.innerHTML = '';

    // Tag do Passo Atual
    const stepTag = document.createElement('span');
    stepTag.className = 'question-step-tag';
    stepTag.textContent = `Pergunta ${game.currentStep} de ${game.totalSteps}`;
    challengeCard.appendChild(stepTag);

    // Botão de Tempo Extra (+10s) exclusivo do Modo Difícil
    if (game.difficulty === 'dificil') {
        const extraTimeBtn = document.createElement('button');
        extraTimeBtn.id = 'btnExtraTime';
        extraTimeBtn.type = 'button';
        extraTimeBtn.className = 'btn-extra-time';
        extraTimeBtn.title = 'Ganhe +10s nesta pergunta (ao usar, o bônus de dobro de XP cai para 1x nesta questão)';
        extraTimeBtn.innerHTML = `
            <span class="btn-extra-time-icon">⏳</span>
            <span class="btn-extra-time-label">+10s</span>
            <span class="btn-extra-time-badge">-Dobro XP</span>
        `;
        extraTimeBtn.addEventListener('click', useExtraTime);
        challengeCard.appendChild(extraTimeBtn);
    }

    // Seleciona o gerador de acordo com o modo
    switch (game.mode) {
        case 'quiz':
            renderQuizQuestion(challengeCard);
            break;
        case 'geometria':
            renderGeometriaQuestion(challengeCard);
            break;
        case 'cartesiano':
            renderCartesianoQuestion(challengeCard);
            break;
        case 'radiciacao':
            renderRadiciacaoQuestion(challengeCard);
            break;
        case 'equacoes':
            renderEquacoesQuestion(challengeCard);
            break;
        case 'fracoes':
            renderFracoesQuestion(challengeCard);
            break;
    }
}

// ==========================================================================
// 1. JOGO 1: QUIZ DAS 4 OPERAÇÕES (ADIÇÃO, SUBTRAÇÃO, MULTIPLICAÇÃO, DIVISÃO)
// ==========================================================================
function renderQuizQuestion(container) {
    const hard = game.difficulty === 'dificil';
    const easy = game.difficulty === 'facil';
    let num1, num2, op, questionText, correctAnswer;
    let attempts = 0;

    do {
        attempts++;
        const opType = Math.floor(Math.random() * 4);
        if (opType === 0) {
            // Adição (+)
            num1 = easy ? randInt(5, 40)  : hard ? randInt(120, 750) : randInt(40, 250);
            num2 = easy ? randInt(5, 40)  : hard ? randInt(120, 750) : randInt(40, 250);
            op = '+'; correctAnswer = num1 + num2;
        } else if (opType === 1) {
            // Subtração (-)
            num1 = easy ? randInt(10, 60) : hard ? randInt(300, 999) : randInt(100, 350);
            num2 = easy ? randInt(5, num1) : hard ? randInt(100, num1 - 20) : randInt(20, num1 - 10);
            op = '-'; correctAnswer = num1 - num2;
        } else if (opType === 2) {
            // Multiplicação (×)
            num1 = easy ? randInt(2, 9)   : hard ? randInt(12, 35)  : randInt(6, 16);
            num2 = easy ? randInt(2, 9)   : hard ? randInt(11, 25)  : randInt(6, 15);
            op = '×'; correctAnswer = num1 * num2;
        } else {
            // Divisão (÷)
            num2 = easy ? randInt(2, 9)   : hard ? randInt(11, 25)  : randInt(4, 14);
            correctAnswer = easy ? randInt(2, 10) : hard ? randInt(12, 35) : randInt(8, 22);
            num1 = num2 * correctAnswer; op = '÷';
        }
        questionText = `Quanto é ${num1} ${op} ${num2}?`;
    } while (game.askedQuestions.has(questionText) && attempts < 50);

    game.askedQuestions.add(questionText);

    const questionTitle = document.createElement('h2');
    questionTitle.className = 'question-main-text';
    questionTitle.textContent = questionText;
    container.appendChild(questionTitle);

    const options = generateOptions(correctAnswer);
    renderOptionsGrid(container, options, correctAnswer);
}

// ==========================================================================
// 2. JOGO 2: GEOMETRIA 2D & 3D
// ==========================================================================

const shapes2D3D = [
    // --- FIGURAS 2D (Todas com suas imagens locais) ---
    { 
        name: 'Quadrado', 
        type: '2D', 
        image: '../imagens/geometria/quadrado.jpg',
        sides: 4,
        vertices: 4
    },
    { 
        name: 'Retângulo', 
        type: '2D', 
        image: '../imagens/geometria/retangulo.jpg',
        sides: 4,
        vertices: 4
    },
    { 
        name: 'Círculo', 
        type: '2D', 
        image: '../imagens/geometria/circulo.jpg',
        sides: 0,
        vertices: 0
    },
    { 
        name: 'Triângulo', 
        type: '2D', 
        image: '../imagens/geometria/triangulo.jpg',
        sides: 3,
        vertices: 3
    },
    { 
        name: 'Pentágono', 
        type: '2D', 
        image: '../imagens/geometria/pentagono.jpg',
        sides: 5,
        vertices: 5
    },
    { 
        name: 'Hexágono', 
        type: '2D', 
        image: '../imagens/geometria/hexagono.jpg',
        sides: 6,
        vertices: 6
    },
    { 
        name: 'Heptágono', 
        type: '2D', 
        image: '../imagens/geometria/heptagono.jpg',
        sides: 7,
        vertices: 7
    },
    { 
        name: 'Octógono', 
        type: '2D', 
        image: '../imagens/geometria/octogono.jpg',
        sides: 8,
        vertices: 8
    },
    { 
        name: 'Losango', 
        type: '2D', 
        image: '../imagens/geometria/losango.jpg',
        sides: 4,
        vertices: 4
    },
    { 
        name: 'Trapézio', 
        type: '2D', 
        image: '../imagens/geometria/trapezio.jpg',
        sides: 4,
        vertices: 4
    },

    // --- FIGURAS 3D (Todas com suas imagens locais) ---
    { 
        name: 'Cubo', 
        type: '3D', 
        image: '../imagens/geometria/cubo.jpg',
        faces: 6,
        vertices: 8,
        edges: 12
    },
    { 
        name: 'Cilindro', 
        type: '3D', 
        image: '../imagens/geometria/cilindro.png',
        faces: 3,
        vertices: 0,
        edges: 2
    },
    { 
        name: 'Esfera', 
        type: '3D', 
        image: '../imagens/geometria/esfera.png',
        faces: 1,
        vertices: 0,
        edges: 0
    },
    { 
        name: 'Cone', 
        type: '3D', 
        image: '../imagens/geometria/cone.jpg',
        faces: 2,
        vertices: 1,
        edges: 1
    },
    { 
        name: 'Pirâmide', 
        type: '3D', 
        image: '../imagens/geometria/piramide.jpg',
        faces: 5,
        vertices: 5,
        edges: 8
    },
    { 
        name: 'Pirâmide triangular', 
        type: '3D', 
        image: '../imagens/geometria/piramide_triangular.jpg',
        faces: 4,
        vertices: 4,
        edges: 6
    },
    { 
        name: 'Paralelepípedo', 
        type: '3D', 
        image: '../imagens/geometria/paralelepipedo.jpg',
        faces: 6,
        vertices: 8,
        edges: 12
    },
    { 
        name: 'Prisma pentagonal', 
        type: '3D', 
        image: '../imagens/geometria/prisma_pentagonal.png',
        faces: 7,
        vertices: 10,
        edges: 15
    },
    { 
        name: 'Prisma hexagonal', 
        type: '3D', 
        image: '../imagens/geometria/prisma_hexagonal.png',
        faces: 8,
        vertices: 12,
        edges: 18
    },
    { 
        name: 'Prisma heptagonal', 
        type: '3D', 
        image: '../imagens/geometria/prisma_heptagonal.png',
        faces: 9,
        vertices: 14,
        edges: 21
    },
    { 
        name: 'Prisma octagonal', 
        type: '3D', 
        image: '../imagens/geometria/prisma_octagonal.jpg',
        faces: 10,
        vertices: 16,
        edges: 24
    }
];

function renderGeometriaQuestion(container) {
    const hard = game.difficulty === 'dificil';
    const easy = game.difficulty === 'facil';
    const selectedShape = shapes2D3D[Math.floor(Math.random() * shapes2D3D.length)];

    // No Fácil: Verdadeiro ou Falso
    // No Médio: 50% V/F, 50% perguntas de lados/faces (múltipla escolha)
    // No Difícil: 75% perguntas de lados/faces/vértices/arestas (múltipla escolha)
    const askProperties = easy ? false : (hard ? Math.random() < 0.75 : Math.random() < 0.5);

    if (askProperties) {
        let questionText, correctAnswer;

        if (selectedShape.type === '2D') {
            const askVertices = hard && Math.random() > 0.5;
            if (askVertices) {
                questionText = `Quantos vértices possui o(a) "${selectedShape.name}" exibido(a) abaixo?`;
                correctAnswer = selectedShape.vertices;
            } else {
                questionText = `Quantos lados possui o(a) "${selectedShape.name}" exibido(a) abaixo?`;
                correctAnswer = selectedShape.sides;
            }
        } else {
            // Figura 3D: faces, vértices ou arestas
            const propPick = hard ? Math.floor(Math.random() * 3) : 0;
            if (propPick === 0) {
                questionText = `Quantas faces possui o(a) "${selectedShape.name}" exibido(a) abaixo?`;
                correctAnswer = selectedShape.faces;
            } else if (propPick === 1) {
                questionText = `Quantos vértices possui o(a) "${selectedShape.name}" exibido(a) abaixo?`;
                correctAnswer = selectedShape.vertices;
            } else {
                questionText = `Quantas arestas possui o(a) "${selectedShape.name}" exibido(a) abaixo?`;
                correctAnswer = selectedShape.edges;
            }
        }

        const title = document.createElement('h2');
        title.className = 'question-main-text';
        title.textContent = questionText;
        container.appendChild(title);

        const displayBox = document.createElement('div');
        displayBox.className = 'geometry-display-box';
        displayBox.innerHTML = `
            <img src="${selectedShape.image}" 
                 alt="${selectedShape.name}" 
                 class="geometry-img">
        `;
        container.appendChild(displayBox);

        const options = generateOptions(correctAnswer, 0);
        renderOptionsGrid(container, options, correctAnswer);
    } else {
        // Formato Verdadeiro ou Falso
        const isTrueStatement = Math.random() > 0.5;
        let statementName = selectedShape.name;

        if (!isTrueStatement) {
            const otherShapes = shapes2D3D.filter(s => s.name !== selectedShape.name);
            statementName = otherShapes[Math.floor(Math.random() * otherShapes.length)].name;
        }

        const questionText = `A figura geométrica exibida abaixo é um(a) "${statementName}"?`;

        const title = document.createElement('h2');
        title.className = 'question-main-text';
        title.textContent = questionText;
        container.appendChild(title);

        const displayBox = document.createElement('div');
        displayBox.className = 'geometry-display-box';
        displayBox.innerHTML = `
            <img src="${selectedShape.image}" 
                 alt="${selectedShape.name}" 
                 class="geometry-img">
        `;
        container.appendChild(displayBox);

        const tfContainer = document.createElement('div');
        tfContainer.className = 'true-false-container';

        const btnTrue = document.createElement('button');
        btnTrue.className = 'btn-tf-true';
        btnTrue.innerHTML = '<span>VERDADEIRO</span> ✔️';
        btnTrue.addEventListener('click', () => handleAnswer(isTrueStatement, btnTrue, [btnTrue, btnFalse]));

        const btnFalse = document.createElement('button');
        btnFalse.className = 'btn-tf-false';
        btnFalse.innerHTML = '<span>FALSO</span> ❌';
        btnFalse.addEventListener('click', () => handleAnswer(!isTrueStatement, btnFalse, [btnTrue, btnFalse]));

        tfContainer.appendChild(btnTrue);
        tfContainer.appendChild(btnFalse);
        container.appendChild(tfContainer);
    }
}

// ==========================================================================
// 3. JOGO 3: GAME DO PLANO CARTESIANO INTERATIVO (CANVAS COM EIXOS X E Y)
// ==========================================================================
function renderCartesianoQuestion(container) {
    const hard = game.difficulty === 'dificil';
    const easy = game.difficulty === 'facil';
    // Escala de grade: Fácil: 5 (-5 a 5) | Médio: 7 (-7 a 7) | Difícil: 7 (-7 a 7)
    const maxCoord = easy ? 5 : 7;
    const showLabels = !hard; // No modo difícil, remove as indicações numéricas dos eixos!

    // Sorteia Coordenadas entre -maxCoord e +maxCoord (evitando 0,0)
    let targetX = randInt(-maxCoord, maxCoord);
    let targetY = randInt(-maxCoord, maxCoord);

    if (targetX === 0 && targetY === 0) targetX = Math.min(3, maxCoord);

    const headerWrapper = document.createElement('div');
    headerWrapper.className = 'cartesiano-header-box';

    let alertHtml = '';
    if (hard) {
        alertHtml = `
            <div class="cartesiano-hard-alert-banner">
                <span class="hard-alert-icon">⚠️</span>
                <span class="hard-alert-text"><strong>Modo Difícil:</strong> As indicações dos eixos foram removidas. Conte as divisões a partir da origem <code>(0,0)</code>!</span>
            </div>
        `;
    }

    headerWrapper.innerHTML = `
        <div class="cartesiano-instruction-card">
            <span>Clique no ponto correspondente às coordenadas:</span>
            <span class="coord-target-badge">X = ${targetX}, Y = ${targetY}</span>
        </div>
        ${alertHtml}
    `;
    container.appendChild(headerWrapper);

    // Canvas do Plano Cartesiano
    const canvasWrapper = document.createElement('div');
    canvasWrapper.className = 'cartesiano-canvas-wrapper';
    
    const canvas = document.createElement('canvas');
    canvas.id = 'cartesianoCanvas';
    canvas.width = 480;
    canvas.height = 480;
    canvasWrapper.appendChild(canvas);
    container.appendChild(canvasWrapper);

    const ctx = canvas.getContext('2d');
    drawCartesianGrid(ctx, canvas.width, canvas.height, maxCoord, showLabels);

    // Clique no Canvas com margem segura
    let answered = false;
    canvas.addEventListener('click', (e) => {
        if (answered || game.isAnswering) return;

        const rect = canvas.getBoundingClientRect();
        const scaleX = canvas.width / rect.width;
        const scaleY = canvas.height / rect.height;
        const clickX = (e.clientX - rect.left) * scaleX;
        const clickY = (e.clientY - rect.top) * scaleY;

        // Margem de segurança de 30px em cada borda
        const margin = 30;
        const maxRadius = (canvas.width / 2) - margin;
        const step = Math.floor(maxRadius / maxCoord);
        const centerX = canvas.width / 2;
        const centerY = canvas.height / 2;

        const userX = Math.round((clickX - centerX) / step);
        const userY = Math.round((centerY - clickY) / step);

        // Se o clique for fora do limite do gráfico, ignora
        if (Math.abs(userX) > maxCoord || Math.abs(userY) > maxCoord) return;

        answered = true;
        const isCorrect = (userX === targetX && userY === targetY);

        // Desenhar Ponto do Jogador no Canvas
        const ptPxX = centerX + userX * step;
        const ptPxY = centerY - userY * step;

        ctx.beginPath();
        ctx.arc(ptPxX, ptPxY, hard ? 7 : 8, 0, 2 * Math.PI);
        ctx.fillStyle = isCorrect ? '#10b981' : '#ef4444';
        ctx.fill();
        ctx.lineWidth = 2.5;
        ctx.strokeStyle = '#ffffff';
        ctx.stroke();

        handleAnswer(isCorrect, canvasWrapper, []);
    });
}

/**
 * Desenha a Grade do Plano Cartesiano no Canvas com margem segura
 */
function drawCartesianGrid(ctx, width, height, maxCoord = 7, showLabels = true) {
    const isLight = document.documentElement.dataset.theme === 'light';
    ctx.clearRect(0, 0, width, height);

    if (isLight) {
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, width, height);
    }

    // Margem de 30px para garantir que números e pontos periféricos (7, -7) nunca sejam cortados
    const margin = 30;
    const maxRadius = (width / 2) - margin;
    const step = Math.floor(maxRadius / maxCoord);
    const centerX = width / 2;
    const centerY = height / 2;
    const gridSpan = maxCoord * step;

    // Grade de Fundo
    ctx.strokeStyle = isLight ? 'rgba(0, 0, 0, 0.09)' : 'rgba(255, 255, 255, 0.08)';
    ctx.lineWidth = 1;

    for (let i = -maxCoord; i <= maxCoord; i++) {
        const lineX = centerX + i * step;
        const lineY = centerY + i * step;

        // Linha Vertical
        ctx.beginPath();
        ctx.moveTo(lineX, centerY - gridSpan);
        ctx.lineTo(lineX, centerY + gridSpan);
        ctx.stroke();

        // Linha Horizontal
        ctx.beginPath();
        ctx.moveTo(centerX - gridSpan, lineY);
        ctx.lineTo(centerX + gridSpan, lineY);
        ctx.stroke();
    }

    // Eixos Principais (X e Y) - alinhados perfeitamente com os limites da grade
    ctx.strokeStyle = isLight ? '#0284c7' : '#00a8ff';
    ctx.lineWidth = 3;

    // Eixo X
    ctx.beginPath();
    ctx.moveTo(centerX - gridSpan, centerY);
    ctx.lineTo(centerX + gridSpan, centerY);
    ctx.stroke();

    // Eixo Y
    ctx.beginPath();
    ctx.moveTo(centerX, centerY - gridSpan);
    ctx.lineTo(centerX, centerY + gridSpan);
    ctx.stroke();

    // Rótulos numéricos nos eixos (ocultados no modo difícil)
    if (showLabels) {
        ctx.fillStyle = isLight ? '#334155' : '#94a3b8';
        const fontSize = 11;
        ctx.font = `bold ${fontSize}px Plus Jakarta Sans`;
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';

        for (let i = -maxCoord; i <= maxCoord; i++) {
            if (i !== 0) {
                // Rótulo Eixo X (abaixo da linha do eixo)
                ctx.fillText(i, centerX + i * step, centerY + 16);
                // Rótulo Eixo Y (à esquerda da linha do eixo)
                ctx.fillText(i, centerX - 16, centerY - i * step);
            }
        }
    }

    // Origem (0,0)
    ctx.fillStyle = isLight ? '#b45309' : '#fbbf24';
    ctx.font = 'bold 12px Plus Jakarta Sans';
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';
    ctx.fillText('0', centerX - 10, centerY + 12);
}

// ==========================================================================
// 4. JOGO 4: JOGO SOBRE RADICIAÇÃO (SEM REPETIR PERGUNTAS)
// ==========================================================================
function renderRadiciacaoQuestion(container) {
    const hard = game.difficulty === 'dificil';
    const easy = game.difficulty === 'facil';
    let questionText, correctAnswer;
    let attempts = 0;

    do {
        attempts++;
        if (easy) {
            const type = Math.floor(Math.random() * 2);
            if (type === 0) {
                const base = randInt(2, 6);
                questionText = `Qual é o valor da raiz quadrada √${base * base}?`;
                correctAnswer = base;
            } else {
                const base = randInt(2, 4);
                questionText = `Qual é o valor da raiz cúbica ³√${base ** 3}?`;
                correctAnswer = base;
            }
        } else if (!hard) { // Médio
            const type = Math.floor(Math.random() * 3);
            if (type === 0) {
                const base = randInt(5, 14);
                questionText = `Qual é o valor da raiz quadrada √${base * base}?`;
                correctAnswer = base;
            } else if (type === 1) {
                const base = randInt(2, 6);
                questionText = `Qual é o valor da raiz cúbica ³√${base ** 3}?`;
                correctAnswer = base;
            } else {
                const b1 = randInt(3, 10);
                const b2 = randInt(2, 8);
                questionText = `Calcule o valor de: √${b1 * b1} + √${b2 * b2}`;
                correctAnswer = b1 + b2;
            }
        } else { // Difícil: raízes maiores (até 25), cúbicas maiores e operações mistas
            const type = Math.floor(Math.random() * 4);
            if (type === 0) {
                // Raiz quadrada de números maiores (13 a 25) -> ex: √169, √225, √256, √400, √625
                const base = randInt(13, 25);
                questionText = `Qual é o valor da raiz quadrada √${base * base}?`;
                correctAnswer = base;
            } else if (type === 1) {
                // Raiz cúbica maior (4 a 10) -> ex: ³√125, ³√216, ³√343, ³√512, ³√729, ³√1000
                const base = randInt(4, 10);
                questionText = `Qual é o valor da raiz cúbica ³√${base ** 3}?`;
                correctAnswer = base;
            } else if (type === 2) {
                // Operação mista de raízes quadradas: subtração ou multiplicação
                const isMultiply = Math.random() > 0.5;
                if (isMultiply) {
                    const b1 = randInt(3, 12);
                    const b2 = randInt(2, 6);
                    questionText = `Calcule o valor de: √${b1 * b1} × √${b2 * b2}`;
                    correctAnswer = b1 * b2;
                } else {
                    const b1 = randInt(12, 22);
                    const b2 = randInt(3, b1 - 1);
                    questionText = `Calcule o valor de: √${b1 * b1} - √${b2 * b2}`;
                    correctAnswer = b1 - b2;
                }
            } else {
                // Combinação mista: Raiz Quadrada com Raiz Cúbica (ex: √100 - ³√27 ou √64 + ³√8)
                const isSub = Math.random() > 0.5;
                const bSqrt = randInt(6, 16);
                const bCbrt = randInt(2, 5);
                if (isSub && bSqrt > bCbrt) {
                    questionText = `Calcule o valor de: √${bSqrt * bSqrt} - ³√${bCbrt ** 3}`;
                    correctAnswer = bSqrt - bCbrt;
                } else {
                    questionText = `Calcule o valor de: √${bSqrt * bSqrt} + ³√${bCbrt ** 3}`;
                    correctAnswer = bSqrt + bCbrt;
                }
            }
        }
    } while (game.askedQuestions.has(questionText) && attempts < 50);

    game.askedQuestions.add(questionText);

    const title = document.createElement('h2');
    title.className = 'question-main-text';
    title.textContent = questionText;
    container.appendChild(title);

    const options = generateOptions(correctAnswer);
    renderOptionsGrid(container, options, correctAnswer);
}

// ==========================================================================
// 5. JOGO 5: JOGO SOBRE EQUAÇÕES DO 1º GRAU (SEM REPETIR PERGUNTAS)
// ==========================================================================
function renderEquacoesQuestion(container) {
    const hard = game.difficulty === 'dificil';
    const easy = game.difficulty === 'facil';
    let questionText, correctAnswer;
    let attempts = 0;

    do {
        attempts++;
        const type = easy ? 0 : Math.floor(Math.random() * 3);
        const xVal = easy ? randInt(1,6) : hard ? randInt(5,20) : randInt(1,12);

        if (type === 0) {
            const a = easy ? randInt(2,4) : hard ? randInt(5,10) : randInt(2,5);
            const b = easy ? randInt(1,8) : hard ? randInt(10,30) : randInt(1,15);
            const c = a * xVal + b;
            questionText = `Determine o valor de x na equação: ${a}x + ${b} = ${c}`;
            correctAnswer = xVal;
        } else if (type === 1) {
            const a = easy ? randInt(2,4) : hard ? randInt(5,10) : randInt(2,5);
            const b = easy ? randInt(1,8) : hard ? randInt(10,30) : randInt(1,15);
            const c = a * xVal - b;
            questionText = `Determine o valor de x na equação: ${a}x - ${b} = ${c}`;
            correctAnswer = xVal;
        } else {
            const cVal = randInt(1, hard ? 6 : 3);
            const aVal = cVal + randInt(1, hard ? 6 : 4);
            const bVal = randInt(1, hard ? 20 : 10);
            const dVal = (aVal - cVal) * xVal + bVal;
            questionText = `Resolva para x: ${aVal}x + ${bVal} = ${cVal}x + ${dVal}`;
            correctAnswer = xVal;
        }
    } while (game.askedQuestions.has(questionText) && attempts < 50);

    game.askedQuestions.add(questionText);

    const title = document.createElement('h2');
    title.className = 'question-main-text';
    title.textContent = questionText;
    container.appendChild(title);

    const options = generateOptions(correctAnswer);
    renderOptionsGrid(container, options, correctAnswer);
}

// ==========================================================================
// 6. JOGO 6: OPERAÇÕES COM FRAÇÕES (DENOMINADORES IGUAIS - VERDADEIRO OU FALSO)
// ==========================================================================
function renderFracoesQuestion(container) {
    let questionKey;
    let n1, n2, d, opSymbol, correctNum, correctDen, displayNum, displayDen, isTrue;
    let attempts = 0;

    do {
        attempts++;
        // Denominador comum entre 2 e 9
        d = Math.floor(Math.random() * 8) + 2;
        const opType = Math.floor(Math.random() * 4); // 0: +, 1: -, 2: *, 3: /

        if (opType === 0) {
            // Adição: n1/d + n2/d = (n1 + n2)/d
            opSymbol = '+';
            n1 = Math.floor(Math.random() * 6) + 1;
            n2 = Math.floor(Math.random() * 6) + 1;
            correctNum = n1 + n2;
            correctDen = d;
        } else if (opType === 1) {
            // Subtração: n1/d - n2/d = (n1 - n2)/d
            opSymbol = '−';
            n1 = Math.floor(Math.random() * 7) + 2; // 2 a 8
            n2 = Math.floor(Math.random() * (n1 - 1)) + 1; // 1 a n1-1 (resultado positivo)
            correctNum = n1 - n2;
            correctDen = d;
        } else if (opType === 2) {
            // Multiplicação: (n1/d) * (n2/d) = (n1 * n2)/(d * d)
            opSymbol = '×';
            n1 = Math.floor(Math.random() * 4) + 1;
            n2 = Math.floor(Math.random() * 4) + 1;
            correctNum = n1 * n2;
            correctDen = d * d;
        } else {
            // Divisão: (n1/d) ÷ (n2/d) = n1/n2
            opSymbol = '÷';
            n1 = Math.floor(Math.random() * 6) + 1;
            n2 = Math.floor(Math.random() * 6) + 1;
            correctNum = n1;
            correctDen = n2;
        }

        isTrue = Math.random() > 0.5;

        if (isTrue) {
            displayNum = correctNum;
            displayDen = correctDen;
        } else {
            // Gera um erro sutil e educativo
            const fakeType = Math.floor(Math.random() * 3);
            if (fakeType === 0) {
                // Erro clássico: somar os denominadores na adição ou alterar denominador
                displayNum = correctNum;
                displayDen = (opType === 0) ? d * 2 : (correctDen + (Math.random() > 0.5 ? 1 : 2));
            } else if (fakeType === 1) {
                // Erro: numerador com diferença de 1 ou 2
                const delta = Math.random() > 0.5 ? 1 : -1;
                displayNum = Math.max(1, correctNum + delta);
                displayDen = correctDen;
            } else {
                // Erro: valor com pequena alteração
                displayNum = correctNum + 2;
                displayDen = correctDen;
            }

            // Garantir que não ficou acidentalmente idêntico à resposta correta
            if (displayNum === correctNum && displayDen === correctDen) {
                displayNum += 1;
            }
        }

        questionKey = `${n1}/${d}${opSymbol}${n2}/${d}=${displayNum}/${displayDen}`;
    } while (game.askedQuestions.has(questionKey) && attempts < 50);

    game.askedQuestions.add(questionKey);

    // Título da questão
    const title = document.createElement('h2');
    title.className = 'question-main-text';
    title.textContent = 'A igualdade com frações abaixo está correta?';
    container.appendChild(title);

    // Display estilizado da equação fracionária
    const eqBox = document.createElement('div');
    eqBox.className = 'fraction-calc-display';
    eqBox.innerHTML = `
        <div class="fraction-item">
            <span class="num">${n1}</span>
            <span class="bar"></span>
            <span class="den">${d}</span>
        </div>
        <span class="fraction-operator">${opSymbol}</span>
        <div class="fraction-item">
            <span class="num">${n2}</span>
            <span class="bar"></span>
            <span class="den">${d}</span>
        </div>
        <span class="fraction-operator">=</span>
        <div class="fraction-item fraction-result">
            <span class="num">${displayNum}</span>
            <span class="bar"></span>
            <span class="den">${displayDen}</span>
        </div>
    `;
    container.appendChild(eqBox);

    // Opções Verdadeiro / Falso
    const tfContainer = document.createElement('div');
    tfContainer.className = 'true-false-container';

    const btnTrue = document.createElement('button');
    btnTrue.className = 'btn-tf-true';
    btnTrue.innerHTML = '<span>VERDADEIRO</span> ✔️';
    btnTrue.addEventListener('click', () => handleAnswer(isTrue, btnTrue, [btnTrue, btnFalse]));

    const btnFalse = document.createElement('button');
    btnFalse.className = 'btn-tf-false';
    btnFalse.innerHTML = '<span>FALSO</span> ❌';
    btnFalse.addEventListener('click', () => handleAnswer(!isTrue, btnFalse, [btnTrue, btnFalse]));

    tfContainer.appendChild(btnTrue);
    tfContainer.appendChild(btnFalse);
    container.appendChild(tfContainer);
}

// ==========================================================================
// FUNÇÕES UTILITÁRIAS DE OPÇÕES E INTERAÇÃO
// ==========================================================================

/** Inteiro aleatório entre min e max (inclusive) */
function randInt(min, max) {
    return Math.floor(Math.random() * (max - min + 1)) + min;
}

/**
 * Gera 4 opções plausíveis para a resposta correta
 */
function generateOptions(correct, minVal = 0) {
    const opts = new Set([correct]);
    let attempts = 0;
    while (opts.size < 4 && attempts < 100) {
        attempts++;
        const factor = Math.max(1, Math.round(correct * 0.15));
        const offset = (randInt(1, Math.max(6, factor))) * (Math.random() > 0.5 ? 1 : -1);
        const wrongOpt = correct + offset;
        if (wrongOpt >= minVal && wrongOpt !== correct) {
            opts.add(wrongOpt);
        }
    }
    let fallback = Math.max(minVal, correct - 2);
    while (opts.size < 4) {
        if (!opts.has(fallback) && fallback >= minVal && fallback !== correct) {
            opts.add(fallback);
        }
        fallback++;
    }
    return Array.from(opts).sort(() => Math.random() - 0.5);
}

/**
 * Renderiza o Grid de 4 Botões
 */
function renderOptionsGrid(container, options, correctAnswer) {
    const grid = document.createElement('div');
    grid.className = 'options-grid-container';

    const allButtons = [];

    options.forEach(opt => {
        const btn = document.createElement('button');
        btn.className = 'btn-game-option';
        btn.textContent = opt;
        btn.addEventListener('click', () => {
            handleAnswer(opt === correctAnswer, btn, allButtons, correctAnswer);
        });
        allButtons.push(btn);
        grid.appendChild(btn);
    });

    container.appendChild(grid);
}

/**
 * Trata o clique de resposta
 */
function handleAnswer(isCorrect, clickedBtn, allButtons, correctAnswer = null) {
    if (game.isAnswering) return;
    game.isAnswering = true;
    clearInterval(game.timerInterval);

    // Desativa botão de tempo extra se ainda não tiver sido clicado
    const extraTimeBtn = document.getElementById('btnExtraTime');
    if (extraTimeBtn) {
        extraTimeBtn.disabled = true;
    }

    game.historyAccuracy.total++;

    if (isCorrect) {
        // No modo difícil com tempo extra acionado, perde o dobro de XP (recebe 1x em vez de 2x)
        const activeMultiplier = (game.difficulty === 'dificil' && game.questionUsedExtraTime) ? 1 : game.xpMultiplier;
        const xpGained = Math.round(100 * activeMultiplier);
        game.score += xpGained;
        game.historyAccuracy.correct++;
        if (clickedBtn && clickedBtn.classList) clickedBtn.classList.add('correct');
    } else {
        game.lives--;
        if (clickedBtn && clickedBtn.classList) clickedBtn.classList.add('wrong');
        if (allButtons && correctAnswer !== null) {
            allButtons.forEach(b => {
                if (parseInt(b.textContent) === correctAnswer) b.classList.add('correct');
            });
        }
    }

    updateHeaderUI();

    setTimeout(() => {
        game.currentStep++;
        loadNextQuestion();
    }, 1200);
}

/**
 * Atualiza Corações, Timer, Pontos e Barra de Progresso no Header
 */
function updateHeaderUI() {
    const heartsContainer = document.getElementById('statHearts');
    const scorePill = document.getElementById('statScorePill');
    const progressBar = document.getElementById('gameProgressBar');

    if (heartsContainer) {
        heartsContainer.innerHTML = '';
        for (let i = 1; i <= game.maxLives; i++) {
            const heart = document.createElement('span');
            heart.className = `heart-icon ${i > game.lives ? 'lost' : ''}`;
            heart.textContent = '❤️';
            heartsContainer.appendChild(heart);
        }
    }

    if (scorePill) {
        scorePill.textContent = `⭐ ${game.score} XP`;
    }

    if (progressBar) {
        const pct = Math.min(((game.currentStep - 1) / game.totalSteps) * 100, 100);
        progressBar.style.width = `${pct}%`;
    }
}

/**
 * Reinicia o Cronômetro da Pergunta com base na dificuldade
 */
function resetTimer() {
    clearInterval(game.timerInterval);
    const timerPill = document.getElementById('statTimerPill');

    const duration = game.timerDuration;
    if (timerPill) {
        timerPill.style.display = '';
        timerPill.textContent = `⏱️ ${duration}s`;
    }
    game.timer = duration;

    game.timerInterval = setInterval(() => {
        game.timer--;
        if (timerPill) timerPill.textContent = `⏱️ ${game.timer}s`;
        if (game.timer <= 0) {
            clearInterval(game.timerInterval);
            handleAnswer(false, null, []);
        }
    }, 1000);
}

/**
 * Adiciona +10 segundos na pergunta atual (Exclusivo do Modo Difícil)
 * Penalidade: o jogador perde o dobro de XP nessa questão (ganha 1x XP em vez de 2x ao acertar).
 * Pode ser usado 1 única vez por pergunta.
 */
function useExtraTime() {
    if (game.difficulty !== 'dificil' || game.questionUsedExtraTime || game.isAnswering) return;

    game.questionUsedExtraTime = true;
    game.timer += 10;

    const timerPill = document.getElementById('statTimerPill');
    if (timerPill) {
        timerPill.textContent = `⏱️ ${game.timer}s`;
        timerPill.classList.remove('timer-boost-pulse');
        void timerPill.offsetWidth; // Força repaint para reiniciar a animação de pulso
        timerPill.classList.add('timer-boost-pulse');
    }

    const btn = document.getElementById('btnExtraTime');
    if (btn) {
        btn.disabled = true;
        btn.classList.add('used');
        btn.title = '+10s já resgatados nesta questão (Bônus de dobro de XP desativado)';
        btn.innerHTML = `
            <span class="btn-extra-time-icon">⏳</span>
            <span class="btn-extra-time-label">+10s Usado</span>
            <span class="btn-extra-time-badge">1x XP</span>
        `;
    }
}

/**
 * Conclusão de Partida e Exibição do Modal de Resultado
 */
function finishGame() {
    clearInterval(game.timerInterval);
    const modalOverlay = document.getElementById('gameModalOverlay');
    if (!modalOverlay) return;

    const modalScore = document.getElementById('modalScore');
    const modalAccuracy = document.getElementById('modalAccuracy');
    const modalTitle = document.getElementById('modalTitle');

    const accuracyPct = game.historyAccuracy.total > 0 
        ? Math.round((game.historyAccuracy.correct / game.historyAccuracy.total) * 100)
        : 0;

    if (modalScore) modalScore.textContent = `+${game.score} XP`;
    if (modalAccuracy) modalAccuracy.textContent = `${accuracyPct}%`;

    const isWin = game.lives > 0;
    if (modalTitle) {
        modalTitle.textContent = isWin ? 'Partida Concluída! 🎉' : 'Fim de Jogo! 💔';
    }

    // Nomes legíveis para o modo
    const modeNames = {
        quiz: 'Quiz das 4 Operações',
        geometria: 'Geometria 2D & 3D',
        cartesiano: 'Plano Cartesiano',
        radiciacao: 'Radiciação',
        equacoes: 'Equações do 1º Grau',
        fracoes: 'Operações com Frações'
    };

    // Envia os dados da partida para o backend salvar no BD e na sessão
    fetch('salvar_partida.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            modo_jogo: (modeNames[game.mode] || 'Desafio Matemático') + ' (' + DIFFICULTY_CONFIG[game.difficulty].label + ')',
            pontos_ganhos: game.score,
            acertos: game.historyAccuracy.correct,
            total_questoes: game.historyAccuracy.total,
            resultado: isWin ? 'Vitória' : 'Derrota'
        })
    })
    .then(res => res.json())
    .then(data => {
        console.log('Partida salva com sucesso:', data);
        if (data && data.rank) {
            const rankIcon = document.getElementById('modalRankIcon');
            const rankName = document.getElementById('modalRankName');
            const rankTier = document.getElementById('modalRankTier');
            if (rankIcon && data.rank.icone) rankIcon.textContent = data.rank.icone;
            if (rankName && data.rank.nome_completo) rankName.textContent = data.rank.nome_completo;
            if (rankTier && data.rank.tier) rankTier.textContent = data.rank.tier;
        }
    })
    .catch(err => {
        console.error('Erro ao salvar partida:', err);
    });

    modalOverlay.classList.add('show');
}

