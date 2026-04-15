<?php
session_start();

// Proteção: Se não estiver logado, volta para o login
if (!isset($_SESSION['logado'])) {
    header("Location: acesso.php");
    exit;
}

// Se for o Admin tentando entrar na área de cliente, avisa (opcional)
$seuCpfAdmin = '71590928563';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Atelier - Agendamento</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f1ea; text-align: center; padding: 50px; }
        .card { background: white; padding: 30px; border-radius: 10px; display: inline-block; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        h1 { color: #333; }
        .btn-sair { color: red; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Olá, <?php echo $_SESSION['usuario_nome']; ?>!</h1>
        <p>Bem-vindo à sua área de agendamentos.</p>
        <p>Em breve, você poderá escolher suas peças e horários aqui.</p>
        <br>
        <a href="logout.php" class="btn-sair">Sair da conta</a>
    </div>
</body>
</html>