-- Tabella Mansioni Lavoratore (MySQL)
-- Migration di riferimento: 2026_07_06_100000_create_worker_mansioni_table.php
--
-- Da eseguire dopo che `workers` esiste già.

CREATE TABLE `worker_mansioni` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `worker_id` BIGINT UNSIGNED NOT NULL,
    `mansione` ENUM('trasportatore','posatore') NOT NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `worker_mansioni_worker_id_mansione_unique` (`worker_id`, `mansione`),
    CONSTRAINT `worker_mansioni_worker_id_foreign` FOREIGN KEY (`worker_id`) REFERENCES `workers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Backfill: assegna mansione 'trasportatore' a tutti i worker già esistenti.
INSERT INTO `worker_mansioni` (`worker_id`, `mansione`, `created_at`, `updated_at`)
SELECT `id`, 'trasportatore', NOW(), NOW() FROM `workers`;
