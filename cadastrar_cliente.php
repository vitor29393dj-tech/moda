<?php
// 1. INICIA A SESSÃO IMEDIATAMENTE
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config/database.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Limpa o CPF para salvar apenas números no banco
    $nome = $_POST['nome'];
    $cpf = preg_replace('/\D/', '', $_POST['cpf']); 
    $telefone = $_POST['telefone'];
    $senha_pura = $_POST['senha'];

    $senha_hash = password_hash($senha_pura, PASSWORD_DEFAULT);

    try {
        $db = Database::getInstance()->getConnection();

        $sql = "INSERT INTO clientes (nome, cpf, telefone, senha) VALUES (:nome, :cpf, :telefone, :senha)";
        $stmt = $db->prepare($sql);

        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':cpf', $cpf);
        $stmt->bindParam(':telefone', $telefone);
        $stmt->bindParam(':senha', $senha_hash);

        if ($stmt->execute()) {
            // O PULO DO GATO: Já loga o usuário automaticamente após cadastrar
            $id_gerado = $db->lastInsertId();
            
            $_SESSION['usuario_id'] = $id_gerado;
            $_SESSION['usuario_nome'] = $nome;
            $_SESSION['logado'] = true;
            
            // Garante que a sessão seja gravada antes do redirecionamento
            session_write_close();

            echo "<script>alert('Cadastro realizado com sucesso! Bem-vindo, $nome'); window.location.href='index.php';</script>";
            exit;
        }
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            echo "<script>alert('Erro: Este CPF já está cadastrado.'); window.location.href='acesso.php';</script>";
        } else {
            echo "Erro no sistema: " . $e->getMessage();
        }
    }
}
?>