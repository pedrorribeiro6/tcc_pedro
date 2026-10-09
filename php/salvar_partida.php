<?php
session_start();
require_once 'conexao.php';

header('Content-Type: application/json; charset=utf-8');

// Recebe dados JSON do fetch em games.js
$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);

if (!$data) {
    $data = $_POST;
}

$modo_jogo = $data['modo_jogo'] ?? 'Desafio Matemático';
$pontos_ganhos = intval($data['pontos_ganhos'] ?? 0);
$acertos = intval($data['acertos'] ?? 0);
$total_questoes = intval($data['total_questoes'] ?? 0);
$resultado = $data['resultado'] ?? 'Concluído';

// Calcula a porcentagem de acertos
$precisao_porcentagem = ($total_questoes > 0) ? round(($acertos / $total_questoes) * 100) : 0;

$usuario_id = $_SESSION['usuario_id'] ?? null;
$usuario_nome = $_SESSION['usuario_nome'] ?? 'Jovem Arquimedes';

// Salva na sessão (garante funcionamento offline / sem BD)
if (!isset($_SESSION['historico'])) {
    $_SESSION['historico'] = [];
}

$novaPartida = [
    'modo_jogo' => $modo_jogo,
    'resultado' => $resultado,
    'pontos_ganhos' => $pontos_ganhos,
    'acertos' => $acertos,
    'total_questoes' => $total_questoes,
    'precisao_porcentagem' => $precisao_porcentagem,
    'data_partida' => date('d/m/Y H:i')
];

require_once 'rank_helper.php';

// Adiciona no início do histórico da sessão
array_unshift($_SESSION['historico'], $novaPartida);

// Atualiza os pontos na sessão
$_SESSION['usuario_pontos'] = ($_SESSION['usuario_pontos'] ?? 2450) + $pontos_ganhos;
$_SESSION['usuario_nivel'] = calcularNivel($_SESSION['usuario_pontos']);

// Se houver conexão com o Banco de Dados e usuário autenticado, persiste na tabela
if ($pdo && $usuario_id) {
    try {
        $stmt = $pdo->prepare("
            INSERT INTO historico_partidas 
            (usuario_id, modo_jogo, resultado, pontos_ganhos, precisao_porcentagem, acertos, total_questoes) 
            VALUES (:uid, :modo, :resultado, :pontos, :precisao, :acertos, :total)
        ");
        $stmt->execute([
            'uid' => $usuario_id,
            'modo' => $modo_jogo,
            'resultado' => $resultado,
            'pontos' => $pontos_ganhos,
            'precisao' => $precisao_porcentagem,
            'acertos' => $acertos,
            'total' => $total_questoes
        ]);

        // Atualiza tabela de usuários
        $stmtUser = $pdo->prepare("UPDATE usuarios SET pontos = pontos + :pontos, nivel = :nivel WHERE id = :uid");
        $stmtUser->execute([
            'pontos' => $pontos_ganhos,
            'nivel' => $_SESSION['usuario_nivel'],
            'uid' => $usuario_id
        ]);
    } catch (PDOException $e) {
        error_log("Erro ao salvar partida no BD: " . $e->getMessage());
    }
}

$rank_info = obterInfoRank($_SESSION['usuario_nivel'], $_SESSION['usuario_pontos']);

echo json_encode([
    'success' => true,
    'novo_total_pontos' => $_SESSION['usuario_pontos'],
    'novo_nivel' => $_SESSION['usuario_nivel'],
    'rank' => $rank_info,
    'precisao' => $precisao_porcentagem
]);
exit;
