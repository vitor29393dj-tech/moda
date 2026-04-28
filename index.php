<?php session_start(); ?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Atelier — Catálogo & Agendamentos</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link
    href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;1,300;1,400&family=DM+Sans:wght@300;400;500&display=swap"
    rel="stylesheet" />
  <style>
    /* ============================================================
   DESIGN SYSTEM — Atelier
   Estética: editorial de moda minimalista / luxo refinado
   Paleta: off-white creme · grafite · dourado discreto · blush
   ============================================================ */

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

      --font-display: 'Cormorant Garamond', serif;
      --font-body: 'DM Sans', sans-serif;

      --radius: 3px;
      --shadow: 0 2px 24px rgba(44, 43, 41, .08);
      --shadow-lg: 0 8px 48px rgba(44, 43, 41, .14);
      --transition: .25s cubic-bezier(.4, 0, .2, 1);
    }

    *,
    *::before,
    *::after {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    html {
      scroll-behavior: smooth;
    }

    body {
      font-family: var(--font-body);
      background: var(--cream);
      color: var(--charcoal);
      font-size: 15px;
      line-height: 1.6;
      min-height: 100vh;
    }

    /* ---- SCROLLBAR ---- */
    ::-webkit-scrollbar {
      width: 6px;
    }

    ::-webkit-scrollbar-track {
      background: var(--cream);
    }

    ::-webkit-scrollbar-thumb {
      background: var(--blush-dark);
      border-radius: 3px;
    }

    /* ---- TYPOGRAPHY ---- */
    h1,
    h2,
    h3,
    h4 {
      font-family: var(--font-display);
      font-weight: 400;
      letter-spacing: .02em;
    }

    /* ============================================================
   HEADER / NAV
   ============================================================ */
    header {
      position: sticky;
      top: 0;
      z-index: 100;
      background: rgba(247, 243, 238, .92);
      backdrop-filter: blur(12px);
      border-bottom: 1px solid rgba(184, 150, 90, .18);
      padding: 0 clamp(16px, 5vw, 64px);
    }

    .nav-inner {
      max-width: 1320px;
      margin: 0 auto;
      display: flex;
      align-items: center;
      justify-content: space-between;
      height: 68px;
    }

    .logo {
      font-family: var(--font-display);
      font-size: 1.7rem;
      font-weight: 300;
      letter-spacing: .12em;
      color: var(--charcoal);
      text-decoration: none;
    }

    .logo span {
      color: var(--gold);
      font-style: italic;
    }

    nav {
      display: flex;
      gap: 4px;
    }

    .nav-btn {
      font-family: var(--font-body);
      font-size: .78rem;
      font-weight: 500;
      letter-spacing: .08em;
      text-transform: uppercase;
      padding: 8px 18px;
      border: 1px solid transparent;
      border-radius: var(--radius);
      background: transparent;
      color: var(--graphite);
      cursor: pointer;
      transition: var(--transition);
    }

    .nav-btn:hover {
      color: var(--gold);
      border-color: rgba(184, 150, 90, .3);
    }

    .nav-btn.active {
      color: var(--gold);
      border-color: var(--gold);
      background: rgba(184, 150, 90, .06);
    }

    /* ============================================================
   HERO
   ============================================================ */
    .hero {
      background: linear-gradient(135deg, var(--charcoal) 0%, #3d3b38 100%);
      color: var(--cream);
      padding: clamp(48px, 8vw, 96px) clamp(16px, 5vw, 64px);
      position: relative;
      overflow: hidden;
    }

    .hero::before {
      content: '';
      position: absolute;
      inset: 0;
      background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23B8965A' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }

    .hero-inner {
      max-width: 1320px;
      margin: 0 auto;
      position: relative;
    }

    .hero h1 {
      font-size: clamp(2.4rem, 6vw, 4.8rem);
      font-weight: 300;
      line-height: 1.1;
      margin-bottom: 16px;
    }

    .hero h1 em {
      color: var(--gold-light);
      font-style: italic;
    }

    .hero p {
      font-size: 1rem;
      color: rgba(247, 243, 238, .7);
      max-width: 480px;
      margin-bottom: 36px;
    }

    .btn-primary {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      font-family: var(--font-body);
      font-size: .82rem;
      font-weight: 500;
      letter-spacing: .1em;
      text-transform: uppercase;
      padding: 14px 32px;
      background: var(--gold);
      color: var(--warm-white);
      border: none;
      border-radius: var(--radius);
      cursor: pointer;
      transition: var(--transition);
      text-decoration: none;
    }

    .btn-primary:hover {
      background: var(--gold-light);
      transform: translateY(-1px);
      box-shadow: 0 6px 20px rgba(184, 150, 90, .35);
    }

    /* ============================================================
   PAGES — visible/hidden
   ============================================================ */
    .page {
      display: none;
    }

    .page.active {
      display: block;
    }

    .page-inner {
      max-width: 1320px;
      margin: 0 auto;
      padding: clamp(32px, 5vw, 64px) clamp(16px, 5vw, 64px);
    }

    /* Section label */
    .section-label {
      font-family: var(--font-body);
      font-size: .72rem;
      font-weight: 500;
      letter-spacing: .16em;
      text-transform: uppercase;
      color: var(--gold);
      margin-bottom: 8px;
    }

    .section-title {
      font-size: clamp(1.8rem, 4vw, 3rem);
      font-weight: 300;
      margin-bottom: 40px;
      padding-bottom: 16px;
      border-bottom: 1px solid rgba(184, 150, 90, .2);
    }

    /* ============================================================
   CATÁLOGO — filter + grid
   ============================================================ */
    .filter-bar {
      display: flex;
      gap: 8px;
      flex-wrap: wrap;
      margin-bottom: 32px;
    }

    .filter-chip {
      font-family: var(--font-body);
      font-size: .75rem;
      font-weight: 500;
      letter-spacing: .06em;
      text-transform: uppercase;
      padding: 7px 16px;
      border: 1px solid rgba(44, 43, 41, .18);
      border-radius: 20px;
      background: transparent;
      color: var(--graphite);
      cursor: pointer;
      transition: var(--transition);
    }

    .filter-chip:hover,
    .filter-chip.active {
      background: var(--charcoal);
      color: var(--cream);
      border-color: var(--charcoal);
    }

    .catalog-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
      gap: 24px;
    }

    .card {
      background: var(--warm-white);
      border-radius: var(--radius);
      overflow: hidden;
      box-shadow: var(--shadow);
      transition: var(--transition);
      cursor: pointer;
      position: relative;
    }

    .card:hover {
      transform: translateY(-4px);
      box-shadow: var(--shadow-lg);
    }

    .card-img {
      width: 100%;
      aspect-ratio: 3/4;
      object-fit: cover;
      display: block;
      transition: transform .4s ease;
    }

    .card:hover .card-img {
      transform: scale(1.03);
    }

    .card-img-wrap {
      overflow: hidden;
      position: relative;
    }

    .card-badge {
      position: absolute;
      top: 12px;
      left: 12px;
      font-size: .68rem;
      font-weight: 500;
      letter-spacing: .06em;
      text-transform: uppercase;
      padding: 4px 10px;
      background: var(--charcoal);
      color: var(--cream);
      border-radius: 2px;
    }

    .card-body {
      padding: 16px 18px 20px;
    }

    .card-cat {
      font-size: .7rem;
      letter-spacing: .1em;
      text-transform: uppercase;
      color: var(--gold);
      font-weight: 500;
      margin-bottom: 4px;
    }

    .card-name {
      font-family: var(--font-display);
      font-size: 1.2rem;
      font-weight: 400;
      margin-bottom: 6px;
      line-height: 1.3;
    }

    .card-desc {
      font-size: .82rem;
      color: var(--graphite);
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
      margin-bottom: 14px;
    }

    .card-footer {
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .card-price {
      font-family: var(--font-display);
      font-size: 1.3rem;
      font-weight: 500;
      color: var(--charcoal);
    }

    .btn-agendar {
      font-size: .72rem;
      font-weight: 500;
      letter-spacing: .08em;
      text-transform: uppercase;
      padding: 8px 16px;
      background: var(--charcoal);
      color: var(--cream);
      border: none;
      border-radius: var(--radius);
      cursor: pointer;
      transition: var(--transition);
    }

    .btn-agendar:hover {
      background: var(--gold);
    }

    /* ============================================================
   MODAL — detalhe da peça
   ============================================================ */
    .modal-overlay {
      display: none;
      position: fixed;
      inset: 0;
      z-index: 200;
      background: rgba(44, 43, 41, .55);
      backdrop-filter: blur(4px);
      align-items: center;
      justify-content: center;
      padding: 16px;
    }

    .modal-overlay.open {
      display: flex;
    }

    .modal {
      background: var(--warm-white);
      border-radius: var(--radius);
      max-width: 860px;
      width: 100%;
      max-height: 90vh;
      overflow-y: auto;
      box-shadow: var(--shadow-lg);
      animation: modalIn .28s cubic-bezier(.4, 0, .2, 1);
    }

    @keyframes modalIn {
      from {
        opacity: 0;
        transform: scale(.96) translateY(12px);
      }

      to {
        opacity: 1;
        transform: scale(1) translateY(0);
      }
    }

    .modal-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
    }

    @media(max-width:640px) {
      .modal-grid {
        grid-template-columns: 1fr;
      }
    }

    .modal-img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      min-height: 320px;
    }

    .modal-body {
      padding: 32px 28px;
      display: flex;
      flex-direction: column;
    }

    .modal-close {
      align-self: flex-end;
      background: none;
      border: none;
      cursor: pointer;
      color: var(--graphite);
      font-size: 1.4rem;
      width: 32px;
      height: 32px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
      transition: var(--transition);
      margin-bottom: 12px;
    }

    .modal-close:hover {
      background: var(--blush);
      color: var(--charcoal);
    }

    .modal-cat {
      font-size: .7rem;
      letter-spacing: .14em;
      text-transform: uppercase;
      color: var(--gold);
      margin-bottom: 6px;
    }

    .modal-name {
      font-family: var(--font-display);
      font-size: 2rem;
      font-weight: 400;
      margin-bottom: 12px;
    }

    .modal-desc {
      font-size: .9rem;
      color: var(--graphite);
      flex: 1;
      margin-bottom: 20px;
    }

    .modal-price {
      font-family: var(--font-display);
      font-size: 1.8rem;
      color: var(--charcoal);
      margin-bottom: 24px;
    }

    /* ============================================================
   FORMS
   ============================================================ */
    .form-section {
      max-width: 560px;
    }

    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 16px;
    }

    @media(max-width:480px) {
      .form-row {
        grid-template-columns: 1fr;
      }
    }

    .field {
      margin-bottom: 20px;
    }

    label {
      display: block;
      font-size: .72rem;
      font-weight: 500;
      letter-spacing: .08em;
      text-transform: uppercase;
      color: var(--graphite);
      margin-bottom: 6px;
    }

    input[type=text],
    input[type=tel],
    input[type=date],
    input[type=time],
    select,
    textarea {
      width: 100%;
      padding: 12px 14px;
      background: var(--cream);
      border: 1px solid rgba(44, 43, 41, .18);
      border-radius: var(--radius);
      color: var(--charcoal);
      font-family: var(--font-body);
      font-size: .9rem;
      transition: var(--transition);
      outline: none;
    }

    input:focus,
    select:focus,
    textarea:focus {
      border-color: var(--gold);
      background: var(--warm-white);
      box-shadow: 0 0 0 3px rgba(184, 150, 90, .12);
    }

    input.error,
    select.error {
      border-color: var(--error);
    }

    .field-error {
      font-size: .72rem;
      color: var(--error);
      margin-top: 4px;
      display: none;
    }

    .field-error.show {
      display: block;
    }

    select {
      appearance: none;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%234A4845' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: right 12px center;
      padding-right: 36px;
    }

    .btn-submit {
      font-family: var(--font-body);
      font-size: .82rem;
      font-weight: 500;
      letter-spacing: .1em;
      text-transform: uppercase;
      padding: 14px 40px;
      background: var(--charcoal);
      color: var(--cream);
      border: none;
      border-radius: var(--radius);
      cursor: pointer;
      transition: var(--transition);
      margin-top: 8px;
    }

    .btn-submit:hover {
      background: var(--gold);
      transform: translateY(-1px);
    }

    .btn-submit:disabled {
      opacity: .5;
      cursor: not-allowed;
      transform: none;
    }

    /* ============================================================
   TOAST / ALERTS
   ============================================================ */
    .toast-container {
      position: fixed;
      bottom: 24px;
      right: 24px;
      z-index: 999;
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    .toast {
      min-width: 280px;
      max-width: 360px;
      padding: 14px 18px;
      border-radius: var(--radius);
      font-size: .85rem;
      font-weight: 400;
      box-shadow: var(--shadow-lg);
      animation: toastIn .25s ease;
      display: flex;
      align-items: flex-start;
      gap: 10px;
    }

    @keyframes toastIn {
      from {
        opacity: 0;
        transform: translateX(20px);
      }

      to {
        opacity: 1;
        transform: none;
      }
    }

    .toast.success {
      background: var(--charcoal);
      color: var(--cream);
    }

    .toast.error {
      background: var(--error);
      color: #fff;
    }

    .toast.info {
      background: var(--gold);
      color: #fff;
    }

    .toast-icon {
      font-size: 1.1rem;
      flex-shrink: 0;
    }

    /* ============================================================
   CONFIRMAÇÃO DE AGENDAMENTO
   ============================================================ */
    .confirm-card {
      background: var(--warm-white);
      border-radius: var(--radius);
      max-width: 520px;
      padding: 40px;
      box-shadow: var(--shadow-lg);
      display: none;
    }

    .confirm-card.show {
      display: block;
      animation: modalIn .3s ease;
    }

    .confirm-tag {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      font-size: .72rem;
      font-weight: 500;
      letter-spacing: .1em;
      text-transform: uppercase;
      color: var(--success);
      margin-bottom: 24px;
    }

    .confirm-tag::before {
      content: '✓';
      width: 22px;
      height: 22px;
      background: var(--success);
      color: #fff;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: .75rem;
    }

    .confirm-title {
      font-family: var(--font-display);
      font-size: 1.8rem;
      font-weight: 400;
      margin-bottom: 24px;
      line-height: 1.2;
    }

    .confirm-rows {
      display: flex;
      flex-direction: column;
      gap: 12px;
      margin-bottom: 32px;
    }

    .confirm-row {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      padding-bottom: 12px;
      border-bottom: 1px solid rgba(44, 43, 41, .08);
      font-size: .88rem;
      gap: 16px;
    }

    .confirm-row:last-child {
      border-bottom: none;
      padding-bottom: 0;
    }

    .confirm-row .label-sm {
      color: var(--graphite);
      font-weight: 400;
      flex-shrink: 0;
    }

    .confirm-row .val {
      font-weight: 500;
      text-align: right;
    }

    .btn-novo {
      margin-right: 12px;
    }

    .btn-outline {
      font-family: var(--font-body);
      font-size: .8rem;
      font-weight: 500;
      letter-spacing: .08em;
      text-transform: uppercase;
      padding: 12px 24px;
      background: transparent;
      border: 1px solid var(--charcoal);
      color: var(--charcoal);
      border-radius: var(--radius);
      cursor: pointer;
      transition: var(--transition);
    }

    .btn-outline:hover {
      background: var(--charcoal);
      color: var(--cream);
    }

    /* ============================================================
   AGENDAMENTO FORM — steps
   ============================================================ */
    .steps {
      display: flex;
      gap: 0;
      margin-bottom: 40px;
      border-bottom: 1px solid rgba(44, 43, 41, .1);
    }

    .step {
      padding: 12px 24px 14px;
      font-size: .75rem;
      font-weight: 500;
      letter-spacing: .08em;
      text-transform: uppercase;
      color: var(--graphite);
      border-bottom: 2px solid transparent;
      cursor: default;
      transition: var(--transition);
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .step.active {
      color: var(--gold);
      border-bottom-color: var(--gold);
    }

    .step.done {
      color: var(--graphite);
    }

    .step-num {
      width: 22px;
      height: 22px;
      border-radius: 50%;
      background: rgba(44, 43, 41, .1);
      color: var(--graphite);
      font-size: .68rem;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .step.active .step-num {
      background: var(--gold);
      color: #fff;
    }

    .step.done .step-num {
      background: var(--success);
      color: #fff;
    }

    .step-content {
      display: none;
    }

    .step-content.active {
      display: block;
    }

    /* Roupa selector inside agendamento */
    .roupa-selector {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
      gap: 14px;
      margin-bottom: 24px;
    }

    .roupa-option {
      border: 2px solid transparent;
      border-radius: var(--radius);
      cursor: pointer;
      overflow: hidden;
      transition: var(--transition);
      background: var(--warm-white);
    }

    .roupa-option:hover {
      border-color: rgba(184, 150, 90, .4);
    }

    .roupa-option.selected {
      border-color: var(--gold);
    }

    .roupa-option img {
      width: 100%;
      aspect-ratio: 1;
      object-fit: cover;
      display: block;
    }

    .roupa-option .ro-name {
      padding: 8px 10px;
      font-size: .8rem;
      font-weight: 500;
      font-family: var(--font-display);
      font-size: 1rem;
    }

    /* horários disponíveis */
    .time-slots {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      margin-top: 8px;
    }

    .time-slot {
      padding: 8px 16px;
      border: 1px solid rgba(44, 43, 41, .2);
      border-radius: 20px;
      font-size: .8rem;
      cursor: pointer;
      transition: var(--transition);
      background: var(--warm-white);
    }

    .time-slot:hover {
      border-color: var(--gold);
      color: var(--gold);
    }

    .time-slot.selected {
      background: var(--charcoal);
      color: var(--cream);
      border-color: var(--charcoal);
    }

    /* loading spinner */
    .spinner {
      width: 18px;
      height: 18px;
      border: 2px solid rgba(247, 243, 238, .3);
      border-top-color: #fff;
      border-radius: 50%;
      animation: spin .6s linear infinite;
      display: inline-block;
    }

    @keyframes spin {
      to {
        transform: rotate(360deg);
      }
    }

    /* ============================================================
   RESPONSIVE
   ============================================================ */
    @media(max-width:768px) {
      .nav-btn span {
        display: none;
      }

      .catalog-grid {
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 14px;
      }

      .steps {
        overflow-x: auto;
      }
    }

    @media(max-width:480px) {
      .hero h1 {
        font-size: 2rem;
      }
    }

    /* Selected roupa preview in form */
    .selected-roupa-preview {
      display: flex;
      gap: 14px;
      align-items: center;
      padding: 14px;
      background: var(--cream);
      border-radius: var(--radius);
      margin-bottom: 24px;
      border: 1px solid rgba(184, 150, 90, .2);
    }

    .selected-roupa-preview img {
      width: 64px;
      height: 80px;
      object-fit: cover;
      border-radius: 2px;
    }

    .srp-info .srp-name {
      font-family: var(--font-display);
      font-size: 1.1rem;
    }

    .srp-info .srp-price {
      font-size: .82rem;
      color: var(--gold);
    }

    /* ESTILOS DO CARROSSEL (Cole aqui no final) */
    .swiper-pagination-bullet-active {
      background: #b8960c !important; /* Cor dourada da bolinha ativa */
    }
    .swiper {
      width: 100%;
      height: auto;
      border-radius: 8px; /* Deixa as pontas das fotos arredondadas */
    }

    /* Estilos para o Modal de Login e Cadastro */
#modalLoginOverlay {
    display: none; /* Mantém escondido até clicar no botão */
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.85); /* Fundo escuro semi-transparente */
    z-index: 2000;
    justify-content: center;
    align-items: center;
    backdrop-filter: blur(5px); /* Efeito de desfoque no fundo */
}

#modalLoginOverlay.open {
    display: flex; /* Mostra o modal */
}

