-- ============================================================
-- Atelier DB - Script de Criação do Banco de Dados
-- Sistema de Catálogo e Agendamentos de Moda
-- ============================================================

CREATE DATABASE IF NOT EXISTS atelier_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE atelier_db;

-- ------------------------------------------------------------
-- Tabela: clientes
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS clientes (
    id          INT UNSIGNED    AUTO_INCREMENT PRIMARY KEY,
    nome        VARCHAR(120)    NOT NULL,
    cpf         CHAR(14)        NOT NULL UNIQUE,   -- formato: 000.000.000-00
    telefone    VARCHAR(20)     NOT NULL,
    criado_em   TIMESTAMP       DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- Tabela: roupas
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS roupas (
    id          INT UNSIGNED    AUTO_INCREMENT PRIMARY KEY,
    nome        VARCHAR(120)    NOT NULL,
    descricao   TEXT,
    preco       DECIMAL(10,2),
    imagem_url  VARCHAR(255),
    categoria   VARCHAR(60),
    ativo       TINYINT(1)      DEFAULT 1,
    criado_em   TIMESTAMP       DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- Tabela: agendamentos
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS agendamentos (
    id              INT UNSIGNED    AUTO_INCREMENT PRIMARY KEY,
    cliente_id      INT UNSIGNED    NOT NULL,
    roupa_id        INT UNSIGNED    NOT NULL,
    data_agendamento DATE           NOT NULL,
    horario         TIME            NOT NULL,
    status          ENUM('pendente','confirmado','cancelado') DEFAULT 'pendente',
    observacoes     TEXT,
    criado_em       TIMESTAMP       DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_agend_cliente FOREIGN KEY (cliente_id)
        REFERENCES clientes(id) ON DELETE CASCADE ON UPDATE CASCADE,

    CONSTRAINT fk_agend_roupa  FOREIGN KEY (roupa_id)
        REFERENCES roupas(id)   ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- Dados de Exemplo — Catálogo
-- ------------------------------------------------------------
INSERT INTO roupas (nome, descricao, preco, imagem_url, categoria) VALUES
('Vestido Midi Floral',    'Vestido com estampa floral delicada, tecido leve ideal para o verão. Caimento perfeito.',           189.90, 'https://images.unsplash.com/photo-1595777457583-95e059d581b8?w=600&q=80', 'Vestidos'),
('Blazer Estruturado',     'Blazer de alfaiataria com estrutura firme. Ideal para looks formais e casuais sofisticados.',        349.00, 'https://images.unsplash.com/photo-1594938298603-c8148c4b984e?w=600&q=80', 'Blazers'),
('Calça Wide Leg',         'Calça de cintura alta com perna larga. Conforto e estilo em qualquer ocasião.',                    229.90, 'https://images.unsplash.com/photo-1509551388413-e18d0ac5d495?w=600&q=80', 'Calças'),
('Blusa de Seda',          'Blusa com tecido acetinado e caimento fluido. Elegância em cada detalhe.',                         159.00, 'https://images.unsplash.com/photo-1485231183945-fffde7a3b9f3?w=600&q=80', 'Blusas'),
('Saia Plissada',          'Saia midi plissada em tons neutros. Versatilidade para compor looks do dia a dia ao jantar.',      199.00, 'https://images.unsplash.com/photo-1583496661160-fb5a870b3b80?w=600&q=80', 'Saias'),
('Conjunto Linho',         'Conjunto de calça e blusa em linho puro. Frescor e sofisticação para o calor.',                   289.00, 'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=600&q=80', 'Conjuntos'),
('Vestido Slip Dress',     'Vestido estilo slip com alças finas e tecido acetinado. Minimalismo moderno.',                     219.00, 'https://images.unsplash.com/photo-1502716119720-b23a93e5fe1b?w=600&q=80', 'Vestidos'),
('Camisa Oversized',       'Camisa ampla em algodão de alta gramatura. Conforto com personalidade.',                           139.90, 'https://images.unsplash.com/photo-1434389677669-e08b4cac3105?w=600&q=80', 'Camisas');
