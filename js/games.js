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

const shapes2D3D = [
    // --- FIGURAS 2D ---
    { 
        name: 'Quadrado', 
        type: '2D', 
        image: '../imagens/geometria/quadrado.png',
        svg: `<svg viewBox="0 0 120 120" class="geometry-svg">
            <rect x="20" y="20" width="80" height="80" rx="4" fill="rgba(0, 168, 255, 0.15)" stroke="#00a8ff" stroke-width="4"/>
        </svg>` 
    },
    { 
        name: 'Retângulo', 
        type: '2D', 
        image: '../imagens/geometria/retangulo.png',
        svg: `<svg viewBox="0 0 120 120" class="geometry-svg">
            <rect x="12" y="32" width="96" height="56" rx="4" fill="rgba(56, 189, 248, 0.15)" stroke="#38bdf8" stroke-width="4"/>
        </svg>` 
    },
    { 
        name: 'Círculo', 
        type: '2D', 
        image: '../imagens/geometria/circulo.png',
        svg: `<svg viewBox="0 0 120 120" class="geometry-svg">
            <circle cx="60" cy="60" r="44" fill="rgba(251, 191, 36, 0.15)" stroke="#fbbf24" stroke-width="4"/>
        </svg>` 
    },
    { 
        name: 'Triângulo', 
        type: '2D', 
        image: '../imagens/geometria/triangulo.png',
        svg: `<svg viewBox="0 0 120 120" class="geometry-svg">
            <polygon points="60,16 16,100 104,100" stroke-linejoin="round" fill="rgba(16, 185, 129, 0.15)" stroke="#10b981" stroke-width="4"/>
        </svg>` 
    },
    { 
        name: 'Pentágono', 
        type: '2D', 
        image: '../imagens/geometria/pentagono.png',
        svg: `<svg viewBox="0 0 120 120" class="geometry-svg">
            <polygon points="60,16 104,48 87,98 33,98 16,48" stroke-linejoin="round" fill="rgba(245, 158, 11, 0.15)" stroke="#f59e0b" stroke-width="4"/>
        </svg>` 
    },
    { 
        name: 'Hexágono', 
        type: '2D', 
        image: '../imagens/geometria/hexagono.png',
        svg: `<svg viewBox="0 0 120 120" class="geometry-svg">
            <polygon points="60,15 99,37 99,83 60,105 21,83 21,37" stroke-linejoin="round" fill="rgba(56, 189, 248, 0.15)" stroke="#38bdf8" stroke-width="4"/>
        </svg>` 
    },
    { 
        name: 'Heptágono', 
        type: '2D', 
        image: '../imagens/geometria/heptagono.png',
        svg: `<svg viewBox="0 0 120 120" class="geometry-svg">
            <polygon points="60,15 95,32 104,70 80,101 40,101 16,70 25,32" stroke-linejoin="round" fill="rgba(167, 139, 250, 0.15)" stroke="#a78bfa" stroke-width="4"/>
        </svg>` 
    },
    { 
        name: 'Octógono', 
        type: '2D', 
        image: '../imagens/geometria/octogono.png',
        svg: `<svg viewBox="0 0 120 120" class="geometry-svg">
            <polygon points="42,16 78,16 104,42 104,78 78,104 42,104 16,78 16,42" stroke-linejoin="round" fill="rgba(251, 113, 133, 0.15)" stroke="#fb7185" stroke-width="4"/>
        </svg>` 
    },
    { 
        name: 'Losango', 
        type: '2D', 
        image: '../imagens/geometria/losango.png',
        svg: `<svg viewBox="0 0 120 120" class="geometry-svg">
            <polygon points="60,14 104,60 60,106 16,60" stroke-linejoin="round" fill="rgba(34, 211, 238, 0.15)" stroke="#22d3ee" stroke-width="4"/>
        </svg>` 
    },
    { 
        name: 'Trapézio', 
        type: '2D', 
        image: '../imagens/geometria/trapezio.png',
        svg: `<svg viewBox="0 0 120 120" class="geometry-svg">
            <polygon points="35,26 85,26 106,94 14,94" stroke-linejoin="round" fill="rgba(245, 158, 11, 0.15)" stroke="#f59e0b" stroke-width="4"/>
        </svg>` 
    },

    // --- FIGURAS 3D SÓLIDAS E PROFISSIONAIS ---
    { 
        name: 'Cubo', 
        type: '3D', 
        image: '../imagens/geometria/cubo.png',
        svg: `<svg viewBox="0 0 120 120" class="geometry-svg">
            <defs>
                <linearGradient id="cubeTop" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#38bdf8"/>
                    <stop offset="100%" stop-color="#0ea5e9"/>
                </linearGradient>
                <linearGradient id="cubeLeft" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#0284c7"/>
                    <stop offset="100%" stop-color="#0369a1"/>
                </linearGradient>
                <linearGradient id="cubeRight" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#0369a1"/>
                    <stop offset="100%" stop-color="#075985"/>
                </linearGradient>
            </defs>
            <!-- Face Superior -->
            <polygon points="60,18 96,38 60,58 24,38" fill="url(#cubeTop)" stroke="#e0f2fe" stroke-width="2.5" stroke-linejoin="round"/>
            <!-- Face Esquerda -->
            <polygon points="24,38 60,58 60,98 24,78" fill="url(#cubeLeft)" stroke="#e0f2fe" stroke-width="2.5" stroke-linejoin="round"/>
            <!-- Face Direita -->
            <polygon points="60,58 96,38 96,78 60,98" fill="url(#cubeRight)" stroke="#e0f2fe" stroke-width="2.5" stroke-linejoin="round"/>
        </svg>` 
    },
    { 
        name: 'Cilindro', 
        type: '3D', 
        image: '../imagens/geometria/cilindro.png',
        svg: `<svg viewBox="0 0 120 120" class="geometry-svg">
            <defs>
                <linearGradient id="cylBody" x1="0%" y1="0%" x2="100%" y2="0%">
                    <stop offset="0%" stop-color="#0369a1"/>
                    <stop offset="30%" stop-color="#38bdf8"/>
                    <stop offset="70%" stop-color="#0284c7"/>
                    <stop offset="100%" stop-color="#082f49"/>
                </linearGradient>
                <linearGradient id="cylTop" x1="0%" y1="0%" x2="0%" y2="100%">
                    <stop offset="0%" stop-color="#7dd3fc"/>
                    <stop offset="100%" stop-color="#38bdf8"/>
                </linearGradient>
            </defs>
            <!-- Corpo -->
            <path d="M26,36 C26,46 94,46 94,36 L94,84 C94,94 26,94 26,84 Z" fill="url(#cylBody)" stroke="#0284c7" stroke-width="2"/>
            <!-- Base inferior curva pontilhada (profundidade) -->
            <ellipse cx="60" cy="84" rx="34" ry="9" fill="none" stroke="#e0f2fe" stroke-width="2" stroke-dasharray="4 3"/>
            <!-- Topo -->
            <ellipse cx="60" cy="36" rx="34" ry="9" fill="url(#cylTop)" stroke="#e0f2fe" stroke-width="2.5"/>
        </svg>` 
    },
    { 
        name: 'Esfera', 
        type: '3D', 
        image: '../imagens/geometria/esfera.png',
        svg: `<svg viewBox="0 0 120 120" class="geometry-svg">
            <defs>
                <radialGradient id="sphereGrad" cx="38%" cy="32%" r="68%">
                    <stop offset="0%" stop-color="#ffffff"/>
                    <stop offset="22%" stop-color="#38bdf8"/>
                    <stop offset="65%" stop-color="#0284c7"/>
                    <stop offset="100%" stop-color="#082f49"/>
                </radialGradient>
            </defs>
            <!-- Corpo Esférico 3D com luz especular -->
            <circle cx="60" cy="60" r="44" fill="url(#sphereGrad)" stroke="#7dd3fc" stroke-width="2"/>
            <!-- Linha do equador sutil -->
            <ellipse cx="60" cy="60" rx="44" ry="12" fill="none" stroke="rgba(255, 255, 255, 0.4)" stroke-width="1.8" stroke-dasharray="4 3"/>
        </svg>` 
    },
    { 
        name: 'Cone', 
        type: '3D', 
        image: '../imagens/geometria/cone.png',
        svg: `<svg viewBox="0 0 120 120" class="geometry-svg">
            <defs>
                <linearGradient id="coneBody" x1="0%" y1="0%" x2="100%" y2="0%">
                    <stop offset="0%" stop-color="#0369a1"/>
                    <stop offset="35%" stop-color="#38bdf8"/>
                    <stop offset="75%" stop-color="#0284c7"/>
                    <stop offset="100%" stop-color="#082f49"/>
                </linearGradient>
                <linearGradient id="coneBase" x1="0%" y1="0%" x2="0%" y2="100%">
                    <stop offset="0%" stop-color="#0284c7"/>
                    <stop offset="100%" stop-color="#0369a1"/>
                </linearGradient>
            </defs>
            <!-- Corpo do cone -->
            <path d="M60,18 L26,88 C36,98 84,98 94,88 Z" fill="url(#coneBody)" stroke="#0284c7" stroke-width="2"/>
            <!-- Elipse da base -->
            <ellipse cx="60" cy="88" rx="34" ry="9" fill="url(#coneBase)" stroke="#e0f2fe" stroke-width="2.5"/>
            <!-- Arco traseiro tracejado -->
            <path d="M26,88 C32,80 88,80 94,88" fill="none" stroke="#e0f2fe" stroke-width="2" stroke-dasharray="4 3"/>
        </svg>` 
    },
    { 
        name: 'Pirâmide', 
        type: '3D', 
        image: '../imagens/geometria/piramide.png',
        svg: `<svg viewBox="0 0 120 120" class="geometry-svg">
            <defs>
                <linearGradient id="pyrLeft" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#fbbf24"/>
                    <stop offset="100%" stop-color="#d97706"/>
                </linearGradient>
                <linearGradient id="pyrRight" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#d97706"/>
                    <stop offset="100%" stop-color="#78350f"/>
                </linearGradient>
            </defs>
            <!-- Base oculta pontilhada -->
            <polygon points="22,82 58,68 98,78 62,96" fill="none" stroke="rgba(251,191,36,0.4)" stroke-width="1.5" stroke-dasharray="4 3"/>
            <!-- Face esquerda -->
            <polygon points="60,16 22,82 62,96" fill="url(#pyrLeft)" stroke="#fef3c7" stroke-width="2.5" stroke-linejoin="round"/>
            <!-- Face direita -->
            <polygon points="60,16 62,96 98,78" fill="url(#pyrRight)" stroke="#fef3c7" stroke-width="2.5" stroke-linejoin="round"/>
        </svg>` 
    },
    { 
        name: 'Pirâmide triangular', 
        type: '3D', 
        image: '../imagens/geometria/piramide_triangular.png',
        svg: `<svg viewBox="0 0 120 120" class="geometry-svg">
            <defs>
                <linearGradient id="tetLeft" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#fbbf24"/>
                    <stop offset="100%" stop-color="#f59e0b"/>
                </linearGradient>
                <linearGradient id="tetRight" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#d97706"/>
                    <stop offset="100%" stop-color="#92400e"/>
                </linearGradient>
            </defs>
            <!-- Aresta interna pontilhada -->
            <line x1="60" y1="16" x2="60" y2="72" stroke="rgba(251,191,36,0.6)" stroke-width="1.8" stroke-dasharray="4 3"/>
            <!-- Face esquerda -->
            <polygon points="60,16 22,86 64,94" fill="url(#tetLeft)" stroke="#fef3c7" stroke-width="2.5" stroke-linejoin="round"/>
            <!-- Face direita -->
            <polygon points="60,16 64,94 98,82" fill="url(#tetRight)" stroke="#fef3c7" stroke-width="2.5" stroke-linejoin="round"/>
            <polygon points="22,86 64,94 98,82" fill="rgba(146, 64, 14, 0.4)" stroke="#fef3c7" stroke-width="2" stroke-linejoin="round"/>
        </svg>` 
    },
    { 
        name: 'Paralelepípedo', 
        type: '3D', 
        image: '../imagens/geometria/paralelepipedo.png',
        svg: `<svg viewBox="0 0 120 120" class="geometry-svg">
            <defs>
                <linearGradient id="parTop" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#38bdf8"/>
                    <stop offset="100%" stop-color="#0ea5e9"/>
                </linearGradient>
                <linearGradient id="parFront" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#0284c7"/>
                    <stop offset="100%" stop-color="#0369a1"/>
                </linearGradient>
                <linearGradient id="parSide" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#0369a1"/>
                    <stop offset="100%" stop-color="#075985"/>
                </linearGradient>
            </defs>
            <!-- Topo -->
            <polygon points="46,22 104,28 78,48 20,42" fill="url(#parTop)" stroke="#e0f2fe" stroke-width="2.5" stroke-linejoin="round"/>
            <!-- Frente -->
            <polygon points="20,42 78,48 78,92 20,86" fill="url(#parFront)" stroke="#e0f2fe" stroke-width="2.5" stroke-linejoin="round"/>
            <!-- Lateral Direita -->
            <polygon points="78,48 104,28 104,72 78,92" fill="url(#parSide)" stroke="#e0f2fe" stroke-width="2.5" stroke-linejoin="round"/>
        </svg>` 
    },
    { 
        name: 'Prisma triangular', 
        type: '3D', 
        image: '../imagens/geometria/prisma_triangular.png',
        svg: `<svg viewBox="0 0 120 120" class="geometry-svg">
            <defs>
                <linearGradient id="prmTop" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#38bdf8"/>
                    <stop offset="100%" stop-color="#0ea5e9"/>
                </linearGradient>
                <linearGradient id="prmSide" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#0284c7"/>
                    <stop offset="100%" stop-color="#0369a1"/>
                </linearGradient>
            </defs>
            <!-- Face superior inclinada -->
            <polygon points="24,54 64,18 96,44 56,80" fill="url(#prmTop)" stroke="#e0f2fe" stroke-width="2.5" stroke-linejoin="round"/>
            <!-- Face triangular frontal -->
            <polygon points="24,54 56,80 24,96" fill="url(#prmSide)" stroke="#e0f2fe" stroke-width="2.5" stroke-linejoin="round"/>
            <!-- Lateral inferior -->
            <polygon points="56,80 96,44 96,62 56,98" fill="#0369a1" stroke="#e0f2fe" stroke-width="2.5" stroke-linejoin="round"/>
            <polygon points="24,96 56,98 96,62" fill="#075985" stroke="#e0f2fe" stroke-width="2.5" stroke-linejoin="round"/>
        </svg>` 
    },
    { 
        name: 'Prisma pentagonal', 
        type: '3D', 
        image: '../imagens/geometria/prisma_pentagonal.png',
        svg: `<svg viewBox="0 0 120 120" class="geometry-svg">
            <defs>
                <linearGradient id="pntTop" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#7dd3fc"/>
                    <stop offset="100%" stop-color="#38bdf8"/>
                </linearGradient>
                <linearGradient id="pntFront1" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#0284c7"/>
                    <stop offset="100%" stop-color="#0369a1"/>
                </linearGradient>
            </defs>
            <!-- Topo Pentagonal -->
            <polygon points="40,28 72,20 94,42 76,64 36,54" fill="url(#pntTop)" stroke="#e0f2fe" stroke-width="2.5" stroke-linejoin="round"/>
            <!-- Faces laterais verticais -->
            <polygon points="36,54 76,64 76,96 36,86" fill="url(#pntFront1)" stroke="#e0f2fe" stroke-width="2.5" stroke-linejoin="round"/>
            <polygon points="76,64 94,42 94,74 76,96" fill="#0369a1" stroke="#e0f2fe" stroke-width="2.5" stroke-linejoin="round"/>
        </svg>` 
    },
    { 
        name: 'Prisma hexagonal', 
        type: '3D', 
        image: '../imagens/geometria/prisma_hexagonal.png',
        svg: `<svg viewBox="0 0 120 120" class="geometry-svg">
            <defs>
                <linearGradient id="hexTopGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#7dd3fc"/>
                    <stop offset="100%" stop-color="#38bdf8"/>
                </linearGradient>
                <linearGradient id="hexFront1" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#0284c7"/>
                    <stop offset="100%" stop-color="#0369a1"/>
                </linearGradient>
                <linearGradient id="hexFront2" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#0369a1"/>
                    <stop offset="100%" stop-color="#075985"/>
                </linearGradient>
            </defs>
            <!-- Topo Hexagonal -->
            <polygon points="36,28 66,20 92,34 88,54 58,62 32,50" fill="url(#hexTopGrad)" stroke="#e0f2fe" stroke-width="2.5" stroke-linejoin="round"/>
            <!-- Lateral 1 -->
            <polygon points="32,50 58,62 58,94 32,82" fill="url(#hexFront1)" stroke="#e0f2fe" stroke-width="2.5" stroke-linejoin="round"/>
            <!-- Lateral 2 -->
            <polygon points="58,62 88,54 88,86 58,94" fill="url(#hexFront2)" stroke="#e0f2fe" stroke-width="2.5" stroke-linejoin="round"/>
        </svg>` 
    }
];