.login-card {
    background: #111; /* Fundo preto elegante */
    padding: 30px;
    border-radius: 12px;
    width: 90%;
    max-width: 400px;
    border: 1px solid #c5a059; /* Borda dourada do Atelier */
    position: relative;
    box-shadow: 0 10px 30px rgba(0,0,0,0.5);
}

.gold-title {
    color: #c5a059;
    font-family: 'Playfair Display', serif;
    text-align: center;
    margin-bottom: 10px;
}

.subtitle {
    color: #ccc;
    text-align: center;
    font-size: 0.9rem;
    margin-bottom: 25px;
}

.input-group {
    margin-bottom: 15px;
}

.input-group label {
    display: block;
    color: #c5a059;
    font-size: 0.8rem;
    margin-bottom: 5px;
    font-weight: bold;
}

.input-group input {
    width: 100%;
    padding: 12px;
    background: #222;
    border: 1px solid #333;
    border-radius: 5px;
    color: white;
    outline: none;
}

.input-group input:focus {
    border-color: #c5a059;
}

.btn-gold {
    background: #c5a059;
    color: #000;
    width: 100%;
    padding: 12px;
    border: none;
    border-radius: 5px;
    font-weight: bold;
    cursor: pointer;
    margin-top: 10px;
    transition: 0.3s;
}

