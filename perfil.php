<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atelier — Meu Perfil</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;1,300;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --cream: #F7F3EE;
            --warm-white: #FDFAF7;
            --charcoal: #2C2B29;
            --graphite: #4A4845;
            --gold: #B8965A;
            --gold-light: #D4AF7A;
            --blush: #E8D5CC;
            --blush-dark: #C9A99A;
            --success: #5A8A6A;
            --error: #A85555;
            --perigo: #8B2635;
            --font-display: 'Cormorant Garamond', serif;
            --font-body: 'DM Sans', sans-serif;
            --radius: 3px;
            --shadow: 0 2px 24px rgba(44,43,41,.08);
            --shadow-lg: 0 8px 48px rgba(44,43,41,.14);
            --transition: .25s cubic-bezier(.4,0,.2,1);
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }
        body { font-family: var(--font-body); background: var(--cream); color: var(--charcoal); font-size: 15px; line-height: 1.6; min-height: 100vh; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: var(--cream); }
        ::-webkit-scrollbar-thumb { background: var(--blush-dark); border-radius: 3px; }
        h1, h2, h3 { font-family: var(--font-display); font-weight: 400; letter-spacing: .02em; }

        /* ── HEADER ── */
        header { position: sticky; top: 0; z-index: 100; background: rgba(247,243,238,.92); backdrop-filter: blur(12px); border-bottom: 1px solid rgba(184,150,90,.18); padding: 0 clamp(16px,5vw,64px); }
        .nav-inner { max-width: 1320px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between; height: 68px; }
        .logo { font-family: var(--font-display); font-size: 1.7rem; font-weight: 300; letter-spacing: .12em; color: var(--charcoal); text-decoration: none; }
        .logo span { color: var(--gold); font-style: italic; }
        nav { display: flex; gap: 4px; align-items: center; }
        .nav-btn { font-family: var(--font-body); font-size: .78rem; font-weight: 500; letter-spacing: .08em; text-transform: uppercase; padding: 8px 18px; border: 1px solid transparent; border-radius: var(--radius); background: transparent; color: var(--graphite); cursor: pointer; transition: var(--transition); text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
        .nav-btn:hover { color: var(--gold); border-color: rgba(184,150,90,.3); }
        .nav-btn.active { color: var(--gold); border-color: var(--gold); background: rgba(184,150,90,.06); }
        .nav-btn.sair { color: #c0392b; }
        .nav-btn.sair:hover { border-color: #c0392b; }

        /* Mini avatar no nav */
        .nav-avatar { width: 28px; height: 28px; border-radius: 50%; object-fit: cover; border: 1.5px solid var(--gold); }
        .nav-avatar-letra { width: 28px; height: 28px; border-radius: 50%; background: var(--gold); display: inline-flex; align-items: center; justify-content: center; font-family: var(--font-display); font-size: .95rem; color: white; font-weight: 500; border: 1.5px solid var(--gold); }

        /* ── HERO ── */
        .perfil-hero { background: linear-gradient(135deg, var(--charcoal) 0%, #3d3b38 100%); padding: clamp(40px,7vw,80px) clamp(16px,5vw,64px); position: relative; overflow: hidden; }
        .perfil-hero::before { content: ''; position: absolute; inset: 0; background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23B8965A' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E"); }
        .perfil-hero-inner { max-width: 1320px; margin: 0 auto; position: relative; display: flex; align-items: center; gap: 32px; flex-wrap: wrap; }

        /* ── AVATAR GRANDE (hero) ── */
        .avatar-wrapper {
            position: relative;
            width: 110px; height: 110px;
            flex-shrink: 0;
        }
        .avatar-img {
            width: 110px; height: 110px; border-radius: 50%;
            object-fit: cover;
            border: 3px solid rgba(184,150,90,.5);
            box-shadow: 0 4px 24px rgba(0,0,0,.35);
            display: block;
        }
        .avatar-letra {
            width: 110px; height: 110px; border-radius: 50%;
            background: linear-gradient(135deg, var(--gold), var(--gold-light));
            display: flex; align-items: center; justify-content: center;
            font-family: var(--font-display); font-size: 3rem; font-weight: 500;
            color: var(--warm-white);
            border: 3px solid rgba(184,150,90,.5);
            box-shadow: 0 4px 24px rgba(184,150,90,.35);
        }

        /* Botão câmera sobre avatar */
        .btn-camera {
            position: absolute; bottom: 2px; right: 2px;
            width: 34px; height: 34px; border-radius: 50%;
            background: var(--gold); border: 2px solid #fff;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: var(--transition);
            box-shadow: 0 2px 8px rgba(0,0,0,.25);
        }
        .btn-camera:hover { background: var(--gold-light); transform: scale(1.08); }
        .btn-camera svg { width: 16px; height: 16px; stroke: white; fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
        #inputFoto { display: none; }

        .perfil-hero h1 { font-size: clamp(1.8rem,4vw,3rem); font-weight: 300; color: var(--cream); line-height: 1.1; }
        .perfil-hero h1 em { color: var(--gold-light); font-style: italic; }
        .perfil-hero p { font-size: .88rem; color: rgba(247,243,238,.6); margin-top: 6px; }

        /* Barra de progresso do upload */
        .upload-progress-bar {
            position: absolute; bottom: 0; left: 0; right: 0;
            height: 3px; background: rgba(255,255,255,.15); border-radius: 0 0 50% 50%;
            overflow: hidden; display: none;
        }
        .upload-progress-fill { height: 100%; background: var(--gold-light); width: 0%; transition: width .3s ease; }

        /* ── LAYOUT ── */
        .perfil-body { max-width: 1320px; margin: 0 auto; padding: clamp(28px,5vw,52px) clamp(16px,5vw,64px); display: grid; grid-template-columns: 1fr 1fr; gap: 28px; }
        @media (max-width: 820px) { .perfil-body { grid-template-columns: 1fr; } }

        /* ── CARD ── */
        .card-perfil { background: var(--warm-white); border-radius: 4px; box-shadow: var(--shadow); overflow: hidden; }
        .card-perfil-header { padding: 20px 28px 16px; border-bottom: 1px solid rgba(184,150,90,.15); display: flex; align-items: center; gap: 10px; }
        .card-perfil-header .icone { font-size: 1.1rem; }
        .card-perfil-header h2 { font-size: 1.25rem; font-weight: 400; color: var(--charcoal); }
        .card-perfil-body { padding: 22px 28px 28px; }

        /* ── FOTO NO CARD ── */
        .foto-card-bloco {
            display: flex; align-items: center; gap: 20px;
            padding: 18px 0 22px;
            border-bottom: 1px solid rgba(44,43,41,.06);
            margin-bottom: 4px;
        }
        .foto-card-avatar { position: relative; width: 72px; height: 72px; flex-shrink: 0; }
        .foto-card-avatar .avatar-img-sm { width: 72px; height: 72px; border-radius: 50%; object-fit: cover; border: 2px solid rgba(184,150,90,.35); display: block; }
        .foto-card-avatar .avatar-letra-sm { width: 72px; height: 72px; border-radius: 50%; background: linear-gradient(135deg, var(--gold), var(--gold-light)); display: flex; align-items: center; justify-content: center; font-family: var(--font-display); font-size: 1.8rem; color: white; font-weight: 500; }
        .foto-card-info { flex: 1; }
        .foto-card-info p { font-size: .82rem; color: var(--graphite); margin-bottom: 10px; line-height: 1.5; }
        .foto-card-acoes { display: flex; gap: 8px; flex-wrap: wrap; }

        /* ── DADOS ── */
        .dado-linha { display: flex; align-items: flex-start; gap: 14px; padding: 13px 0; border-bottom: 1px solid rgba(44,43,41,.06); }
        .dado-linha:last-of-type { border-bottom: none; }
        .dado-icone { width: 34px; height: 34px; border-radius: 50%; background: rgba(184,150,90,.1); display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: .88rem; }
        .dado-info { flex: 1; }
        .dado-label { font-size: .7rem; text-transform: uppercase; letter-spacing: .1em; color: var(--gold); font-weight: 500; margin-bottom: 2px; }
        .dado-valor { font-size: .97rem; color: var(--charcoal); }
        .dado-valor.muted { color: var(--graphite); font-size: .88rem; }

        /* Skeleton */
        .skeleton { background: linear-gradient(90deg,#ede8e2 25%,#f5f1eb 50%,#ede8e2 75%); background-size: 200% 100%; animation: shimmer 1.4s infinite; border-radius: 4px; }
        @keyframes shimmer { to { background-position: -200% 0; } }
        .sk-line { height: 15px; margin-bottom: 8px; }
        .sk-sm { width: 38%; } .sk-md { width: 62%; } .sk-lg { width: 82%; }

        /* ── FORM EDIÇÃO ── */
        .form-grupo { margin-bottom: 16px; }
        .form-grupo label { display: block; font-size: .7rem; text-transform: uppercase; letter-spacing: .1em; color: var(--gold); font-weight: 500; margin-bottom: 6px; }
        .form-grupo input { width: 100%; padding: 11px 14px; border: 1px solid rgba(44,43,41,.15); border-radius: var(--radius); background: var(--cream); font-family: var(--font-body); font-size: .93rem; color: var(--charcoal); outline: none; transition: border-color var(--transition); }
        .form-grupo input:focus { border-color: var(--gold); box-shadow: 0 0 0 3px rgba(184,150,90,.08); }
        .form-grupo input:disabled { opacity: .45; cursor: not-allowed; background: #eee; }
        .form-dica { font-size: .73rem; color: var(--graphite); margin-top: 4px; }

        /* ── BOTÕES ── */
        .btn-row { display: flex; gap: 8px; margin-top: 8px; flex-wrap: wrap; }
        .btn { display: inline-flex; align-items: center; gap: 6px; font-family: var(--font-body); font-size: .75rem; font-weight: 500; letter-spacing: .08em; text-transform: uppercase; padding: 10px 20px; border-radius: var(--radius); cursor: pointer; transition: var(--transition); border: 1px solid transparent; white-space: nowrap; }
        .btn-ouro { background: var(--gold); color: var(--warm-white); border-color: var(--gold); }
        .btn-ouro:hover { background: var(--gold-light); transform: translateY(-1px); box-shadow: 0 4px 14px rgba(184,150,90,.3); }
        .btn-outline { background: transparent; color: var(--charcoal); border-color: rgba(44,43,41,.25); }
        .btn-outline:hover { border-color: var(--gold); color: var(--gold); }
        .btn-perigo { background: transparent; color: var(--perigo); border-color: rgba(139,38,53,.3); }
        .btn-perigo:hover { background: var(--perigo); color: white; border-color: var(--perigo); }
        .btn-sm { padding: 7px 14px; font-size: .7rem; }
        .btn:disabled { opacity: .5; cursor: not-allowed; transform: none !important; }

        /* ── AGENDAMENTOS ── */
        .ag-status { display: inline-block; padding: 3px 9px; border-radius: 20px; font-size: .65rem; font-weight: 500; text-transform: uppercase; letter-spacing: .06em; }
        .ag-status.pendente { background: #fff3e0; color: #e65100; border: 1px solid #ffcc80; }
        .ag-status.concluido { background: #e8f5e9; color: #2e7d32; border: 1px solid #a5d6a7; }
        .ag-status.cancelado { background: #fce4ec; color: #880e4f; border: 1px solid #f48fb1; }
        .ag-item { padding: 13px 0; border-bottom: 1px solid rgba(44,43,41,.06); display: flex; align-items: center; justify-content: space-between; gap: 12px; }
        .ag-item:last-child { border-bottom: none; }
        .ag-info h4 { font-family: var(--font-display); font-size: 1rem; font-weight: 400; margin-bottom: 2px; }
        .ag-info p { font-size: .78rem; color: var(--graphite); }
        .ag-vazio { text-align: center; padding: 28px 0; color: var(--graphite); font-size: .88rem; }
        .ag-vazio .icone-vazio { font-size: 2.2rem; margin-bottom: 10px; opacity: .4; display: block; }

        /* ── ZONA DE PERIGO ── */
        .zona-perigo { margin-top: 26px; padding: 20px 26px; border: 1px solid rgba(139,38,53,.2); border-radius: 4px; background: #fff8f8; }
        .zona-perigo h3 { font-family: var(--font-body); font-size: .75rem; text-transform: uppercase; letter-spacing: .1em; color: var(--perigo); margin-bottom: 6px; font-weight: 600; }
        .zona-perigo p { font-size: .83rem; color: var(--graphite); margin-bottom: 14px; line-height: 1.6; }

        /* ── MODAL EXCLUIR ── */
        .modal-overlay-ex { display: none; position: fixed; inset: 0; z-index: 500; background: rgba(44,43,41,.55); backdrop-filter: blur(4px); align-items: center; justify-content: center; padding: 16px; }
        .modal-overlay-ex.open { display: flex; }
        .modal-ex { background: var(--warm-white); border-radius: 4px; max-width: 420px; width: 100%; padding: 38px 34px; box-shadow: var(--shadow-lg); animation: modalIn .28s cubic-bezier(.4,0,.2,1); text-align: center; }
        @keyframes modalIn { from { opacity:0; transform:scale(.96) translateY(12px); } to { opacity:1; transform:scale(1) translateY(0); } }
        .modal-ex .icone-perigo { font-size: 2.8rem; margin-bottom: 14px; display: block; }
        .modal-ex h2 { font-size: 1.6rem; margin-bottom: 10px; }
        .modal-ex p { font-size: .87rem; color: var(--graphite); line-height: 1.7; margin-bottom: 22px; }
        .modal-botoes { display: flex; gap: 10px; margin-top: 4px; }

        /* ── TOAST ── */
        .toast-container { position: fixed; bottom: 24px; right: 24px; z-index: 9999; display: flex; flex-direction: column; gap: 10px; }
        .toast { display: flex; align-items: center; gap: 10px; padding: 13px 18px; border-radius: var(--radius); background: var(--charcoal); color: var(--cream); font-size: .84rem; box-shadow: var(--shadow-lg); animation: toastIn .3s ease; min-width: 240px; max-width: 360px; }
        .toast.success { background: #1b4332; border-left: 3px solid #5A8A6A; }
        .toast.error { background: #4a0e0e; border-left: 3px solid #A85555; }
        @keyframes toastIn { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:translateY(0); } }

        /* Spinner */
        .spinner { display: inline-block; width: 13px; height: 13px; border: 2px solid rgba(255,255,255,.3); border-top-color: white; border-radius: 50%; animation: spin .6s linear infinite; flex-shrink: 0; }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* Anel de loading no avatar */
        .avatar-wrapper.carregando .avatar-img,
        .avatar-wrapper.carregando .avatar-letra { opacity: .5; }
        .avatar-wrapper.carregando::after {
            content: '';
            position: absolute; inset: 0;
            border-radius: 50%;
            border: 3px solid transparent;
            border-top-color: var(--gold-light);
            animation: spin .8s linear infinite;
        }
    </style>
</head>
<body>

<!-- HEADER -->
<header>
    <div class="nav-inner">
        <a href="index.php" class="logo">AT<span>EL</span>IER</a>
        <nav>
            <a href="index.php" class="nav-btn">Catálogo</a>
            <a href="index.php" class="nav-btn">Agendamento</a>
            <a href="perfil.php" class="nav-btn active">
                <span id="navAvatarWrap">
                    <span class="nav-avatar-letra" id="navLetra"><?= strtoupper(mb_substr($_SESSION['usuario_nome'], 0, 1)) ?></span>
                </span>
                Olá, <?= htmlspecialchars(strtoupper($_SESSION['usuario_nome'])) ?>
            </a>
            <button class="nav-btn sair" onclick="confirmarSair()">Sair</button>
        </nav>
    </div>
</header>

<!-- HERO -->
<div class="perfil-hero">
    <div class="perfil-hero-inner">

        <!-- Avatar clicável -->
        <div class="avatar-wrapper" id="avatarWrapper" title="Clique para trocar a foto">
            <span class="avatar-letra" id="avatarLetra"><?= strtoupper(mb_substr($_SESSION['usuario_nome'], 0, 1)) ?></span>
            <img class="avatar-img" id="avatarImg" src="" alt="Foto de perfil" style="display:none;" onclick="document.getElementById('inputFoto').click()">
            <label class="btn-camera" for="inputFoto" title="Alterar foto">
                <svg viewBox="0 0 24 24"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
            </label>
            <input type="file" id="inputFoto" accept="image/jpeg,image/png,image/webp" onchange="enviarFoto(this)">
            <div class="upload-progress-bar" id="progressBar"><div class="upload-progress-fill" id="progressFill"></div></div>
        </div>

        <div>
            <h1>Meu <em>Perfil</em></h1>
            <p id="heroSubtitulo">Clique na foto para atualizar sua imagem</p>
        </div>
    </div>
</div>

<!-- CORPO -->
<div class="perfil-body">

    <!-- COLUNA 1 -->
    <div>
        <div class="card-perfil">
            <div class="card-perfil-header">
                <span class="icone">👤</span>
                <h2>Seus Dados</h2>
            </div>
            <div class="card-perfil-body">

                <!-- Bloco da foto dentro do card -->
                <div class="foto-card-bloco">
                    <div class="foto-card-avatar">
                        <img class="avatar-img-sm" id="fotoCardImg" src="" alt="" style="display:none;">
                        <div class="avatar-letra-sm" id="fotoCardLetra"><?= strtoupper(mb_substr($_SESSION['usuario_nome'], 0, 1)) ?></div>
                    </div>
                    <div class="foto-card-info">
                        <p>JPG, PNG ou WEBP · Máx. 4 MB<br>A foto aparece no seu perfil e no cabeçalho do site.</p>
                        <div class="foto-card-acoes">
                            <label class="btn btn-ouro btn-sm" for="inputFoto2" style="cursor:pointer;">
                                📷 Trocar foto
                            </label>
                            <input type="file" id="inputFoto2" accept="image/jpeg,image/png,image/webp" style="display:none;" onchange="enviarFoto(this)">
                            <button class="btn btn-outline btn-sm" id="btnRemoverFoto" onclick="removerFoto()" style="display:none;">
                                ✕ Remover
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Skeleton -->
                <div id="skeletonDados">
                    <div class="sk-line skeleton sk-sm" style="margin:16px 0 8px;height:11px;"></div>
                    <div class="sk-line skeleton sk-lg"></div>
                    <div class="sk-line skeleton sk-md" style="margin:16px 0 8px;height:11px;"></div>
                    <div class="sk-line skeleton sk-lg"></div>
                    <div class="sk-line skeleton sk-md" style="margin:16px 0 8px;height:11px;"></div>
                    <div class="sk-line skeleton sk-lg"></div>
                </div>

                <!-- View -->
                <div id="viewDados" style="display:none;">
                    <div class="dado-linha">
                        <div class="dado-icone">✦</div>
                        <div class="dado-info">
                            <div class="dado-label">Nome Completo</div>
                            <div class="dado-valor" id="exNome">—</div>
                        </div>
                    </div>
                    <div class="dado-linha">
                        <div class="dado-icone">🪪</div>
                        <div class="dado-info">
                            <div class="dado-label">CPF</div>
                            <div class="dado-valor" id="exCpf">—</div>
                        </div>
                    </div>
                    <div class="dado-linha">
                        <div class="dado-icone">📱</div>
                        <div class="dado-info">
                            <div class="dado-label">Telefone</div>
                            <div class="dado-valor" id="exTel">—</div>
                        </div>
                    </div>
                    <div class="dado-linha">
                        <div class="dado-icone">📅</div>
                        <div class="dado-info">
                            <div class="dado-label">Cliente desde</div>
                            <div class="dado-valor muted" id="exCriado">—</div>
                        </div>
                    </div>
                    <div class="btn-row" style="margin-top:18px;">
                        <button class="btn btn-ouro" onclick="abrirEdicao()">✎ Editar dados</button>
                    </div>
                </div>

                <!-- Form edição -->
                <div id="formEdicao" style="display:none;">
                    <div class="form-grupo" style="margin-top:4px;">
                        <label>Nome Completo</label>
                        <input type="text" id="editNome" placeholder="Seu nome completo">
                    </div>
                    <div class="form-grupo">
                        <label>CPF <span style="color:var(--graphite);font-style:italic;text-transform:none;letter-spacing:0;font-size:.75rem;">(não pode ser alterado)</span></label>
                        <input type="text" id="editCpf" disabled>
                    </div>
                    <div class="form-grupo">
                        <label>Telefone</label>
                        <input type="text" id="editTel" placeholder="(00) 00000-0000">
                    </div>
                    <div class="form-grupo">
                        <label>Nova Senha <span style="color:var(--graphite);font-style:italic;text-transform:none;letter-spacing:0;font-size:.75rem;">(deixe em branco para manter)</span></label>
                        <input type="password" id="editSenha" placeholder="Mínimo 6 caracteres">
                    </div>
                    <div class="btn-row">
                        <button class="btn btn-ouro" id="btnSalvar" onclick="salvarEdicao()">Salvar alterações</button>
                        <button class="btn btn-outline" onclick="cancelarEdicao()">Cancelar</button>
                    </div>
                </div>

            </div>
        </div>

        <!-- Zona de perigo -->
        <div class="zona-perigo">
            <h3>⚠ Zona de Perigo</h3>
            <p>Ao excluir sua conta, todos os seus dados e agendamentos serão removidos <strong>permanentemente</strong>. Esta ação não pode ser desfeita.</p>
            <button class="btn btn-perigo" onclick="abrirModalExcluir()">✕ Excluir minha conta</button>
        </div>
    </div>

    <!-- COLUNA 2: Agendamentos -->
    <div class="card-perfil">
        <div class="card-perfil-header">
            <span class="icone">🗓</span>
            <h2>Meus Agendamentos</h2>
        </div>
        <div class="card-perfil-body" id="painelAgendamentos">
            <div id="skeletonAg">
                <div class="sk-line skeleton sk-lg" style="height:13px;margin-bottom:8px;"></div>
                <div class="sk-line skeleton sk-md" style="margin-bottom:22px;"></div>
                <div class="sk-line skeleton sk-lg" style="height:13px;margin-bottom:8px;"></div>
                <div class="sk-line skeleton sk-sm"></div>
            </div>
            <div id="listaAg" style="display:none;"></div>
        </div>
    </div>

</div>

<!-- MODAL EXCLUIR -->
<div class="modal-overlay-ex" id="modalExcluir">
    <div class="modal-ex">
        <span class="icone-perigo">🗑️</span>
        <h2>Excluir Conta</h2>
        <p>Esta ação é <strong>permanente e irreversível</strong>. Todos os seus dados, foto e agendamentos serão deletados.<br><br>Para confirmar, digite sua senha:</p>
        <div class="form-grupo" style="text-align:left;">
            <label>Sua Senha</label>
            <input type="password" id="senhaExcluir" placeholder="Digite sua senha">
        </div>
        <div class="modal-botoes">
            <button class="btn btn-outline" style="flex:1;justify-content:center;" onclick="fecharModalExcluir()">Cancelar</button>
            <button class="btn btn-perigo" style="flex:1;justify-content:center;" id="btnConfirmarExcluir" onclick="confirmarExclusao()">✕ Excluir conta</button>
        </div>
    </div>
</div>

<!-- Toasts -->
<div class="toast-container" id="toastContainer"></div>

<script>
// ── ESTADO ──────────────────────────────────────────────────────────────────
let dadosCliente = {};

// ── INIT ────────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', carregarPerfil);

// ── CARREGAR PERFIL ──────────────────────────────────────────────────────────
async function carregarPerfil() {
    try {
        const res  = await fetch('controllers/PerfilController.php?action=buscar');
        const data = await res.json();
        if (!data.success) { toast('Erro ao carregar perfil.', 'error'); return; }

        dadosCliente = data.cliente;

        // Dados
        document.getElementById('exNome').textContent   = data.cliente.nome;
        document.getElementById('exCpf').textContent    = formatarCpf(data.cliente.cpf);
        document.getElementById('exTel').textContent    = data.cliente.telefone || 'Não informado';
        document.getElementById('exCriado').textContent = formatarData(data.cliente.criado_em);

        document.getElementById('skeletonDados').style.display = 'none';
        document.getElementById('viewDados').style.display     = 'block';

        // Foto
        if (data.cliente.foto_perfil) {
            aplicarFoto('uploads/fotos_perfil/' + data.cliente.foto_perfil);
        } else {
            aplicarLetra(data.cliente.nome);
        }

        renderizarAgendamentos(data.agendamentos);

    } catch (e) {
        toast('Falha de conexão.', 'error');
    }
}

// ── FOTO DE PERFIL ───────────────────────────────────────────────────────────

// Aplica a foto em todos os lugares (hero, card, nav)
function aplicarFoto(url) {
    const src = url + (url.includes('?') ? '' : '?v=' + Date.now());

    // Hero
    const imgHero = document.getElementById('avatarImg');
    imgHero.src = src;
    imgHero.style.display = 'block';
    document.getElementById('avatarLetra').style.display = 'none';

    // Card
    const imgCard = document.getElementById('fotoCardImg');
    imgCard.src = src;
    imgCard.style.display = 'block';
    document.getElementById('fotoCardLetra').style.display = 'none';

    // Nav
    atualizarNavAvatar(src);

    // Botão remover visível
    document.getElementById('btnRemoverFoto').style.display = 'inline-flex';
}

function aplicarLetra(nome) {
    const inicial = (nome || '?').charAt(0).toUpperCase();

    // Hero
    document.getElementById('avatarImg').style.display = 'none';
    const heroLetra = document.getElementById('avatarLetra');
    heroLetra.style.display = 'flex';
    heroLetra.textContent = inicial;

    // Card
    document.getElementById('fotoCardImg').style.display = 'none';
    const cardLetra = document.getElementById('fotoCardLetra');
    cardLetra.style.display = 'flex';
    cardLetra.textContent = inicial;

    // Nav
    atualizarNavAvatar(null, inicial);

    document.getElementById('btnRemoverFoto').style.display = 'none';
}

function atualizarNavAvatar(url, inicial) {
    const wrap = document.getElementById('navAvatarWrap');
    if (url) {
        wrap.innerHTML = `<img class="nav-avatar" src="${url}" alt="Avatar">`;
    } else {
        wrap.innerHTML = `<span class="nav-avatar-letra">${inicial || '?'}</span>`;
    }
}

// Upload via input (hero ou card)
async function enviarFoto(input) {
    const arquivo = input.files[0];
    if (!arquivo) return;

    // Preview imediato antes mesmo de enviar
    const urlLocal = URL.createObjectURL(arquivo);
    aplicarFoto(urlLocal);

    // Loading no avatar do hero
    const wrapper = document.getElementById('avatarWrapper');
    wrapper.classList.add('carregando');

    // Barra de progresso
    const barEl   = document.getElementById('progressBar');
    const fillEl  = document.getElementById('progressFill');
    barEl.style.display = 'block';
    fillEl.style.width  = '0%';

    const formData = new FormData();
    formData.append('foto', arquivo);

    // XHR para ter progresso real
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'controllers/PerfilController.php?action=foto');

    xhr.upload.onprogress = (e) => {
        if (e.lengthComputable) {
            fillEl.style.width = Math.round((e.loaded / e.total) * 100) + '%';
        }
    };

    xhr.onload = () => {
        wrapper.classList.remove('carregando');
        barEl.style.display = 'none';
        fillEl.style.width  = '0%';
        input.value = ''; // limpa o input

        try {
            const data = JSON.parse(xhr.responseText);
            if (data.success) {
                aplicarFoto(data.foto_url);
                dadosCliente.foto_perfil = data.foto_url;
                toast('Foto de perfil atualizada! ✦', 'success');
            } else {
                aplicarLetra(dadosCliente.nome); // reverte o preview
                toast(data.error || 'Erro ao enviar foto.', 'error');
            }
        } catch (e) {
            aplicarLetra(dadosCliente.nome);
            toast('Erro inesperado.', 'error');
        }
    };

    xhr.onerror = () => {
        wrapper.classList.remove('carregando');
        barEl.style.display = 'none';
        aplicarLetra(dadosCliente.nome);
        toast('Falha de conexão.', 'error');
        input.value = '';
    };

    xhr.send(formData);
}

async function removerFoto() {
    if (!confirm('Remover sua foto de perfil?')) return;

    const btn = document.getElementById('btnRemoverFoto');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner"></span>';

    try {
        const res  = await fetch('controllers/PerfilController.php?action=remover_foto', { method: 'POST' });
        const data = await res.json();
        if (data.success) {
            dadosCliente.foto_perfil = null;
            aplicarLetra(dadosCliente.nome);
            toast('Foto removida.', 'success');
        } else {
            toast('Erro ao remover foto.', 'error');
        }
    } catch(e) {
        toast('Falha de conexão.', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '✕ Remover';
    }
}

// ── AGENDAMENTOS ─────────────────────────────────────────────────────────────
function renderizarAgendamentos(lista) {
    document.getElementById('skeletonAg').style.display = 'none';
    const el = document.getElementById('listaAg');
    el.style.display = 'block';

    if (!lista || lista.length === 0) {
        el.innerHTML = `
            <div class="ag-vazio">
                <span class="icone-vazio">🪡</span>
                <p>Você ainda não tem agendamentos.<br>
                <a href="index.php" style="color:var(--gold);text-decoration:none;font-weight:500;">Explorar o catálogo →</a></p>
            </div>`;
        return;
    }

    const meses = ['jan','fev','mar','abr','mai','jun','jul','ago','set','out','nov','dez'];
    el.innerHTML = lista.map(ag => {
        const dt  = ag.data_agendamento ? new Date(ag.data_agendamento + 'T00:00:00') : null;
        const dataStr = dt ? `${dt.getDate()} ${meses[dt.getMonth()]}. ${dt.getFullYear()}` : '—';
        const hora    = ag.horario ? ag.horario.slice(0, 5) : '';
        const status  = (ag.status || 'pendente').toLowerCase();
        return `
        <div class="ag-item">
            <div class="ag-info">
                <h4>${ag.roupa_nome}</h4>
                <p>${dataStr}${hora ? ' · ' + hora : ''}</p>
            </div>
            <span class="ag-status ${status}">${status}</span>
        </div>`;
    }).join('');

    if (lista.length >= 5) {
        el.innerHTML += `<p style="text-align:center;margin-top:16px;font-size:.8rem;color:var(--graphite);">Exibindo os 5 mais recentes.</p>`;
    }
}

// ── EDIÇÃO ───────────────────────────────────────────────────────────────────
function abrirEdicao() {
    document.getElementById('editNome').value  = dadosCliente.nome || '';
    document.getElementById('editCpf').value   = formatarCpf(dadosCliente.cpf);
    document.getElementById('editTel').value   = dadosCliente.telefone || '';
    document.getElementById('editSenha').value = '';
    document.getElementById('viewDados').style.display  = 'none';
    document.getElementById('formEdicao').style.display = 'block';
}
function cancelarEdicao() {
    document.getElementById('formEdicao').style.display = 'none';
    document.getElementById('viewDados').style.display  = 'block';
}

async function salvarEdicao() {
    const nome  = document.getElementById('editNome').value.trim();
    const tel   = document.getElementById('editTel').value.trim();
    const senha = document.getElementById('editSenha').value.trim();

    if (!nome || nome.length < 3) { toast('Nome deve ter pelo menos 3 caracteres.', 'error'); return; }
    if (!tel || tel.replace(/\D/g,'').length < 10) { toast('Telefone inválido.', 'error'); return; }
    if (senha && senha.length < 6) { toast('A nova senha deve ter pelo menos 6 caracteres.', 'error'); return; }

    const btn = document.getElementById('btnSalvar');
    btn.disabled = true; btn.innerHTML = '<span class="spinner"></span> Salvando…';

    try {
        const res  = await fetch('controllers/PerfilController.php?action=editar', {
            method: 'POST',
            headers: {'Content-Type':'application/json'},
            body: JSON.stringify({ nome, telefone: tel, senha }),
        });
        const data = await res.json();

        if (data.success) {
            dadosCliente.nome = data.novo_nome;
            dadosCliente.telefone = tel;
            document.getElementById('exNome').textContent = data.novo_nome;
            document.getElementById('exTel').textContent  = tel;
            // Atualiza inicial se não tiver foto
            if (!dadosCliente.foto_perfil) aplicarLetra(data.novo_nome);
            cancelarEdicao();
            toast('Dados atualizados com sucesso! ✦', 'success');
        } else {
            (data.erros || [data.error || 'Erro.']).forEach(e => toast(e, 'error'));
        }
    } catch(e) { toast('Falha de conexão.', 'error'); }
    finally { btn.disabled = false; btn.innerHTML = 'Salvar alterações'; }
}

// ── EXCLUIR CONTA ─────────────────────────────────────────────────────────────
function abrirModalExcluir() {
    document.getElementById('senhaExcluir').value = '';
    document.getElementById('modalExcluir').classList.add('open');
    document.body.style.overflow = 'hidden';
}
function fecharModalExcluir() {
    document.getElementById('modalExcluir').classList.remove('open');
    document.body.style.overflow = '';
}
document.getElementById('modalExcluir').addEventListener('click', function(e) {
    if (e.target === this) fecharModalExcluir();
});

async function confirmarExclusao() {
    const senha = document.getElementById('senhaExcluir').value.trim();
    if (!senha) { toast('Digite sua senha para confirmar.', 'error'); return; }

    const btn = document.getElementById('btnConfirmarExcluir');
    btn.disabled = true; btn.innerHTML = '<span class="spinner"></span> Excluindo…';

    try {
        const res  = await fetch('controllers/PerfilController.php?action=excluir', {
            method: 'POST',
            headers: {'Content-Type':'application/json'},
            body: JSON.stringify({ senha }),
        });
        const data = await res.json();
        if (data.success) {
            fecharModalExcluir();
            toast('Conta excluída. Até logo! 👋', 'success', 3000);
            setTimeout(() => window.location.href = 'index.php', 3000);
        } else {
            toast(data.error || 'Erro ao excluir conta.', 'error');
            btn.disabled = false; btn.innerHTML = '✕ Excluir conta';
        }
    } catch(e) { toast('Falha de conexão.', 'error'); btn.disabled = false; btn.innerHTML = '✕ Excluir conta'; }
}

// ── LOGOUT ───────────────────────────────────────────────────────────────────
function confirmarSair() {
    if (confirm('Tem certeza que deseja sair?')) window.location.href = 'logout.php';
}

// ── TOAST ────────────────────────────────────────────────────────────────────
function toast(msg, tipo = 'info', dur = 4000) {
    const t = document.createElement('div');
    t.className = `toast ${tipo}`;
    t.textContent = msg;
    document.getElementById('toastContainer').appendChild(t);
    setTimeout(() => { t.style.opacity='0'; t.style.transition='opacity .4s'; setTimeout(()=>t.remove(),400); }, dur);
}

// ── HELPERS ───────────────────────────────────────────────────────────────────
function formatarCpf(cpf) {
    if (!cpf) return '—';
    const c = cpf.replace(/\D/g,'');
    return c.length===11 ? c.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/,'$1.$2.$3-$4') : cpf;
}
function formatarData(str) {
    if (!str) return '—';
    return new Date(str).toLocaleDateString('pt-BR',{day:'2-digit',month:'long',year:'numeric'});
}
</script>

</body>
</html>