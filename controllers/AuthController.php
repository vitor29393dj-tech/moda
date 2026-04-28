<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Importa a conexão do banco de dados
require_once '../config/database.php';

$cpf = $_POST['cpf'] ?? ''; 
$senha = $_POST['senha'] ?? '';

if (!empty($cpf) && !empty($senha)) {
    try {
        $cpfLimpo = preg_replace('/\D/', '', $cpf);
        $db = Database::getInstance()->getConnection();
        
        $sql = "SELECT * FROM clientes WHERE cpf = :cpf";
        $stmt = $db->prepare($sql);
        $stmt->execute(['cpf' => $cpfLimpo]);
        $user = $stmt->fetch();

        if ($user && password_verify($senha, $user['senha'])) {
            // LOGIN SUCESSO
            $_SESSION['usuario_id'] = $user['id'];
            $_SESSION['usuario_nome'] = $user['nome']; 
            $_SESSION['usuario_cpf'] = $user['cpf']; // IMPORTANTE: Adicione esta linha
            $_SESSION['logado'] = true;

            if ($cpfLimpo === '71590928563') {
                header('Location: ../admin/dashboard.php');
            } else {
                header('Location: ../index.php');
            }
            exit;
        } else {
            // FALHA NO LOGIN: Volta para a index e avisa o erro
            echo "<script>alert('CPF ou Senha incorretos!'); window.location.href='../index.php';</script>";
            exit;
        }
    } catch (Exception $e) {
        // ERRO DE SISTEMA: Volta para a index
        echo "<script>alert('Erro no servidor. Tente novamente.'); window.location.href='../index.php';</script>";
        exit;
    }
} else {
    // CAMPOS VAZIOS: Volta para a index
    echo "<script>alert('Preencha todos os campos!'); window.location.href='../index.php';</script>";
    exit;
}