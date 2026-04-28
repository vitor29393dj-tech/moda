<?php
session_start();
require_once '../config/database.php';

$seuCpfAdmin = '71590928563';

if (!isset($_SESSION['logado']) || $_SESSION['usuario_cpf'] !== $seuCpfAdmin) {
    header('Location: ../index.php');
    exit;
}

$pdo  = Database::getInstance()->getConnection();
$erro = '';
$id   = intval($_GET['id'] ?? 0);

if (!$id) {
    header('Location: dashboard.php?secao=catalogo');
    exit;
}

// Busca a peça atual no banco
$stmt = $pdo->prepare("SELECT * FROM roupas WHERE id = :id");
$stmt->execute(['id' => $id]);
$peca = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$peca) {
    header('Location: dashboard.php?secao=catalogo');
    exit;
}

// ── PROCESSA O FORMULÁRIO DE EDIÇÃO ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['_acao'] ?? '') === 'editar') {
    $nome      = trim($_POST['nome'] ?? '');
    $categoria = trim($_POST['categoria'] ?? '');
    $modelo    = trim($_POST['modelo'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $preco     = trim($_POST['preco'] ?? '0.00');
    $status    = $_POST['status'] ?? 'disponivel';

    if (empty($nome) || empty($categoria) || empty($modelo)) {
        $erro = 'Preencha todos os campos obrigatórios.';
    } else {
        $novaImagem = $peca['imagem_url']; // mantém a imagem atual por padrão

        // Verifica se enviou nova imagem
        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
            $arquivo = $_FILES['imagem'];
            $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'webp'];
            $extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));

            if (!in_array($extensao, $extensoesPermitidas)) {
                $erro = 'Formato de imagem inválido. Use JPG, PNG ou WEBP.';
            } elseif ($arquivo['size'] > 5 * 1024 * 1024) {
                $erro = 'A imagem não pode ultrapassar 5MB.';
            } else {
                $nomeArquivo    = uniqid('peca_') . '.' . $extensao;
                $diretorio      = '../img/';
                if (!is_dir($diretorio)) mkdir($diretorio, 0755, true);
                $caminhoFinal   = $diretorio . $nomeArquivo;

                if (move_uploaded_file($arquivo['tmp_name'], $caminhoFinal)) {
                    // Remove a imagem antiga do servidor (se existir)
                    $imagemAntiga = $diretorio . $peca['imagem_url'];
                    if (file_exists($imagemAntiga)) unlink($imagemAntiga);
                    $novaImagem = $nomeArquivo;
                } else {
                    $erro = 'Falha ao fazer upload da imagem.';
                }
            }
        }

        if (!$erro) {
            try {
                $sql = "UPDATE roupas 
                        SET nome = :nome, categoria = :categoria, modelo = :modelo,
                            descricao = :descricao, preco = :preco,
                            imagem_url = :imagem_url, status_estoque = :status
                        WHERE id = :id";
                $upd = $pdo->prepare($sql);
                $upd->execute([
                    'nome'       => $nome,
                    'categoria'  => $categoria,
                    'modelo'     => $modelo,
                    'descricao'  => $descricao,
                    'preco'      => str_replace(',', '.', $preco),
                    'imagem_url' => $novaImagem,
                    'status'     => $status,
                    'id'         => $id,
                ]);

                header('Location: dashboard.php?secao=catalogo&status=editado');
                exit;
            } catch (PDOException $e) {
                $erro = 'Erro ao atualizar no banco: ' . $e->getMessage();
            }
        }

        // Atualiza o array local para repopular o form corretamente após erro
        if ($erro) {
            $peca = array_merge($peca, [
                'nome'      => $nome,
                'categoria' => $categoria,
                'modelo'    => $modelo,
                'descricao' => $descricao,
                'preco'     => $preco,
                'status_estoque' => $status,
            ]);
        }
    }
}

