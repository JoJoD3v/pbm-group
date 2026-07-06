-- Tabelle per la feature "Chiusura del giorno"
-- Import manuale su MySQL. Richiede tabelle `users` e `workers` gia' presenti.

CREATE TABLE `chiusure_giorno` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `data_chiusura` DATE NOT NULL,
    `created_by` BIGINT UNSIGNED NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    PRIMARY KEY (`id`),
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `chiusura_giorno_righe` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `chiusura_giorno_id` BIGINT UNSIGNED NOT NULL,
    `worker_id` BIGINT UNSIGNED NOT NULL,
    `apertura_fondo_cassa` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `apertura_carta` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `chiusura_fondo_cassa` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `chiusura_carta` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    PRIMARY KEY (`id`),
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
