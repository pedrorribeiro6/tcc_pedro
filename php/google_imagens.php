<?php

header('Content-Type: application/json; charset=utf-8');

$apiKey = getenv('GOOGLE_CUSTOM_SEARCH_API_KEY');
$searchEngineId = getenv('GOOGLE_CUSTOM_SEARCH_ENGINE_ID');
$tipo = ($_GET['tipo'] ?? '2d') === '3d' ? '3D' : '2D';
$termo = $tipo === '3D'
    ? 'figuras geométricas espaciais 3D matemática'
    : 'figuras geométricas planas 2D matemática';

if (!$apiKey || !$searchEngineId) {
    http_response_code(503);
    echo json_encode([
        'erro' => 'Configure GOOGLE_CUSTOM_SEARCH_API_KEY e GOOGLE_CUSTOM_SEARCH_ENGINE_ID no ambiente do PHP.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$url = 'https://www.googleapis.com/customsearch/v1?' . http_build_query([
    'key' => $apiKey,
    'cx' => $searchEngineId,
    'q' => $termo,
    'searchType' => 'image',
    'safe' => 'active',
    'num' => 8
]);

$curl = curl_init($url);
curl_setopt_array($curl, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 10,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTPHEADER => ['Accept: application/json']
]);
$resposta = curl_exec($curl);
$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
$erroCurl = curl_error($curl);
curl_close($curl);

if ($resposta === false || $erroCurl) {
    http_response_code(502);
    echo json_encode(['erro' => 'Não foi possível consultar o Google agora.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$dados = json_decode($resposta, true);
if ($status >= 400 || isset($dados['error'])) {
    http_response_code($status >= 400 ? $status : 502);
    echo json_encode(['erro' => 'O Google recusou a consulta de imagens. Verifique a API e o mecanismo configurados.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$imagens = [];
foreach ($dados['items'] ?? [] as $item) {
    if (!empty($item['link']) && !empty($item['image']['contextLink'])) {
        $imagens[] = [
            'titulo' => $item['title'] ?? 'Figura geométrica',
            'imagem' => $item['link'],
            'fonte' => $item['image']['contextLink']
        ];
    }
}

echo json_encode(['tipo' => $tipo, 'imagens' => $imagens], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);