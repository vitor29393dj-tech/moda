<?php
/**
 * Controller: Agendamento
 * Gerencia criação e consulta de agendamentos
 */

require_once __DIR__ . '/../models/AgendamentoModel.php';
require_once __DIR__ . '/../models/ClienteModel.php';
require_once __DIR__ . '/../models/RoupaModel.php';

class AgendamentoController {
    private AgendamentoModel $model;
    private ClienteModel $clienteModel;
    private RoupaModel $roupaModel;

    public function __construct() {
        $this->model        = new AgendamentoModel();
        $this->clienteModel = new ClienteModel();
        $this->roupaModel   = new RoupaModel();
    }

    /**
     * POST ?action=criar
     * Cria um agendamento após validações
     */
    public function criar(): void {
        header('Content-Type: application/json; charset=utf-8');

        $raw   = file_get_contents('php://input');
        $dados = json_decode($raw, true) ?? $_POST;

        $erros = [];

        // Valida cliente
        $clienteId = filter_var($dados['cliente_id'] ?? 0, FILTER_VALIDATE_INT);
        if (!$clienteId || !$this->clienteModel->buscarPorId($clienteId)) {
            $erros[] = 'Cliente inválido ou não encontrado.';
        }

        // Valida roupa
        $roupaId = filter_var($dados['roupa_id'] ?? 0, FILTER_VALIDATE_INT);
        if (!$roupaId || !$this->roupaModel->buscarPorId($roupaId)) {
            $erros[] = 'Peça inválida ou não encontrada.';
        }

        // Valida data (deve ser hoje ou futura)
        $data = $dados['data_agendamento'] ?? '';
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data) || strtotime($data) < strtotime('today')) {
            $erros[] = 'Data inválida. Escolha hoje ou uma data futura.';
        }

        // Valida horário
        $horario = $dados['horario'] ?? '';
        if (!preg_match('/^\d{2}:\d{2}$/', $horario)) {
            $erros[] = 'Horário inválido.';
        }

        if ($erros) {
            http_response_code(422);
            echo json_encode(['success' => false, 'erros' => $erros]);
            return;
        }

        // Verifica disponibilidade
        if (!$this->model->horarioDisponivel($data, $horario . ':00', $roupaId)) {
            http_response_code(409);
            echo json_encode([
                'success' => false,
                'erros'   => ['Este horário já está reservado para esta peça. Escolha outro horário.'],
            ]);
            return;
        }

        $id = $this->model->criar([
            'cliente_id'      => $clienteId,
            'roupa_id'        => $roupaId,
            'data_agendamento' => $data,
            'horario'         => $horario . ':00',
            'observacoes'     => trim($dados['observacoes'] ?? ''),
        ]);

        $agendamento = $this->model->buscarPorId($id);

        echo json_encode([
            'success'     => true,
            'message'     => 'Agendamento realizado com sucesso!',
            'agendamento' => $agendamento,
        ]);
    }

    /**
     * GET ?action=buscar&id=X
     * Retorna detalhes de um agendamento
     */
    public function buscar(): void {
        header('Content-Type: application/json; charset=utf-8');
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'ID inválido.']);
            return;
        }

        $ag = $this->model->buscarPorId($id);
        if (!$ag) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Agendamento não encontrado.']);
            return;
        }

        echo json_encode(['success' => true, 'agendamento' => $ag]);
    }
}

// Roteamento
$ctrl   = new AgendamentoController();
$action = $_GET['action'] ?? ($_POST['action'] ?? '');

match ($action) {
    'agendar' => $ctrl->criar(), // ADICIONE ESTA LINHA: Agora ele aceita seu HTML
    'criar'   => $ctrl->criar(), // Mantemos esta para não quebrar outras partes
    'buscar'  => $ctrl->buscar(),
    default  => (function () {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(400);
        echo json_encode(['error' => 'Ação inválida.']);
    })()
};