.btn-gold:hover {
    background: #e2ba73;
}

.switch-text {
    color: #888;
    text-align: center;
    margin-top: 20px;
    font-size: 0.85rem;
}

.switch-text a {
    color: #c5a059;
    text-decoration: none;
    font-weight: bold;
}

.modal-close {
    position: absolute;
    top: 15px;
    right: 15px;
    background: none;
    border: none;
    color: #888;
    font-size: 24px;
    cursor: pointer;
}

  </style>
</head>

<body>

  <!-- ============================================================
     HEADER
     ============================================================ -->
<header>
  <div class="nav-inner">
    <a href="#" class="logo">AT<span>EL</span>IER</a>
    <nav>
      <button class="nav-btn active" onclick="showPage('catalogo')">
        <span>Catálogo</span>
      </button>

      <?php if(isset($_SESSION['usuario_id'])): ?>
    <button class="nav-btn" id="btnPerfil">
        <span>OLÁ, <?php echo strtoupper($_SESSION['usuario_nome']); ?></span>
    </button>
    
        <button class="nav-btn" onclick="showPage('agendamento')">
          <span>Agendamento</span>
        </button>
        
        <button class="nav-btn" onclick="confirmarSair()" style="color: #ff4d4d; border-left: 1px solid #ddd; margin-left: 10px;">
          <span>Sair</span>
        </button>

      <?php else: ?>
        <button class="nav-btn" onclick="abrirModalLogin()" id="btnPerfil">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 5px;">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
            <circle cx="12" cy="7" r="4"></circle>
          </svg>
          <span>Entrar</span>
        </button>
      <?php endif; ?>
    </nav>
  </div>
</header>

