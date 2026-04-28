<?php
session_start();
require_once '../config/database.php';

$seuCpfAdmin = '71590928563';

if (!isset($_SESSION['logado']) || $_SESSION['usuario_cpf'] !== $seuCpfAdmin) {
    header('Location: ../index.php');
    exit;
}

$secao = $_GET['secao'] ?? 'agendamentos';

try {
    $pdo = Database::getInstance()->getConnection();

    if ($secao === 'clientes') {
        $cpfLogado = $_SESSION['usuario_cpf'];
        $stmt = $pdo->prepare("SELECT * FROM clientes WHERE cpf != :cpf ORDER BY nome ASC");
        $stmt->execute(['cpf' => $cpfLogado]);
        $dados = $stmt->fetchAll();
    }
    elseif ($secao === 'catalogo') {
        $stmt = $pdo->query("SELECT * FROM roupas ORDER BY nome ASC");
        $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    else {
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
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600&family=DM+Sans:wght@400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --bege-claro: #F7F3EE;
            --dourado: #B8965A;
            --preto-luxo: #121212;
            --cinza-escuro: #2C2B29;
            --branco: #FFFFFF;
        }
        * { box-sizing: border-box; }
        body { font-family: 'DM Sans', sans-serif; background-color: var(--bege-claro); margin: 0; display: flex; }

        .sidebar { width: 250px; height: 100vh; background-color: var(--preto-luxo); color: var(--bege-claro); padding: 30px 20px; position: fixed; top: 0; left: 0; }
        .sidebar h2 { font-family: 'Cormorant Garamond', serif; letter-spacing: 4px; border-bottom: 1px solid var(--dourado); padding-bottom: 15px; text-align: center; color: var(--dourado); margin-bottom: 10px; }
        .sidebar a { display: block; color: var(--bege-claro); text-decoration: none; padding: 15px 10px; font-size: 13px; text-transform: uppercase; transition: 0.3s; border-radius: 4px; margin-bottom: 5px; letter-spacing: 0.5px; }
        .sidebar a:hover, .sidebar a.active { background-color: var(--dourado); color: var(--preto-luxo); font-weight: bold; }
        .sidebar a.nova-peca { margin-top: 10px; border: 1px solid var(--dourado); color: var(--dourado); text-align: center; font-size: 12px; letter-spacing: 1px; }
        .sidebar a.nova-peca:hover { background-color: var(--dourado); color: var(--preto-luxo); }

        .main-content { margin-left: 250px; padding: 40px 60px; width: calc(100% - 250px); }
        .header-dashboard { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 2px solid #ddd; padding-bottom: 20px; }
        .header-dashboard h1 { font-family: 'Cormorant Garamond', serif; font-size: 32px; font-weight: 500; color: var(--cinza-escuro); }

        .alerta { padding: 13px 18px; border-radius: 3px; margin-bottom: 22px; font-size: 13px; display: flex; align-items: center; gap: 10px; }
        .alerta-sucesso { background: #F0F7F2; border-left: 3px solid #2e7d32; color: #2e7d32; }
        .alerta-excluido { background: #FDF2F3; border-left: 3px solid #8B2635; color: #8B2635; }

        .catalogo-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; }
        .card-tabela { background: var(--branco); padding: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border-radius: 8px; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 14px 15px; background-color: var(--bege-claro); font-size: 11px; color: var(--cinza-escuro); text-transform: uppercase; letter-spacing: 0.5px; }
        td { padding: 14px 15px; border-bottom: 1px solid #eee; font-size: 14px; vertical-align: middle; }
        tr:last-child td { border-bottom: none; }

        .status { padding: 5px 11px; border-radius: 20px; font-size: 10px; font-weight: bold; text-transform: uppercase; display: inline-block; }
        .status-pendente { background: #fff3e0; color: #ef6c00; border: 1px solid #ffcc80; }
        .status-concluido { background: #e8f5e9; color: #2e7d32; border: 1px solid #a5d6a7; }

        .btn-acao { text-decoration: none; width: 30px; height: 30px; line-height: 30px; font-size: 14px; border-radius: 50%; margin-right: 5px; color: white; display: inline-block; transition: 0.3s; text-align: center; }
        .btn-check { background-color: #2e7d32; }
        .btn-del { background-color: #c62828; }

        .btn-nova-peca { display: inline-block; text-decoration: none; padding: 9px 20px; background: var(--cinza-escuro); color: var(--bege-claro); font-size: 11px; text-transform: uppercase; letter-spacing: 1px; border-radius: 3px; transition: 0.3s; }
        .btn-nova-peca:hover { background: var(--dourado); color: var(--branco); }

        .acoes-catalogo { display: flex; align-items: center; justify-content: center; gap: 6px; flex-wrap: wrap; }

        .btn-toggle { text-decoration: none; padding: 6px 11px; border-radius: 3px; font-size: 11px; display: inline-block; background: var(--dourado); color: white; transition: 0.3s; white-space: nowrap; }
        .btn-toggle:hover { opacity: 0.85; }

        .btn-editar { text-decoration: none; padding: 6px 11px; border-radius: 3px; font-size: 11px; display: inline-block; background: #2C5282; color: white; transition: 0.3s; white-space: nowrap; }
        .btn-editar:hover { background: #1A365D; }

        .btn-excluir-dash { padding: 6px 11px; border-radius: 3px; font-size: 11px; display: inline-block; background: #c62828; color: white; transition: 0.3s; white-space: nowrap; cursor: pointer; border: none; font-family: 'DM Sans', sans-serif; }
        .btn-excluir-dash:hover { background: #8B1A1A; }

        .btn-logout { background-color: #c62828; color: white !important; padding: 8px 20px; border-radius: 4px; text-transform: uppercase; font-size: 12px; text-decoration: none; font-weight: bold; }

        /* MODAL */
        .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.55); z-index: 1000; align-items: center; justify-content: center; }
        .modal-overlay.aberto { display: flex; }
        .modal-box { background: var(--branco); border-radius: 4px; padding: 45px 40px; max-width: 400px; width: 90%; text-align: center; box-shadow: 0 20px 60px rgba(0,0,0,0.2); }
        .modal-icone { font-size: 36px; margin-bottom: 16px; }
        .modal-box h2 { font-family: 'Cormorant Garamond', serif; font-size: 26px; font-weight: 500; color: var(--cinza-escuro); margin-bottom: 12px; }
        .modal-box p { font-size: 14px; color: #666; line-height: 1.7; margin-bottom: 28px; }
        .modal-box p strong { color: var(--cinza-escuro); }
        .modal-botoes { display: flex; gap: 12px; }
        .modal-btn-cancelar { flex: 1; padding: 13px; background: var(--bege-claro); color: var(--cinza-escuro); border: 1px solid #DDD; border-radius: 2px; font-family: 'DM Sans', sans-serif; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; cursor: pointer; transition: 0.3s; }
        .modal-btn-cancelar:hover { background: #EEE; }
        .modal-btn-confirmar { flex: 1; padding: 13px; background: #c62828; color: white; border: none; border-radius: 2px; font-family: 'DM Sans', sans-serif; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; cursor: pointer; transition: 0.3s; text-decoration: none; display: flex; align-items: center; justify-content: center; }
        .modal-btn-confirmar:hover { background: #8B1A1A; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>ATELIER</h2>
    <a href="dashboard.php" class="<?= ($secao == 'agendamentos') ? 'active' : '' ?>">Agendamentos</a>
    <a href="dashboard.php?secao=catalogo" class="<?= ($secao == 'catalogo') ? 'active' : '' ?>">Catálogo / Estoque</a>
    <a href="dashboard.php?secao=clientes" class="<?= ($secao == 'clientes') ? 'active' : '' ?>">Clientes</a>
    <?php if ($secao === 'catalogo'): ?>
    <a href="cadastrar_roupa.php" class="nova-peca">+ Nova Peça</a>
    <?php endif; ?>
    <a href="../logout.php">Sair</a>
</div>

<div class="main-content">

    <div class="header-dashboard">
        <h1>
            <?php
                if ($secao === 'clientes') echo 'Gestão de Clientes';
                elseif ($secao === 'catalogo') echo 'Catálogo de Produtos';
                else echo 'Controle de Agendamentos';
            ?>
        </h1>
        <a href="../logout.php" class="btn-logout">Encerrar Sessão</a>
    </div>

    <?php if (isset($_GET['status'])): ?>
        <?php if ($_GET['status'] === 'cadastrado'): ?>
        <div class="alerta alerta-sucesso">✓ Nova peça cadastrada com sucesso no catálogo!</div>
        <?php elseif ($_GET['status'] === 'editado'): ?>
        <div class="alerta alerta-sucesso">✓ Informações da peça atualizadas com sucesso!</div>
        <?php elseif ($_GET['status'] === 'excluido'): ?>
        <div class="alerta alerta-excluido">✕ Peça excluída permanentemente do catálogo.</div>
        <?php endif; ?>
    <?php endif; ?>

    <?php if ($secao === 'catalogo'): ?>
    <div class="catalogo-header">
        <p style="font-size: 13px; color: #888;"><?= count($dados) ?> peça(s) cadastrada(s)</p>
        <a href="cadastrar_roupa.php" class="btn-nova-peca">+ Cadastrar Nova Peça</a>
    </div>
    <?php endif; ?>

    <div class="card-tabela">
        <table>

            <?php if ($secao === 'clientes'): ?>
                <thead><tr>
                    <th>Nome Completo</th>
                    <th>CPF Registrado</th>
                    <th>Telefone</th>
                </tr></thead>
                <tbody>
                    <?php foreach ($dados as $c): ?>
                    <tr>
                        <td><?= htmlspecialchars($c['nome']) ?></td>
                        <td><?= htmlspecialchars($c['cpf']) ?></td>
                        <td><?= htmlspecialchars($c['telefone'] ?? 'Não informado') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>

            <?php elseif ($secao === 'catalogo'): ?>
                <thead><tr>
                    <th style="width: 75px;">Foto</th>
                    <th>Peça / Modelo</th>
                    <th>Categoria</th>
                    <th style="text-align:center;">Status</th>
                    <th style="text-align:center; min-width:280px;">Ações</th>
                </tr></thead>
                <tbody>
                    <?php foreach ($dados as $r): ?>
                    <tr>
                        <td>
                            <img src="../img/<?= htmlspecialchars($r['imagem_url']) ?>"
                                 alt="Foto"
                                 style="width:58px;height:58px;object-fit:cover;border-radius:4px;border:1px solid #ddd;display:block;">
                        </td>
                        <td>
                            <strong><?= htmlspecialchars($r['nome']) ?></strong>
                            <?php if (!empty($r['modelo'])): ?>
                            <br><small style="color:#888;font-size:12px;"><?= htmlspecialchars($r['modelo']) ?></small>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($r['categoria']) ?></td>
                        <td style="text-align:center;">
                            <?php if ($r['status_estoque'] === 'disponivel'): ?>
                                <span class="status status-concluido">Disponível</span>
                            <?php else: ?>
                                <span class="status status-pendente">Indisponível</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align:center;">
                            <div class="acoes-catalogo">
                                <a href="acoes_catalogo.php?id=<?= $r['id'] ?>&acao=toggle" class="btn-toggle" title="Alternar disponibilidade">⇄ Status</a>
                                <a href="editar_roupa.php?id=<?= $r['id'] ?>" class="btn-editar" title="Editar peça">✎ Editar</a>
                                <button class="btn-excluir-dash" onclick="abrirModalExcluir(<?= $r['id'] ?>, '<?= htmlspecialchars(addslashes($r['nome'])) ?>')" title="Excluir peça">✕ Excluir</button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>

            <?php else: ?>
                <thead><tr>
                    <th>Cliente</th>
                    <th>Data e Horário</th>
                    <th>Tipo de Peça</th>
                    <th style="text-align:center;">Status</th>
                    <th style="text-align:center;">Gerenciar</th>
                </tr></thead>
                <tbody>
                    <?php foreach ($dados as $a): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($a['cliente_nome']) ?></strong></td>
                        <td><?= date('d/m/Y', strtotime($a['data_agendamento'])) ?> às <?= substr($a['horario'], 0, 5) ?></td>
                        <td><?= htmlspecialchars($a['roupa_nome']) ?></td>
                        <td style="text-align:center;">
                            <span class="status status-<?= strtolower(trim($a['status'] ?? 'pendente')) ?>">
                                <?= htmlspecialchars($a['status'] ?? 'PENDENTE') ?>
                            </span>
                        </td>
                        <td style="text-align:center;">
                            <a href="acoes_agendamento.php?id=<?= $a['id'] ?>&acao=concluir" class="btn-acao btn-check" title="Concluir">✓</a>
                            <a href="acoes_agendamento.php?id=<?= $a['id'] ?>&acao=excluir" class="btn-acao btn-del" title="Excluir">✕</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            <?php endif; ?>

        </table>
    </div>
</div>

<!-- MODAL DE CONFIRMAÇÃO DE EXCLUSÃO -->
<div class="modal-overlay" id="modalExcluir">
    <div class="modal-box">
        <div class="modal-icone">🗑️</div>
        <h2>Confirmar Exclusão</h2>
        <p>Você está prestes a excluir permanentemente a peça<br><strong id="modalNomePeca"></strong>.<br><br>Esta ação não pode ser desfeita.</p>
        <div class="modal-botoes">
            <button class="modal-btn-cancelar" onclick="fecharModal()">Cancelar</button>
            <a id="linkConfirmarExclusao" href="#" class="modal-btn-confirmar">Sim, Excluir</a>
        </div>
    </div>
</div>

<script>
    function abrirModalExcluir(id, nome) {
        document.getElementById('modalNomePeca').textContent = '"' + nome + '"';
        document.getElementById('linkConfirmarExclusao').href = 'acoes_catalogo.php?id=' + id + '&acao=excluir';
        document.getElementById('modalExcluir').classList.add('aberto');
    }
    function fecharModal() {
        document.getElementById('modalExcluir').classList.remove('aberto');
    }
    document.getElementById('modalExcluir').addEventListener('click', function(e) {
        if (e.target === this) fecharModal();
    });
</script>

</body>
</html>