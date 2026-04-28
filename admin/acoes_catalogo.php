<?php
session_start();
require_once '../config/database.php';

$seuCpfAdmin = '71590928563';

if (!isset($_SESSION['logado']) || $_SESSION['usuario_cpf'] !== $seuCpfAdmin) {
    header('Location: ../index.php');
    exit;
}

try {
    $pdo = Database::getInstance()->getConnection();

    $id   = intval($_GET['id'] ?? 0);
    $acao = $_GET['acao'] ?? '';

    if ($id && $acao) {

        // ── TOGGLE DE DISPONIBILIDADE ──
        if ($acao === 'toggle') {
            $stmt = $pdo->prepare("SELECT status_estoque FROM roupas WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $peca = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($peca) {
                $novoStatus = ($peca['status_estoque'] === 'disponivel') ? 'indisponivel' : 'disponivel';
                $upd = $pdo->prepare("UPDATE roupas SET status_estoque = :novo WHERE id = :id");
                $upd->execute(['novo' => $novoStatus, 'id' => $id]);
            }

            header('Location: dashboard.php?secao=catalogo');
            exit;
        }

        // ── EXCLUIR PEÇA ──
        if ($acao === 'excluir') {
            // Busca a imagem antes de deletar para removê-la do servidor
            $stmt = $pdo->prepare("SELECT imagem_url FROM roupas WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $peca = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($peca) {
                // Remove do banco
                $del = $pdo->prepare("DELETE FROM roupas WHERE id = :id");
                $del->execute(['id' => $id]);

                // Remove o arquivo de imagem do servidor (se existir)
                $caminhoImagem = '../img/' . $peca['imagem_url'];
                if (!empty($peca['imagem_url']) && file_exists($caminhoImagem)) {
                    unlink($caminhoImagem);
                }
            }

            header('Location: dashboard.php?secao=catalogo&status=excluido');
            exit;
        }
    }

    // Fallback: volta para o catálogo se nenhuma ação válida
    header('Location: dashboard.php?secao=catalogo');
    exit;

} catch (PDOException $e) {
    die("Erro ao processar alteração: " . $e->getMessage());
}