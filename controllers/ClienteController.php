<?php
/**
 * Controller: Cliente
 * Recebe requisições da view, valida dados e aciona o model
 */

require_once __DIR__ . '/../models/ClienteModel.php';

class ClienteController {
    private ClienteModel $model;

    public function __construct() {
        $this->model = new ClienteModel();
    }

    /**
     * POST /controllers/ClienteController.php?action=cadastrar
     * Cadastra ou retorna cliente existente (por CPF)
     */
    public function cadastrar(): void {
        // Removido o header JSON para permitir o alerta em JavaScript
        
        $dados = $this->getPostJson();

        // --- Validação ---
        $erros = [];

        $nome = trim($dados['nome'] ?? '');
        if (empty($nome) || strlen($nome) < 3) {
            $erros[] = 'Nome completo é obrigatório (mínimo 3 caracteres).';
        }

        $cpf = $this->limparCpf($dados['cpf'] ?? '');
        if (!$this->validarCpf($cpf)) {
            $erros[] = 'CPF inválido.';
        }

        $telefone = preg_replace('/\D/', '', $dados['telefone'] ?? '');
        if (strlen($telefone) < 10 || strlen($telefone) > 11) {
            $erros[] = 'Telefone inválido.';
        }

        // Se houver erros, mostra o primeiro erro em um alerta e volta
        if ($erros) {
            echo "<script>
                    alert('" . $erros[0] . "');
                    window.history.back();
                  </script>";
            return;
        }

        $cpfFormatado = $this->formatarCpf($cpf);

        // Verifica se CPF já existe no banco 'moda'
        $existente = $this->model->buscarPorCpf($cpfFormatado);
        if ($existente) {
            echo "<script>
                    alert('Bem-vindo de volta! Identificamos seu cadastro.');
                    window.location.href = '../index.html#agendamento'; 
                  </script>";
            return;
        }

        // Cria o novo cliente usando o Model
        $id = $this->model->criar([
            'nome'     => $nome,
            'cpf'      => $cpfFormatado,
            'telefone' => $telefone,
        ]);

        // Redirecionamento de sucesso para o fluxo do site
        echo "<script>
                alert('Cadastro realizado com sucesso!');
                window.location.href = '../index.html#agendamento'; 
              </script>";
    }

    // ---- Helpers ----

    private function getPostJson(): array {
        $raw = file_get_contents('php://input');
        $dados = json_decode($raw, true);
        return is_array($dados) ? $dados : $_POST;
    }

    private function limparCpf(string $cpf): string {
        return preg_replace('/\D/', '', $cpf);
    }

    private function formatarCpf(string $cpf): string {
        return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpf);
    }

    private function validarCpf(string $cpf): bool {
        if (strlen($cpf) !== 11 || preg_match('/^(\d)\1{10}$/', $cpf)) return false;

        for ($t = 9; $t <= 10; $t++) {
            $sum = 0;
            for ($i = 0; $i < $t; $i++) {
                $sum += $cpf[$i] * ($t + 1 - $i);
            }
            $r = ((10 * $sum) % 11) % 10;
            if ($cpf[$t] != $r) return false;
        }
        return true;
    }
}

// --- Roteamento simples ---
$ctrl = new ClienteController();
$action = $_GET['action'] ?? $_POST['action'] ?? '';

match ($action) {
    'cadastrar' => $ctrl->cadastrar(),
    default     => (function () {
        http_response_code(400);
        echo "Ação inválida.";
    })()
};