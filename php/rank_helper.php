<?php
/**
 * Helper do Sistema Ranqueado por Nível / XP
 * 21 Patentes divididas em 7 Categorias (3 divisões cada)
 * Mapeamento baseado nas faixas de XP fornecidas pelo usuário.
 */

/**
 * Retorna as faixas de XP por nível (1 a 21)
 */
function obterFaixasXP() {
    return [
        1  => ['min' => 0,      'max' => 1000],   // Bronze I
        2  => ['min' => 1000,   'max' => 3000],   // Bronze II
        3  => ['min' => 3000,   'max' => 5000],   // Bronze III
        4  => ['min' => 5000,   'max' => 7500],   // Prata I
        5  => ['min' => 7500,   'max' => 10000],  // Prata II
        6  => ['min' => 10000,  'max' => 15000],  // Prata III
        7  => ['min' => 15000,  'max' => 18000],  // Ouro I
        8  => ['min' => 18000,  'max' => 21000],  // Ouro II
        9  => ['min' => 21000,  'max' => 25000],  // Ouro III
        10 => ['min' => 25000,  'max' => 30000],  // Platina I
        11 => ['min' => 30000,  'max' => 35000],  // Platina II
        12 => ['min' => 35000,  'max' => 40000],  // Platina III
        13 => ['min' => 40000,  'max' => 45000],  // Diamante I
        14 => ['min' => 45000,  'max' => 50000],  // Diamante II
        15 => ['min' => 50000,  'max' => 60000],  // Diamante III
        16 => ['min' => 60000,  'max' => 70000],  // Lendário I
        17 => ['min' => 70000,  'max' => 80000],  // Lendário II
        18 => ['min' => 80000,  'max' => 100000], // Lendário III
        19 => ['min' => 100000, 'max' => 150000], // Mestre I
        20 => ['min' => 150000, 'max' => 200000], // Mestre II
        21 => ['min' => 200000, 'max' => 300000], // Mestre III (e acima)
    ];
}

/**
 * Calcula o nível (1 a 21) a partir da pontuação total de XP
 */
function calcularNivel($pontos) {
    $pontos = max(0, intval($pontos));

    if ($pontos >= 200000) {
        return 21;
    }

    $faixas = obterFaixasXP();
    foreach ($faixas as $nivel => $faixa) {
        if ($pontos < $faixa['max']) {
            return $nivel;
        }
    }

    return 21;
}

/**
 * Retorna as informações completas da patente de acordo com o nível e XP
 */
function obterInfoRank($nivel, $pontos = null) {
    $nivel = max(1, min(21, intval($nivel)));
    $faixas = obterFaixasXP();
    $faixaAtual = $faixas[$nivel] ?? $faixas[1];

    $categorias = [
        [
            'nome' => 'Bronze',
            'icone' => '🥉',
            'cor' => '#cd7f32',
            'slug' => 'bronze',
            'desc' => 'Dando os primeiros passos no universo dos cálculos matemáticos.'
        ],
        [
            'nome' => 'Prata',
            'icone' => '🥈',
            'cor' => '#94a3b8',
            'slug' => 'prata',
            'desc' => 'Construindo uma base sólida em raciocínio lógico e operações.'
        ],
        [
            'nome' => 'Ouro',
            'icone' => '🥇',
            'cor' => '#fbbf24',
            'slug' => 'ouro',
            'desc' => 'Dominando estratégias com agilidade e raciocínio afiado.'
        ],
        [
            'nome' => 'Platina',
            'icone' => '💠',
            'cor' => '#38bdf8',
            'slug' => 'platina',
            'desc' => 'Precisão matemática impecável em desafios cada vez mais complexos.'
        ],
        [
            'nome' => 'Diamante',
            'icone' => '💎',
            'cor' => '#c084fc',
            'slug' => 'diamante',
            'desc' => 'Mente brilhante capaz de solucionar problemas com maestria.'
        ],
        [
            'nome' => 'Lendário',
            'icone' => '⚔️',
            'cor' => '#f43f5e',
            'slug' => 'lendario',
            'desc' => 'Entre os mais habilidosos da plataforma, raciocínio supremo!'
        ],
        [
            'nome' => 'Mestre',
            'icone' => '👑',
            'cor' => '#eab308',
            'slug' => 'mestre',
            'desc' => 'O topo absoluto do conhecimento matemático! Um verdadeiro mestre.'
        ]
    ];

    $romanos = [
        1 => 'I',
        2 => 'II',
        3 => 'III'
    ];

    $indiceZero = $nivel - 1;
    $catIndex = intdiv($indiceZero, 3);
    $divisao = ($indiceZero % 3) + 1;
    $divisaoRomana = $romanos[$divisao] ?? (string)$divisao;

    $cat = $categorias[$catIndex];
    $nomeRank = $cat['nome'] . ' ' . $divisaoRomana;

    $pontosVal = ($pontos !== null) ? max(0, intval($pontos)) : $faixaAtual['min'];
    $faixaTamanho = max(1, $faixaAtual['max'] - $faixaAtual['min']);
    $progressoNaFaixa = max(0, $pontosVal - $faixaAtual['min']);
    $porcentagem_xp = ($nivel >= 21) ? 100 : min(100, max(0, round(($progressoNaFaixa / $faixaTamanho) * 100)));

    if ($nivel >= 21) {
        return [
            'nivel' => $nivel,
            'nome' => 'Mestre III',
            'nome_completo' => 'Mestre III',
            'categoria' => 'Mestre',
            'tier' => 'Mestre',
            'divisao' => 3,
            'divisao_romana' => 'III',
            'icone' => $cat['icone'],
            'cor' => $cat['cor'],
            'slug' => 'mestre',
            'badge_class' => 'mestre',
            'descricao' => $cat['desc'],
            'is_max' => true,
            'xp_atual' => $pontosVal,
            'xp_min' => $faixaAtual['min'],
            'xp_max' => $faixaAtual['max'],
            'xp_proximo' => $faixaAtual['max'],
            'xp_necessario' => 0,
            'porcentagem_xp' => 100,
            'proximo_nome' => 'Patente Máxima',
            'proximo_rank' => 'Patente Máxima'
        ];
    }

    // Próximo rank
    $proxNivel = $nivel + 1;
    $proxIndiceZero = $proxNivel - 1;
    $proxCatIndex = intdiv($proxIndiceZero, 3);
    $proxDivisao = ($proxIndiceZero % 3) + 1;
    $proxDivisaoRomana = $romanos[$proxDivisao] ?? (string)$proxDivisao;
    $proximoNome = $categorias[$proxCatIndex]['nome'] . ' ' . $proxDivisaoRomana;

    return [
        'nivel' => $nivel,
        'nome' => $nomeRank,
        'nome_completo' => $nomeRank,
        'categoria' => $cat['nome'],
        'tier' => $cat['nome'],
        'divisao' => $divisao,
        'divisao_romana' => $divisaoRomana,
        'icone' => $cat['icone'],
        'cor' => $cat['cor'],
        'slug' => $cat['slug'],
        'badge_class' => $cat['slug'],
        'descricao' => $cat['desc'],
        'is_max' => false,
        'xp_atual' => $pontosVal,
        'xp_min' => $faixaAtual['min'],
        'xp_max' => $faixaAtual['max'],
        'xp_proximo' => $faixaAtual['max'],
        'xp_necessario' => max(0, $faixaAtual['max'] - $pontosVal),
        'porcentagem_xp' => $porcentagem_xp,
        'proximo_nome' => $proximoNome,
        'proximo_rank' => $proximoNome,
        'proximo_xp_meta' => $faixaAtual['max']
    ];
}
