<?php
/**
 * Model: Cliente
 * Responsável pelas operações de banco de dados relacionadas a clientes
 */

require_once __DIR__ . '/../config/database.php';

class ClienteModel {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /** Cadastra um novo cliente. Retorna o ID inserido. */
    public function criar(array $dados): int {
        $sql = "INSERT INTO clientes (nome, cpf, telefone)
                VALUES (:nome, :cpf, :telefone)";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':nome'     => trim($dados['nome']),
            ':cpf'      => $dados['cpf'],
            ':telefone' => $dados['telefone'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    /** Busca um cliente pelo CPF. */
    public function buscarPorCpf(string $cpf): array|false {
        $stmt = $this->db->prepare(
            "SELECT * FROM clientes WHERE cpf = :cpf LIMIT 1"
        );
        $stmt->execute([':cpf' => $cpf]);
        return $stmt->fetch();
    }

    /** Busca um cliente pelo ID. */
    public function buscarPorId(int $id): array|false {
        $stmt = $this->db->prepare(
            "SELECT * FROM clientes WHERE id = :id LIMIT 1"
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    /** Lista todos os clientes. */
    public function listarTodos(): array {
        return $this->db->query(
            "SELECT * FROM clientes ORDER BY nome ASC"
        )->fetchAll();
    }
}
