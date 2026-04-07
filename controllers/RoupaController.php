<?php
/**
 * Controller: Roupa
 * Fornece dados do catálogo de roupas em JSON
 */

require_once __DIR__ . '/../models/RoupaModel.php';

class RoupaController {
    private RoupaModel $model;

    public function __construct() {
        $this->model = new RoupaModel();
    }

    /** GET ?action=listar — retorna todas as roupas ativas */
    public function listar(): void {
        header('Content-Type: application/json; charset=utf-8');
        $roupas = $this->model->listarAtivas();
        echo json_encode(['success' => true, 'roupas' => $roupas]);
    }

    /** GET ?action=detalhe&id=X — retorna uma roupa específica */
    public function detalhe(): void {
        header('Content-Type: application/json; charset=utf-8');
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'ID inválido.']);
            return;
        }

        $roupa = $this->model->buscarPorId($id);
        if (!$roupa) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Peça não encontrada.']);
            return;
        }

        echo json_encode(['success' => true, 'roupa' => $roupa]);
    }
}

// Roteamento
$ctrl = new RoupaController();
$action = $_GET['action'] ?? '';

match ($action) {
    'listar'  => $ctrl->listar(),
    'detalhe' => $ctrl->detalhe(),
    default   => (function () {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(400);
        echo json_encode(['error' => 'Ação inválida.']);
    })()
};
