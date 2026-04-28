<?php
// 1. Inicia a sessão IMEDIATAMENTE como primeira ação do arquivo
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Conexão com o banco
try {
    $pdo = new PDO("mysql:host=localhost;dbname=moda", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro ao conectar: " . $e->getMessage());
}

$acao = $_POST['acao'] ?? '';

// --- PARTE 1: CADASTRO ---
if ($acao === 'cadastro') {
    $nome = $_POST['nome'];
    $cpf = $_POST['cpf'];
    $cpf = preg_replace('/\D/', '', $cpf); 
    $senha = $_POST['senha'];
    $telefone = $_POST['telefone'] ?? '';

    if (empty($nome) || empty($cpf) || empty($senha) || empty($telefone)) {
        header('Location: index.php?msg=campos_obrigatorios&tipo=erro');
        exit;
    }

    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    try {
        $sql = "INSERT INTO clientes (nome, cpf, senha, telefone) VALUES (:nome, :cpf, :senha, :telefone)";
        $stmt = $pdo->prepare($sql);
        
        $stmt->execute([
            'nome'     => $nome, 
            'cpf'      => $cpf, 
            'senha'    => $senhaHash,
            'telefone' => $telefone 
        ]);
        
        // --- NOVO REDIRECIONAMENTO ---
        // Se o CPF cadastrado for o seu de Admin, já loga e manda para o Painel
        if ($cpf === '71590928563') {
            $_SESSION['usuario_id'] = $pdo->lastInsertId();
            $_SESSION['usuario_nome'] = $nome;
            $_SESSION['logado'] = true;
            header('Location: admin/dashboard.php');
        } else {
            // Se for cliente comum, volta para a index com mensagem de sucesso
            header('Location: index.php?msg=cadastro_ok&tipo=sucesso');
        }
        exit; 

    } catch (PDOException $e) {
        // Se der erro (como CPF repetido), volta para a index
        header('Location: index.php?msg=cpf_existente&tipo=erro');
        exit;
    }
}

// --- PARTE 2: LOGIN ---
if ($acao === 'login') {
    $cpf = $_POST['cpf'];
    $cpf = preg_replace('/\D/', '', $cpf); 
    $senha = $_POST['senha'];

    $sql = "SELECT * FROM clientes WHERE cpf = :cpf";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['cpf' => $cpf]);
    $user = $stmt->fetch();

    if ($user && password_verify($senha, $user['senha'])) {
        // SALVANDO DADOS NA SESSÃO
        $_SESSION['usuario_id']   = $user['id'];
        $_SESSION['usuario_nome'] = $user['nome'];
        $_SESSION['usuario_cpf']  = $user['cpf']; // ESSENCIAL PARA O DASHBOARD
        $_SESSION['logado']       = true;

        $seuCpfAdmin = '71590928563'; 

        if ($cpf === $seuCpfAdmin) {
            header('Location: admin/dashboard.php');
        } else {
            header('Location: index.php'); // Mudado de agendamento.php para index.php
        }
        exit;
    } else {
        echo "<script>alert('CPF ou Senha incorretos!'); window.location.href='index.php';</script>";
        exit;
    }
}