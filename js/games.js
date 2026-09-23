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
    
    if (modeParam && ['quiz', 'geometria', 'cartesiano', 'radiciacao', 'equacoes', 'fracoes'].includes(modeParam)) {
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
        equacoes: { title: 'Equações do 1º Grau', icon: '🧮' },
        fracoes: { title: 'Operações com Frações', icon: '½' }
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
        case 'fracoes':
            renderFracoesQuestion(challengeCard);
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

const shapes2D3D = [
    // --- FIGURAS 2D (Todas com suas imagens locais) ---
    { 
        name: 'Quadrado', 
        type: '2D', 
        image: '../imagens/geometria/quadrado.jpg' 
    },
    { 
        name: 'Retângulo', 
        type: '2D', 
        image: '../imagens/geometria/retangulo.jpg' 
    },
    { 
        name: 'Círculo', 
        type: '2D', 
        image: '../imagens/geometria/circulo.jpg' 
    },
    { 
        name: 'Triângulo', 
        type: '2D', 
        image: '../imagens/geometria/triangulo.jpg' 
    },
    { 
        name: 'Pentágono', 
        type: '2D', 
        image: '../imagens/geometria/pentagono.jpg' 
    },
    { 
        name: 'Hexágono', 
        type: '2D', 
        image: '../imagens/geometria/hexagono.jpg' 
    },
    { 
        name: 'Heptágono', 
        type: '2D', 
        image: '../imagens/geometria/heptagono.jpg' 
    },
    { 
        name: 'Octógono', 
        type: '2D', 
        image: '../imagens/geometria/octogono.jpg' 
    },
    { 
        name: 'Losango', 
        type: '2D', 
        image: '../imagens/geometria/losango.jpg' 
    },
    { 
        name: 'Trapézio', 
        type: '2D', 
        image: '../imagens/geometria/trapezio.jpg' 
    },

    // --- FIGURAS 3D (Todas com suas imagens locais) ---
    { 
        name: 'Cubo', 
        type: '3D', 
        image: '../imagens/geometria/cubo.jpg' 
    },
    { 
        name: 'Cilindro', 
        type: '3D', 
        image: '../imagens/geometria/cilindro.png' 
    },
    { 
        name: 'Esfera', 
        type: '3D', 
        image: '../imagens/geometria/esfera.png' 
    },
    { 
        name: 'Cone', 
        type: '3D', 
        image: '../imagens/geometria/cone.jpg' 
    },
    { 
        name: 'Pirâmide', 
        type: '3D', 
        image: '../imagens/geometria/piramide.jpg' 
    },
    { 
        name: 'Pirâmide triangular', 
        type: '3D', 
        image: '../imagens/geometria/piramide_triangular.jpg' 
    },
    { 
        name: 'Paralelepípedo', 
        type: '3D', 
        image: '../imagens/geometria/paralelepipedo.jpg' 
    },
    { 
        name: 'Prisma pentagonal', 
        type: '3D', 
        image: '../imagens/geometria/prisma_pentagonal.png' 
    },
    { 
        name: 'Prisma hexagonal', 
        type: '3D', 
        image: '../imagens/geometria/prisma_hexagonal.png' 
    },
    { 
        name: 'Prisma heptagonal', 
        type: '3D', 
        image: '../imagens/geometria/prisma_heptagonal.png' 
    },
    { 
        name: 'Prisma octagonal', 
        type: '3D', 
        image: '../imagens/geometria/prisma_octagonal.jpg' 
    }
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

    // Box para renderizar a figura com imagem real do projeto
    const displayBox = document.createElement('div');
    displayBox.className = 'geometry-display-box';

    displayBox.innerHTML = `
        <img src="${selectedShape.image}" 
             alt="${selectedShape.name}" 
             class="geometry-img">
    `;
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
 * Modos sem cronômetro: 'equacoes' e 'fracoes'
 */
function resetTimer() {
    clearInterval(game.timerInterval);
    const timerPill = document.getElementById('statTimerPill');

    // Modos que não usam cronômetro — o aluno resolve sem pressão de tempo
    const noTimerModes = ['equacoes', 'fracoes'];
    if (noTimerModes.includes(game.mode)) {
        if (timerPill) timerPill.style.display = 'none';
        return;
    }

    // Modos normais com cronômetro de 30s
    if (timerPill) {
        timerPill.style.display = '';
        timerPill.textContent = `⏱️ 30s`;
    }
    game.timer = 30;

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
        equacoes: 'Equações do 1º Grau',
        fracoes: 'Operações com Frações'
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