<div id="hero" class="hero">
  <div class="hero-inner" style="display: flex; align-items: center; justify-content: space-between; gap: 40px; flex-wrap: wrap;">
    
    <div class="hero-texto" style="flex: 1; min-width: 300px;">
      <p class="section-label">Coleção Atual</p>
      <h1>Estilo que<br /><em>conta histórias</em></h1>
      <p>Explore nosso catálogo exclusivo e agende sua visita para experimentar cada peça com toda a atenção que você merece.</p>
      
      <?php if(isset($_SESSION['usuario_id'])): ?>
        <button class="btn-primary" onclick="showPage('agendamento')">
          Agendar Visita
        </button>
      <?php else: ?>
        <button class="btn-primary" onclick="abrirModalLogin()">
          Faça Login para Agendar
        </button>
      <?php endif; ?>
    </div>

   <div class="hero-carrossel" style="flex: 1; min-width: 300px; width: 100%; max-width: 500px;">
      <div class="swiper mySwiper">
        <div class="swiper-wrapper">
          <div class="swiper-slide">
            <img src="img/banner1.webp" alt="Destaque 1" style="width:100%; border-radius: 8px;">
          </div>
          <div class="swiper-slide">
            <img src="img/banner2.jpg" alt="Destaque 2" style="width:100%; border-radius: 8px;">
          </div>
          <div class="swiper-slide">
            <img src="img/banner3.webp" alt="Destaque 3" style="width:100%; border-radius: 8px;">
          </div>
        </div>
        <div class="swiper-pagination"></div>
      </div>
    </div>
  </div>
</div>
  <!-- ============================================================
     PAGE: CATÁLOGO
     ============================================================ -->
  <div id="page-catalogo" class="page active">
    <div class="page-inner">
      <p class="section-label">Nosso Acervo</p>
      <h2 class="section-title">Catálogo de Peças</h2>

      <div class="filter-bar" id="filterBar">
        <button class="filter-chip active" data-cat="all">Todas</button>
      </div>

      <div class="catalog-grid" id="catalogGrid">
        <!-- Preenchido via JS -->
        <div style="grid-column:1/-1;text-align:center;padding:48px;color:var(--graphite)">
          Carregando catálogo…
        </div>
      </div>
    </div>
  </div>

  <!-- ============================================================
     PAGE: CADASTRO
     ============================================================ -->
  <div id="page-cadastro" class="page">
    <div class="page-inner">
      <p class="section-label">Primeira Visita?</p>
      <h2 class="section-title">Cadastro de Cliente</h2>

      <div class="form-section">
        <form id="formCadastro" action="controllers/ClienteController.php?action=cadastrar" method="POST">
          <div class="field">
            <label for="nome">Nome Completo *</label>
            <input type="text" id="nome" name="nome" placeholder="Seu nome completo" autocomplete="name" />
            <div class="field-error" id="erroNome">Nome é obrigatório (mínimo 3 caracteres).</div>
          </div>

          <div class="form-row">
            <div class="field">
              <label for="cpf">CPF *</label>
              <input type="text" id="cpf" name="cpf" placeholder="000.000.000-00" maxlength="14" />
              <div class="field-error" id="erroCpf">CPF inválido.</div>
            </div>
            <div class="field">
              <label for="telefone">Telefone *</label>
              <input type="tel" id="telefone" name="telefone" placeholder="(11) 99999-9999" maxlength="15" />
              <div class="field-error" id="erroTelefone">Telefone inválido.</div>
            </div>
          </div>

          <button type="submit" class="btn-submit" id="btnCadastrar">
            Cadastrar
          </button>
        </form>

        <!-- Confirmação de cadastro inline -->
        <div id="cadastroConfirm"
          style="display:none;margin-top:28px;padding:24px;background:var(--warm-white);border-radius:var(--radius);border-left:3px solid var(--gold);">
          <div
            style="font-size:.72rem;letter-spacing:.1em;text-transform:uppercase;color:var(--gold);margin-bottom:8px;">
            Cliente Identificado</div>
          <div id="cadastroNome" style="font-family:var(--font-display);font-size:1.4rem;margin-bottom:4px;"></div>
          <div id="cadastroCpf" style="font-size:.82rem;color:var(--graphite);margin-bottom:16px;"></div>
          <button class="btn-primary" onclick="irParaAgendamento()" style="padding:11px 24px;font-size:.78rem;">
            Agendar Agora →
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- ============================================================
     PAGE: AGENDAMENTO
     ============================================================ -->
  <div id="page-agendamento" class="page">
    <div class="page-inner">
      <p class="section-label">Reserve sua Visita</p>
      <h2 class="section-title">Novo Agendamento</h2>

      <!-- Steps indicator -->
      <div class="steps">
        <div class="step active" id="stepTab1">
          <div class="step-num">1</div> Escolha a Peça
        </div>
        <div class="step" id="stepTab2">
          <div class="step-num">2</div> Data & Horário
        </div>
        <div class="step" id="stepTab3">
          <div class="step-num">3</div> Seus Dados
        </div>
      </div>

      <!-- STEP 1: Escolher roupa -->
      <div class="step-content active" id="step1">
        <p style="font-size:.85rem;color:var(--graphite);margin-bottom:20px;">Selecione a peça que deseja experimentar:
        </p>
        <div class="roupa-selector" id="roupaSelector">
          <div style="font-size:.85rem;color:var(--graphite)">Carregando peças…</div>
        </div>
        <button class="btn-submit" onclick="irStep(2)" id="btnStep1Next" disabled>
          Próximo →
        </button>
      </div>

      <!-- STEP 2: Data e horário -->
      <div class="step-content" id="step2">
        <div class="selected-roupa-preview" id="roupaPreview" style="display:none"></div>

        <div class="form-row">
          <div class="field">
            <label for="dataAg">Data *</label>
            <input type="date" id="dataAg" min="" />
          </div>
          <div class="field">
            <label>Horário *</label>
            <div class="time-slots" id="timeSlots">
              <!-- Gerado via JS -->
            </div>
          </div>
        </div>

        <div class="field">
          <label for="obsAg">Observações (opcional)</label>
          <textarea id="obsAg" rows="3" placeholder="Tamanho, preferências, dúvidas…"
            style="resize:vertical"></textarea>
        </div>

        <div style="display:flex;gap:12px;">
          <button class="btn-outline" onclick="irStep(1)">← Voltar</button>
          <button class="btn-submit" onclick="irStep(3)" id="btnStep2Next" disabled>Próximo →</button>
        </div>
      </div>

      <!-- STEP 3: Dados do cliente -->
     <div class="step-content" id="step3">
  <p style="font-size:.85rem;color:var(--graphite);margin-bottom:20px;">
    Já é cadastrado? Informe seu CPF para recuperar seus dados.
  </p>

  <form action="controllers/AgendamentoController.php" method="POST" class="form-section">
    
    <input type="hidden" name="action" value="agendar">

    <input type="hidden" name="cliente_id" id="cliente_id" value=""> 
    <input type="hidden" name="roupa_id" id="roupa_id" value="">
    <input type="hidden" name="data_agendamento" id="data_agendamento" value="">
    <input type="hidden" name="horario" id="horario" value="">
    
    <div class="field">
      <label for="agCpf">CPF *</label>
      <input type="text" id="agCpf" name="cpf" placeholder="000.000.000-00" maxlength="14" required />
      <div class="field-error" id="erroAgCpf">CPF inválido.</div>
    </div>

    <div id="dadosClienteExtra">
      <div class="field">
        <label for="agNome">Nome Completo *</label>
        <input type="text" id="agNome" name="nome" placeholder="Seu nome completo" required />
      </div>
      <div class="field">
        <label for="agTel">Telefone *</label>
        <input type="tel" id="agTel" name="telefone" placeholder="(11) 99999-9999" maxlength="15" required />
      </div>
    </div>

    <div style="display:flex;gap:12px;margin-top:8px;">
      <button type="button" class="btn-outline" onclick="irStep(2)">← Voltar</button>
      <button type="button" onclick="finalizarAgendamentoManual()" class="btn-submit" id="btnFinalizar">
  Confirmar Agendamento
