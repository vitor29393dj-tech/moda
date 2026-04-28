<?php
session_start();

// 1. SEGURANÇA: Verifica se é o admin logado
$seuCpfAdmin = '71590928563'; 
if (!isset($_SESSION['logado']) || $_SESSION['usuario_cpf'] !== $seuCpfAdmin) {
    header('Location: ../index.php');
    exit;
}

// 2. Conexão com o banco
try {
    $pdo = new PDO("mysql:host=localhost;dbname=moda", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $id = $_GET['id'] ?? null;
    $acao = $_GET['acao'] ?? '';

    if ($id && $acao === 'toggle') {
        // Busca o status atual da peça
        $stmt = $pdo->prepare("SELECT status_estoque FROM roupas WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $peca = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($peca) {
            // Inverte o status
            $novoStatus = ($peca['status_estoque'] === 'disponivel') ? 'indisponivel' : 'disponivel';

            // Atualiza no banco de dados
            $update = $pdo->prepare("UPDATE roupas SET status_estoque = :novo WHERE id = :id");
            $update->execute([
                'novo' => $novoStatus,
                'id'   => $id
            ]);
        }
    }

    // 3. Volta para a página do catálogo
    header('Location: dashboard.php?secao=catalogo');
    exit;

} catch (PDOException $e) {
    die("Erro ao processar alteração: " . $e->getMessage());
}