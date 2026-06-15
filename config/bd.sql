CREATE TABLE pizzas (
    idPizza INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL,
    ingredientes VARCHAR(255) NOT NULL,
    valor DECIMAL(10, 2) NOT NULL
);
 
INSERT INTO pizzas (nome, ingredientes, valor) VALUES
('Calabresa', 'Mussarela, calabresa fatiada e cebola', 45.50),
('Mussarela', 'Mussarela e molho de tomate', 40.00),
('Frango com Catupiry', 'Frango desfiado, catupiry e mussarela', 52.90),
('Portuguesa', 'Mussarela, presunto, ovo, ervilha, cebola e calabresa', 62.90),
('Moda do Juca', 'Mussarela, peito de peru, palmito, alho poró e alcaparras', 72.50);
 
SELECT * FROM pizzas

CREATE TABLE bebidas (
    idBebida INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL,
    alcoolica BIT NOT NULL,
    valor DECIMAL(10, 2) NOT NULL
);

INSERT INTO bebidas (nome, alcoolica, valor) VALUES
('Coca-Cola', 0, 7.50),
('Guaraná', 0, 6.50),
('Suco de Laranja', 0, 8.00),
('Água Mineral', 0, 4.00),
('Água com Gás', 0, 4.50),
('Chá Gelado', 0, 6.00),
('Refrigerante Zero', 0, 7.00),

('Cerveja Pilsen', 1, 9.00),
('Cerveja IPA', 1, 12.50),
('Cerveja Artesanal', 1, 15.00),
('Vinho Tinto', 1, 18.00),
('Vinho Branco', 1, 17.50),
('Caipirinha', 1, 14.00),
('Whisky Dose', 1, 20.00),
('Vodka Dose', 1, 18.00),
('Gin Tônica', 1, 16.00);

SELECT * FROM bebidas