// Cada figura mantém o link para busca caso o usuário queira pesquisar
shapes2D3D.forEach((shape) => {
    const searchTerms = `${shape.name} ${shape.type} geometria matemática sólido`;
    shape.googleUrl = `https://www.google.com/search?tbm=isch&q=${encodeURIComponent(searchTerms)}`;
});

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

    // Box para renderizar a figura com foto real e fallback
    const displayBox = document.createElement('div');
    displayBox.className = 'geometry-display-box';

    displayBox.innerHTML = `
        <img src="${selectedShape.image}" 
             alt="${selectedShape.name}" 
             class="geometry-img"
             onerror="this.style.display='none'; if (this.nextElementSibling) this.nextElementSibling.style.display='flex';">
        <div class="geometry-svg-fallback" style="display:none; width:100%; height:100%; align-items:center; justify-content:center;">
            ${selectedShape.svg}
        </div>
    `;
    container.appendChild(displayBox);

    const referenceLink = document.createElement('a');
    referenceLink.className = 'geometry-reference-link';
    referenceLink.href = selectedShape.googleUrl;
    referenceLink.target = '_blank';
    referenceLink.rel = 'noopener noreferrer';
    referenceLink.textContent = `Pesquisar "${selectedShape.name}" no Google Imagens ↗`;
    container.appendChild(referenceLink);

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

