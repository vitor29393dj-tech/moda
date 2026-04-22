<?php
session_start();

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
    $cpf = preg_replace('/\D/', '', $cpf); // Remove pontos e traços do CPF
    $senha = $_POST['senha'];
    
    // 1. CAPTURAR O TELEFONE (recebe o que o usuário digitou com a máscara)
    $telefone = $_POST['telefone'] ?? '';

    // 2. VALIDAR (adicionamos o $telefone na checagem de campos vazios)
    if (empty($nome) || empty($cpf) || empty($senha) || empty($telefone)) {
        header('Location: acesso.php?msg=campos_obrigatorios&tipo=erro');
        exit;
    }

    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    try {
        // 3. ATUALIZAR A SQL (incluindo a coluna 'telefone' e o placeholder ':telefone')
        $sql = "INSERT INTO clientes (nome, cpf, senha, telefone) VALUES (:nome, :cpf, :senha, :telefone)";
        $stmt = $pdo->prepare($sql);
        
        // EXECUTAR (passando o valor do telefone para o banco)
        $stmt->execute([
            'nome'     => $nome, 
            'cpf'      => $cpf, 
            'senha'    => $senhaHash,
            'telefone' => $telefone 
        ]);
        
        header('Location: acesso.php?msg=cadastro_ok&tipo=sucesso');
    } catch (PDOException $e) {
        // Caso queira ver o erro real se algo falhar, mude para: die($e->getMessage());
        header('Location: acesso.php?msg=cpf_existente&tipo=erro');
    }
    exit;
}

// --- PARTE 2: LOGIN ---
if ($acao === 'login') {
    $cpf = $_POST['cpf'];
    $cpf = preg_replace('/\D/', '', $cpf); // Limpa o CPF para a busca no banco
    $senha = $_POST['senha'];

    $sql = "SELECT * FROM clientes WHERE cpf = :cpf";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['cpf' => $cpf]);
    $user = $stmt->fetch();

    if ($user && password_verify($senha, $user['senha'])) {
        // Guarda os dados na sessão
        $_SESSION['logado'] = true;
        $_SESSION['usuario_id'] = $user['id'];
        $_SESSION['usuario_nome'] = $user['nome'];
        $_SESSION['usuario_cpf'] = $user['cpf'];

        // Seu CPF de Administrador
        $seuCpfAdmin = '71590928563'; 

        // REDIRECIONAMENTO INTELIGENTE
        if ($cpf === $seuCpfAdmin) { 
            // Se for você, vai para o painel administrativo
            header('Location: admin/dashboard.php');
        } else {
            // Se for cliente (como o teste 6), volta para a HOME personalizada
            header('Location: index.php');
        }
    } else {
        header('Location: acesso.php?msg=login_invalido&tipo=erro');
    }
    exit;
}