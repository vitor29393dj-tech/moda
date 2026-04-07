<?php
/**
 * Model: Roupa
 * Operações de banco de dados relacionadas ao catálogo de roupas
 */

require_once __DIR__ . '/../config/database.php';

class RoupaModel {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /** Retorna todas as roupas ativas. */
    public function listarAtivas(): array {
        return $this->db->query(
            "SELECT * FROM roupas WHERE ativo = 1 ORDER BY categoria, nome"
        )->fetchAll();
    }

    /** Busca roupa por ID. */
    public function buscarPorId(int $id): array|false {
        $stmt = $this->db->prepare(
            "SELECT * FROM roupas WHERE id = :id AND ativo = 1 LIMIT 1"
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    /** Lista categorias únicas. */
    public function listarCategorias(): array {
        return $this->db->query(
            "SELECT DISTINCT categoria FROM roupas WHERE ativo = 1 AND categoria IS NOT NULL ORDER BY categoria"
        )->fetchAll(PDO::FETCH_COLUMN);
    }
}
