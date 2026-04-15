<?php
session_start();

// 1. SEGURANÇA: Só entra se estiver logado E for o seu CPF de admin
$seuCpfAdmin = '71590928563'; // O mesmo que você usou na logica_acesso.php

if (!isset($_SESSION['logado']) || $_SESSION['usuario_cpf'] !== $seuCpfAdmin) {
    header('Location: ../acesso.php');
    exit;
}

$secao = $_GET['secao'] ?? 'agendamentos';

try {
    $pdo = new PDO("mysql:host=localhost;dbname=moda", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

   if ($secao === 'clientes') {
        $cpfLogado = $_SESSION['usuario_cpf']; 
        $sql = "SELECT * FROM clientes WHERE cpf != '$cpfLogado' ORDER BY nome ASC";
        $stmt = $pdo->query($sql);
        $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $sql = "SELECT a.id, a.data_agendamento, a.horario, a.status, c.nome as cliente_nome, r.nome as roupa_nome 
                FROM agendamentos a
                JOIN clientes c ON a.cliente_id = c.id
                JOIN roupas r ON a.roupa_id = r.id
                ORDER BY a.data_agendamento ASC";
        $stmt = $pdo->query($sql);
        $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    die("Erro ao conectar ao banco: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Atelier — Painel Administrativo</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --bege-claro: #F7F3EE;
            --dourado: #B8965A;
            --preto-luxo: #121212;
            --cinza-escuro: #2C2B29;
            --branco: #FFFFFF;
        }

        body { font-family: 'DM Sans', sans-serif; background-color: var(--bege-claro); margin: 0; display: flex; }

        .sidebar { width: 250px; height: 100vh; background-color: var(--preto-luxo); color: var(--bege-claro); padding: 30px 20px; position: fixed; }
        .sidebar h2 { font-family: serif; letter-spacing: 4px; border-bottom: 1px solid var(--dourado); padding-bottom: 15px; text-align: center; color: var(--dourado); }
        .sidebar a { display: block; color: var(--bege-claro); text-decoration: none; padding: 15px 10px; font-size: 13px; text-transform: uppercase; transition: 0.3s; border-radius: 4px; margin-bottom: 5px; }
        .sidebar a:hover, .sidebar a.active { background-color: var(--dourado); color: var(--preto-luxo); font-weight: bold; }

        .main-content { margin-left: 250px; padding: 40px 60px; width: calc(100% - 250px); box-sizing: border-box; }
        .header-dashboard { display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; border-bottom: 2px solid #ddd; padding-bottom: 20px; }
        
        .card-tabela { background: var(--branco); padding: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border-radius: 8px; }
        
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 15px; background-color: var(--bege-claro); font-size: 12px; color: var(--cinza-escuro); text-transform: uppercase; }
        td { padding: 15px; border-bottom: 1px solid #eee; font-size: 14px; }

        .status { padding: 6px 12px; border-radius: 20px; font-size: 10px; font-weight: bold; text-transform: uppercase; display: inline-block; }
        .status-pendente { background: #fff3e0; color: #ef6c00; border: 1px solid #ffcc80; }
        .status-concluido { background: #e8f5e9; color: #2e7d32; border: 1px solid #a5d6a7; }

        .btn-acao { text-decoration: none; width: 30px; height: 30px; line-height: 30px; font-size: 14px; border-radius: 50%; margin-right: 5px; color: white; display: inline-block; transition: 0.3s; text-align: center; }
        .btn-check { background-color: #2e7d32; }
        .btn-del { background-color: #c62828; }
        .btn-logout { background-color: #c62828; color: white !important; padding: 8px 20px; border-radius: 4px; text-transform: uppercase; font-size: 12px; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>ATELIER</h2>
        <a href="dashboard.php" class="<?= $secao == 'agendamentos' ? 'active' : '' ?>">Agendamentos</a>
        <a href="dashboard.php?secao=clientes" class="<?= $secao == 'clientes' ? 'active' : '' ?>">Clientes</a>
        <a href="../logout.php">Sair</a>
    </div>

    <div class="main-content">
        <div class="header-dashboard">
            <h1><?= $secao === 'clientes' ? 'Gestão de Clientes' : 'Controle de Agendamentos' ?></h1>
            <a href="../logout.php" class="btn-logout" style="text-decoration: none; font-weight: bold;">Encerrar Sessão</a>
        </div>

        <div class="card-tabela">
            <table>
                <?php if ($secao === 'clientes'): ?>
                    <thead>
                        <tr>
                            <th>Nome Completo</th>
                            <th>CPF Registrado</th>
                            <th>Telefone</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dados as $c): ?>
                        <tr>
                            <td><?= htmlspecialchars($c['nome']) ?></td>
                            <td><?= htmlspecialchars($c['cpf']) ?></td>
                            <td><?= htmlspecialchars($c['telefone'] ?? 'Não informado') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                <?php else: ?>
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Data e Horário</th>
                            <th>Tipo de Peça</th>
                            <th style="text-align: center;">Status</th>
                            <th style="text-align: center;">Gerenciar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dados as $a): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($a['cliente_nome']) ?></strong></td>
                            <td><?= date('d/m/Y', strtotime($a['data_agendamento'])) ?> às <?= substr($a['horario'], 0, 5) ?></td>
                            <td><?= htmlspecialchars($a['roupa_nome']) ?></td>
                            
                            <td style="text-align: center;">
                                <?php 
                                    $status_banco = !empty($a['status']) ? $a['status'] : 'PENDENTE';
                                    $classe_css = strtolower(trim($status_banco));
                                ?>
                                <span class="status status-<?= $classe_css ?>">
                                    <?= htmlspecialchars($status_banco) ?>
                                </span>
                            </td>

                            <td style="text-align: center;">
                                <a href="acoes_agendamento.php?id=<?= $a['id'] ?>&acao=concluir" 
                                   class="btn-acao btn-check" onclick="return confirm('Concluir este serviço?')">✓</a>

                                <a href="acoes_agendamento.php?id=<?= $a['id'] ?>&acao=excluir" 
                                   class="btn-acao btn-del" onclick="return confirm('Excluir permanentemente?')">✕</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                <?php endif; ?>
            </table>
        </div>
    </div>
</body>
</html>