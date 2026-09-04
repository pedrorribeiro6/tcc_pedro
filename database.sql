CREATE DATABASE IF NOT EXISTS `the_mathematical_game` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `the_mathematical_game`;

-- Tabela de Usuários com suporte a Login Tradicional e Google OAuth
CREATE TABLE IF NOT EXISTS `usuarios` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nome` VARCHAR(150) NOT NULL,
    `email` VARCHAR(190) NOT NULL UNIQUE,
    `senha` VARCHAR(255) NULL, -- Nulo quando o login for exclusivamente via Google
    `google_id` VARCHAR(100) NULL UNIQUE, -- ID único retornado pela Google API
    `foto_perfil` VARCHAR(255) NULL,
    `nivel` INT DEFAULT 1,
    `pontos` INT DEFAULT 0,
    `criado_em` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `atualizado_em` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de Histórico de Partidas
CREATE TABLE IF NOT EXISTS `historico_partidas` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `usuario_id` INT NOT NULL,
    `modo_jogo` VARCHAR(100) NOT NULL,
    `resultado` VARCHAR(50) NOT NULL,
    `pontos_ganhos` INT DEFAULT 0,
    `precisao_porcentagem` INT DEFAULT 0,
    `acertos` INT DEFAULT 0,
    `total_questoes` INT DEFAULT 0,
    `data_partida` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

