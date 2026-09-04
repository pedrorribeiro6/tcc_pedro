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
    mode: 'quiz', // 'quiz', 'geometria', 'cartesiano', 'radiciacao', 'equacoes'
    score: 0,
    lives: 3,
    maxLives: 3,
    currentStep: 1,
    totalSteps: 10,
    timer: 30,
    timerInterval: null,
    currentQuestion: null,
    askedQuestions: new Set(),
    isAnswering: false,
    historyAccuracy: { correct: 0, total: 0 }
};

/**
 * Inicialização e Leitura da URL (?game=...)
 */
function initGameEngine() {
    const urlParams = new URLSearchParams(window.location.search);
    const modeParam = urlParams.get('game');
    
    if (modeParam && ['quiz', 'geometria', 'cartesiano', 'radiciacao', 'equacoes'].includes(modeParam)) {
        game.mode = modeParam;
    }

    setupGameHeader();
    startNewGame();
}

/**
 * Configura o cabeçalho e os títulos de acordo com o modo escolhido
 */
function setupGameHeader() {
    const modeTitleEl = document.getElementById('gameModeTitle');
    const modeIconEl = document.getElementById('gameModeIcon');

    if (!modeTitleEl || !modeIconEl) return;

    const modesInfo = {
        quiz: { title: 'Quiz das 4 Operações', icon: '⚡' },
        geometria: { title: 'Desafio de Geometria 2D & 3D', icon: '📐' },
        cartesiano: { title: 'Plano Cartesiano', icon: '🗺️' },
        radiciacao: { title: 'Jogo de Radiciação', icon: '√' },
        equacoes: { title: 'Equações do 1º Grau', icon: '🧮' }
    };

    const currentInfo = modesInfo[game.mode] || modesInfo.quiz;
    modeTitleEl.textContent = currentInfo.title;
    modeIconEl.textContent = currentInfo.icon;
}

/**
 * Reinicia a partida
 */
function startNewGame() {
    game.score = 0;
    game.lives = 3;
    game.currentStep = 1;
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
    }
}

// ==========================================================================
// 1. JOGO 1: QUIZ DAS 4 OPERAÇÕES (ADIÇÃO, SUBTRAÇÃO, MULTIPLICAÇÃO, DIVISÃO)
// ==========================================================================
function renderQuizQuestion(container) {
    let num1, num2, op, questionText, correctAnswer;
    let attempts = 0;

    // Gerador que evita repetição
    do {
        attempts++;
        const opType = Math.floor(Math.random() * 4);
        if (opType === 0) {
            // Adição
            num1 = Math.floor(Math.random() * 80) + 10;
            num2 = Math.floor(Math.random() * 80) + 10;
            op = '+';
            correctAnswer = num1 + num2;
        } else if (opType === 1) {
            // Subtração
            num1 = Math.floor(Math.random() * 90) + 20;
            num2 = Math.floor(Math.random() * num1) + 5;
            op = '-';
            correctAnswer = num1 - num2;
        } else if (opType === 2) {
            // Multiplicação
            num1 = Math.floor(Math.random() * 12) + 2;
            num2 = Math.floor(Math.random() * 12) + 2;
            op = '×';
            correctAnswer = num1 * num2;
        } else {
            // Divisão Exata
            num2 = Math.floor(Math.random() * 10) + 2;
            correctAnswer = Math.floor(Math.random() * 12) + 2;
            num1 = num2 * correctAnswer;
            op = '÷';
        }
        questionText = `Quanto é ${num1} ${op} ${num2}?`;
    } while (game.askedQuestions.has(questionText) && attempts < 50);

    game.askedQuestions.add(questionText);

    const questionTitle = document.createElement('h2');
    questionTitle.className = 'question-main-text';
    questionTitle.textContent = questionText;
    container.appendChild(questionTitle);

    // Gerar 4 Opções
    const options = generateOptions(correctAnswer);
    renderOptionsGrid(container, options, correctAnswer);
}

// ==========================================================================
// 2. JOGO 2: GEOMETRIA 2D & 3D (2 OPÇÕES: VERDADEIRO / FALSO)
// ==========================================================================
function regularPolygonPoints(sides, centerX, centerY, radius, rotation = -Math.PI / 2) {
    return Array.from({ length: sides }, (_, index) => {
        const angle = rotation + (index * 2 * Math.PI) / sides;
        return `${centerX + radius * Math.cos(angle)},${centerY + radius * Math.sin(angle)}`;
    }).join(' ');
}

