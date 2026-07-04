-- Tabelle Appaltatori, Lavoro Servizi, Borderò (MySQL)
-- Migration di riferimento:
--   2026_07_04_100000_create_bordero_tables.php
--   2026_07_04_100001_add_appaltatore_to_works_and_work_servizi.php (rinominata; vedi note sotto)
--   2026_07_05_100000_create_appaltatori_table.php
--   2026_07_05_100001_add_appaltatore_to_works_and_work_servizi.php
--
-- Da eseguire dopo che `works`, `customers`, `workers`, `services` esistono già.

-- =========================================================
-- Borderò
-- =========================================================

CREATE TABLE `pezzi_bordero` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nome_pezzo` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `pezzi_bordero_nome_pezzo_unique` (`nome_pezzo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `bordero` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `work_id` BIGINT UNSIGNED NOT NULL,
    `worker_id` BIGINT UNSIGNED NULL,
    `status` VARCHAR(255) NOT NULL DEFAULT 'In Sospeso',
    `note_tecniche` TEXT NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `bordero_work_id_unique` (`work_id`),
    CONSTRAINT `bordero_work_id_foreign` FOREIGN KEY (`work_id`) REFERENCES `works` (`id`) ON DELETE CASCADE,
    CONSTRAINT `bordero_worker_id_foreign` FOREIGN KEY (`worker_id`) REFERENCES `workers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `bordero_pezzi` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `bordero_id` BIGINT UNSIGNED NOT NULL,
    `pezzo_bordero_id` BIGINT UNSIGNED NULL,
    `nome_pezzo` VARCHAR(255) NOT NULL,
    `quantita` INT NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `bordero_pezzi_bordero_id_foreign` FOREIGN KEY (`bordero_id`) REFERENCES `bordero` (`id`) ON DELETE CASCADE,
    CONSTRAINT `bordero_pezzi_pezzo_bordero_id_foreign` FOREIGN KEY (`pezzo_bordero_id`) REFERENCES `pezzi_bordero` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- Appaltatori
-- =========================================================

CREATE TABLE `appaltatori` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `tipo_soggetto` ENUM('fisica','giuridica') NOT NULL,
    `full_name` VARCHAR(255) NULL,
    `codice_fiscale` VARCHAR(255) NULL,
    `ragione_sociale` VARCHAR(255) NULL,
    `partita_iva` VARCHAR(255) NULL,
    `address` VARCHAR(255) NULL,
    `phone` VARCHAR(255) NULL,
    `email` VARCHAR(255) NULL,
    `latitude_appaltatore` DECIMAL(10,7) NULL,
    `longitude_appaltatore` DECIMAL(10,7) NULL,
    `note` TEXT NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- Lavoro Servizi: colonna appaltatore_id su works + tabella work_servizi
-- =========================================================

ALTER TABLE `works`
    ADD COLUMN `appaltatore_id` BIGINT UNSIGNED NULL AFTER `customer_id`,
    MODIFY COLUMN `customer_id` BIGINT UNSIGNED NULL,
    MODIFY COLUMN `nome_destinazione` VARCHAR(255) NULL,
    MODIFY COLUMN `indirizzo_destinazione` VARCHAR(255) NULL;

ALTER TABLE `works`
    ADD CONSTRAINT `works_appaltatore_id_foreign` FOREIGN KEY (`appaltatore_id`) REFERENCES `appaltatori` (`id`) ON DELETE SET NULL;

CREATE TABLE `work_servizi` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `work_id` BIGINT UNSIGNED NOT NULL,
    `service_id` BIGINT UNSIGNED NULL,
    `nome_servizio` VARCHAR(255) NOT NULL,
    `prezzo_unitario` DECIMAL(10,2) NOT NULL,
    `quantita` INT NOT NULL DEFAULT 1,
    `iva_applicata` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `work_servizi_work_id_foreign` FOREIGN KEY (`work_id`) REFERENCES `works` (`id`) ON DELETE CASCADE,
    CONSTRAINT `work_servizi_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