$categorias = ['Vestido', 'Blusa', 'Saia', 'Calça', 'Conjunto', 'Acessório', 'Traje Sob Medida', 'Outro'];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atelier — Editar Peça</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --bege-claro: #F7F3EE;
            --bege-medio: #EDE7DC;
            --dourado: #B8965A;
            --preto-luxo: #121212;
            --cinza-escuro: #2C2B29;
            --cinza-medio: #6B6860;
            --branco: #FFFFFF;
            --erro: #8B2635;
            --sucesso: #2E5D3A;
            --perigo: #7B1D1D;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'DM Sans', sans-serif; background-color: var(--bege-claro); display: flex; min-height: 100vh; }

        /* SIDEBAR */
        .sidebar { width: 250px; height: 100vh; background-color: var(--preto-luxo); color: var(--bege-claro); padding: 30px 20px; position: fixed; top: 0; left: 0; }
        .sidebar h2 { font-family: 'Cormorant Garamond', serif; letter-spacing: 6px; font-size: 18px; border-bottom: 1px solid var(--dourado); padding-bottom: 15px; text-align: center; color: var(--dourado); margin-bottom: 30px; }
        .sidebar a { display: block; color: var(--bege-claro); text-decoration: none; padding: 13px 12px; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; transition: 0.3s; border-radius: 3px; margin-bottom: 4px; }
        .sidebar a:hover, .sidebar a.active { background-color: var(--dourado); color: var(--preto-luxo); font-weight: 500; }
        .sidebar a.destaque { margin-top: 20px; border: 1px solid var(--dourado); color: var(--dourado); text-align: center; }
        .sidebar a.destaque:hover { background-color: var(--dourado); color: var(--preto-luxo); }

        /* CONTEÚDO */
        .main-content { margin-left: 250px; padding: 50px 70px; width: calc(100% - 250px); }

        .page-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 45px; padding-bottom: 20px; border-bottom: 1px solid #DDD; }
        .page-header h1 { font-family: 'Cormorant Garamond', serif; font-size: 34px; font-weight: 500; color: var(--cinza-escuro); letter-spacing: 1px; }
        .page-header p { font-size: 13px; color: var(--cinza-medio); margin-top: 4px; }
        .btn-voltar { text-decoration: none; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; color: var(--cinza-medio); border-bottom: 1px solid transparent; transition: 0.3s; }
        .btn-voltar:hover { color: var(--dourado); border-color: var(--dourado); }

        .alerta { padding: 15px 20px; border-radius: 3px; margin-bottom: 30px; font-size: 14px; display: flex; align-items: center; gap: 10px; }
        .alerta-erro { background: #FDF2F3; border-left: 3px solid var(--erro); color: var(--erro); }

        /* FORM */
        .form-card { background: var(--branco); border-radius: 4px; box-shadow: 0 6px 30px rgba(0,0,0,0.06); overflow: hidden; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; }

        /* Painel upload */
        .upload-panel { background: var(--bege-medio); padding: 50px 40px; display: flex; flex-direction: column; align-items: center; justify-content: center; border-right: 1px solid #E2DDD6; }
        .upload-zone { width: 100%; max-width: 300px; aspect-ratio: 3/4; border: 2px dashed #C5BDB3; border-radius: 4px; display: flex; flex-direction: column; align-items: center; justify-content: center; cursor: pointer; transition: all 0.3s; position: relative; overflow: hidden; background: var(--branco); }
        .upload-zone:hover, .upload-zone.dragover { border-color: var(--dourado); box-shadow: 0 0 0 4px rgba(184,150,90,0.1); }
        .upload-zone input[type="file"] { position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%; }
        .upload-icone { width: 44px; height: 44px; margin-bottom: 14px; opacity: 0.35; }
        .upload-texto-principal { font-size: 13px; font-weight: 500; color: var(--cinza-escuro); margin-bottom: 5px; }
        .upload-texto-sub { font-size: 11px; color: var(--cinza-medio); text-align: center; line-height: 1.6; }

        /* Preview da imagem atual */
        .imagem-atual { position: absolute; inset: 0; }
        .imagem-atual img { width: 100%; height: 100%; object-fit: cover; }
        .imagem-atual-overlay { position: absolute; inset: 0; background: rgba(0,0,0,0.45); display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 6px; opacity: 0; transition: 0.3s; }
        .upload-zone:hover .imagem-atual-overlay { opacity: 1; }
        .imagem-atual-overlay span { color: white; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; }
        .imagem-atual-overlay svg { stroke: white; width: 28px; height: 28px; }

        /* Preview novo */
        .upload-preview { display: none; position: absolute; inset: 0; }
        .upload-preview img { width: 100%; height: 100%; object-fit: cover; }
        .upload-preview-overlay { position: absolute; inset: 0; background: rgba(0,0,0,0.4); display: flex; align-items: center; justify-content: center; opacity: 0; transition: 0.3s; }
        .upload-preview:hover .upload-preview-overlay { opacity: 1; }
        .upload-preview-overlay span { color: white; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; }

        .upload-dica { margin-top: 14px; font-size: 11px; color: var(--cinza-medio); text-align: center; line-height: 1.8; }
        .badge-imagem { margin-top: 10px; background: rgba(184,150,90,0.12); color: var(--dourado); font-size: 11px; padding: 4px 12px; border-radius: 20px; border: 1px solid rgba(184,150,90,0.3); }

        /* Painel dados */
        .dados-panel { padding: 45px 45px; }
        .form-section-title { font-family: 'Cormorant Garamond', serif; font-size: 20px; font-weight: 500; color: var(--cinza-escuro); letter-spacing: 1px; margin-bottom: 28px; padding-bottom: 12px; border-bottom: 1px solid var(--bege-medio); }
        .form-grupo { margin-bottom: 22px; }
        .form-grupo label { display: block; font-size: 11px; text-transform: uppercase; letter-spacing: 1.5px; color: var(--cinza-medio); margin-bottom: 8px; font-weight: 500; }
        .form-grupo label span.obrigatorio { color: var(--dourado); margin-left: 2px; }
        .form-grupo input[type="text"], .form-grupo select, .form-grupo textarea { width: 100%; padding: 13px 15px; border: 1px solid #E0DAD2; background: #FDFCFA; border-radius: 2px; font-family: 'DM Sans', sans-serif; font-size: 14px; color: var(--cinza-escuro); outline: none; transition: border-color 0.3s, box-shadow 0.3s; appearance: none; }
        .form-grupo input[type="text"]:focus, .form-grupo select:focus, .form-grupo textarea:focus { border-color: var(--dourado); box-shadow: 0 0 0 3px rgba(184,150,90,0.08); }
        .form-grupo textarea { resize: vertical; min-height: 80px; }
        .select-wrapper { position: relative; }
        .select-wrapper::after { content: '▾'; position: absolute; right: 14px; top: 50%; transform: translateY(-50%); color: var(--cinza-medio); pointer-events: none; font-size: 12px; }

        /* Radio status */
        .radio-grupo { display: flex; gap: 12px; }
        .radio-opcao { flex: 1; }
        .radio-opcao input[type="radio"] { display: none; }
        .radio-opcao label { display: flex; align-items: center; justify-content: center; gap: 8px; padding: 11px; border: 1px solid #E0DAD2; border-radius: 2px; cursor: pointer; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; transition: all 0.3s; background: #FDFCFA; }
        .radio-opcao input[type="radio"]:checked + label { border-color: var(--dourado); background: rgba(184,150,90,0.06); color: var(--dourado); font-weight: 500; }
        .dot { width: 8px; height: 8px; border-radius: 50%; }
        .dot-disponivel { background: #2E7D32; }
        .dot-indisponivel { background: #C62828; }

        /* Botões de ação */
        .divider { height: 1px; background: var(--bege-medio); margin: 24px 0; }
        .botoes-form { display: flex; gap: 12px; }
        .btn-salvar { flex: 1; padding: 15px; background: var(--cinza-escuro); color: var(--bege-claro); border: none; font-family: 'DM Sans', sans-serif; font-size: 12px; text-transform: uppercase; letter-spacing: 2px; font-weight: 500; cursor: pointer; transition: all 0.3s; border-radius: 2px; }
        .btn-salvar:hover { background: var(--dourado); color: var(--branco); }

        /* Zona de exclusão */
        .zona-perigo { margin-top: 40px; padding: 25px 30px; border: 1px solid #F5C6CB; border-radius: 4px; background: #FFF8F8; }
        .zona-perigo h3 { font-size: 13px; text-transform: uppercase; letter-spacing: 1px; color: var(--perigo); margin-bottom: 8px; font-weight: 500; }
        .zona-perigo p { font-size: 13px; color: #666; margin-bottom: 18px; line-height: 1.6; }
        .btn-excluir { display: inline-block; padding: 11px 24px; background: transparent; color: var(--perigo); border: 1px solid #F5C6CB; border-radius: 2px; font-family: 'DM Sans', sans-serif; font-size: 12px; text-transform: uppercase; letter-spacing: 1.5px; cursor: pointer; transition: all 0.3s; }
        .btn-excluir:hover { background: var(--perigo); color: white; border-color: var(--perigo); }

        /* Modal de confirmação de exclusão */
        .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.55); z-index: 1000; align-items: center; justify-content: center; }
        .modal-overlay.aberto { display: flex; }
        .modal-box { background: var(--branco); border-radius: 4px; padding: 45px 40px; max-width: 420px; width: 90%; text-align: center; box-shadow: 0 20px 60px rgba(0,0,0,0.2); }
        .modal-icone { width: 52px; height: 52px; background: #FFF0F0; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; }
        .modal-icone svg { stroke: var(--perigo); width: 26px; height: 26px; }
        .modal-box h2 { font-family: 'Cormorant Garamond', serif; font-size: 24px; font-weight: 500; color: var(--cinza-escuro); margin-bottom: 12px; }
        .modal-box p { font-size: 14px; color: var(--cinza-medio); line-height: 1.7; margin-bottom: 30px; }
        .modal-box p strong { color: var(--cinza-escuro); }
        .modal-botoes { display: flex; gap: 12px; }
        .modal-btn-cancelar { flex: 1; padding: 13px; background: var(--bege-claro); color: var(--cinza-escuro); border: 1px solid #DDD; border-radius: 2px; font-family: 'DM Sans', sans-serif; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; cursor: pointer; transition: 0.3s; }
        .modal-btn-cancelar:hover { background: #EEE; }
        .modal-btn-confirmar { flex: 1; padding: 13px; background: var(--perigo); color: white; border: none; border-radius: 2px; font-family: 'DM Sans', sans-serif; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; cursor: pointer; transition: 0.3s; }
        .modal-btn-confirmar:hover { background: #5C0000; }
    </style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h2>ATELIER</h2>
    <a href="dashboard.php">Agendamentos</a>
    <a href="dashboard.php?secao=catalogo" class="active">Catálogo / Estoque</a>
    <a href="dashboard.php?secao=clientes">Clientes</a>
    <a href="cadastrar_roupa.php" class="destaque">+ Nova Peça</a>
    <a href="../logout.php" style="position: absolute; bottom: 30px; left: 20px; right: 20px;">Sair</a>
</div>

<!-- CONTEÚDO -->
<div class="main-content">

    <div class="page-header">
        <div>
            <h1>Editar Peça</h1>
            <p>Atualize as informações de <strong><?= htmlspecialchars($peca['nome']) ?></strong></p>
        </div>
        <a href="dashboard.php?secao=catalogo" class="btn-voltar">← Voltar ao Catálogo</a>
    </div>

    <?php if ($erro): ?>
    <div class="alerta alerta-erro"><span>⚠</span> <?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>

    <div class="form-card">
        <form action="editar_roupa.php?id=<?= $id ?>" method="POST" enctype="multipart/form-data" id="formEditar">
            <input type="hidden" name="_acao" value="editar">

            <div class="form-grid">

                <!-- PAINEL ESQUERDO: Imagem -->
                <div class="upload-panel">
                    <div class="upload-zone" id="uploadZone">
                        <input type="file" name="imagem" id="inputImagem" accept="image/jpeg,image/png,image/webp">

                        <!-- Imagem atual do banco -->
                        <div class="imagem-atual" id="imagemAtual">
                            <img src="../img/<?= htmlspecialchars($peca['imagem_url']) ?>" alt="Foto atual">
                            <div class="imagem-atual-overlay">
                                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.5"><path d="M3 9a2 2 0 012-2h.172a2 2 0 001.414-.586l.828-.828A2 2 0 018.828 5h6.344a2 2 0 011.414.586l.828.828A2 2 0 0018.828 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><circle cx="12" cy="13" r="3"/></svg>
                                <span>Trocar Foto</span>
                            </div>
                        </div>

                        <!-- Preview da nova imagem (aparece ao selecionar) -->
                        <div class="upload-preview" id="uploadPreview">
                            <img id="previewImg" src="" alt="Nova foto">
                            <div class="upload-preview-overlay"><span>Trocar Foto</span></div>
                        </div>
                    </div>

                    <p class="upload-dica">
                        Deixe em branco para manter a foto atual<br>
                        JPG, PNG ou WEBP · Máx. 5MB
                    </p>
                    <span class="badge-imagem" id="badgeImagem" style="display:none;">✓ Nova foto selecionada</span>
                </div>

                <!-- PAINEL DIREITO: Dados -->
                <div class="dados-panel">
                    <p class="form-section-title">Informações da Peça</p>

                    <div class="form-grupo">
                        <label>Nome da Peça <span class="obrigatorio">*</span></label>
                        <input type="text" name="nome" placeholder="Ex: Vestido Midi Plissado" value="<?= htmlspecialchars($peca['nome']) ?>" required>
                    </div>

                    <div class="form-grupo">
                        <label>Categoria <span class="obrigatorio">*</span></label>
                        <div class="select-wrapper">
                            <select name="categoria" required>
                                <?php foreach ($categorias as $cat): ?>
                                <option value="<?= $cat ?>" <?= $peca['categoria'] === $cat ? 'selected' : '' ?>><?= $cat ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-grupo">
                        <label>Peça / Modelo <span class="obrigatorio">*</span></label>
                        <input type="text" name="modelo" placeholder="Ex: Corte A-line, Manga Longa" value="<?= htmlspecialchars($peca['modelo'] ?? '') ?>" required>
                    </div>

                    <div class="form-grupo">
                        <label>Preço (R$)</label>
                        <input type="text" name="preco" placeholder="0,00" value="<?= htmlspecialchars(number_format((float)($peca['preco'] ?? 0), 2, ',', '.')) ?>">
                    </div>

                    <div class="form-grupo">
                        <label>Descrição da Peça</label>
                        <textarea name="descricao" rows="3" placeholder="Detalhes sobre o tecido, caimento..."><?= htmlspecialchars($peca['descricao'] ?? '') ?></textarea>
                    </div>

                    <div class="divider"></div>

                    <div class="form-grupo">
                        <label>Disponibilidade</label>
                        <div class="radio-grupo">
                            <div class="radio-opcao">
                                <input type="radio" name="status" id="r_disponivel" value="disponivel" <?= $peca['status_estoque'] === 'disponivel' ? 'checked' : '' ?>>
                                <label for="r_disponivel"><span class="dot dot-disponivel"></span> Disponível</label>
                            </div>
                            <div class="radio-opcao">
                                <input type="radio" name="status" id="r_indisponivel" value="indisponivel" <?= $peca['status_estoque'] === 'indisponivel' ? 'checked' : '' ?>>
                                <label for="r_indisponivel"><span class="dot dot-indisponivel"></span> Indisponível</label>
                            </div>
                        </div>
                    </div>

                    <div class="botoes-form">
                        <button type="submit" class="btn-salvar">Salvar Alterações</button>
                    </div>

                </div>
            </div>
        </form>
    </div>

    <!-- ZONA DE PERIGO: Exclusão -->
    <div class="zona-perigo">
        <h3>⚠ Zona de Perigo</h3>
        <p>
            Ao excluir esta peça, ela será <strong>removida permanentemente</strong> do catálogo e do banco de dados.
            Agendamentos que referenciam esta peça podem ser afetados. Esta ação não pode ser desfeita.
        </p>
        <button class="btn-excluir" onclick="abrirModal()">Excluir esta Peça</button>
    </div>

</div>

<!-- MODAL DE CONFIRMAÇÃO DE EXCLUSÃO -->
<div class="modal-overlay" id="modalExcluir">
    <div class="modal-box">
        <div class="modal-icone">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="1.5">
                <polyline points="3 6 5 6 21 6"/>
                <path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/>
                <path d="M10 11v6M14 11v6"/>
                <path d="M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2"/>
            </svg>
        </div>
        <h2>Confirmar Exclusão</h2>
        <p>
            Você está prestes a excluir permanentemente a peça<br>
            <strong>"<?= htmlspecialchars($peca['nome']) ?>"</strong>.<br><br>
            Tem certeza que deseja continuar?
        </p>
        <div class="modal-botoes">
            <button class="modal-btn-cancelar" onclick="fecharModal()">Cancelar</button>
            <form action="acoes_catalogo.php" method="GET" style="flex: 1;">
                <input type="hidden" name="id" value="<?= $id ?>">
                <input type="hidden" name="acao" value="excluir">
                <button type="submit" class="modal-btn-confirmar" style="width:100%;">Sim, Excluir</button>
            </form>
        </div>
    </div>
</div>

<script>
    // Upload / preview
    const input      = document.getElementById('inputImagem');
    const preview    = document.getElementById('uploadPreview');
    const imagemAtual = document.getElementById('imagemAtual');
    const previewImg = document.getElementById('previewImg');
    const zona       = document.getElementById('uploadZone');
    const badge      = document.getElementById('badgeImagem');

    input.addEventListener('change', function () {
        if (this.files[0]) mostrarPreview(this.files[0]);
    });

    zona.addEventListener('dragover', (e) => { e.preventDefault(); zona.classList.add('dragover'); });
    zona.addEventListener('dragleave', () => zona.classList.remove('dragover'));
    zona.addEventListener('drop', (e) => {
        e.preventDefault();
        zona.classList.remove('dragover');
        const file = e.dataTransfer.files[0];
        if (file && file.type.startsWith('image/')) {
            const dt = new DataTransfer();
            dt.items.add(file);
            input.files = dt.files;
            mostrarPreview(file);
        }
    });

    function mostrarPreview(file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            previewImg.src = e.target.result;
            imagemAtual.style.display = 'none';
            preview.style.display = 'block';
            badge.style.display = 'inline-block';
        };
        reader.readAsDataURL(file);
    }

    // Modal
    function abrirModal() {
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