function createPrismSvg(sides) {
    const front = regularPolygonPoints(sides, 38, 58, 27);
    const back = regularPolygonPoints(sides, 62, 42, 27);
    const frontPoints = front.split(' ');
    const backPoints = back.split(' ');
    const connectors = frontPoints.map((point, index) => `<line x1="${point.split(',')[0]}" y1="${point.split(',')[1]}" x2="${backPoints[index].split(',')[0]}" y2="${backPoints[index].split(',')[1]}"/>`).join('');

    return `<svg viewBox="0 0 100 100" class="geometry-svg"><polygon points="${front}" fill="none" stroke="#00a8ff" stroke-width="3"/><polygon points="${back}" fill="none" stroke="#fbbf24" stroke-width="3"/>${connectors}</svg>`;
}

const shapes2D3D = [
    { name: 'Quadrado', type: '2D', svg: '<svg viewBox="0 0 100 100" class="geometry-svg"><rect x="15" y="15" width="70" height="70" fill="none" stroke="#00a8ff" stroke-width="4"/></svg>' },
    { name: 'Retângulo', type: '2D', svg: '<svg viewBox="0 0 100 100" class="geometry-svg"><rect x="10" y="25" width="80" height="50" fill="none" stroke="#38bdf8" stroke-width="4"/></svg>' },
    { name: 'Círculo', type: '2D', svg: '<svg viewBox="0 0 100 100" class="geometry-svg"><circle cx="50" cy="50" r="38" fill="none" stroke="#fbbf24" stroke-width="4"/></svg>' },
    { name: 'Triângulo', type: '2D', svg: '<svg viewBox="0 0 100 100" class="geometry-svg"><polygon points="50,12 12,88 88,88" fill="none" stroke="#10b981" stroke-width="4"/></svg>' },
    { name: 'Pentágono', type: '2D', svg: `<svg viewBox="0 0 100 100" class="geometry-svg"><polygon points="${regularPolygonPoints(5, 50, 50, 38)}" fill="none" stroke="#f59e0b" stroke-width="4"/></svg>` },
    { name: 'Hexágono', type: '2D', svg: `<svg viewBox="0 0 100 100" class="geometry-svg"><polygon points="${regularPolygonPoints(6, 50, 50, 38)}" fill="none" stroke="#38bdf8" stroke-width="4"/></svg>` },
    { name: 'Heptágono', type: '2D', svg: `<svg viewBox="0 0 100 100" class="geometry-svg"><polygon points="${regularPolygonPoints(7, 50, 50, 38)}" fill="none" stroke="#a78bfa" stroke-width="4"/></svg>` },
    { name: 'Octógono', type: '2D', svg: `<svg viewBox="0 0 100 100" class="geometry-svg"><polygon points="${regularPolygonPoints(8, 50, 50, 38)}" fill="none" stroke="#fb7185" stroke-width="4"/></svg>` },
    { name: 'Losango', type: '2D', svg: '<svg viewBox="0 0 100 100" class="geometry-svg"><polygon points="50,10 88,50 50,90 12,50" fill="none" stroke="#22d3ee" stroke-width="4"/></svg>' },
    { name: 'Trapézio', type: '2D', svg: '<svg viewBox="0 0 100 100" class="geometry-svg"><polygon points="30,18 70,18 90,82 10,82" fill="none" stroke="#f59e0b" stroke-width="4"/></svg>' },
    { name: 'Cubo', type: '3D', svg: '<svg viewBox="0 0 100 100" class="geometry-svg"><path d="M25,32 L65,32 L65,72 L25,72 Z M40,17 L80,17 L80,57 L40,57 Z M25,32 L40,17 M65,32 L80,17 M65,72 L80,57 M25,72 L40,57" fill="none" stroke="#00a8ff" stroke-width="3"/></svg>' },
    { name: 'Cilindro', type: '3D', svg: '<svg viewBox="0 0 100 100" class="geometry-svg"><ellipse cx="50" cy="25" rx="35" ry="12" fill="none" stroke="#00a8ff" stroke-width="3"/><path d="M15,25 V75 M85,25 V75" fill="none" stroke="#fbbf24" stroke-width="3"/><ellipse cx="50" cy="75" rx="35" ry="12" fill="none" stroke="#00a8ff" stroke-width="3"/></svg>' },
    { name: 'Esfera', type: '3D', svg: '<svg viewBox="0 0 100 100" class="geometry-svg"><circle cx="50" cy="50" r="38" fill="none" stroke="#00a8ff" stroke-width="3"/><ellipse cx="50" cy="50" rx="38" ry="12" fill="none" stroke="#fbbf24" stroke-width="2.5" stroke-dasharray="5,5"/></svg>' },
    { name: 'Cone', type: '3D', svg: '<svg viewBox="0 0 100 100" class="geometry-svg"><ellipse cx="50" cy="80" rx="35" ry="12" fill="none" stroke="#00a8ff" stroke-width="3"/><line x1="50" y1="15" x2="15" y2="80" stroke="#fbbf24" stroke-width="3"/><line x1="50" y1="15" x2="85" y2="80" stroke="#fbbf24" stroke-width="3"/></svg>' },
    { name: 'Pirâmide', type: '3D', svg: '<svg viewBox="0 0 100 100" class="geometry-svg"><polygon points="50,10 15,80 85,80" fill="none" stroke="#fbbf24" stroke-width="3"/><line x1="50" y1="10" x2="50" y2="80" stroke="#00a8ff" stroke-width="2.5" stroke-dasharray="4,4"/></svg>' },
    { name: 'Pirâmide triangular', type: '3D', svg: '<svg viewBox="0 0 100 100" class="geometry-svg"><path d="M50,12 L15,78 L85,78 Z M50,12 L50,78 M15,78 L62,62" fill="none" stroke="#fbbf24" stroke-width="3"/></svg>' },
    { name: 'Pirâmide quadrangular', type: '3D', svg: '<svg viewBox="0 0 100 100" class="geometry-svg"><path d="M50,10 L15,72 L50,88 L85,72 Z M50,10 V88 M15,72 L85,72" fill="none" stroke="#fbbf24" stroke-width="3"/></svg>' },
    { name: 'Paralelepípedo', type: '3D', svg: '<svg viewBox="0 0 100 100" class="geometry-svg"><path d="M18,35 L62,25 L82,40 L38,50 Z M18,35 V75 L38,90 V50 M38,50 L82,40 V80 L38,90 M62,25 V65" fill="none" stroke="#38bdf8" stroke-width="3"/></svg>' },
    { name: 'Cilindro pentagonal', type: '3D', svg: createPrismSvg(5) },
    { name: 'Cilindro hexagonal', type: '3D', svg: createPrismSvg(6) },
    { name: 'Cilindro heptagonal', type: '3D', svg: createPrismSvg(7) },
    { name: 'Cilindro octagonal', type: '3D', svg: createPrismSvg(8) }
];

