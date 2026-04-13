<?php
session_start();

if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header('Location: login.html');
    exit;
}

// Pegar qual seção mostrar (padrão é agendamentos)
$secao = $_GET['secao'] ?? 'agendamentos';

try {
    $pdo = new PDO("mysql:host=localhost;dbname=moda", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Lógica para carregar os dados dependendo da seção
    if ($secao === 'clientes') {
        $sql = "SELECT * FROM clientes ORDER BY nome ASC";
        $stmt = $pdo->query($sql);
        $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        // SQL de Agendamentos (com JOIN)
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
            --cinza-escuro: #2C2B29;
            --branco: #FFFFFF;
        }

        body { font-family: 'DM Sans', sans-serif; background-color: var(--bege-claro); margin: 0; display: flex; }

        /* Menu Lateral */
        .sidebar { width: 250px; height: 100vh; background-color: var(--cinza-escuro); color: var(--bege-claro); padding: 30px 20px; position: fixed; }
        .sidebar h2 { font-family: serif; letter-spacing: 2px; border-bottom: 1px solid rgba(247,243,238,0.2); padding-bottom: 10px; }
        .sidebar a { display: block; color: var(--bege-claro); text-decoration: none; padding: 12px 0; font-size: 14px; text-transform: uppercase; transition: 0.3s; }
        .sidebar a:hover, .sidebar a.active { color: var(--dourado); font-weight: bold; }

        /* Conteúdo */
        .main-content { margin-left: 250px; padding: 40px; width: calc(100% - 250px); }
        .header-dashboard { display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; }
        .card-tabela { background: var(--branco); padding: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border-radius: 4px; }
        
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 12px; background-color: var(--bege-claro); font-size: 12px; }
        td { padding: 15px 12px; border-bottom: 1px solid #eee; font-size: 14px; }

        /* Cores de Status */
        .status { padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; text-transform: uppercase; }
        .status-pendente { background: #fff3e0; color: #ef6c00; }
        .status-confirmado { background: #e3f2fd; color: #1565c0; }
        .status-concluido { background: #e8f5e9; color: #2e7d32; }

        .btn-acao { text-decoration: none; padding: 4px 8px; font-size: 12px; border-radius: 3px; margin-right: 5px; color: white; }
        .btn-check { background-color: #2e7d32; }
        .btn-del { background-color: #c62828; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>ATELIER</h2>
        <a href="dashboard.php" class="<?= $secao == 'agendamentos' ? 'active' : '' ?>">Agendamentos</a>
        <a href="dashboard.php?secao=clientes" class="<?= $secao == 'clientes' ? 'active' : '' ?>">Clientes</a>
        <a href="#">Catálogo</a>
    </div>

    <div class="main-content">
        <div class="header-dashboard">
            <h1><?= $secao === 'clientes' ? 'Lista de Clientes' : 'Painel de Agendamentos' ?></h1>
            <a href="../controllers/AuthController.php?action=logout" class="btn-logout" style="color: red; text-decoration: none;">Sair</a>
        </div>

        <div class="card-tabela">
            <table>
                <?php if ($secao === 'clientes'): ?>
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>CPF</th>
                            <th>Telefone</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dados as $c): ?>
                        <tr>
                            <td><?= htmlspecialchars($c['nome']) ?></td>
                            <td><?= htmlspecialchars($c['cpf']) ?></td>
                            <td><?= htmlspecialchars($c['telefone']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>

                <?php else: ?>
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Data/Hora</th>
                            <th>Peça</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dados as $a): ?>
                        <tr>
                            <td><?= htmlspecialchars($a['cliente_nome']) ?></td>
                            <td><?= date('d/m/Y', strtotime($a['data_agendamento'])) ?> às <?= substr($a['horario'], 0, 5) ?></td>
                            <td><?= htmlspecialchars($a['roupa_nome']) ?></td>
                            <td>
                                <span class="status status-<?= strtolower($a['status']) ?>">
                                    <?= $a['status'] ?>
                                </span>
                            </td>
                            <td>
                                <a href="#" class="btn-acao btn-check" title="Concluir">✓</a>
                                <a href="#" class="btn-acao btn-del" title="Excluir">✕</a>
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