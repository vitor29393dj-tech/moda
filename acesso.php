<?php
$msg = $_GET['msg'] ?? '';
$tipo = $_GET['tipo'] ?? '';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Atelier — Acesso</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;600&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
  
  <style>
    /* RESET E BASE */
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    :root {
      --creme:       #f5f0e8;
      --dourado:     #b8960c;
      --dourado-claro: #d4af37;
      --preto:       #0d0d0d;
      --preto-suave: #1a1a1a;
      --cinza:       #6b6560;
    }

    body {
      font-family: 'DM Sans', sans-serif;
      background-color: var(--preto);
      color: var(--creme);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 40px 20px;
    }

    .wrapper {
      position: relative;
      z-index: 1;
      width: 100%;
      max-width: 450px;
    }

    .header {
      text-align: center;
      margin-bottom: 50px;
    }
    .logo-titulo {
      font-family: 'Cormorant Garamond', serif;
      font-size: 3.5rem;
      font-weight: 300;
      letter-spacing: 0.25em;
      text-transform: uppercase;
      margin-bottom: 10px;
    }
    .logo-subtitulo {
      font-size: 0.75rem;
      letter-spacing: 0.5em;
      color: var(--dourado);
      text-transform: uppercase;
      font-weight: 400;
    }

    .msg-feedback {
      margin-bottom: 30px;
      padding: 15px 25px;
      border-radius: 4px;
      font-size: 0.85rem;
      text-align: center;
    }
    .msg-feedback.erro { background: rgba(139,32,32,0.15); color: #e8a0a0; border: 1px solid #8b2020; }
    .msg-feedback.sucesso { background: rgba(26,74,46,0.15); color: #90c8a8; border: 1px solid #2a7a4e; }

    .auth-container {
      background: rgba(255,255,255,0.015);
      border: 1px solid rgba(184,150,12,0.1);
      padding: 60px 45px;
      border-radius: 4px;
      position: relative;
    }

    .form-panel { display: none; animation: fadeIn 0.6s ease forwards; }
    .form-panel.active { display: block; }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to   { opacity: 1; transform: translateY(0); }
    }

    .form-titulo {
      font-family: 'Cormorant Garamond', serif;
      font-size: 2.8rem;
      font-weight: 300;
      margin-bottom: 15px;
      line-height: 1.1;
      letter-spacing: -0.01em;
    }
    .form-descricao {
      font-size: 0.9rem;
      color: var(--cinza);
      margin-bottom: 45px;
      font-weight: 300;
      letter-spacing: 0.02em;
    }

    .campo { margin-bottom: 30px; text-align: left; position: relative; }
    .campo label {
      display: block; font-size: 0.7rem; letter-spacing: 0.2em;
      text-transform: uppercase; color: var(--dourado); margin-bottom: 8px;
    }
    .campo input {
      width: 100%; background: transparent; border: none;
      border-bottom: 1px solid rgba(214,201,176,0.3);
      color: var(--creme); font-size: 1rem; padding: 12px 0; outline: none;
      transition: border-color 0.3s ease;
    }
    .campo input:focus {
      border-bottom-color: var(--dourado-claro);
    }

    .btn-submit {
      width: 100%; padding: 18px; background: transparent;
      border: 1px solid var(--dourado); color: var(--dourado);
      text-transform: uppercase; letter-spacing: 0.35em;
      font-weight: 500; cursor: pointer; transition: 0.3s ease;
      margin-top: 15px;
    }
    .btn-submit:hover {
      background: var(--dourado); color: var(--preto);
      box-shadow: 0 5px 25px rgba(184,150,12,0.25);
    }

    .toggle-link {
      display: block; text-align: center; margin-top: 35px;
      font-size: 0.85rem; color: var(--cinza); cursor: pointer;
      font-weight: 300; letter-spacing: 0.03em;
    }
    .toggle-link span {
      color: var(--dourado); text-decoration: underline;
      transition: color 0.3s ease;
    }
    .toggle-link:hover span {
      color: var(--dourado-claro);
    }

    input:-webkit-autofill {
      -webkit-text-fill-color: var(--creme) !important;
      -webkit-box-shadow: 0 0 0px 1000px var(--preto) inset !important;
    }
  </style>
</head>
<body>

  <div class="wrapper">
    <header class="header">
      <h1 class="logo-titulo">Atelier</h1>
      <p class="logo-subtitulo">Haute Couture & Costura</p>
    </header>

    <?php if (!empty($msg)): ?>
      <?php
        $classe = ($tipo === 'erro') ? 'erro' : 'sucesso';
        $mensagens = [
          'login_invalido'     => 'CPF ou senha incorretos.',
          'cadastro_ok'        => 'Cadastro realizado com sucesso! Acesse sua conta.',
          'cpf_existente'      => 'Este CPF já possui um cadastro.',
          'campos_obrigatorios'=> 'Preencha todos os campos obrigatórios antes de continuar.',
        ];
        $texto = $mensagens[$msg] ?? htmlspecialchars($msg);
      ?>
      <div class="msg-feedback <?= $classe ?>"><?= $texto ?></div>
    <?php endif; ?>

    <div class="auth-container">
      <div id="panel-login" class="form-panel active">
        <h2 class="form-titulo">Acesse sua conta</h2>
        <p class="form-descricao">Entre para ver seus agendamentos.</p>
        <form action="controllers/AuthController.php" method="POST" novalidate>
          <input type="hidden" name="acao" value="login">
          <div class="campo">
            <label>CPF</label>
            <input type="text" name="cpf" id="login-cpf" placeholder="000.000.000-00" required>
          </div>
          <div class="campo">
            <label>Senha</label>
            <input type="password" name="senha" placeholder="••••••••" required>
          </div>
          <button type="submit" class="btn-submit">Entrar</button>
        </form>
        <a class="toggle-link" onclick="toggleForm('cadastro')">Primeira vez aqui? <span>Cadastre-se</span></a>
      </div>

      <div id="panel-cadastro" class="form-panel">
        <h2 class="form-titulo">Crie seu cadastro</h2>
        <p class="form-descricao">Registre-se para agendar seus serviços.</p>
        <form action="controllers/AuthController.php" method="POST" novalidate>
            <input type="hidden" name="acao" value="cadastro">
            
            <div class="campo">
                <label>Nome completo</label>
                <input type="text" name="nome" placeholder="Seu nome" required>
            </div>

            <div class="campo">
                <label>CPF</label>
                <input type="text" name="cpf" id="cad-cpf" placeholder="000.000.000-00" required>
            </div>

            <div class="campo">
                <label>Telefone</label>
                <input type="text" name="telefone" id="cad-telefone" placeholder="(00) 00000-0000" maxlength="15" required>
            </div>

            <div class="campo">
                <label>Senha</label>
                <input type="password" name="senha" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn-submit">Criar conta</button>
        </form>
        <a class="toggle-link" onclick="toggleForm('login')">Já tem conta? <span>Acesse aqui</span></a>
      </div>
    </div>
  </div>

  <script>
    function toggleForm(form) {
      document.getElementById('panel-login').classList.toggle('active', form === 'login');
      document.getElementById('panel-cadastro').classList.toggle('active', form === 'cadastro');
    }

    // MÁSCARA DE CPF
    function mascaraCPF(input) {
      input.addEventListener('input', function () {
        let v = this.value.replace(/\D/g, '').slice(0, 11);
        if (v.length > 9) v = v.replace(/(\d{3})(\d{3})(\d{3})(\d{1,2})/, '$1.$2.$3-$4');
        else if (v.length > 6) v = v.replace(/(\d{3})(\d{3})(\d{1,3})/, '$1.$2.$3');
        else if (v.length > 3) v = v.replace(/(\d{3})(\d{1,3})/, '$1.$2');
        this.value = v;
      });
    }

    // MÁSCARA DE TELEFONE (NOVA)
    function mascaraTelefone(input) {
      input.addEventListener('input', function () {
        let v = this.value.replace(/\D/g, ''); // Remove tudo que não é número
        v = v.slice(0, 11); // Limita a 11 números (DDD + 9 dígitos)

        if (v.length > 10) {
          // Formato (00) 00000-0000
          v = v.replace(/^(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
        } else if (v.length > 5) {
          // Formato (00) 0000-0000
          v = v.replace(/^(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
        } else if (v.length > 2) {
          // Formato (00) 0000
          v = v.replace(/^(\d{2})(\d{0,5})/, '($1) $2');
        } else if (v.length > 0) {
          // Formato (00
          v = v.replace(/^(\d{0,2})/, '($1');
        }
        this.value = v;
      });
    }

    mascaraCPF(document.getElementById('login-cpf'));
    mascaraCPF(document.getElementById('cad-cpf'));
    mascaraTelefone(document.getElementById('cad-telefone'));
  </script>

</body>
</html>