function renderGeometriaQuestion(container) {
    const selectedShape = shapes2D3D[Math.floor(Math.random() * shapes2D3D.length)];
    const isTrueStatement = Math.random() > 0.5;

    let statementName = selectedShape.name;
    if (!isTrueStatement) {
        // Seleciona um nome falso diferente
        const otherShapes = shapes2D3D.filter(s => s.name !== selectedShape.name);
        statementName = otherShapes[Math.floor(Math.random() * otherShapes.length)].name;
    }

    const questionText = `A figura geométrica exibida abaixo é um(a) "${statementName}"?`;

    const title = document.createElement('h2');
    title.className = 'question-main-text';
    title.textContent = questionText;
    container.appendChild(title);

    // Box para renderizar a figura geométrica
    const displayBox = document.createElement('div');
    displayBox.className = 'geometry-display-box';
    displayBox.innerHTML = selectedShape.svg;
    container.appendChild(displayBox);

    // Renderizar 2 Opções (Verdadeiro ou Falso)
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

// ==========================================================================
// 3. JOGO 3: GAME DO PLANO CARTESIANO INTERATIVO (CANVAS COM EIXOS X E Y)
// ==========================================================================
function renderCartesianoQuestion(container) {
    // Sorteia Coordenadas entre -5 e 5 (evitando 0,0 para ficar dinâmico)
    let targetX = (Math.floor(Math.random() * 10) - 5);
    let targetY = (Math.floor(Math.random() * 10) - 5);

    if (targetX === 0 && targetY === 0) targetX = 3;

    const instructionBox = document.createElement('div');
    instructionBox.className = 'cartesiano-instruction-card';
    instructionBox.innerHTML = `
        <span>Clique no ponto correspondente às coordenadas:</span>
        <span class="coord-target-badge">X = ${targetX}, Y = ${targetY}</span>
    `;
    container.appendChild(instructionBox);

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
    drawCartesianGrid(ctx, canvas.width, canvas.height);

    // Clique no Canvas
    let answered = false;
    canvas.addEventListener('click', (e) => {
        if (answered || game.isAnswering) return;

        const rect = canvas.getBoundingClientRect();
        const clickX = e.clientX - rect.left;
        const clickY = e.clientY - rect.top;

        // Converter Pixel para Coordenadas Cartesianas (-5 a +5)
        const gridSize = canvas.width / 12; // 12 divisões (-6 a +6)
        const centerX = canvas.width / 2;
        const centerY = canvas.height / 2;

        const userX = Math.round((clickX - centerX) / gridSize);
        const userY = Math.round((centerY - clickY) / gridSize);

        answered = true;
        const isCorrect = (userX === targetX && userY === targetY);

        // Desenhar Ponto do Jogador no Canvas
        const ptPxX = centerX + targetX * gridSize;
        const ptPxY = centerY - targetY * gridSize;

        ctx.beginPath();
        ctx.arc(ptPxX, ptPxY, 10, 0, 2 * Math.PI);
        ctx.fillStyle = isCorrect ? '#10b981' : '#ef4444';
        ctx.fill();
        ctx.lineWidth = 3;
        ctx.strokeStyle = '#ffffff';
        ctx.stroke();

        handleAnswer(isCorrect, canvasWrapper, []);
    });
}

/**
 * Desenha a Grade do Plano Cartesiano no Canvas
 */
function drawCartesianGrid(ctx, width, height) {
    const isLight = document.documentElement.dataset.theme === 'light';
    ctx.clearRect(0, 0, width, height);

    if (isLight) {
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, width, height);
    }

    const divisions = 12;
    const step = width / divisions;
    const centerX = width / 2;
    const centerY = height / 2;

    // Grade de Fundo
    ctx.strokeStyle = isLight ? 'rgba(0, 0, 0, 0.09)' : 'rgba(255, 255, 255, 0.08)';
    ctx.lineWidth = 1;

    for (let i = 0; i <= divisions; i++) {
        // Linhas Verticais
        ctx.beginPath();
        ctx.moveTo(i * step, 0);
        ctx.lineTo(i * step, height);
        ctx.stroke();

        // Linhas Horizontais
        ctx.beginPath();
        ctx.moveTo(0, i * step);
        ctx.lineTo(width, i * step);
        ctx.stroke();
    }

    // Eixos Principais (X e Y)
    ctx.strokeStyle = isLight ? '#0284c7' : '#00a8ff';
    ctx.lineWidth = 3;

    // Eixo X
    ctx.beginPath();
    ctx.moveTo(0, centerY);
    ctx.lineTo(width, centerY);
    ctx.stroke();

    // Eixo Y
    ctx.beginPath();
    ctx.moveTo(centerX, 0);
    ctx.lineTo(centerX, height);
    ctx.stroke();

    // Rótulos numéricos nos eixos
    ctx.fillStyle = isLight ? '#334155' : '#94a3b8';
    ctx.font = 'bold 12px Plus Jakarta Sans';
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';

    for (let i = -5; i <= 5; i++) {
        if (i !== 0) {
            // Rótulo Eixo X
            ctx.fillText(i, centerX + i * step, centerY + 18);
            // Rótulo Eixo Y
            ctx.fillText(i, centerX - 18, centerY - i * step);
        }
    }

    // Origem (0,0)
    ctx.fillStyle = isLight ? '#b45309' : '#fbbf24';
    ctx.fillText('0', centerX - 12, centerY + 14);
}

