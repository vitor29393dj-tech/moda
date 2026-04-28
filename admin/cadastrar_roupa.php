<?php
session_start();
require_once '../config/database.php';

$seuCpfAdmin = '71590928563';

// Segurança: apenas admin acessa
if (!isset($_SESSION['logado']) || $_SESSION['usuario_cpf'] !== $seuCpfAdmin) {
    header('Location: ../index.php');
    exit;
}

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome      = trim($_POST['nome'] ?? '');
    $categoria = trim($_POST['categoria'] ?? '');
    $modelo    = trim($_POST['modelo'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $preco     = trim($_POST['preco'] ?? '0.00');
    $status    = $_POST['status'] ?? 'disponivel';

    // Validação dos campos
    if (empty($nome) || empty($categoria) || empty($modelo) || empty($preco)) {
        $erro = 'Preencha todos os campos obrigatórios.';
    } elseif (!isset($_FILES['imagem']) || $_FILES['imagem']['error'] !== UPLOAD_ERR_OK) {
        $erro = 'Selecione uma imagem para a peça.';
    } else {
        $arquivo = $_FILES['imagem'];
        $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'webp'];
        $extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));

        if (!in_array($extensao, $extensoesPermitidas)) {
            $erro = 'Formato de imagem inválido. Use JPG, PNG ou WEBP.';
        } elseif ($arquivo['size'] > 5 * 1024 * 1024) {
            $erro = 'A imagem não pode ultrapassar 5MB.';
        } else {
            // Gera nome único para evitar conflitos
            $nomeArquivo = uniqid('peca_') . '.' . $extensao;
            $diretorioDestino = '../img/';

            // Cria o diretório se não existir
            if (!is_dir($diretorioDestino)) {
                mkdir($diretorioDestino, 0755, true);
            }

            $caminhoFinal = $diretorioDestino . $nomeArquivo;

            if (move_uploaded_file($arquivo['tmp_name'], $caminhoFinal)) {
                try {
                    $pdo = Database::getInstance()->getConnection();

                    // Verifica se já existe uma peça com o mesmo nome e modelo para evitar duplicidade
                    $check = $pdo->prepare("SELECT id FROM roupas WHERE nome = :nome AND modelo = :modelo");
                    $check->execute(['nome' => $nome, 'modelo' => $modelo]);
                    if ($check->fetch()) {
                        unlink($caminhoFinal); // Remove a imagem que acabou de subir
                        $erro = 'Esta peça (Nome e Modelo) já está cadastrada no catálogo.';
                    } else {

                    $sql = "INSERT INTO roupas (nome, categoria, modelo, descricao, preco, imagem_url, status_estoque, ativo) 
                            VALUES (:nome, :categoria, :modelo, :descricao, :preco, :imagem_url, :status, 1)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([
                        'nome'       => $nome,
                        'categoria'  => $categoria,
                        'modelo'     => $modelo,
                        'descricao'  => $descricao,
                        'preco'      => str_replace(',', '.', $preco),
                        'imagem_url' => $nomeArquivo,
                        'status'     => $status,
                    ]);

                    // Redireciona para o catálogo com mensagem de sucesso para evitar reenvio (PRG Pattern)
                    header('Location: dashboard.php?secao=catalogo&status=cadastrado');
                    exit;
                    }

                } catch (PDOException $e) {
                    // Remove a imagem se o banco falhou
                    unlink($caminhoFinal);
                    $erro = 'Erro ao salvar no banco de dados: ' . $e->getMessage();
                }
            } else {
                $erro = 'Falha ao fazer upload da imagem. Verifique as permissões do diretório.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atelier — Cadastrar Nova Peça</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --bege-claro: #F7F3EE;
            --bege-medio: #EDE7DC;
            --dourado: #B8965A;
            --dourado-claro: #D4AF78;
            --preto-luxo: #121212;
            --cinza-escuro: #2C2B29;
            --cinza-medio: #6B6860;
            --branco: #FFFFFF;
            --erro: #8B2635;
            --sucesso: #2E5D3A;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DM Sans', sans-serif;
            background-color: var(--bege-claro);
            display: flex;
            min-height: 100vh;
        }

        /* ── SIDEBAR (idêntica ao dashboard) ── */
        .sidebar {
            width: 250px;
            height: 100vh;
            background-color: var(--preto-luxo);
            color: var(--bege-claro);
            padding: 30px 20px;
            position: fixed;
            top: 0; left: 0;
        }
        .sidebar h2 {
            font-family: 'Cormorant Garamond', serif;
            letter-spacing: 6px;
            font-size: 18px;
            border-bottom: 1px solid var(--dourado);
            padding-bottom: 15px;
            text-align: center;
            color: var(--dourado);
            margin-bottom: 30px;
        }
        .sidebar a {
            display: block;
            color: var(--bege-claro);
            text-decoration: none;
            padding: 13px 12px;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: 0.3s;
            border-radius: 3px;
            margin-bottom: 4px;
        }
        .sidebar a:hover, .sidebar a.active {
            background-color: var(--dourado);
            color: var(--preto-luxo);
            font-weight: 500;
        }
        .sidebar a.destaque {
            margin-top: 20px;
            border: 1px solid var(--dourado);
            color: var(--dourado);
            text-align: center;
        }
        .sidebar a.destaque:hover {
            background-color: var(--dourado);
            color: var(--preto-luxo);
        }

        /* ── CONTEÚDO PRINCIPAL ── */
        .main-content {
            margin-left: 250px;
            padding: 50px 70px;
            width: calc(100% - 250px);
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 45px;
            padding-bottom: 20px;
            border-bottom: 1px solid #DDD;
        }
        .page-header h1 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 34px;
            font-weight: 500;
            color: var(--cinza-escuro);
            letter-spacing: 1px;
        }
        .page-header p {
            font-size: 13px;
            color: var(--cinza-medio);
            margin-top: 4px;
        }
        .btn-voltar {
            text-decoration: none;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--cinza-medio);
            border-bottom: 1px solid transparent;
            transition: 0.3s;
        }
        .btn-voltar:hover { color: var(--dourado); border-color: var(--dourado); }

        /* ── ALERTAS ── */
        .alerta {
            padding: 15px 20px;
            border-radius: 3px;
            margin-bottom: 30px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .alerta-erro   { background: #FDF2F3; border-left: 3px solid var(--erro); color: var(--erro); }
        .alerta-sucesso { background: #F0F7F2; border-left: 3px solid var(--sucesso); color: var(--sucesso); }

        /* ── FORMULÁRIO ── */
        .form-card {
            background: var(--branco);
            border-radius: 4px;
            box-shadow: 0 6px 30px rgba(0,0,0,0.06);
            overflow: hidden;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0;
        }

        /* Painel de upload — esquerda */
        .upload-panel {
            background: var(--bege-medio);
            padding: 50px 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border-right: 1px solid #E2DDD6;
        }

        .upload-zone {
            width: 100%;
            max-width: 320px;
            aspect-ratio: 3/4;
            border: 2px dashed #C5BDB3;
            border-radius: 4px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
            background: var(--branco);
        }
        .upload-zone:hover, .upload-zone.dragover {
            border-color: var(--dourado);
            box-shadow: 0 0 0 4px rgba(184,150,90,0.1);
        }
        .upload-zone input[type="file"] {
            position: absolute;
            inset: 0;
            opacity: 0;
            cursor: pointer;
            width: 100%;
            height: 100%;
        }
        .upload-icone {
            width: 48px;
            height: 48px;
            margin-bottom: 16px;
            opacity: 0.35;
        }
        .upload-texto-principal {
            font-size: 14px;
            font-weight: 500;
            color: var(--cinza-escuro);
            margin-bottom: 6px;
        }
        .upload-texto-sub {
            font-size: 11px;
            color: var(--cinza-medio);
            text-align: center;
            line-height: 1.6;
        }
        .upload-preview {
            display: none;
            position: absolute;
            inset: 0;
        }
        .upload-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .upload-preview-overlay {
            position: absolute;
            inset: 0;
            background: rgba(0,0,0,0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: 0.3s;
        }
        .upload-preview:hover .upload-preview-overlay { opacity: 1; }
        .upload-preview-overlay span {
            color: white;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .upload-dica {
            margin-top: 16px;
            font-size: 11px;
            color: var(--cinza-medio);
            text-align: center;
            line-height: 1.8;
        }

        /* Painel de dados — direita */
        .dados-panel {
            padding: 50px 45px;
        }

        .form-section-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 20px;
            font-weight: 500;
            color: var(--cinza-escuro);
            letter-spacing: 1px;
            margin-bottom: 30px;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--bege-medio);
        }

        .form-grupo {
            margin-bottom: 24px;
        }
        .form-grupo label {
            display: block;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--cinza-medio);
            margin-bottom: 8px;
            font-weight: 500;
        }
        .form-grupo label span.obrigatorio {
            color: var(--dourado);
            margin-left: 2px;
        }

        .form-grupo input[type="text"],
        .form-grupo select {
            width: 100%;
            padding: 13px 15px;
            border: 1px solid #E0DAD2;
            background: #FDFCFA;
            border-radius: 2px;
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            color: var(--cinza-escuro);
            outline: none;
            transition: border-color 0.3s, box-shadow 0.3s;
            appearance: none;
        }
        .form-grupo input[type="text"]:focus,
        .form-grupo select:focus {
            border-color: var(--dourado);
            box-shadow: 0 0 0 3px rgba(184,150,90,0.08);
        }

        .select-wrapper {
            position: relative;
        }
        .select-wrapper::after {
            content: '▾';
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--cinza-medio);
            pointer-events: none;
            font-size: 12px;
        }

        /* Radio de status */
        .radio-grupo {
            display: flex;
            gap: 15px;
        }
        .radio-opcao {
            flex: 1;
        }
        .radio-opcao input[type="radio"] { display: none; }
        .radio-opcao label {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px;
            border: 1px solid #E0DAD2;
            border-radius: 2px;
            cursor: pointer;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s;
            background: #FDFCFA;
        }
        .radio-opcao input[type="radio"]:checked + label {
            border-color: var(--dourado);
            background: rgba(184,150,90,0.06);
            color: var(--dourado);
            font-weight: 500;
        }
        .radio-opcao label .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }
        .dot-disponivel { background: #2E7D32; }
        .dot-indisponivel { background: #C62828; }

        /* Botão submit */
        .btn-cadastrar {
            width: 100%;
            padding: 16px;
            background: var(--cinza-escuro);
            color: var(--bege-claro);
            border: none;
            font-family: 'DM Sans', sans-serif;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.4s;
            border-radius: 2px;
            margin-top: 10px;
        }
        .btn-cadastrar:hover {
            background: var(--dourado);
            color: var(--branco);
        }
        .btn-cadastrar:active { transform: scale(0.99); }

        /* Linha separadora */
        .divider {
            height: 1px;
            background: var(--bege-medio);
            margin: 28px 0;
        }
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
    <a href="../logout.php" style="margin-top: auto; position: absolute; bottom: 30px; left: 20px; right: 20px;">Sair</a>
</div>

<!-- CONTEÚDO -->
<div class="main-content">

    <div class="page-header">
        <div>
            <h1>Cadastrar Nova Peça</h1>
            <p>Adicione uma nova roupa ao catálogo do atelier</p>
        </div>
        <a href="dashboard.php?secao=catalogo" class="btn-voltar">← Voltar ao Catálogo</a>
    </div>

    <?php if ($erro): ?>
        <div class="alerta alerta-erro">
            <span>⚠</span> <?= htmlspecialchars($erro) ?>
        </div>
    <?php endif; ?>

    <?php if ($sucesso): ?>
        <div class="alerta alerta-sucesso">
            <span>✓</span> <?= htmlspecialchars($sucesso) ?>
            — <a href="dashboard.php?secao=catalogo" style="color: inherit; font-weight: 500;">Ver no catálogo</a>
            &nbsp;|&nbsp; <a href="cadastrar_roupa.php" style="color: inherit; font-weight: 500;">Cadastrar outra peça</a>
        </div>
    <?php endif; ?>

    <div class="form-card">
        <form action="cadastrar_roupa.php" method="POST" enctype="multipart/form-data" id="formCadastro">
            <div class="form-grid">

                <!-- PAINEL ESQUERDO: Upload da Imagem -->
                <div class="upload-panel">

                    <div class="upload-zone" id="uploadZone">
                        <input
                            type="file"
                            name="imagem"
                            id="inputImagem"
                            accept="image/jpeg,image/png,image/webp"
                            required
                        >

                        <!-- Estado padrão -->
                        <div id="uploadPlaceholder">
                            <svg class="upload-icone" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2">
                                <rect x="3" y="3" width="18" height="18" rx="2"/>
                                <circle cx="8.5" cy="8.5" r="1.5"/>
                                <polyline points="21,15 16,10 5,21"/>
                            </svg>
                            <p class="upload-texto-principal">Escolher Foto da Peça</p>
                            <p class="upload-texto-sub">Clique para abrir o<br>explorador de arquivos</p>
                        </div>

                        <!-- Preview após seleção -->
                        <div class="upload-preview" id="uploadPreview">
                            <img id="previewImg" src="" alt="Preview">
                            <div class="upload-preview-overlay">
                                <span>Trocar Foto</span>
                            </div>
                        </div>
                    </div>

                    <p class="upload-dica">
                        JPG, PNG ou WEBP<br>
                        Tamanho máximo: 5MB<br>
                        <strong>Proporção recomendada: 3×4</strong>
                    </p>

                </div>

                <!-- PAINEL DIREITO: Dados da Peça -->
                <div class="dados-panel">

                    <p class="form-section-title">Informações da Peça</p>

                    <div class="form-grupo">
                        <label>Nome da Peça <span class="obrigatorio">*</span></label>
                        <input
                            type="text"
                            name="nome"
                            placeholder="Ex: Vestido Midi Plissado"
                            value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>"
                            required
                        >
                    </div>

                    <div class="form-grupo">
                        <label>Categoria <span class="obrigatorio">*</span></label>
                        <div class="select-wrapper">
                            <select name="categoria" required>
                                <option value="" disabled <?= empty($_POST['categoria']) ? 'selected' : '' ?>>Selecione uma categoria</option>
                                <?php
                                $categorias = ['Vestido', 'Blusa', 'Saia', 'Calça', 'Conjunto', 'Acessório', 'Traje Sob Medida', 'Outro'];
                                foreach ($categorias as $cat):
                                    $sel = (($_POST['categoria'] ?? '') === $cat) ? 'selected' : '';
                                ?>
                                    <option value="<?= $cat ?>" <?= $sel ?>><?= $cat ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-grupo">
                        <label>Peça / Modelo <span class="obrigatorio">*</span></label>
                        <input
                            type="text"
                            name="modelo"
                            placeholder="Ex: Corte A-line, Manga Longa"
                            value="<?= htmlspecialchars($_POST['modelo'] ?? '') ?>"
                            required
                        >
                    </div>

                    <div class="form-grupo">
                        <label>Preço (R$) <span class="obrigatorio">*</span></label>
                        <input
                            type="text"
                            name="preco"
                            placeholder="0,00"
                            value="<?= htmlspecialchars($_POST['preco'] ?? '') ?>"
                            required
                        >
                    </div>

                    <div class="form-grupo">
                        <label>Descrição da Peça</label>
                        <textarea name="descricao" rows="3" 
                            style="width: 100%; padding: 13px; border: 1px solid #E0DAD2; border-radius: 2px; font-family: 'DM Sans', sans-serif;"
                            placeholder="Detalhes sobre o tecido, caimento..."><?= htmlspecialchars($_POST['descricao'] ?? '') ?></textarea>
                    </div>

                    <div class="divider"></div>

                    <div class="form-grupo">
                        <label>Disponibilidade Inicial</label>
                        <div class="radio-grupo">
                            <div class="radio-opcao">
                                <input
                                    type="radio"
                                    name="status"
                                    id="r_disponivel"
                                    value="disponivel"
                                    <?= (($_POST['status'] ?? 'disponivel') === 'disponivel') ? 'checked' : '' ?>
                                >
                                <label for="r_disponivel">
                                    <span class="dot dot-disponivel"></span>
                                    Disponível
                                </label>
                            </div>
                            <div class="radio-opcao">
                                <input
                                    type="radio"
                                    name="status"
                                    id="r_indisponivel"
                                    value="indisponivel"
                                    <?= (($_POST['status'] ?? '') === 'indisponivel') ? 'checked' : '' ?>
                                >
                                <label for="r_indisponivel">
                                    <span class="dot dot-indisponivel"></span>
                                    Indisponível
                                </label>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn-cadastrar">
                        Cadastrar Peça no Catálogo
                    </button>

                </div>
            </div>
        </form>
    </div>

</div>

<script>
    const input = document.getElementById('inputImagem');
    const preview = document.getElementById('uploadPreview');
    const placeholder = document.getElementById('uploadPlaceholder');
    const previewImg = document.getElementById('previewImg');
    const zona = document.getElementById('uploadZone');

    // Ao selecionar arquivo via explorador
    input.addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;
        mostrarPreview(file);
    });

    // Drag & Drop
    zona.addEventListener('dragover', (e) => {
        e.preventDefault();
        zona.classList.add('dragover');
    });
    zona.addEventListener('dragleave', () => zona.classList.remove('dragover'));
    zona.addEventListener('drop', (e) => {
        e.preventDefault();
        zona.classList.remove('dragover');
        const file = e.dataTransfer.files[0];
        if (file && file.type.startsWith('image/')) {
            // Atribui ao input para envio no form
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            input.files = dataTransfer.files;
            mostrarPreview(file);
        }
    });

    function mostrarPreview(file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            previewImg.src = e.target.result;
            placeholder.style.display = 'none';
            preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
</script>

</body>
</html>