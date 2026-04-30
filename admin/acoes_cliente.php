<?php
session_start();
require_once '../config/database.php';

$seuCpfAdmin = '71590928563';

if (!isset($_SESSION['logado']) || $_SESSION['usuario_cpf'] !== $seuCpfAdmin) {
    header('Location: ../index.php');
    exit;
}

$id   = intval($_GET['id']  ?? 0);
$acao = $_GET['acao'] ?? '';

if (!$id || $acao !== 'excluir') {
    header('Location: dashboard.php?secao=clientes');
    exit;
}

try {
    $pdo = Database::getInstance()->getConnection();

    // Busca foto de perfil para apagar do servidor
    $stmt = $pdo->prepare("SELECT foto_perfil, cpf FROM clientes WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$cliente) {
        header('Location: dashboard.php?secao=clientes');
        exit;
    }

    // Impede exclusão do próprio admin
    if ($cliente['cpf'] === $seuCpfAdmin) {
        header('Location: dashboard.php?secao=clientes');
        exit;
    }

    // Remove foto do servidor (se houver)
    if (!empty($cliente['foto_perfil'])) {
        $caminho = '../uploads/fotos_perfil/' . $cliente['foto_perfil'];
        if (file_exists($caminho)) unlink($caminho);
    }

    // Remove agendamentos do cliente e depois o próprio cliente
    $pdo->prepare("DELETE FROM agendamentos WHERE cliente_id = :id")->execute(['id' => $id]);
    $pdo->prepare("DELETE FROM clientes WHERE id = :id")->execute(['id' => $id]);

    header('Location: dashboard.php?secao=clientes&status=cli_excluido');
    exit;

} catch (PDOException $e) {
    die("Erro ao excluir cliente: " . $e->getMessage());
}