<?php
session_start();
require_once '../config/database.php';

// Segurança: Apenas admin acessa
$seuCpfAdmin = '71590928563';
if (!isset($_SESSION['logado']) || $_SESSION['usuario_cpf'] !== $seuCpfAdmin) {
    exit('Acesso negado');
}

try {
    $pdo = Database::getInstance()->getConnection();

    $id = $_GET['id'] ?? null;
    $acao = $_GET['acao'] ?? null;

    if ($id && $acao) {
        if ($acao === 'concluir') {
            // Muda o status para CONCLUIDO
            $sql = "UPDATE agendamentos SET status = 'CONCLUIDO' WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
        } elseif ($acao === 'excluir') {
            // Remove o agendamento do banco
            $sql = "DELETE FROM agendamentos WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
        }
    }

    // Após processar, volta para o dashboard
    header('Location: dashboard.php');
} catch (PDOException $e) {
    die("Erro ao processar: " . $e->getMessage());
}