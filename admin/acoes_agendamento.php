<?php
session_start();
// Segurança: Só processa se estiver logado
if (!isset($_SESSION['logado'])) exit;

try {
    $pdo = new PDO("mysql:host=localhost;dbname=moda", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

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