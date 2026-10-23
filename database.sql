CREATE DATABASE IF NOT EXISTS `banco_tcc`
    DEFAULT CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `banco_tcc`;

CREATE TABLE IF NOT EXISTS `usuarios` (
    `id`            INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `nome`          VARCHAR(150)    NOT NULL,
    `email`         VARCHAR(190)    NOT NULL,
    `senha`         VARCHAR(255)    NULL        DEFAULT NULL,
    `google_id`     VARCHAR(100)    NULL        DEFAULT NULL,
    `foto_perfil`   VARCHAR(255)    NULL        DEFAULT NULL,
    `banner_perfil` VARCHAR(255)    NULL        DEFAULT NULL,
    `nivel`         INT UNSIGNED    NOT NULL    DEFAULT 1,
    `pontos`        INT UNSIGNED    NOT NULL    DEFAULT 0,
    `criado_em`     TIMESTAMP       NOT NULL    DEFAULT CURRENT_TIMESTAMP,
    `atualizado_em` TIMESTAMP       NOT NULL    DEFAULT CURRENT_TIMESTAMP
                                                ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_email`     (`email`),
    UNIQUE KEY `uq_google_id` (`google_id`)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `historico_partidas` (
    `id`                   INT UNSIGNED      NOT NULL AUTO_INCREMENT,
    `usuario_id`           INT UNSIGNED      NOT NULL,
    `modo_jogo`            VARCHAR(100)      NOT NULL,
    `resultado`            VARCHAR(50)       NOT NULL   DEFAULT 'Concluído',
    `pontos_ganhos`        INT UNSIGNED      NOT NULL   DEFAULT 0,
    `precisao_porcentagem` TINYINT UNSIGNED  NOT NULL   DEFAULT 0,
    `acertos`              SMALLINT UNSIGNED NOT NULL   DEFAULT 0,
    `total_questoes`       SMALLINT UNSIGNED NOT NULL   DEFAULT 0,
    `data_partida`         TIMESTAMP         NOT NULL   DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_historico_usuario`
        FOREIGN KEY (`usuario_id`)
        REFERENCES `usuarios` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    INDEX `idx_usuario_data` (`usuario_id`, `data_partida` DESC)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `usuarios` (`nome`, `email`, `senha`, `nivel`, `pontos`) VALUES
(
    'Pedro Demo',
    'demo@thematematicalgame.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    3,
    2750
);

INSERT IGNORE INTO `historico_partidas`
    (`usuario_id`, `modo_jogo`, `resultado`, `pontos_ganhos`, `precisao_porcentagem`, `acertos`, `total_questoes`)
VALUES
    (1, 'Desafio Matemático', 'Vitória',   150, 80, 8, 10),
    (1, 'Contra o Tempo',    'Vitória',   200, 90, 9, 10),
    (1, 'Desafio Matemático', 'Derrota',   50, 40, 4, 10),
    (1, 'Tabuada Rápida',    'Concluído', 100, 70, 7, 10);

CREATE OR REPLACE VIEW `vw_ranking` AS
    SELECT
        `id`,
        `nome`,
        `foto_perfil`,
        `nivel`,
        `pontos`,
        RANK() OVER (ORDER BY `pontos` DESC) AS `posicao`
    FROM `usuarios`
    ORDER BY `pontos` DESC;

CREATE OR REPLACE VIEW `vw_estatisticas_usuario` AS
    SELECT
        u.`id`                                                AS usuario_id,
        u.`nome`,
        COUNT(h.`id`)                                         AS total_partidas,
        COALESCE(SUM(h.`pontos_ganhos`), 0)                  AS total_pontos_partidas,
        COALESCE(ROUND(AVG(h.`precisao_porcentagem`), 1), 0) AS media_precisao,
        COALESCE(SUM(h.`acertos`), 0)                        AS total_acertos,
        COALESCE(SUM(h.`total_questoes`), 0)                 AS total_questoes
    FROM `usuarios` u
    LEFT JOIN `historico_partidas` h ON h.`usuario_id` = u.`id`
    GROUP BY u.`id`, u.`nome`;

-- =============================================================================
-- MIGRAÇÃO / MANUTENÇÃO (Para bancos já existentes com contas duplicadas):
-- Caso a tabela `usuarios` já existia antes e acumulou registros repetidos:
--
-- 1. Remove duplicatas mantendo a conta mais recente (ou com maior ID):
-- DELETE u1 FROM usuarios u1
-- INNER JOIN usuarios u2 
-- WHERE u1.id < u2.id AND LOWER(u1.email) = LOWER(u2.email);
--
-- 2. Garante a restrição UNIQUE na coluna `email`:
-- ALTER TABLE `usuarios` ADD UNIQUE KEY `uq_email` (`email`);
-- =============================================================================