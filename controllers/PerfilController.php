<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'error' => 'Não autenticado.']);
    exit;
}

$pdo    = Database::getInstance()->getConnection();
$action = $_GET['action'] ?? '';
$userId = $_SESSION['usuario_id'];

// ── BUSCAR DADOS DO PERFIL ──────────────────────────────────────────────────
if ($action === 'buscar') {
    $stmt = $pdo->prepare("SELECT id, nome, cpf, telefone, foto_perfil, criado_em FROM clientes WHERE id = :id");
    $stmt->execute(['id' => $userId]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$cliente) {
        echo json_encode(['success' => false, 'error' => 'Usuário não encontrado.']);
        exit;
    }

    $stmtAg = $pdo->prepare(
        "SELECT a.id, a.data_agendamento, a.horario, a.status, r.nome AS roupa_nome
         FROM agendamentos a
         JOIN roupas r ON a.roupa_id = r.id
         WHERE a.cliente_id = :id
         ORDER BY a.data_agendamento DESC
         LIMIT 5"
    );
    $stmtAg->execute(['id' => $userId]);
    $agendamentos = $stmtAg->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'cliente' => $cliente, 'agendamentos' => $agendamentos]);
    exit;
}

// ── UPLOAD DE FOTO DE PERFIL ────────────────────────────────────────────────
if ($action === 'foto' && $_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'error' => 'Nenhuma foto enviada ou erro no upload.']);
        exit;
    }

    $arquivo   = $_FILES['foto'];
    $permitidos = ['image/jpeg', 'image/png', 'image/webp'];
    $finfo     = finfo_open(FILEINFO_MIME_TYPE);
    $mimeReal  = finfo_file($finfo, $arquivo['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mimeReal, $permitidos)) {
        echo json_encode(['success' => false, 'error' => 'Formato inválido. Use JPG, PNG ou WEBP.']);
        exit;
    }

    if ($arquivo['size'] > 4 * 1024 * 1024) {
        echo json_encode(['success' => false, 'error' => 'A foto não pode ultrapassar 4 MB.']);
        exit;
    }

    // Diretório de destino
    $dir = '../uploads/fotos_perfil/';
    if (!is_dir($dir)) mkdir($dir, 0755, true);

    // Busca foto antiga para apagar depois
    $stmtFoto = $pdo->prepare("SELECT foto_perfil FROM clientes WHERE id = :id");
    $stmtFoto->execute(['id' => $userId]);
    $antiga = $stmtFoto->fetchColumn();

    // Nome único baseado no ID do usuário (substitui qualquer foto anterior)
    $ext      = match($mimeReal) {
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        default      => 'jpg'
    };
    $nomeArq  = 'cliente_' . $userId . '_' . time() . '.' . $ext;
    $destino  = $dir . $nomeArq;

    if (!move_uploaded_file($arquivo['tmp_name'], $destino)) {
        echo json_encode(['success' => false, 'error' => 'Falha ao salvar a foto. Verifique as permissões da pasta.']);
        exit;
    }

    // Salva no banco
    $pdo->prepare("UPDATE clientes SET foto_perfil = :foto WHERE id = :id")
        ->execute(['foto' => $nomeArq, 'id' => $userId]);

    // Remove foto antiga do servidor (se houver e for diferente)
    if ($antiga && $antiga !== $nomeArq && file_exists($dir . $antiga)) {
        unlink($dir . $antiga);
    }

    // Atualiza sessão
    $_SESSION['usuario_foto'] = $nomeArq;

    echo json_encode([
        'success'  => true,
        'message'  => 'Foto de perfil atualizada!',
        'foto_url' => 'uploads/fotos_perfil/' . $nomeArq . '?v=' . time(),
    ]);
    exit;
}

// ── REMOVER FOTO DE PERFIL ──────────────────────────────────────────────────
if ($action === 'remover_foto' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmtFoto = $pdo->prepare("SELECT foto_perfil FROM clientes WHERE id = :id");
    $stmtFoto->execute(['id' => $userId]);
    $antiga = $stmtFoto->fetchColumn();

    if ($antiga) {
        $caminho = '../uploads/fotos_perfil/' . $antiga;
        if (file_exists($caminho)) unlink($caminho);
        $pdo->prepare("UPDATE clientes SET foto_perfil = NULL WHERE id = :id")->execute(['id' => $userId]);
        unset($_SESSION['usuario_foto']);
    }

    echo json_encode(['success' => true, 'message' => 'Foto removida.']);
    exit;
}

// ── EDITAR DADOS ────────────────────────────────────────────────────────────
if ($action === 'editar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $body  = json_decode(file_get_contents('php://input'), true);
    $nome  = trim($body['nome'] ?? '');
    $tel   = trim($body['telefone'] ?? '');
    $senha = trim($body['senha'] ?? '');

    $erros = [];
    if (empty($nome) || mb_strlen($nome) < 3)
        $erros[] = 'Nome deve ter pelo menos 3 caracteres.';
    if (empty($tel) || strlen(preg_replace('/\D/', '', $tel)) < 10)
        $erros[] = 'Telefone inválido.';

    if ($erros) { echo json_encode(['success' => false, 'erros' => $erros]); exit; }

    if (!empty($senha)) {
        if (strlen($senha) < 6) {
            echo json_encode(['success' => false, 'erros' => ['A senha deve ter pelo menos 6 caracteres.']]);
            exit;
        }
        $hash = password_hash($senha, PASSWORD_DEFAULT);
        $pdo->prepare("UPDATE clientes SET nome=:nome, telefone=:tel, senha=:senha WHERE id=:id")
            ->execute(['nome' => $nome, 'tel' => $tel, 'senha' => $hash, 'id' => $userId]);
    } else {
        $pdo->prepare("UPDATE clientes SET nome=:nome, telefone=:tel WHERE id=:id")
            ->execute(['nome' => $nome, 'tel' => $tel, 'id' => $userId]);
    }

    $_SESSION['usuario_nome'] = $nome;
    echo json_encode(['success' => true, 'message' => 'Dados atualizados!', 'novo_nome' => $nome]);
    exit;
}

// ── EXCLUIR CONTA ───────────────────────────────────────────────────────────
if ($action === 'excluir' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $body  = json_decode(file_get_contents('php://input'), true);
    $senha = trim($body['senha'] ?? '');

    $stmt = $pdo->prepare("SELECT senha, foto_perfil FROM clientes WHERE id = :id");
    $stmt->execute(['id' => $userId]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$cliente || !password_verify($senha, $cliente['senha'])) {
        echo json_encode(['success' => false, 'error' => 'Senha incorreta.']);
        exit;
    }

    // Remove foto do servidor
    if ($cliente['foto_perfil']) {
        $caminho = '../uploads/fotos_perfil/' . $cliente['foto_perfil'];
        if (file_exists($caminho)) unlink($caminho);
    }

    $pdo->prepare("DELETE FROM agendamentos WHERE cliente_id = :id")->execute(['id' => $userId]);
    $pdo->prepare("DELETE FROM clientes WHERE id = :id")->execute(['id' => $userId]);
    session_destroy();

    echo json_encode(['success' => true]);
    exit;
}

echo json_encode(['success' => false, 'error' => 'Ação inválida.']);