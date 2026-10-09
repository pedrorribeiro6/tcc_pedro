<?php
session_start();
require_once 'conexao.php';

header('Content-Type: application/json; charset=utf-8');

// Garante sessão ativa
$usuario_id = $_SESSION['usuario_id'] ?? null;
if (!$usuario_id && !isset($_SESSION['usuario_nome'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'mensagem' => 'Sessão expirada. Faça login novamente.']);
    exit;
}

$nome = trim($_POST['nome'] ?? '');
$senha_atual = $_POST['senha_atual'] ?? '';
$nova_senha = $_POST['nova_senha'] ?? '';
$confirmar_senha = $_POST['confirmar_senha'] ?? '';
$avatar_preset = trim($_POST['avatar_preset'] ?? '');
$banner_preset = trim($_POST['banner_preset'] ?? '');

if (empty($nome)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'mensagem' => 'O nome de exibição não pode ficar vazio.']);
    exit;
}

$novaFoto = $_SESSION['usuario_foto'] ?? '../imagens/logo_matematico.png';
$bannerPadrao = '../imagens/banners/banner_matematico.svg';
$novoBanner = $_SESSION['usuario_banner'] ?? $bannerPadrao;
$remover_banner = isset($_POST['remover_banner']) && $_POST['remover_banner'] === '1';

// 1. Processar Presets e Remoção de Banner
if ($remover_banner) {
    // Se o usuário tinha um banner personalizado nos uploads, remove o arquivo físico antigo
    if (!empty($_SESSION['usuario_banner']) && strpos($_SESSION['usuario_banner'], '../uploads/banners/') === 0) {
        $arquivoAntigo = __DIR__ . '/' . $_SESSION['usuario_banner'];
        if (file_exists($arquivoAntigo)) {
            @unlink($arquivoAntigo);
        }
    }
    $novoBanner = $bannerPadrao;
} else {
    if (!empty($avatar_preset) && strpos($avatar_preset, '..') === 0) {
        $novaFoto = $avatar_preset;
    }
    if (!empty($banner_preset) && strpos($banner_preset, '..') === 0) {
        $novoBanner = $banner_preset;
    }
}

// 2. Upload de Foto de Perfil
$extensoesValidas = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
$maxBytes = 5 * 1024 * 1024; // 5 MB

if (isset($_FILES['foto_upload']) && $_FILES['foto_upload']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['foto_upload'];
    if ($file['size'] > $maxBytes) {
        http_response_code(400);
        echo json_encode(['success' => false, 'mensagem' => 'A foto de perfil excede o limite máximo de 5MB.']);
        exit;
    }
    
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $extensoesValidas)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'mensagem' => 'Formato de foto inválido. Use JPG, PNG ou WEBP.']);
        exit;
    }

    $uploadDir = __DIR__ . '/../uploads/avatars/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $novoNomeArquivo = 'avatar_' . bin2hex(random_bytes(10)) . '.' . $ext;
    $destino = $uploadDir . $novoNomeArquivo;

    if (move_uploaded_file($file['tmp_name'], $destino)) {
        $novaFoto = '../uploads/avatars/' . $novoNomeArquivo;
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'mensagem' => 'Não foi possível salvar o arquivo de foto no servidor.']);
        exit;
    }
}

// 3. Upload de Banner de Perfil
if (isset($_FILES['banner_upload']) && $_FILES['banner_upload']['error'] === UPLOAD_ERR_OK) {
    $fileB = $_FILES['banner_upload'];
    if ($fileB['size'] > $maxBytes) {
        http_response_code(400);
        echo json_encode(['success' => false, 'mensagem' => 'O banner excede o limite máximo de 5MB.']);
        exit;
    }
    
    $extB = strtolower(pathinfo($fileB['name'], PATHINFO_EXTENSION));
    if (!in_array($extB, $extensoesValidas)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'mensagem' => 'Formato de banner inválido. Use JPG, PNG ou WEBP.']);
        exit;
    }

    $uploadDirB = __DIR__ . '/../uploads/banners/';
    if (!is_dir($uploadDirB)) {
        mkdir($uploadDirB, 0755, true);
    }

    $novoNomeBanner = 'banner_' . bin2hex(random_bytes(10)) . '.' . $extB;
    $destinoB = $uploadDirB . $novoNomeBanner;

    if (move_uploaded_file($fileB['tmp_name'], $destinoB)) {
        $novoBanner = '../uploads/banners/' . $novoNomeBanner;
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'mensagem' => 'Não foi possível salvar o arquivo de banner no servidor.']);
        exit;
    }
}

// 4. Alteração de Senha (opcional)
$senhaAtualizada = false;
$hashNovaSenha = null;

if (!empty($nova_senha)) {
    if (strlen($nova_senha) < 6) {
        http_response_code(400);
        echo json_encode(['success' => false, 'mensagem' => 'A nova senha deve ter no mínimo 6 caracteres.']);
        exit;
    }

    if ($nova_senha !== $confirmar_senha) {
        http_response_code(400);
        echo json_encode(['success' => false, 'mensagem' => 'A confirmação da nova senha não confere.']);
        exit;
    }

    if ($pdo && $usuario_id) {
        $stmtUser = $pdo->prepare("SELECT senha FROM usuarios WHERE id = :id LIMIT 1");
        $stmtUser->execute(['id' => $usuario_id]);
        $row = $stmtUser->fetch();

        if ($row && !empty($row['senha'])) {
            if (empty($senha_atual) || !password_verify($senha_atual, $row['senha'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'mensagem' => 'A senha atual informada está incorreta.']);
                exit;
            }
        }
    }

    $hashNovaSenha = password_hash($nova_senha, PASSWORD_BCRYPT);
    $senhaAtualizada = true;
}

// 5. Persistência no Banco de Dados
if ($pdo && $usuario_id) {
    try {
        if ($senhaAtualizada && $hashNovaSenha) {
            $stmtUpdate = $pdo->prepare("
                UPDATE usuarios 
                SET nome = :nome, foto_perfil = :foto, banner_perfil = :banner, senha = :senha 
                WHERE id = :id
            ");
            $stmtUpdate->execute([
                'nome' => $nome,
                'foto' => $novaFoto,
                'banner' => $novoBanner,
                'senha' => $hashNovaSenha,
                'id' => $usuario_id
            ]);
        } else {
            $stmtUpdate = $pdo->prepare("
                UPDATE usuarios 
                SET nome = :nome, foto_perfil = :foto, banner_perfil = :banner 
                WHERE id = :id
            ");
            $stmtUpdate->execute([
                'nome' => $nome,
                'foto' => $novaFoto,
                'banner' => $novoBanner,
                'id' => $usuario_id
            ]);
        }
    } catch (PDOException $e) {
        error_log("Erro ao salvar perfil no BD: " . $e->getMessage());
    }
}

// 6. Atualizar Variáveis de Sessão
$_SESSION['usuario_nome'] = $nome;
$_SESSION['usuario_foto'] = $novaFoto;
$_SESSION['usuario_banner'] = $novoBanner;

echo json_encode([
    'success' => true,
    'mensagem' => 'Perfil atualizado com sucesso!',
    'usuario' => [
        'nome' => $nome,
        'foto' => $novaFoto,
        'banner' => $novoBanner
    ]
]);
exit;
