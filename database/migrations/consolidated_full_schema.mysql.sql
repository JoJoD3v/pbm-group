-- Schema completo consolidato (MySQL)
-- Generato a partire dalle migration Laravel in database/migrations/2026_07_10_*
-- Da eseguire in ordine su un database MySQL vuoto (rispetta le dipendenze FK).

CREATE TABLE `users` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `first_name` VARCHAR(255) NOT NULL,
    `last_name` VARCHAR(255) NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `role` ENUM('sviluppatore','amministratore','dipendente') NOT NULL,
    `phone` VARCHAR(255) NULL,
    `email_verified_at` TIMESTAMP NULL,
    `password` VARCHAR(255) NOT NULL,
    `remember_token` VARCHAR(100) NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `password_reset_tokens` (
    `email` VARCHAR(255) NOT NULL,
    `token` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP NULL,
    PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `sessions` (
    `id` VARCHAR(255) NOT NULL,
    `user_id` BIGINT UNSIGNED NULL,
    `ip_address` VARCHAR(45) NULL,
    `user_agent` TEXT NULL,
    `payload` LONGTEXT NOT NULL,
    `last_activity` INT NOT NULL,
    PRIMARY KEY (`id`),
    KEY `sessions_user_id_index` (`user_id`),
    KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `materials` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `eer_code` VARCHAR(255) NULL,
    `prezzo` DECIMAL(10,2) NULL,
    `note` TEXT NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `deposits` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `address` VARCHAR(255) NOT NULL,
    `n_aut_comunicazione` VARCHAR(255) NULL,
    `numero_iscrizione_albo` VARCHAR(255) NULL,
    `tipo` VARCHAR(255) NULL,
    `destinazione` VARCHAR(255) NULL,
    `piva` VARCHAR(255) NULL,
    `data_scadenza` DATE NULL,
    `latitude` DECIMAL(10,7) NULL,
    `longitude` DECIMAL(10,7) NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `customers` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `customer_type` ENUM('fisica','giuridica') NOT NULL,
    `full_name` VARCHAR(255) NULL,
    `codice_fiscale` VARCHAR(255) NULL,
    `ragione_sociale` VARCHAR(255) NULL,
    `partita_iva` VARCHAR(255) NULL,
    `address` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `note` TEXT NULL,
    `latitude_customer` DECIMAL(10,7) NULL,
    `longitude_customer` DECIMAL(10,7) NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

CREATE TABLE `warehouses` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nome_sede` VARCHAR(255) NOT NULL,
    `indirizzo` VARCHAR(255) NOT NULL,
    `latitude_warehouse` DECIMAL(10,7) NULL,
    `longitude_warehouse` DECIMAL(10,7) NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `vehicles` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nome` VARCHAR(255) NOT NULL,
    `targa` VARCHAR(255) NOT NULL,
    `scadenza_assicurazione` DATE NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `vehicles_targa_unique` (`targa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `credit_cards` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `numero_carta` TEXT NOT NULL,
    `scadenza_carta` DATE NOT NULL,
    `fondo_carta` DECIMAL(10,2) NOT NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `services` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nome_servizio` VARCHAR(255) NOT NULL,
    `prezzo_servizio` DECIMAL(10,2) NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `workers` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_worker` VARCHAR(255) NOT NULL,
    `name_worker` VARCHAR(255) NOT NULL,
    `cognome_worker` VARCHAR(255) NOT NULL,
    `license_worker` VARCHAR(255) NOT NULL,
    `worker_email` VARCHAR(255) NOT NULL,
    `phone_worker` VARCHAR(255) NULL,
    `fondo_cassa` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `colore_bg` VARCHAR(7) NULL,
    `colore_font` VARCHAR(7) NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `workers_id_worker_unique` (`id_worker`),
    UNIQUE KEY `workers_worker_email_unique` (`worker_email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

INSERT INTO `worker_mansioni` (`worker_id`, `mansione`, `created_at`, `updated_at`)
SELECT `id`, 'trasportatore', NOW(), NOW() FROM `workers`;

CREATE TABLE `works` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `tipo_lavoro` VARCHAR(255) NOT NULL,
    `customer_id` BIGINT UNSIGNED NULL,
    `appaltatore_id` BIGINT UNSIGNED NULL,
    `status_lavoro` VARCHAR(255) NOT NULL DEFAULT 'In Sospeso',
    `data_esecuzione` DATETIME NULL,
    `costo_lavoro` DECIMAL(10,2) NULL,
    `modalita_pagamento` VARCHAR(255) NULL,
    `nome_partenza` VARCHAR(255) NULL,
    `indirizzo_partenza` VARCHAR(255) NULL,
    `latitude_partenza` DECIMAL(10,7) NULL,
    `longitude_partenza` DECIMAL(10,7) NULL,
    `materiale` VARCHAR(255) NULL,
    `codice_eer` VARCHAR(255) NULL,
    `material_id` BIGINT UNSIGNED NULL,
    `prezzo_materiale` DECIMAL(10,2) NOT NULL DEFAULT 1.00,
    `quantita_materiale` DECIMAL(10,2) NOT NULL DEFAULT 1.00,
    `iva_applicata` TINYINT(1) NOT NULL DEFAULT 0,
    `nome_destinazione` VARCHAR(255) NULL,
    `indirizzo_destinazione` VARCHAR(255) NULL,
    `deposit_id` BIGINT UNSIGNED NULL,
    `warehouse_destinazione_id` BIGINT UNSIGNED NULL,
    `latitude_destinazione` DECIMAL(10,7) NULL,
    `longitude_destinazione` DECIMAL(10,7) NULL,
    `note` TEXT NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `works_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
    CONSTRAINT `works_appaltatore_id_foreign` FOREIGN KEY (`appaltatore_id`) REFERENCES `appaltatori` (`id`) ON DELETE SET NULL,
    CONSTRAINT `works_material_id_foreign` FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`) ON DELETE SET NULL,
    CONSTRAINT `works_deposit_id_foreign` FOREIGN KEY (`deposit_id`) REFERENCES `deposits` (`id`) ON DELETE SET NULL,
    CONSTRAINT `works_warehouse_destinazione_id_foreign` FOREIGN KEY (`warehouse_destinazione_id`) REFERENCES `warehouses` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

CREATE TABLE `ricevute` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `work_id` BIGINT UNSIGNED NOT NULL,
    `numero_ricevuta` VARCHAR(255) NOT NULL,
    `fattura` TINYINT(1) NOT NULL DEFAULT 0,
    `riserva_controlli` TINYINT(1) NOT NULL DEFAULT 0,
    `nome_ricevente` VARCHAR(255) NOT NULL,
    `firma_base64` TEXT NULL,
    `pagamento_effettuato` TINYINT(1) NOT NULL DEFAULT 0,
    `somma_pagamento` DECIMAL(10,2) NULL,
    `foto_bolla` VARCHAR(255) NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `ricevute_numero_ricevuta_unique` (`numero_ricevuta`),
    CONSTRAINT `ricevute_work_id_foreign` FOREIGN KEY (`work_id`) REFERENCES `works` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cash_movements` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `worker_id` BIGINT UNSIGNED NOT NULL,
    `work_id` BIGINT UNSIGNED NULL,
    `tipo_movimento` ENUM('entrata','uscita') NOT NULL,
    `importo` DECIMAL(10,2) NOT NULL,
    `motivo` VARCHAR(255) NULL,
    `metodo_pagamento` ENUM('contanti','dkv','carta') NOT NULL,
    `credit_card_id` BIGINT UNSIGNED NULL,
    `data_movimento` DATE NOT NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `cash_movements_worker_id_foreign` FOREIGN KEY (`worker_id`) REFERENCES `workers` (`id`) ON DELETE CASCADE,
    CONSTRAINT `cash_movements_work_id_foreign` FOREIGN KEY (`work_id`) REFERENCES `works` (`id`) ON DELETE SET NULL,
    CONSTRAINT `cash_movements_credit_card_id_foreign` FOREIGN KEY (`credit_card_id`) REFERENCES `credit_cards` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `vehicle_assignment_logs` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `vehicle_id` BIGINT UNSIGNED NOT NULL,
    `worker_id` BIGINT UNSIGNED NOT NULL,
    `data_assegnazione` DATETIME NOT NULL,
    `data_restituzione` DATETIME NULL,
    `note` TEXT NULL,
    `operazione` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `vehicle_assignment_logs_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE,
    CONSTRAINT `vehicle_assignment_logs_worker_id_foreign` FOREIGN KEY (`worker_id`) REFERENCES `workers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `credit_card_recharges` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `credit_card_id` BIGINT UNSIGNED NOT NULL,
    `user_id` BIGINT UNSIGNED NULL,
    `importo` DECIMAL(10,2) NOT NULL,
    `data_ricarica` DATETIME NOT NULL,
    `note` TEXT NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `credit_card_recharges_credit_card_id_foreign` FOREIGN KEY (`credit_card_id`) REFERENCES `credit_cards` (`id`) ON DELETE CASCADE,
    CONSTRAINT `credit_card_recharges_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `deposit_material` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `deposit_id` BIGINT UNSIGNED NOT NULL,
    `material_id` BIGINT UNSIGNED NOT NULL,
    `quantity` DECIMAL(10,2) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `deposit_material_deposit_id_foreign` FOREIGN KEY (`deposit_id`) REFERENCES `deposits` (`id`) ON DELETE CASCADE,
    CONSTRAINT `deposit_material_material_id_foreign` FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `work_worker` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `work_id` BIGINT UNSIGNED NOT NULL,
    `worker_id` BIGINT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `work_worker_work_id_foreign` FOREIGN KEY (`work_id`) REFERENCES `works` (`id`) ON DELETE CASCADE,
    CONSTRAINT `work_worker_worker_id_foreign` FOREIGN KEY (`worker_id`) REFERENCES `workers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `vehicle_worker` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `vehicle_id` BIGINT UNSIGNED NOT NULL,
    `worker_id` BIGINT UNSIGNED NOT NULL,
    `data_assegnazione` DATETIME NOT NULL,
    `data_restituzione` DATETIME NULL,
    `note` TEXT NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `vehicle_worker_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE,
    CONSTRAINT `vehicle_worker_worker_id_foreign` FOREIGN KEY (`worker_id`) REFERENCES `workers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `credit_card_worker` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `credit_card_id` BIGINT UNSIGNED NOT NULL,
    `worker_id` BIGINT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `credit_card_worker_credit_card_id_foreign` FOREIGN KEY (`credit_card_id`) REFERENCES `credit_cards` (`id`) ON DELETE CASCADE,
    CONSTRAINT `credit_card_worker_worker_id_foreign` FOREIGN KEY (`worker_id`) REFERENCES `workers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