</button>
    </div>
  </form>
</div>

      <!-- CONFIRMAÇÃO FINAL -->
      <div class="confirm-card" id="confirmCard">
        <div class="confirm-tag">Agendamento Confirmado</div>
        <h3 class="confirm-title">Sua visita está<br />reservada!</h3>
        <div class="confirm-rows" id="confirmRows"></div>
        <button class="btn-outline btn-novo" onclick="resetAgendamento()">Novo Agendamento</button>
        <button class="btn-primary" onclick="showPage('catalogo')" style="padding:12px 24px;font-size:.8rem;">
          Ver Catálogo
        </button>
      </div>

    </div>
  </div>

  <!-- ============================================================
     MODAL — detalhe da peça
     ============================================================ -->
  <div class="modal-overlay" id="modalOverlay" onclick="fecharModal(event)">
    <div class="modal" id="modal">
      <div class="modal-grid">
        <div class="card-img-wrap">
          <img id="modalImg" src="" alt="" class="modal-img" />
        </div>
        <div class="modal-body">
          <button class="modal-close" onclick="fecharModal()">&times;</button>
          <div id="modalCat" class="modal-cat"></div>
          <h3 id="modalNome" class="modal-name"></h3>
          <p id="modalDesc" class="modal-desc"></p>
          <div id="modalPreco" class="modal-price"></div>
          <button class="btn-primary" id="modalBtnAgendar" style="width:100%;justify-content:center;">
            FAÇA LOGIN / CADASTRO PARA AGENDAR
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- TOAST -->
  <div class="toast-container" id="toastContainer"></div>

  <!-- ============================================================
     JAVASCRIPT
     ============================================================ -->
  <script>
    /* ============================================================
       STATE
       ============================================================ */
    const state = {
      roupas: [],      // catálogo completo
      cliente: null,    // cliente logado
      agendamento: {
        roupa_id: null,
        roupa: null,
        data: null,
        horario: null,
        observacoes: '',
      }
    };

    /* ============================================================
       NAVIGATION
       ============================================================ */
    function showPage(name) {
      document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
      document.querySelectorAll('.nav-btn').forEach(b => b.classList.remove('active'));
      document.getElementById('page-' + name).classList.add('active');
      const idx = ['catalogo', 'cadastro', 'agendamento'].indexOf(name);
      document.querySelectorAll('.nav-btn')[idx]?.classList.add('active');
      document.getElementById('hero').style.display = name === 'catalogo' ? '' : 'none';
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    /* ============================================================
       TOAST
       ============================================================ */
    function toast(msg, type = 'success', dur = 4000) {
      const icons = { success: '✓', error: '✕', info: 'i' };
      const el = document.createElement('div');
      el.className = `toast ${type}`;
      el.innerHTML = `<span class="toast-icon">${icons[type] || 'i'}</span><span>${msg}</span>`;
      document.getElementById('toastContainer').appendChild(el);
      setTimeout(() => el.remove(), dur);
    }

    /* ============================================================
       API HELPERS
       ============================================================ */
    async function api(url, opts = {}) {
      try {
        const r = await fetch(url, {
          headers: { 'Content-Type': 'application/json' },
          ...opts,
        });
        const data = await r.json();
        return { ok: r.ok, status: r.status, data };
      } catch (e) {
        return { ok: false, data: { error: 'Erro de conexão.' } };
      }
    }

    /* ============================================================
       CATÁLOGO — carrega e renderiza roupas
       ============================================================ */
    async function carregarCatalogo() {
      const res = await api('controllers/RoupaController.php?action=listar');
      if (!res.ok || !res.data.roupas) {
        document.getElementById('catalogGrid').innerHTML =
          `<div style="grid-column:1/-1;text-align:center;padding:48px;color:var(--error)">
        Não foi possível carregar o catálogo.<br/>
        <small style="font-size:.78rem;color:var(--graphite)">Verifique a conexão com o banco de dados.</small>
      </div>`;
        return;
      }

      state.roupas = res.data.roupas;
      renderizarCatalogo(state.roupas);
      renderizarFiltros();
      renderizarRoupasNoAgendamento();
    }

    function renderizarCatalogo(roupas) {
      const grid = document.getElementById('catalogGrid');
      if (!roupas.length) {
        grid.innerHTML = `<div style="grid-column:1/-1;text-align:center;padding:48px;color:var(--graphite)">Nenhuma peça encontrada.</div>`;
        return;
      }

      grid.innerHTML = roupas.map(r => `
    <div class="card" onclick="abrirModal(${r.id})">
      <div class="card-img-wrap">
        <img class="card-img"
             src="img/${r.imagem_url || 'https://placehold.co/400x533/F7F3EE/B8965A?text=Sem+Foto'}"
             alt="${r.nome}" loading="lazy"/>
        ${r.categoria ? `<div class="card-badge">${r.categoria}</div>` : ''}
      </div>
      <div class="card-body">
        ${r.categoria ? `<div class="card-cat">${r.categoria}</div>` : ''}
        <h3 class="card-name">${r.nome}</h3>
        <p class="card-desc">${r.descricao || ''}</p>
        <div class="card-footer">
          <div class="card-price">${r.preco ? 'R$ ' + parseFloat(r.preco).toFixed(2).replace('.', ',') : ''}</div>
          <button class="btn-agendar" onclick="event.stopPropagation();agendarRoupa(${r.id})">
            Agendar
          </button>
        </div>
      </div>
    </div>
  `).join('');
    }

    function renderizarFiltros() {
      const cats = [...new Set(state.roupas.map(r => r.categoria).filter(Boolean))];
      const bar = document.getElementById('filterBar');
      bar.innerHTML = `<button class="filter-chip active" data-cat="all" onclick="filtrar(this,'all')">Todas</button>`;
      cats.forEach(c => {
        const btn = document.createElement('button');
        btn.className = 'filter-chip';
        btn.dataset.cat = c;
        btn.textContent = c;
        btn.onclick = () => filtrar(btn, c);
        bar.appendChild(btn);
      });
    }

    function filtrar(btn, cat) {
      document.querySelectorAll('.filter-chip').forEach(c => c.classList.remove('active'));
      btn.classList.add('active');
      const filtradas = cat === 'all' ? state.roupas : state.roupas.filter(r => r.categoria === cat);
      renderizarCatalogo(filtradas);
    }

    /* ============================================================
       MODAL
       ============================================================ */
    function abrirModal(id) {
      const r = state.roupas.find(x => x.id == id);
      if (!r) return;
      document.getElementById('modalImg').src = 'img/'+ r.imagem_url || '';
      document.getElementById('modalImg').alt = r.nome;
      document.getElementById('modalCat').textContent = r.categoria || '';
      document.getElementById('modalNome').textContent = r.nome;
      document.getElementById('modalDesc').textContent = r.descricao || '';
      document.getElementById('modalPreco').textContent = r.preco
        ? 'R$ ' + parseFloat(r.preco).toFixed(2).replace('.', ',') : '';
      document.getElementById('modalBtnAgendar').onclick = () => { fecharModal(); agendarRoupa(id); };
      document.getElementById('modalOverlay').classList.add('open');
      document.body.style.overflow = 'hidden';
    }

    function fecharModal(e) {
      if (e && e.target !== document.getElementById('modalOverlay')) return;
      document.getElementById('modalOverlay').classList.remove('open');
      document.body.style.overflow = '';
    }

   /* ============================================================
   CATÁLOGO → AGENDAMENTO atalho (AJUSTADO PARA LOGIN)
   ============================================================ */
function agendarRoupa(id) {
    // 1. Salva a peça selecionada
    const r = state.roupas.find(x => x.id == id);
    if (r) {
        state.agendamento.roupa_id = r.id;
        state.agendamento.roupa = r;
    }

    if (USUARIO_LOGADO) {
        // SE LOGADO: Vai para a página de agendamento no Step 2 (Data)
        showPage('agendamento');
        irStep(2); 
        
        // Preenche os dados do usuário automaticamente no Step 3
        if (DADOS_USUARIO) {
            document.querySelector('input[name="nome"]').value = DADOS_USUARIO.nome;
            document.querySelector('input[name="cpf"]').value = DADOS_USUARIO.cpf;
            document.querySelector('input[name="telefone"]').value = DADOS_USUARIO.telefone;
        }
    } else {
        // SE NÃO LOGADO: Abre o modal de login
        const modal = document.getElementById('modalLoginOverlay');
        if (modal) modal.style.display = 'flex';
    }
}

    /* ============================================================
       CADASTRO DE CLIENTE
       ============================================================ */
    let clienteLogado = null; // cliente identificado
     /*
    document.getElementById('formCadastro').addEventListener('submit', async function (e) {
      e.preventDefault();

      const nome = document.getElementById('nome').value.trim();
      const cpf = document.getElementById('cpf').value.trim();
      const telefone = document.getElementById('telefone').value.trim();

      // Validação front-end
      let valido = true;

      if (nome.length < 3) {
        document.getElementById('erroNome').classList.add('show');
        document.getElementById('nome').classList.add('error');
        valido = false;
      } else {
        document.getElementById('erroNome').classList.remove('show');
        document.getElementById('nome').classList.remove('error');
      }

      if (!validarCpfJS(cpf)) {
        document.getElementById('erroCpf').classList.add('show');
        document.getElementById('cpf').classList.add('error');
        valido = false;
      } else {
        document.getElementById('erroCpf').classList.remove('show');
        document.getElementById('cpf').classList.remove('error');
      }

      const fone = telefone.replace(/\D/g, '');
      if (fone.length < 10 || fone.length > 11) {
        document.getElementById('erroTelefone').classList.add('show');
        document.getElementById('telefone').classList.add('error');
        valido = false;
      } else {
        document.getElementById('erroTelefone').classList.remove('show');
        document.getElementById('telefone').classList.remove('error');
      }

      if (!valido) return;

      const btn = document.getElementById('btnCadastrar');
      btn.disabled = true;
      btn.innerHTML = '<span class="spinner"></span> Salvando…';

      const res = await api('controllers/ClienteController.php?action=cadastrar', {
        method: 'POST',
        body: JSON.stringify({ nome, cpf, telefone }),
      });

      btn.disabled = false;
      btn.textContent = 'Cadastrar';

      if (res.ok && res.data.success) {
        clienteLogado = res.data.cliente;
        state.cliente = clienteLogado;

        const confirm = document.getElementById('cadastroConfirm');
        document.getElementById('cadastroNome').textContent = clienteLogado.nome;
        document.getElementById('cadastroCpf').textContent = 'CPF: ' + clienteLogado.cpf;
        confirm.style.display = 'block';

        toast(res.data.message, 'success');
        document.getElementById('formCadastro').reset();
      } else {
        const erros = res.data.erros || [res.data.error || 'Erro ao cadastrar.'];
        erros.forEach(err => toast(err, 'error')); 
      }
    });
         */
    function irParaAgendamento() {
      showPage('agendamento');
    }

    /* ============================================================
       CPF MASK + VALIDATION
       ============================================================ */
    function aplicarMascaraCpf(input) {
      let v = input.value.replace(/\D/g, '').slice(0, 11);
      v = v.replace(/(\d{3})(\d)/, '$1.$2')
        .replace(/(\d{3})(\d)/, '$1.$2')
        .replace(/(\d{3})(\d{1,2})$/, '$1-$2');
      input.value = v;
    }

    function aplicarMascaraTel(input) {
      let v = input.value.replace(/\D/g, '').slice(0, 11);
      v = v.length === 11
        ? v.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3')
        : v.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
      input.value = v;
    }

    function validarCpfJS(cpf) {
      const c = cpf.replace(/\D/g, '');
      if (c.length !== 11 || /^(\d)\1{10}$/.test(c)) return false;
      let s, r;
      s = 0;
      for (let i = 0; i < 9; i++) s += +c[i] * (10 - i);
      r = (s * 10 % 11) % 10; if (r !== +c[9]) return false;
      s = 0;
      for (let i = 0; i < 10; i++) s += +c[i] * (11 - i);
      r = (s * 10 % 11) % 10; return r === +c[10];
    }

    // Aplicar máscaras
    document.getElementById('cpf').addEventListener('input', function () { aplicarMascaraCpf(this); });
    document.getElementById('telefone').addEventListener('input', function () { aplicarMascaraTel(this); });

    /* ============================================================
       AGENDAMENTO — steps
       ============================================================ */
       let stepAtual = 1;

   function irStep(n) {
  // Validação antes de avançar
  if (n === 2 && stepAtual === 1 && !state.agendamento.roupa_id) {
    toast('Selecione uma peça para continuar.', 'error'); return;
  }
  
  if (n === 3 && stepAtual === 2) {
    if (!state.agendamento.data) { toast('Selecione uma data.', 'error'); return; }
    if (!state.agendamento.horario) { toast('Selecione um horário.', 'error'); return; }
    state.agendamento.observacoes = document.getElementById('obsAg').value;

   // --- NOVA PONTE DINÂMICA (ATUALIZADA) ---
    const campoCliente = document.getElementById('cliente_id');
    
    // Se a busca por CPF já preencheu o campo (cliente antigo), mantemos.
    // Se estiver vazio, tentamos o state ou deixamos vazio (cliente novo).
    if (campoCliente.value === "") {
        campoCliente.value = state.cliente ? state.cliente.id : ""; 
    }

    document.getElementById('roupa_id').value = state.agendamento.roupa_id;
    document.getElementById('data_agendamento').value = state.agendamento.data;
    document.getElementById('horario').value = state.agendamento.horario;
  }

  stepAtual = n;

  document.querySelectorAll('.step-content').forEach((c, i) => {
    c.classList.toggle('active', i + 1 === n);
  });

  document.querySelectorAll('.step').forEach((s, i) => {
    s.classList.toggle('active', i + 1 === n);
    s.classList.toggle('done', i + 1 < n);
    const num = s.querySelector('.step-num');
    if (i + 1 < n) num.textContent = '✓';
    else num.textContent = i + 1;
  });

  if (n === 2) atualizarPreviewRoupa();
  if (n === 3) preencherDadosCliente();
}

  function renderizarRoupasNoAgendamento() {
    const sel = document.getElementById('roupaSelector');
    if (!sel) return;

    // Essa linha pergunta ao PHP se existe um usuário logado
    const usuarioLogado = <?php echo isset($_SESSION['usuario_id']) ? 'true' : 'false'; ?>;

    sel.innerHTML = state.roupas.map(r => {
        // Se estiver logado, o botão vira "Agendar Agora". Se não, continua "Identifique-se"
        const textoBotao = usuarioLogado ? "Agendar Agora" : "Identifique-se para Agendar";
        
        return `
            <div class="roupa-option ${state.agendamento.roupa_id == r.id ? 'selected' : ''}" id="ro-${r.id}">
              
              <img src="img/${r.imagem_url || 'https://placehold.co/300x300/F7F3EE/B8965A?text=+'}"
                   alt="${r.nome}" loading="lazy"/>
              
              <div class="ro-name">${r.nome}</div>

              <button type="button" class="btn-card-login" 
                      onclick="agendarRoupa(${r.id})"
                      style="width:100%; margin-top:10px; padding:8px; background:var(--gold); color:#fff; border:none; border-radius:4px; cursor:pointer; font-size:0.75rem; text-transform:uppercase;">
                  ${textoBotao}
              </button>

            </div>
        `;
    }).join('');
}
   function selecionarRoupa(r) {
    // 1. Identifica a roupa
    const roupa = typeof r === 'number' ? state.roupas.find(x => x.id == r) : r;
    if (!roupa) return;

    // 2. Guarda a roupa no estado
    state.agendamento.roupa_id = roupa.id;
    state.agendamento.roupa = roupa;

    // 3. Marca visualmente o card
    document.querySelectorAll('.roupa-option').forEach(el => el.classList.remove('selected'));
    document.getElementById('ro-' + roupa.id)?.classList.add('selected');

    // --- NOVA LÓGICA PARA ABRIR O MODAL ---
    const modal = document.getElementById('modalAgendamento');
    if (modal) {
        modal.style.display = 'block'; // Isso faz a janela aparecer na tela
    }

    // 4. Pula para o Passo 3 (Identificação)
    irStep(3); 
    
    // 5. Atualiza o resumo da roupa no modal (para o usuário ver o que escolheu)
    if (typeof atualizarPreviewRoupa === 'function') {
        atualizarPreviewRoupa();
    }
}

    function atualizarPreviewRoupa() {
      const r = state.agendamento.roupa;
      if (!r) return;
      const prev = document.getElementById('roupaPreview');
      prev.style.display = 'flex';
      prev.innerHTML = `
    <img src="img/${r.imagem_url || ''}" alt="${r.nome}"/>
    <div class="srp-info">
      <div class="srp-name">${r.nome}</div>
      ${r.preco ? `<div class="srp-price">R$ ${parseFloat(r.preco).toFixed(2).replace('.', ',')}</div>` : ''}
    </div>
  `;
      gerarSlots();
    }

    function gerarSlots() {
  const slots = ['09:00', '10:00', '11:00', '14:00', '15:00', '16:00', '17:00', '18:00'];
  const cont = document.getElementById('timeSlots');
  
  cont.innerHTML = slots.map(h => `
    <div class="time-slot ${state.agendamento.horario === h ? 'selected' : ''}"
         onclick="selecionarHorario(this, '${h}')">
      ${h}
    </div>
  `).join('');
}

    // Data mínima = hoje
    document.getElementById('dataAg').min = new Date().toISOString().split('T')[0];
    document.getElementById('dataAg').addEventListener('change', function () {
      state.agendamento.data = this.value;
      state.agendamento.horario = null;
      document.querySelectorAll('.time-slot').forEach(s => s.classList.remove('selected'));
      document.getElementById('btnStep2Next').disabled = true;
    });

   async function selecionarHorario(elemento, horario) {
    // AJUSTADO: Agora busca pelo ID correto 'dataAg' que você usa no seu código
    const campoData = document.getElementById('dataAg');
    const dataSelecionada = campoData ? campoData.value : '';

    if (!dataSelecionada) {
        toast('Por favor, selecione uma data primeiro.', 'error');
        return;
    }

    try {
        // Feedback visual de carregamento
        elemento.style.opacity = '0.5';

        // Consulta o servidor (Certifique-se que o AgendamentoController tem a action verificar_disponibilidade)
        const res = await fetch(`controllers/AgendamentoController.php?action=verificar_disponibilidade&data=${dataSelecionada}&horario=${horario}`);
        const resultado = await res.json();

        elemento.style.opacity = '1';

        if (!resultado.disponivel) {
            toast('Desculpe, este horário já está ocupado. Por favor, escolha outro.', 'error');
            return; 
        }

        // Se estiver livre, marca o botão como selecionado
        document.querySelectorAll('.time-slot').forEach(el => el.classList.remove('selected'));
        elemento.classList.add('selected');
        
        // Atualiza o estado global para que o passo 3 funcione
        state.agendamento.horario = horario;
        state.agendamento.data = dataSelecionada; // Sincroniza com a validação do irStep

        // Habilita o botão de "Próximo"
        document.getElementById('btnStep2Next').disabled = false;

    } catch (error) {
        elemento.style.opacity = '1';
        console.error('Erro ao verificar disponibilidade:', error);
        toast('Erro ao verificar horário. Tente novamente.', 'error');
    }
}
   
   /* ============================================================
   STEP 3 — Dados do cliente (BUSCA DINÂMICA)
   ============================================================ */
document.getElementById('agCpf').addEventListener('input', async function () {
    aplicarMascaraCpf(this);
    const c = this.value.replace(/\D/g, '');

    if (c.length === 11) {
        if (validarCpfJS(c)) {
            document.getElementById('erroAgCpf').classList.remove('show');

            try {
                const response = await fetch(`controllers/ClienteController.php?action=buscar&cpf=${c}`);
                const dados = await response.json();

                if (dados.success && dados.cliente) {
                    document.getElementById('agNome').value = dados.cliente.nome;
                    document.getElementById('agTel').value = dados.cliente.telefone;
                    document.getElementById('cliente_id').value = dados.cliente.id;
                    toast('Bem-vindo de volta! Seus dados foram preenchidos.', 'success');
                } else {
                    document.getElementById('agNome').value = '';
                    document.getElementById('agTel').value = '';
                    document.getElementById('cliente_id').value = '';
                }
            } catch (e) {
                console.error("Erro ao buscar cliente:", e);
            }

            // --- AQUI É O LUGAR CERTO DA GARANTIA ---
            const container = document.getElementById('dadosClienteExtra');
            container.style.display = 'block'; // Mostra o bloco
            container.style.visibility = 'visible';
            container.style.height = 'auto';
            container.style.opacity = '1';
            // ---------------------------------------

        } else {
            // Agora o 'else' está colado no 'if (validarCpfJS)' corretamente
            document.getElementById('erroAgCpf').classList.add('show');
        }
    } else {
        document.getElementById('erroAgCpf').classList.remove('show');
    }
});
    /* ============================================================
       FINALIZAR AGENDAMENTO
       ============================================================ */
       /*
    async function finalizarAgendamento() {
      const cpf = document.getElementById('agCpf').value.trim();
      const nome = document.getElementById('agNome').value.trim();
      const tel = document.getElementById('agTel').value.trim();

      // Validação simples
      if (!validarCpfJS(cpf)) {
        document.getElementById('erroAgCpf').classList.add('show');
        toast('CPF inválido.', 'error'); return;
      }

      if (!nome || nome.length < 3) { toast('Nome obrigatório.', 'error'); return; }
      if (tel.replace(/\D/g, '').length < 10) { toast('Telefone inválido.', 'error'); return; }

      const btn = document.getElementById('btnFinalizar');
      btn.disabled = true;
      btn.innerHTML = '<span class="spinner"></span> Confirmando…';

      // 1. Cadastra/recupera cliente
      const resCli = await api('controllers/ClienteController.php?action=cadastrar', {
        method: 'POST',
        body: JSON.stringify({ nome, cpf, telefone: tel }),
      });

      if (!resCli.ok || !resCli.data.success) {
        const erros = resCli.data.erros || [resCli.data.error || 'Erro ao identificar cliente.'];
        erros.forEach(e => toast(e, 'error'));
        btn.disabled = false; btn.textContent = 'Confirmar Agendamento'; return;
      }

      const cliente = resCli.data.cliente;

      // 2. Cria agendamento
      const resAg = await api('controllers/AgendamentoController.php?action=criar', {
        method: 'POST',
        body: JSON.stringify({
          cliente_id: cliente.id,
          roupa_id: state.agendamento.roupa_id,
          data_agendamento: state.agendamento.data,
          horario: state.agendamento.horario,
          observacoes: state.agendamento.observacoes,
        }),
      });

      btn.disabled = false; btn.textContent = 'Confirmar Agendamento';

      if (!resAg.ok || !resAg.data.success) {
        const erros = resAg.data.erros || [resAg.data.error || 'Erro ao criar agendamento.'];
        erros.forEach(e => toast(e, 'error')); return;
      }

      const ag = resAg.data.agendamento;
      exibirConfirmacao(ag, cliente);
    } 
      */

async function finalizarAgendamentoManual() {
  const form = document.querySelector('#step3 form');
  const formData = new FormData(form);

  const btn = document.getElementById('btnFinalizar');
  btn.disabled = true;
  btn.innerHTML = 'Confirmando...';

  // Enviamos o formData direto para o Controller
  const res = await fetch('controllers/AgendamentoController.php?action=agendar', {
    method: 'POST',
    body: formData 
  });

  const data = await res.json();

  if (res.ok && data.success) {
    exibirConfirmacao(data.agendamento, { nome: formData.get('nome') });
  } else {
    // --- MUDANÇA PARA EXIBIR O ERRO DO PHP ---
    // O Controller envia os erros dentro de uma array 'erros'
    const mensagemPersonalizada = (data.erros && data.erros.length > 0) ? data.erros[0] : 'Erro ao agendar';
    
    toast(mensagemPersonalizada, 'error');
    // -----------------------------------------

    btn.disabled = false;
    btn.textContent = 'Confirmar Agendamento';
  }
}
    function exibirConfirmacao(ag, cliente) {
      // Oculta steps
      document.querySelectorAll('.step-content, .steps').forEach(el => el.style.display = 'none');

      const data = ag.data_agendamento
        ? new Date(ag.data_agendamento + 'T00:00:00').toLocaleDateString('pt-BR', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })
        : '';

      document.getElementById('confirmRows').innerHTML = `
    <div class="confirm-row">
      <span class="label-sm">Cliente</span>
      <span class="val">${ag.cliente_nome || cliente.nome}</span>
    </div>
    <div class="confirm-row">
      <span class="label-sm">Peça Escolhida</span>
      <span class="val">${ag.roupa_nome || state.agendamento.roupa?.nome || '—'}</span>
    </div>
    <div class="confirm-row">
      <span class="label-sm">Data</span>
      <span class="val">${data}</span>
    </div>
    <div class="confirm-row">
      <span class="label-sm">Horário</span>
      <span class="val">${ag.horario ? ag.horario.slice(0, 5) : state.agendamento.horario}</span>
    </div>
    <div class="confirm-row">
      <span class="label-sm">Status</span>
      <span class="val" style="color:var(--success)">● Confirmado</span>
    </div>
    ${ag.observacoes ? `<div class="confirm-row"><span class="label-sm">Obs.</span><span class="val">${ag.observacoes}</span></div>` : ''}
  `;

      document.getElementById('confirmCard').classList.add('show');
      toast('Agendamento realizado com sucesso! 🎉', 'success', 5000);
    }

    function resetAgendamento() {
      state.agendamento = { roupa_id: null, roupa: null, data: null, horario: null, observacoes: '' };
      document.querySelectorAll('.step-content, .steps').forEach(el => el.style.removeProperty('display'));
      document.getElementById('confirmCard').classList.remove('show');
      document.getElementById('dataAg').value = '';
      document.getElementById('obsAg').value = '';
      document.getElementById('agCpf').value = '';
      document.getElementById('agNome').value = '';
      document.getElementById('agTel').value = '';
      document.getElementById('roupaPreview').style.display = 'none';
      document.getElementById('btnStep1Next').disabled = true;
      document.getElementById('btnStep2Next').disabled = true;
      document.getElementById('dadosClienteExtra').style.display = 'none';
      irStep(1);
      renderizarRoupasNoAgendamento();
    }

    /* ============================================================
       INIT
       ============================================================ */
    carregarCatalogo();

    // Função para abrir o modal de login
