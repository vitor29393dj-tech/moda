<?php
session_start();

$usuario = $_POST['usuario'] ?? '';
$senha = $_POST['senha'] ?? '';

// Verificação simples para o seu protótipo
if ($usuario === 'admin' && $senha === '1234') {
    $_SESSION['logado'] = true;
    
    // ALTERAÇÃO AQUI: Mudamos de .html para .php
    header('Location: ../admin/dashboard.php'); 
    exit;
} else {
    echo "<script>alert('Acesso negado!'); window.location.href='../admin/login.html';</script>";
}