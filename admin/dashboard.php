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
        $stmt = $pdo->prepare(
            "SELECT id, nome, cpf, telefone, foto_perfil, criado_em
             FROM clientes
             WHERE cpf != :cpf
             ORDER BY nome ASC"
        );
        $stmt->execute(['cpf' => $cpfLogado]);
        $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    elseif ($secao === 'catalogo') {
        $stmt = $pdo->query("SELECT * FROM roupas ORDER BY nome ASC");
        $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    else {
        $sql = "SELECT a.id, a.data_agendamento, a.horario, a.status,
                       c.nome as cliente_nome, r.nome as roupa_nome
                FROM agendamentos a
                JOIN clientes c ON a.cliente_id = c.id
                JOIN roupas r   ON a.roupa_id   = r.id
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
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        /* ── DESIGN TOKENS ── */
        :root {
            --bege:    #F7F3EE;
            --bege-md: #EDE8E0;
            --dourado: #B8965A;
            --dourado-lt: #D4AF7A;
            --preto:   #121212;
            --grafite: #2C2B29;
            --cinza:   #6B6860;
            --branco:  #FFFFFF;
            --verde:   #2e7d32;
            --vermelho:#c62828;
            --azul:    #1a56a0;
            --wa:      #25d366;
            --shadow:  0 2px 16px rgba(0,0,0,.06);
            --shadow-lg:0 8px 40px rgba(0,0,0,.12);
            --radius:  6px;
            --transition:.22s ease;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'DM Sans', sans-serif; background: var(--bege); display: flex; min-height: 100vh; color: var(--grafite); }

        /* ── SIDEBAR ── */
        .sidebar {
            width: 240px; min-height: 100vh;
            background: var(--preto);
            padding: 28px 18px;
            position: fixed; top: 0; left: 0;
            display: flex; flex-direction: column; gap: 4px;
        }
        .sidebar-logo {
            font-family: 'Cormorant Garamond', serif;
            font-size: 17px; letter-spacing: 5px;
            color: var(--dourado);
            text-align: center;
            padding-bottom: 22px;
            border-bottom: 1px solid rgba(184,150,90,.3);
            margin-bottom: 14px;
        }
        .sidebar a {
            display: flex; align-items: center; gap: 10px;
            color: rgba(247,243,238,.7);
            text-decoration: none;
            padding: 12px 14px;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-radius: var(--radius);
            transition: var(--transition);
        }
        .sidebar a:hover { background: rgba(255,255,255,.06); color: var(--bege); }
        .sidebar a.active { background: var(--dourado); color: var(--preto); font-weight: 600; }
        .sidebar a.nova-peca {
            margin-top: 6px;
            border: 1px solid rgba(184,150,90,.4);
            color: var(--dourado);
            justify-content: center;
        }
        .sidebar a.nova-peca:hover { background: var(--dourado); color: var(--preto); }
        .sidebar-spacer { flex: 1; }
        .sidebar a.sair { color: rgba(198,40,40,.8); margin-top: 8px; }
        .sidebar a.sair:hover { background: rgba(198,40,40,.12); color: #f87171; }

        /* Ícones SVG na sidebar */
        .sidebar svg { width: 16px; height: 16px; stroke: currentColor; fill: none; stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round; flex-shrink: 0; }

        /* ── MAIN ── */
        .main-content { margin-left: 240px; padding: 36px 52px; width: calc(100% - 240px); }

        .page-header {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 28px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--bege-md);
        }
        .page-header h1 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 30px; font-weight: 500; color: var(--grafite);
        }
        .btn-logout {
            background: var(--vermelho); color: white;
            padding: 8px 20px; border-radius: var(--radius);
            text-transform: uppercase; font-size: 11px;
            text-decoration: none; font-weight: 600;
            letter-spacing: 1px; transition: var(--transition);
        }
        .btn-logout:hover { background: #8B1A1A; }

        /* ── ALERTAS ── */
        .alerta { padding: 12px 16px; border-radius: var(--radius); margin-bottom: 20px; font-size: 13px; display: flex; align-items: center; gap: 10px; }
        .alerta-sucesso { background: #F0F7F2; border-left: 3px solid var(--verde); color: var(--verde); }
        .alerta-excluido { background: #FDF2F3; border-left: 3px solid #8B2635; color: #8B2635; }

        /* ── CARD TABELA ── */
        .card-tabela {
            background: var(--branco);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        /* ── CLIENTES: toolbar ── */
        .clientes-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 22px;
            border-bottom: 1px solid var(--bege-md);
            gap: 16px;
            flex-wrap: wrap;
        }
        .clientes-toolbar .contagem {
            font-size: 13px; color: var(--cinza);
            white-space: nowrap;
        }
        .clientes-toolbar .contagem strong {
            color: var(--grafite); font-weight: 600;
        }

        /* Campo de busca */
        .busca-wrap {
            position: relative;
            flex: 1;
            max-width: 360px;
        }
        .busca-wrap svg {
            position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
            width: 15px; height: 15px;
            stroke: var(--cinza); fill: none;
            stroke-width: 2; stroke-linecap: round; stroke-linejoin: round;
            pointer-events: none;
        }
        #campoBusca {
            width: 100%;
            padding: 9px 14px 9px 36px;
            border: 1px solid var(--bege-md);
            border-radius: 20px;
            background: var(--bege);
            font-family: 'DM Sans', sans-serif;
            font-size: 13px;
            color: var(--grafite);
            outline: none;
            transition: var(--transition);
        }
        #campoBusca:focus { border-color: var(--dourado); box-shadow: 0 0 0 3px rgba(184,150,90,.1); }
        #campoBusca::placeholder { color: #aaa; }

        /* ── TABELA ── */
        table { width: 100%; border-collapse: collapse; }
        thead th {
            padding: 13px 16px;
            background: var(--bege);
            font-size: 10.5px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .8px;
            color: var(--cinza);
            border-bottom: 1px solid var(--bege-md);
        }
        tbody tr {
            transition: background var(--transition);
        }
        tbody tr:hover { background: #faf8f5; }
        tbody tr.oculto { display: none; }
        td {
            padding: 13px 16px;
            border-bottom: 1px solid #f0ede8;
            font-size: 13.5px;
            vertical-align: middle;
        }
        tbody tr:last-child td { border-bottom: none; }

        /* ── AVATAR DO CLIENTE ── */
        .cli-avatar {
            width: 40px; height: 40px; border-radius: 50%;
            object-fit: cover;
            border: 2px solid rgba(184,150,90,.25);
            display: block;
        }
        .cli-avatar-letra {
            width: 40px; height: 40px; border-radius: 50%;
            background: linear-gradient(135deg, var(--dourado), var(--dourado-lt));
            display: flex; align-items: center; justify-content: center;
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.1rem; font-weight: 500;
            color: white;
            flex-shrink: 0;
            border: 2px solid rgba(184,150,90,.25);
        }
        .cli-info { display: flex; align-items: center; gap: 12px; }
        .cli-nome { font-weight: 500; color: var(--grafite); line-height: 1.3; }
        .cli-desde { font-size: 11px; color: var(--cinza); margin-top: 2px; }

        /* ── CPF formatado ── */
        .cpf-badge {
            font-family: monospace;
            font-size: 12.5px;
            background: var(--bege);
            padding: 3px 9px;
            border-radius: 4px;
            color: var(--grafite);
            letter-spacing: .5px;
        }

        /* ── TELEFONE ── */
        .tel-text { font-size: 13px; color: var(--grafite); }
        .tel-sem { font-size: 12px; color: #bbb; font-style: italic; }

        /* ── AÇÕES DO CLIENTE ── */
        .acoes-cli { display: flex; align-items: center; justify-content: center; gap: 6px; }

        /* Botão ícone base */
        .btn-icon {
            width: 34px; height: 34px; border-radius: 50%;
            display: inline-flex; align-items: center; justify-content: center;
            border: none; cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            flex-shrink: 0;
        }
        .btn-icon svg {
            width: 15px; height: 15px;
            stroke: currentColor; fill: none;
            stroke-width: 2; stroke-linecap: round; stroke-linejoin: round;
        }

        /* Ver detalhes */
        .btn-ver {
            background: rgba(26,86,160,.1);
            color: var(--azul);
        }
        .btn-ver:hover { background: var(--azul); color: white; transform: scale(1.08); }

        /* WhatsApp */
        .btn-wpp {
            background: rgba(37,211,102,.12);
            color: var(--wa);
        }
        .btn-wpp:hover { background: var(--wa); color: white; transform: scale(1.08); }

        /* Excluir */
        .btn-del-cli {
            background: rgba(198,40,40,.1);
            color: var(--vermelho);
        }
        .btn-del-cli:hover { background: var(--vermelho); color: white; transform: scale(1.08); }

        /* ── SEM RESULTADOS ── */
        .sem-resultado {
            display: none;
            padding: 40px;
            text-align: center;
            color: var(--cinza);
        }
        .sem-resultado svg { width: 40px; height: 40px; stroke: #ccc; fill: none; stroke-width: 1.5; margin-bottom: 12px; stroke-linecap: round; stroke-linejoin: round; }
        .sem-resultado p { font-size: 14px; }

        /* ── CATÁLOGO: botões ── */
        .catalogo-toolbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; }
        .btn-nova-peca {
            display: inline-flex; align-items: center; gap: 6px;
            text-decoration: none; padding: 9px 18px;
            background: var(--grafite); color: var(--bege);
            font-size: 11px; text-transform: uppercase;
            letter-spacing: 1px; border-radius: var(--radius);
            transition: var(--transition);
        }
        .btn-nova-peca:hover { background: var(--dourado); }

        .acoes-catalogo { display: flex; align-items: center; justify-content: center; gap: 6px; flex-wrap: wrap; }
        .btn-toggle { text-decoration: none; padding: 6px 11px; border-radius: 3px; font-size: 11px; display: inline-block; background: var(--dourado); color: white; transition: var(--transition); white-space: nowrap; }
        .btn-toggle:hover { opacity: .85; }
        .btn-editar { text-decoration: none; padding: 6px 11px; border-radius: 3px; font-size: 11px; display: inline-block; background: #2C5282; color: white; transition: var(--transition); white-space: nowrap; }
        .btn-editar:hover { background: #1A365D; }
        .btn-excluir-dash { padding: 6px 11px; border-radius: 3px; font-size: 11px; display: inline-block; background: var(--vermelho); color: white; transition: var(--transition); white-space: nowrap; cursor: pointer; border: none; font-family: 'DM Sans', sans-serif; }
        .btn-excluir-dash:hover { background: #8B1A1A; }

        /* ── STATUS BADGES ── */
        .status { padding: 4px 10px; border-radius: 20px; font-size: 10px; font-weight: 700; text-transform: uppercase; display: inline-block; }
        .status-pendente  { background: #fff3e0; color: #ef6c00; border: 1px solid #ffcc80; }
        .status-concluido { background: #e8f5e9; color: var(--verde); border: 1px solid #a5d6a7; }

        /* ── AGENDAMENTOS ── */
        .btn-acao { text-decoration: none; width: 30px; height: 30px; line-height: 30px; font-size: 14px; border-radius: 50%; margin-right: 4px; color: white; display: inline-block; transition: var(--transition); text-align: center; }
        .btn-check { background: var(--verde); }
        .btn-del   { background: var(--vermelho); }
        .btn-check:hover { background: #1b5e20; }
        .btn-del:hover   { background: #8B1A1A; }

        /* ── MODAL EXCLUSÃO PEÇA ── */
        .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.55); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(3px); }
        .modal-overlay.aberto { display: flex; }
        .modal-box { background: var(--branco); border-radius: var(--radius); padding: 44px 38px; max-width: 400px; width: 90%; text-align: center; box-shadow: var(--shadow-lg); animation: modalIn .24s ease; }
        @keyframes modalIn { from { opacity:0; transform:scale(.96) translateY(10px); } to { opacity:1; transform:scale(1); } }
        .modal-icone { font-size: 34px; margin-bottom: 14px; }
        .modal-box h2 { font-family: 'Cormorant Garamond', serif; font-size: 24px; font-weight: 500; color: var(--grafite); margin-bottom: 10px; }
        .modal-box p  { font-size: 13.5px; color: var(--cinza); line-height: 1.7; margin-bottom: 26px; }
        .modal-box p strong { color: var(--grafite); }
        .modal-botoes { display: flex; gap: 10px; }
        .modal-btn-cancelar  { flex:1; padding:12px; background:var(--bege); color:var(--grafite); border:1px solid #ddd; border-radius:3px; font-family:'DM Sans',sans-serif; font-size:12px; text-transform:uppercase; letter-spacing:1px; cursor:pointer; transition:var(--transition); }
        .modal-btn-cancelar:hover { background:#eee; }
        .modal-btn-confirmar { flex:1; padding:12px; background:var(--vermelho); color:white; border:none; border-radius:3px; font-family:'DM Sans',sans-serif; font-size:12px; text-transform:uppercase; letter-spacing:1px; cursor:pointer; transition:var(--transition); text-decoration:none; display:flex; align-items:center; justify-content:center; }
        .modal-btn-confirmar:hover { background:#8B1A1A; }

        /* ── MODAL DETALHES CLIENTE ── */
        .modal-cli-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.55); z-index:1000; align-items:center; justify-content:center; backdrop-filter:blur(3px); }
        .modal-cli-overlay.aberto { display:flex; }
        .modal-cli {
            background:var(--branco); border-radius:var(--radius);
            max-width:460px; width:90%;
            box-shadow:var(--shadow-lg);
            animation:modalIn .24s ease;
            overflow:hidden;
        }
        .modal-cli-header {
            background: linear-gradient(135deg, var(--grafite) 0%, #3d3b38 100%);
            padding: 28px 30px;
            display: flex; align-items: center; gap: 18px;
            position: relative;
        }
        .modal-cli-header .avatar-lg {
            width: 64px; height: 64px; border-radius: 50%;
            border: 3px solid rgba(184,150,90,.5);
            object-fit: cover; display: block; flex-shrink: 0;
        }
        .modal-cli-header .avatar-lg-letra {
            width: 64px; height: 64px; border-radius: 50%;
            background: linear-gradient(135deg, var(--dourado), var(--dourado-lt));
            display: flex; align-items: center; justify-content: center;
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.8rem; font-weight: 500; color: white;
            border: 3px solid rgba(184,150,90,.5);
            flex-shrink: 0;
        }
        .modal-cli-header-txt h3 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.4rem; font-weight: 400; color: #f7f3ee;
            margin-bottom: 4px;
        }
        .modal-cli-header-txt span {
            font-size: 11px; color: rgba(247,243,238,.55);
            text-transform: uppercase; letter-spacing: 1px;
        }
        .modal-cli-fechar {
            position: absolute; top: 14px; right: 16px;
            background: none; border: none; cursor: pointer;
            color: rgba(247,243,238,.6); font-size: 22px;
            line-height: 1; transition: var(--transition);
        }
        .modal-cli-fechar:hover { color: white; }
        .modal-cli-body { padding: 24px 30px 30px; }
        .detalhe-linha {
            display: flex; align-items: center; gap: 14px;
            padding: 13px 0;
            border-bottom: 1px solid #f0ede8;
        }
        .detalhe-linha:last-of-type { border-bottom: none; }
        .detalhe-icone {
            width: 34px; height: 34px; border-radius: 50%;
            background: rgba(184,150,90,.1);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .detalhe-icone svg { width: 15px; height: 15px; stroke: var(--dourado); fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
        .detalhe-txt label { display: block; font-size: 10px; text-transform: uppercase; letter-spacing: .8px; color: var(--dourado); font-weight: 600; margin-bottom: 2px; }
        .detalhe-txt span { font-size: 13.5px; color: var(--grafite); font-family: 'DM Sans', sans-serif; }
        .modal-cli-footer {
            padding: 16px 30px 22px;
            display: flex; gap: 10px; flex-wrap: wrap;
            border-top: 1px solid #f0ede8;
        }
        .btn-modal-wpp {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 10px 18px; background: var(--wa); color: white;
            border-radius: var(--radius); text-decoration: none;
            font-size: 12px; font-weight: 500;
            text-transform: uppercase; letter-spacing: .8px;
            transition: var(--transition);
        }
        .btn-modal-wpp:hover { background: #1da851; }
        .btn-modal-wpp svg { width:16px; height:16px; fill:white; }
        .btn-modal-fechar {
            flex:1; padding:10px 18px; background:var(--bege); color:var(--grafite);
            border:1px solid #ddd; border-radius:var(--radius);
            font-family:'DM Sans',sans-serif; font-size:12px;
            text-transform:uppercase; letter-spacing:.8px;
            cursor:pointer; transition:var(--transition);
        }
        .btn-modal-fechar:hover { background:#eee; }

        /* ── MODAL EXCLUIR CLIENTE ── */
        .modal-del-cli-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.55); z-index:1100; align-items:center; justify-content:center; backdrop-filter:blur(3px); }
        .modal-del-cli-overlay.aberto { display:flex; }
    </style>
</head>
<body>

<!-- ── SIDEBAR ── -->
<div class="sidebar">
    <div class="sidebar-logo">ATELIER</div>

    <a href="dashboard.php" class="<?= ($secao=='agendamentos')?'active':'' ?>">
        <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        Agendamentos
    </a>
    <a href="dashboard.php?secao=catalogo" class="<?= ($secao=='catalogo')?'active':'' ?>">
        <svg viewBox="0 0 24 24"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
        Catálogo / Estoque
    </a>
    <a href="dashboard.php?secao=clientes" class="<?= ($secao=='clientes')?'active':'' ?>">
        <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        Clientes
    </a>
    
    <?php /* botão desativado if ($secao === 'catalogo'): ?>
    <a href="cadastrar_roupa.php" class="nova-peca">+ Nova Peça</a>
    <?php endif; */ ?>
                   
    <div class="sidebar-spacer"></div>

    <a href="../logout.php" class="sair">
        <svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
        Sair
    </a>
</div>

<!-- ── MAIN ── -->
<div class="main-content">

    <div class="page-header">
        <h1>
            <?php
                if ($secao==='clientes')  echo 'Gestão de Clientes';
                elseif ($secao==='catalogo') echo 'Catálogo de Produtos';
                else echo 'Controle de Agendamentos';
            ?>
        </h1>
        <a href="../logout.php" class="btn-logout">Encerrar Sessão</a>
    </div>

    <!-- Alertas -->
    <?php if (isset($_GET['status'])): ?>
        <?php if ($_GET['status']==='cadastrado'): ?>
        <div class="alerta alerta-sucesso">✓ Nova peça cadastrada com sucesso!</div>
        <?php elseif ($_GET['status']==='editado'): ?>
        <div class="alerta alerta-sucesso">✓ Peça atualizada com sucesso!</div>
        <?php elseif ($_GET['status']==='excluido'): ?>
        <div class="alerta alerta-excluido">✕ Peça excluída do catálogo.</div>
        <?php elseif ($_GET['status']==='cli_excluido'): ?>
        <div class="alerta alerta-excluido">✕ Cliente removido do sistema.</div>
        <?php endif; ?>
    <?php endif; ?>

    <!-- ════════════════════════════════════════════
         SEÇÃO CLIENTES
    ════════════════════════════════════════════ -->
    <?php if ($secao === 'clientes'): ?>

    <div class="card-tabela">

        <!-- Toolbar: contagem + busca -->
        <div class="clientes-toolbar">
            <p class="contagem">
                <strong id="contagemVisivel"><?= count($dados) ?></strong>
                de <?= count($dados) ?> cliente(s) registrado(s)
            </p>
            <div class="busca-wrap">
                <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" id="campoBusca" placeholder="Buscar por nome ou CPF…" oninput="filtrarClientes(this.value)" autocomplete="off">
            </div>
        </div>

        <table id="tabelaClientes">
            <thead>
                <tr>
                    <th style="width:52px;">Avatar</th>
                    <th>Nome do Cliente</th>
                    <th>CPF</th>
                    <th>Telefone</th>
                    <th style="text-align:center;">Gerenciar</th>
                </tr>
            </thead>
            <tbody id="tbodyClientes">
                <?php foreach ($dados as $c):
                    $temFoto    = !empty($c['foto_perfil']);
                    $fotoSrc    = '../uploads/fotos_perfil/' . htmlspecialchars($c['foto_perfil'] ?? '');
                    $inicial    = strtoupper(mb_substr($c['nome'], 0, 1));
                    $cpfFmt     = preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', preg_replace('/\D/', '', $c['cpf']));
                    $telFmt     = $c['telefone'] ?? '';
                    $temTel     = !empty($telFmt);
                    $telNum     = preg_replace('/\D/', '', $telFmt);
                    $wppLink    = 'https://wa.me/55' . $telNum;
                    $desde      = !empty($c['criado_em']) ? date('M/Y', strtotime($c['criado_em'])) : '';

                    // Dados para o modal (escapados para JS)
                    $jNome   = htmlspecialchars(addslashes($c['nome']));
                    $jCpf    = htmlspecialchars(addslashes($cpfFmt));
                    $jTel    = htmlspecialchars(addslashes($telFmt ?: 'Não informado'));
                    $jDesde  = htmlspecialchars(addslashes($desde));
                    $jFoto   = $temFoto ? htmlspecialchars(addslashes($fotoSrc)) : '';
                    $jWpp    = $temTel  ? htmlspecialchars(addslashes($wppLink)) : '';
                    $jId     = intval($c['id']);
                ?>
                <tr class="linha-cliente"
                    data-busca="<?= strtolower(htmlspecialchars($c['nome'])) ?> <?= preg_replace('/\D/','',$c['cpf']) ?>">

                    <!-- Avatar -->
                    <td>
                        <?php if ($temFoto): ?>
                            <img src="<?= $fotoSrc ?>" alt="<?= htmlspecialchars($c['nome']) ?>" class="cli-avatar">
                        <?php else: ?>
                            <div class="cli-avatar-letra"><?= $inicial ?></div>
                        <?php endif; ?>
                    </td>

                    <!-- Nome + data de cadastro -->
                    <td>
                        <div class="cli-info" style="display:block;">
                            <div class="cli-nome"><?= htmlspecialchars($c['nome']) ?></div>
                            <?php if ($desde): ?>
                            <div class="cli-desde">Cliente desde <?= $desde ?></div>
                            <?php endif; ?>
                        </div>
                    </td>

                    <!-- CPF -->
                    <td><span class="cpf-badge"><?= $cpfFmt ?></span></td>

                    <!-- Telefone -->
                    <td>
                        <?php if ($temTel): ?>
                            <span class="tel-text"><?= htmlspecialchars($telFmt) ?></span>
                        <?php else: ?>
                            <span class="tel-sem">Não informado</span>
                        <?php endif; ?>
                    </td>

                    <!-- Ações -->
                    <td>
                        <div class="acoes-cli">

                            <!-- Ver detalhes -->
                            <button class="btn-icon btn-ver"
                                title="Ver detalhes"
                                onclick="abrirDetalhes('<?= $jNome ?>','<?= $jCpf ?>','<?= $jTel ?>','<?= $jDesde ?>','<?= $jFoto ?>','<?= $jWpp ?>')">
                                <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>

                            <!-- WhatsApp -->
                            <?php if ($temTel): ?>
                            <a class="btn-icon btn-wpp"
                               href="<?= $wppLink ?>"
                               target="_blank"
                               title="Enviar mensagem no WhatsApp">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
                                </svg>
                            </a>
                            <?php else: ?>
                            <button class="btn-icon btn-wpp" disabled title="Sem telefone cadastrado" style="opacity:.35;cursor:not-allowed;">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
                                </svg>
                            </button>
                            <?php endif; ?>

                            <!-- Excluir -->
                            <button class="btn-icon btn-del-cli"
                                title="Excluir cliente"
                                onclick="abrirExcluirCliente(<?= $jId ?>, '<?= $jNome ?>')">
                                <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                            </button>

                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Mensagem sem resultado -->
        <div class="sem-resultado" id="semResultado">
            <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <p>Nenhum cliente encontrado para "<span id="termoBusca"></span>"</p>
        </div>

    </div><!-- /card-tabela clientes -->

    <!-- ── MODAL: Ver Detalhes do Cliente ── -->
    <div class="modal-cli-overlay" id="modalDetalhes">
        <div class="modal-cli">
            <div class="modal-cli-header">
                <div id="detAvatar"></div>
                <div class="modal-cli-header-txt">
                    <h3 id="detNome">—</h3>
                    <span id="detDesde">—</span>
                </div>
                <button class="modal-cli-fechar" onclick="fecharDetalhes()">×</button>
            </div>
            <div class="modal-cli-body">
                <div class="detalhe-linha">
                    <div class="detalhe-icone">
                        <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M8 2v4M16 2v4M3 10h18"/></svg>
                    </div>
                    <div class="detalhe-txt">
                        <label>CPF Registrado</label>
                        <span id="detCpf">—</span>
                    </div>
                </div>
                <div class="detalhe-linha">
                    <div class="detalhe-icone">
                        <svg viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.38 2 2 0 0 1 3.6 1h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.56a16 16 0 0 0 6 6l.95-.95a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    </div>
                    <div class="detalhe-txt">
                        <label>Telefone</label>
                        <span id="detTel">—</span>
                    </div>
                </div>
                <div class="detalhe-linha">
                    <div class="detalhe-icone">
                        <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                    <div class="detalhe-txt">
                        <label>Cliente desde</label>
                        <span id="detDesdeLong">—</span>
                    </div>
                </div>
            </div>
            <div class="modal-cli-footer">
                <a id="detWppLink" href="#" target="_blank" class="btn-modal-wpp" style="display:none;">
                    <svg viewBox="0 0 448 512" xmlns="http://www.w3.org/2000/svg"><path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"/></svg>
                    Abrir WhatsApp
                </a>
                <button class="btn-modal-fechar" onclick="fecharDetalhes()">Fechar</button>
            </div>
        </div>
    </div>

    <!-- ── MODAL: Excluir Cliente ── -->
    <div class="modal-del-cli-overlay" id="modalExcluirCliente">
        <div class="modal-box">
            <div class="modal-icone">🗑️</div>
            <h2>Excluir Cliente</h2>
            <p>Você está prestes a remover permanentemente o cliente<br><strong id="nomeClienteExcluir"></strong>.<br><br>Todos os agendamentos deste cliente também serão excluídos.</p>
            <div class="modal-botoes">
                <button class="modal-btn-cancelar" onclick="fecharExcluirCliente()">Cancelar</button>
                <a id="linkExcluirCliente" href="#" class="modal-btn-confirmar">Sim, Excluir</a>
            </div>
        </div>
    </div>

    <?php endif; // fim seção clientes ?>


    <!-- ════════════════════════════════════════════
         SEÇÃO CATÁLOGO
    ════════════════════════════════════════════ -->
    <?php if ($secao === 'catalogo'): ?>
    <div class="catalogo-toolbar">
        <p style="font-size:13px;color:var(--cinza);"><?= count($dados) ?> peça(s) cadastrada(s)</p>
        <a href="cadastrar_roupa.php" class="btn-nova-peca">+ Cadastrar Nova Peça</a>
    </div>
    <div class="card-tabela">
        <table>
            <thead><tr>
                <th style="width:75px;">Foto</th>
                <th>Peça / Modelo</th>
                <th>Categoria</th>
                <th style="text-align:center;">Status</th>
                <th style="text-align:center;min-width:280px;">Ações</th>
            </tr></thead>
            <tbody>
                <?php foreach ($dados as $r): ?>
                <tr>
                    <td>
                        <img src="../img/<?= htmlspecialchars($r['imagem_url']) ?>"
                             alt="Foto" style="width:58px;height:58px;object-fit:cover;border-radius:4px;border:1px solid #ddd;display:block;">
                    </td>
                    <td>
                        <strong><?= htmlspecialchars($r['nome']) ?></strong>
                        <?php if (!empty($r['modelo'])): ?>
                        <br><small style="color:#888;font-size:12px;"><?= htmlspecialchars($r['modelo']) ?></small>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($r['categoria']) ?></td>
                    <td style="text-align:center;">
                        <?php if ($r['status_estoque']==='disponivel'): ?>
                            <span class="status status-concluido">Disponível</span>
                        <?php else: ?>
                            <span class="status status-pendente">Indisponível</span>
                        <?php endif; ?>
                    </td>
                    <td style="text-align:center;">
                        <div class="acoes-catalogo">
                            <a href="acoes_catalogo.php?id=<?= $r['id'] ?>&acao=toggle" class="btn-toggle">⇄ Status</a>
                            <a href="editar_roupa.php?id=<?= $r['id'] ?>" class="btn-editar">✎ Editar</a>
                            <button class="btn-excluir-dash"
                                onclick="abrirModalExcluir(<?= $r['id'] ?>, '<?= htmlspecialchars(addslashes($r['nome'])) ?>')">
                                ✕ Excluir
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>


    <!-- ════════════════════════════════════════════
         SEÇÃO AGENDAMENTOS
    ════════════════════════════════════════════ -->
    <?php if ($secao === 'agendamentos'): ?>
    <div class="card-tabela">
        <table>
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
                    <td><?= date('d/m/Y', strtotime($a['data_agendamento'])) ?> às <?= substr($a['horario'],0,5) ?></td>
                    <td><?= htmlspecialchars($a['roupa_nome']) ?></td>
                    <td style="text-align:center;">
                        <span class="status status-<?= strtolower(trim($a['status']??'pendente')) ?>">
                            <?= htmlspecialchars($a['status']??'PENDENTE') ?>
                        </span>
                    </td>
                    <td style="text-align:center;">
                        <a href="acoes_agendamento.php?id=<?= $a['id'] ?>&acao=concluir" class="btn-acao btn-check" title="Concluir">✓</a>
                        <a href="acoes_agendamento.php?id=<?= $a['id'] ?>&acao=excluir"  class="btn-acao btn-del"   title="Excluir">✕</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

</div><!-- /main-content -->


<!-- MODAL EXCLUSÃO DE PEÇA (catálogo) -->
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
// ── BUSCA DE CLIENTES ─────────────────────────────────────────────────────
function filtrarClientes(termo) {
    const t     = termo.toLowerCase().replace(/\D/g, '') || termo.toLowerCase();
    const linhas = document.querySelectorAll('#tbodyClientes .linha-cliente');
    let visiveis = 0;

    linhas.forEach(tr => {
        const busca = tr.dataset.busca; // "nome cpfdigitos"
        const match = busca.includes(termo.toLowerCase()) || busca.includes(t);
        tr.classList.toggle('oculto', !match);
        if (match) visiveis++;
    });

    document.getElementById('contagemVisivel').textContent = visiveis;
    const semRes = document.getElementById('semResultado');
    semRes.style.display = visiveis === 0 ? 'block' : 'none';
    if (visiveis === 0) document.getElementById('termoBusca').textContent = termo;
}

// ── MODAL DETALHES CLIENTE ────────────────────────────────────────────────
function abrirDetalhes(nome, cpf, tel, desde, foto, wpp) {
    document.getElementById('detNome').textContent     = nome;
    document.getElementById('detCpf').textContent      = cpf;
    document.getElementById('detTel').textContent      = tel;
    document.getElementById('detDesdeLong').textContent = desde ? 'Desde ' + desde : 'Não informado';
    document.getElementById('detDesde').textContent    = desde ? 'Cliente desde ' + desde : '';

    // Avatar no modal
    const av = document.getElementById('detAvatar');
    if (foto) {
        av.innerHTML = `<img src="${foto}" class="avatar-lg" alt="${nome}" style="width:64px;height:64px;border-radius:50%;object-fit:cover;border:3px solid rgba(184,150,90,.5);">`;
    } else {
        const inicial = nome.charAt(0).toUpperCase();
        av.innerHTML = `<div class="avatar-lg-letra">${inicial}</div>`;
    }

    // Botão WhatsApp
    const wppBtn = document.getElementById('detWppLink');
    if (wpp) {
        wppBtn.href = wpp;
        wppBtn.style.display = 'inline-flex';
    } else {
        wppBtn.style.display = 'none';
    }

    document.getElementById('modalDetalhes').classList.add('aberto');
    document.body.style.overflow = 'hidden';
}
function fecharDetalhes() {
    document.getElementById('modalDetalhes').classList.remove('aberto');
    document.body.style.overflow = '';
}
document.getElementById('modalDetalhes')?.addEventListener('click', function(e) {
    if (e.target === this) fecharDetalhes();
});

// ── MODAL EXCLUIR CLIENTE ────────────────────────────────────────────────
function abrirExcluirCliente(id, nome) {
    document.getElementById('nomeClienteExcluir').textContent = '"' + nome + '"';
    document.getElementById('linkExcluirCliente').href = 'acoes_cliente.php?id=' + id + '&acao=excluir';
    document.getElementById('modalExcluirCliente').classList.add('aberto');
    document.body.style.overflow = 'hidden';
}
function fecharExcluirCliente() {
    document.getElementById('modalExcluirCliente').classList.remove('aberto');
    document.body.style.overflow = '';
}
document.getElementById('modalExcluirCliente')?.addEventListener('click', function(e) {
    if (e.target === this) fecharExcluirCliente();
});

// ── MODAL EXCLUIR PEÇA (catálogo) ────────────────────────────────────────
function abrirModalExcluir(id, nome) {
    document.getElementById('modalNomePeca').textContent = '"' + nome + '"';
    document.getElementById('linkConfirmarExclusao').href = 'acoes_catalogo.php?id=' + id + '&acao=excluir';
    document.getElementById('modalExcluir').classList.add('aberto');
}
function fecharModal() {
    document.getElementById('modalExcluir').classList.remove('aberto');
}
document.getElementById('modalExcluir')?.addEventListener('click', function(e) {
    if (e.target === this) fecharModal();
});
</script>

</body>
</html>