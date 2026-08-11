-- =============================================================
-- SmartMarket Audit - Estrutura do banco de dados
-- -------------------------------------------------------------
-- Este arquivo contém APENAS a estrutura (sem dados).
-- Nenhum dado real de mercado é publicado aqui.
--
-- Para importar no MySQL:
--   mysql -u root -p < database/schema.sql
-- (ou importe pelo phpMyAdmin: Importar -> selecionar o arquivo)
-- =============================================================

CREATE DATABASE IF NOT EXISTS auditoria_mercado
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

USE auditoria_mercado;

-- Remove as tabelas antigas (ordem respeita as chaves estrangeiras)
DROP TABLE IF EXISTS auditoria_itens;
DROP TABLE IF EXISTS auditoria_camera;
DROP TABLE IF EXISTS vendas_importadas;
DROP TABLE IF EXISTS vendas_sistema;
DROP TABLE IF EXISTS produtos;
DROP TABLE IF EXISTS unidades;

-- -------------------------------------------------------------
-- Unidades de autoatendimento (ex.: condomínios/mercados)
-- -------------------------------------------------------------
CREATE TABLE unidades (
  id INT NOT NULL AUTO_INCREMENT,
  nome_condominio VARCHAR(100) DEFAULT NULL,
  endereco TEXT DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -------------------------------------------------------------
-- Catálogo de produtos do mercado
-- -------------------------------------------------------------
CREATE TABLE produtos (
  id INT NOT NULL AUTO_INCREMENT,
  nome VARCHAR(100) DEFAULT NULL,
  preco DECIMAL(10,2) DEFAULT NULL,
  categoria VARCHAR(50) DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -------------------------------------------------------------
-- Vendas importadas da planilha (fiscal / cupom da compra)
-- -------------------------------------------------------------
CREATE TABLE vendas_importadas (
  id INT NOT NULL AUTO_INCREMENT,
  data DATE NOT NULL,
  hora TIME NOT NULL,
  produto VARCHAR(255) NOT NULL,
  produto_id INT DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -------------------------------------------------------------
-- Vendas registradas no sistema (POS / caixa)
-- -------------------------------------------------------------
CREATE TABLE vendas_sistema (
  id INT NOT NULL AUTO_INCREMENT,
  unidade_id INT DEFAULT NULL,
  produto_id INT DEFAULT NULL,
  data_hora DATETIME DEFAULT NULL,
  valor_pago DECIMAL(10,2) DEFAULT NULL,
  PRIMARY KEY (id),
  KEY unidade_id (unidade_id),
  KEY produto_id (produto_id),
  CONSTRAINT vendas_sistema_ibfk_1 FOREIGN KEY (unidade_id) REFERENCES unidades (id),
  CONSTRAINT vendas_sistema_ibfk_2 FOREIGN KEY (produto_id) REFERENCES produtos (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -------------------------------------------------------------
-- Auditorias feitas pelas câmeras
-- -------------------------------------------------------------
CREATE TABLE auditoria_camera (
  id INT NOT NULL AUTO_INCREMENT,
  unidade_id INT DEFAULT NULL,
  data_hora_video DATETIME DEFAULT NULL,
  status VARCHAR(20) DEFAULT NULL,
  observacao TEXT DEFAULT NULL,
  PRIMARY KEY (id),
  KEY unidade_id (unidade_id),
  CONSTRAINT auditoria_camera_ibfk_1 FOREIGN KEY (unidade_id) REFERENCES unidades (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -------------------------------------------------------------
-- Itens (produtos) de cada auditoria de câmera
-- -------------------------------------------------------------
CREATE TABLE auditoria_itens (
  id INT NOT NULL AUTO_INCREMENT,
  auditoria_id INT NOT NULL,
  produto_id INT NOT NULL,
  quantidade INT NOT NULL,
  PRIMARY KEY (id),
  KEY fk_auditoria (auditoria_id),
  KEY fk_produto (produto_id),
  CONSTRAINT fk_auditoria FOREIGN KEY (auditoria_id) REFERENCES auditoria_camera (id),
  CONSTRAINT fk_produto FOREIGN KEY (produto_id) REFERENCES produtos (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
