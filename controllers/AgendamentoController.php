<?php
session_start(); // ADICIONADO: Necessário para acessar o ID do usuário logado

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
     * Nova função para verificar disponibilidade em tempo real
     */
    public function verificarDisponibilidade(): void {
        header('Content-Type: application/json; charset=utf-8');
        
        $data = $_GET['data'] ?? '';
        $horario = $_GET['horario'] ?? '';

        // Validação básica dos dados recebidos via GET
        if (empty($data) || empty($horario)) {
            echo json_encode(['disponivel' => false, 'erro' => 'Dados incompletos']);
            return;
        }

        // Chama o model que já configuramos para checar a agenda geral
        $disponivel = $this->model->horarioDisponivel($data, $horario . ':00');
        
        echo json_encode(['disponivel' => $disponivel]);
    }

    // ... abaixo seguem suas funções criar() e buscar() ...

    /**
     * POST ?action=criar
     * Cria um agendamento após validações
     */
    public function criar(): void {
        header('Content-Type: application/json; charset=utf-8');

        $raw   = file_get_contents('php://input');
        $dados = json_decode($raw, true) ?? $_POST;

        $erros = [];

        // --- ALTERAÇÃO AQUI: Vínculo Dinâmico com a Sessão ---
        // Em vez de pegar do formulário, pegamos o ID de quem está logado
        if (!isset($_SESSION['logado'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'erros' => ['Você precisa estar logado para agendar.']]);
            return;
        }
        $clienteId = $_SESSION['usuario_id']; 
        // ---------------------------------------------------

        // Valida se o cliente da sessão ainda existe no banco
        if (!$this->clienteModel->buscarPorId($clienteId)) {
            $erros[] = 'Sessão inválida. Faça login novamente.';
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

       // --- VERIFICAÇÃO DE DISPONIBILIDADE GERAL ---
// Removi o $roupaId da função para verificar se o horário está ocupado por QUALQUER agendamento
if (!$this->model->horarioDisponivel($data, $horario . ':00')) {
    http_response_code(409);
    echo json_encode([
        'success' => false,
        'erros'   => ['Desculpe, este horário já está ocupado. Por favor, escolha outro.'],
    ]);
    return;
}

        $id = $this->model->criar([
            'cliente_id'      => $clienteId, // Agora usa o ID correto da sessão
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
    'agendar' => $ctrl->criar(),
    'criar'   => $ctrl->criar(),
    'buscar'  => $ctrl->buscar(),
    // ADICIONAMOS ESTA NOVA ROTA ABAIXO
    'verificar_disponibilidade' => (function() use ($ctrl) {
        header('Content-Type: application/json; charset=utf-8');
        $data = $_GET['data'] ?? '';
        $horario = $_GET['horario'] ?? '';

        // Usamos o método que já ajustamos no AgendamentoModel anteriormente
        $res = new AgendamentoModel();
        $disponivel = $res->horarioDisponivel($data, $horario . ':00');
        
        echo json_encode(['disponivel' => $disponivel]);
    })(),
    default  => (function () {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(400);
        echo json_encode(['error' => 'Ação inválida.']);
    })()
};