// ==========================================================================
// 4. JOGO 4: JOGO SOBRE RADICIAÇÃO (SEM REPETIR PERGUNTAS)
// ==========================================================================
function renderRadiciacaoQuestion(container) {
    let questionText, correctAnswer;
    let attempts = 0;

    do {
        attempts++;
        const type = Math.floor(Math.random() * 3);

        if (type === 0) {
            // Raiz Quadrada simples
            const base = Math.floor(Math.random() * 12) + 2; // 2 a 13
            const rad = base * base;
            questionText = `Qual é o valor da raiz quadrada √${rad}?`;
            correctAnswer = base;
        } else if (type === 1) {
            // Raiz Cúbica simples
            const base = Math.floor(Math.random() * 6) + 2; // 2 a 7
            const rad = base * base * base;
            questionText = `Qual é o valor da raiz cúbica ³√${rad}?`;
            correctAnswer = base;
        } else {
            // Soma/Subtração de Raízes
            const b1 = Math.floor(Math.random() * 8) + 2;
            const b2 = Math.floor(Math.random() * 8) + 2;
            questionText = `Calcule o valor de: √${b1 * b1} + √${b2 * b2}`;
            correctAnswer = b1 + b2;
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
    let questionText, correctAnswer;
    let attempts = 0;

    do {
        attempts++;
        const type = Math.floor(Math.random() * 3);
        const xVal = Math.floor(Math.random() * 12) + 1; // Solução exata para X (1 a 12)

        if (type === 0) {
            // Forma ax + b = c
            const a = Math.floor(Math.random() * 5) + 2;
            const b = Math.floor(Math.random() * 15) + 1;
            const c = a * xVal + b;
            questionText = `Determine o valor de x na equação: ${a}x + ${b} = ${c}`;
            correctAnswer = xVal;
        } else if (type === 1) {
            // Forma ax - b = c
            const a = Math.floor(Math.random() * 5) + 2;
            const b = Math.floor(Math.random() * 15) + 1;
            const c = a * xVal - b;
            questionText = `Determine o valor de x na equação: ${a}x - ${b} = ${c}`;
            correctAnswer = xVal;
        } else {
            // Forma ax + b = cx + d
            const cVal = Math.floor(Math.random() * 3) + 1;
            const aVal = cVal + Math.floor(Math.random() * 4) + 1;
            const bVal = Math.floor(Math.random() * 10) + 1;
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
// FUNÇÕES UTILITÁRIAS DE OPÇÕES E INTERAÇÃO
// ==========================================================================

/**
 * Gera 4 opções plausíveis para a resposta correta
 */
function generateOptions(correct) {
    const opts = new Set([correct]);
    while (opts.size < 4) {
        const offset = (Math.floor(Math.random() * 6) + 1) * (Math.random() > 0.5 ? 1 : -1);
        const wrongOpt = correct + offset;
        if (wrongOpt >= 0 && wrongOpt !== correct) {
            opts.add(wrongOpt);
        }
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

    game.historyAccuracy.total++;

    if (isCorrect) {
        game.score += 100;
        game.historyAccuracy.correct++;
        if (clickedBtn && clickedBtn.classList) {
            clickedBtn.classList.add('correct');
        }
    } else {
        game.lives--;
        if (clickedBtn && clickedBtn.classList) {
            clickedBtn.classList.add('wrong');
        }
        // Se houver botões e uma resposta correta, destaca o correto
        if (allButtons && correctAnswer !== null) {
            allButtons.forEach(b => {
                if (parseInt(b.textContent) === correctAnswer) {
                    b.classList.add('correct');
                }
            });
        }
    }

    updateHeaderUI();

    // Avança para a próxima pergunta após 1.2 segundos
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
 * Reinicia o Cronômetro da Pergunta (30s)
 */
function resetTimer() {
    clearInterval(game.timerInterval);
    game.timer = 30;
    const timerPill = document.getElementById('statTimerPill');
    if (timerPill) timerPill.textContent = `⏱️ ${game.timer}s`;

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
        equacoes: 'Equações do 1º Grau'
    };

    // Envia os dados da partida para o backend salvar no BD e na sessão
    fetch('salvar_partida.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            modo_jogo: modeNames[game.mode] || 'Desafio Matemático',
            pontos_ganhos: game.score,
            acertos: game.historyAccuracy.correct,
            total_questoes: game.historyAccuracy.total,
            resultado: isWin ? 'Vitória' : 'Derrota'
        })
    })
    .then(res => res.json())
    .then(data => {
        console.log('Partida salva com sucesso:', data);
    })
    .catch(err => {
        console.error('Erro ao salvar partida:', err);
    });

    modalOverlay.classList.add('show');
}