function abrirModalLogin() {
    const modal = document.getElementById('modalLoginOverlay');
    modal.classList.add('open');
    document.body.style.overflow = 'hidden'; // Impede o scroll do fundo
}

function fecharModalLogin(e) {
  // Se o clique foi no fundo escuro ou no botão X, ele fecha
  const modal = document.getElementById('modalLoginOverlay');
  
  if (!e || e.target === modal || e.target.classList.contains('modal-close')) {
    if (modal) {
      modal.style.display = 'none';
      // Se você usa classes CSS para abrir/fechar, remova-a também:
      modal.classList.remove('open');
    }
    // Devolve o scroll para a página
    document.body.style.overflow = '';
  }
}

// Troca a visualização para o formulário de Cadastro
function alternarParaCadastro() {
    document.getElementById('areaLogin').style.display = 'none';
    document.getElementById('areaCadastro').style.display = 'block';
}

// Troca a visualização para o formulário de Login
function alternarParaLogin() {
    document.getElementById('areaCadastro').style.display = 'none';
    document.getElementById('areaLogin').style.display = 'block';
}

function confirmarSair() {
    // Abre uma janelinha de confirmação no navegador
    if (confirm("Tem certeza que deseja sair?")) {
        // Se clicar em OK, ele redireciona para o arquivo que mata a sessão
        window.location.href = "logout.php";
    }
}

  </script>
  
  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

  <script>
    var swiper = new Swiper(".mySwiper", {
      loop: true,
      autoplay: { 
        delay: 3000,
        disableOnInteraction: false,
      },
      pagination: { 
        el: ".swiper-pagination", 
        clickable: true 
      },
    });
    
  </script>

  <div id="modalLoginOverlay" class="modal-overlay" onclick="fecharModalLogin(event)">
    <div class="modal-content login-card">
        <button class="modal-close" onclick="fecharModalLogin()">×</button>
        
        <div id="areaLogin">
            <h2 class="gold-title">Bem-vindo</h2>
            <p class="subtitle">Acesse sua conta para agendar serviços.</p>
            
            <form id="formLogin" action="controllers/AuthController.php" method="POST">
              <div class="input-group">
                  <label>CPF</label>
                  <input type="text" name="cpf" id="loginCpf" placeholder="000.000.000-00" required>
              </div>
              <div class="input-group">
                  <label>SENHA</label>
                  <input type="password" name="senha" id="loginSenha" placeholder="********" required>
              </div>
              <button type="submit" class="btn-gold">ENTRAR</button>
            </form>
            
            <p class="switch-text">Não tem conta? <a href="javascript:void(0)" onclick="alternarParaCadastro()">Cadastre-se agora</a></p>
        </div>

        <div id="areaCadastro" style="display: none;">
            <h2 class="gold-title">Crie seu cadastro</h2>
            <p class="subtitle">Registre-se para agendar seus serviços.</p>
            
            <form id="formCadastro" action="logica_acesso.php" method="POST">
    <input type="hidden" name="acao" value="cadastro">

    <div class="input-group">
        <label>NOME COMPLETO</label>
        <input type="text" name="nome" placeholder="Seu nome" required>
    </div>
    <div class="input-group">
        <label>CPF</label>
        <input type="text" name="cpf" id="cad-cpf" placeholder="000.000.000-00" required>
    </div>
    <div class="input-group">
        <label>TELEFONE</label>
        <input type="text" name="telefone" placeholder="(00) 00000-0000" required>
    </div>
    <div class="input-group">
        <label>SENHA</label>
        <input type="password" name="senha" placeholder="********" required>
    </div>
    <button type="submit" class="btn-gold">CRIAR CONTA</button>
</form>
            
            <p class="switch-text">Já tem conta? <a href="javascript:void(0)" onclick="alternarParaLogin()">Fazer Login</a></p>
        </div>
    </div>
</div>

</body>

</html>