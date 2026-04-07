<?php
// 1. INÍCIO DO TRABALHO DO SERVIDOR (O "TOPO")
session_start();

// Verificação de segurança: se não estiver logado, volta para o login
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header('Location: login.html');
    exit;
}

// Conexão com o banco de dados 'moda'
try {
    $pdo = new PDO("mysql:host=localhost;dbname=moda", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // SQL que junta as tabelas para pegar os nomes reais em vez de números (IDs)
    $sql = "SELECT a.data_agendamento, a.horario, a.status, c.nome as cliente_nome, r.nome as roupa_nome 
            FROM agendamentos a
            JOIN clientes c ON a.cliente_id = c.id
            JOIN roupas r ON a.roupa_id = r.id
            ORDER BY a.data_agendamento ASC";
    
    $stmt = $pdo->query($sql);
    $agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erro ao conectar ao banco: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atelier — Painel Administrativo</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --bege-claro: #F7F3EE;
            --dourado: #B8965A;
            --cinza-escuro: #2C2B29;
            --branco: #FFFFFF;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background-color: var(--bege-claro);
            margin: 0;
            display: flex;
        }

        /* Menu Lateral */
        .sidebar {
            width: 250px;
            height: 100vh;
            background-color: var(--cinza-escuro);
            color: var(--bege-claro);
            padding: 30px 20px;
            position: fixed;
        }

        .sidebar h2 {
            font-family: serif;
            letter-spacing: 2px;
            font-size: 20px;
            margin-bottom: 40px;
            border-bottom: 1px solid rgba(247, 243, 238, 0.2);
            padding-bottom: 10px;
        }

        .sidebar a {
            display: block;
            color: var(--bege-claro);
            text-decoration: none;
            padding: 12px 0;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: 0.3s;
        }

        .sidebar a:hover {
            color: var(--dourado);
        }

        /* Conteúdo Principal */
        .main-content {
            margin-left: 250px;
            padding: 40px;
            width: 100%;
        }

        .header-dashboard {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }

        .header-dashboard h1 {
            color: var(--cinza-escuro);
            font-size: 24px;
        }

        .btn-logout {
            color: #cc0000;
            text-decoration: none;
            font-size: 14px;
        }

        /* Tabela de Agendamentos */
        .card-tabela {
            background: var(--branco);
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            border-radius: 4px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th {
            text-align: left;
            padding: 12px;
            background-color: var(--bege-claro);
            color: var(--cinza-escuro);
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 1px;
        }

        td {
            padding: 15px 12px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
            color: #555;
        }

        .status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
            background: #e8f5e9;
            color: #2e7d32;
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>ATELIER</h2>
        <a href="#">Início</a>
        <a href="#">Agendamentos</a>
        <a href="#">Clientes</a>
        <a href="#">Catálogo</a>
    </div>

    <div class="main-content">
        <div class="header-dashboard">
            <h1>Painel de Agendamentos</h1>
            <a href="login.html" class="btn-logout">Sair</a>
        </div>

        <div class="card-tabela">
            <table>
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Data</th>
                        <th>Horário</th>
                        <th>Peça</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($agendamentos) > 0): ?>
                        <?php foreach ($agendamentos as $agendamento): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($agendamento['cliente_nome']); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($agendamento['data_agendamento'])); ?></td>
                            <td><?php echo $agendamento['horario']; ?></td>
                            <td><?php echo htmlspecialchars($agendamento['roupa_nome']); ?></td>
                            <td><span class="status"><?php echo htmlspecialchars($agendamento['status']); ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align:center;">Nenhum agendamento encontrado no banco.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>