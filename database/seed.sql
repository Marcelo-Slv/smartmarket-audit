-- =============================================================
-- SmartMarket Audit - Dados FICTÍCIOS para teste
-- -------------------------------------------------------------
-- ATENÇÃO: tudo aqui é inventado. Nenhum dado real de mercado.
-- Rode SOMENTE depois do schema.sql.
--
--   mysql -u root -p < database/schema.sql
--   mysql -u root -p < database/seed.sql
--
-- As vendas importadas (vendas_importadas) e as auditorias são
-- criadas pelo próprio sistema, pela tela "Importar Planilha".
-- =============================================================

USE auditoria_mercado;

INSERT INTO unidades (nome_condominio, endereco) VALUES
('Residencial Aurora', 'Rua das Flores, 123 - Centro'),
('Edifício Solar', 'Av. Paulista, 1000 - Bela Vista'),
('Condomínio Horizonte', 'Rua do Sol, 45 - Jardim América');

INSERT INTO produtos (nome, preco, categoria) VALUES
('Arroz 5kg', 24.90, 'Mercearia'),
('Feijão 1kg', 8.50, 'Mercearia'),
('Coca-Cola 2L', 11.99, 'Bebidas'),
('Leite Integral 1L', 5.79, 'Laticínios'),
('Pão de Forma', 9.90, 'Padaria'),
('Detergente 500ml', 2.99, 'Limpeza'),
('Sabonete', 1.79, 'Higiene'),
('Salgadinho 250g', 8.99, 'Mercearia');
