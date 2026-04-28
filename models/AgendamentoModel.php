<?php
/**
 * Model: Agendamento
 * Operações de banco relacionadas ao sistema de agendamentos
 */

require_once __DIR__ . '/../config/database.php';

class AgendamentoModel {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /** Cria um novo agendamento. Retorna o ID inserido. */
    public function criar(array $dados): int {
        $sql = "INSERT INTO agendamentos
                    (cliente_id, roupa_id, data_agendamento, horario, observacoes)
                VALUES
                    (:cliente_id, :roupa_id, :data, :horario, :obs)";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':cliente_id' => $dados['cliente_id'],
            ':roupa_id'   => $dados['roupa_id'],
            ':data'       => $dados['data_agendamento'],
            ':horario'    => $dados['horario'],
            ':obs'        => $dados['observacoes'] ?? null,
        ]);

        return (int) $this->db->lastInsertId();
    }

   /** * Verifica se o horário está ocupado por QUALQUER agendamento no atelier.
 */
public function horarioDisponivel(string $data, string $horario): bool {
    $stmt = $this->db->prepare(
        "SELECT COUNT(*) FROM agendamentos 
         WHERE data_agendamento = :data 
           AND horario = :horario 
           AND status != 'cancelado'"
    );
    $stmt->execute([
        ':data'    => $data, 
        ':horario' => $horario
    ]);
    
    // Se o contador for 0, o horário está livre
    return $stmt->fetchColumn() == 0;
}

    /** Busca agendamento com dados completos pelo ID. */
    public function buscarPorId(int $id): array|false {
        $sql = "SELECT
                    a.id, a.data_agendamento, a.horario, a.status, a.observacoes, a.criado_em,
                    c.nome AS cliente_nome, c.cpf AS cliente_cpf, c.telefone AS cliente_telefone,
                    r.nome AS roupa_nome, r.preco AS roupa_preco, r.imagem_url AS roupa_imagem
                FROM agendamentos a
                JOIN clientes c ON c.id = a.cliente_id
                JOIN roupas   r ON r.id = a.roupa_id
                WHERE a.id = :id
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    /** Lista agendamentos de um cliente. */
    public function listarPorCliente(int $cliente_id): array {
        $sql = "SELECT
                    a.id, a.data_agendamento, a.horario, a.status,
                    r.nome AS roupa_nome, r.imagem_url AS roupa_imagem
                FROM agendamentos a
                JOIN roupas r ON r.id = a.roupa_id
                WHERE a.cliente_id = :cid
                ORDER BY a.data_agendamento DESC, a.horario DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':cid' => $cliente_id]);
        return $stmt->fetchAll();
    }
}
