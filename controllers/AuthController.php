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
        
        // Busca o usuário pelo CPF no banco de dados
        $sql = "SELECT * FROM clientes WHERE cpf = :cpf";
        $stmt = $db->prepare($sql);
        $stmt->execute(['cpf' => $cpfLimpo]);
        $user = $stmt->fetch();

        // Verifica se o usuário existe e se a senha está correta
        if ($user && password_verify($senha, $user['senha'])) {
            
            // --- A ALTERAÇÃO ESTÁ AQUI ---
            // Agora pegamos o nome que veio da consulta SQL ($user['nome'])
            $_SESSION['usuario_id'] = $user['id'];
            $_SESSION['usuario_nome'] = $user['nome']; 
            $_SESSION['logado'] = true;

            // Redirecionamento baseado no CPF (Admin ou Cliente)
            if ($cpfLimpo === '71590928563') {
                header('Location: ../admin/dashboard.php');
            } else {
                header('Location: ../index.php');
            }
            exit;
        } else {
            echo "<script>alert('CPF ou Senha incorretos!'); window.location.href='../acesso.php';</script>";
        }
    } catch (Exception $e) {
        die("Erro no servidor: " . $e->getMessage());
    }
} else {
    echo "<script>alert('Preencha todos os campos!'); window.location.href='../acesso.php';</script>";
}