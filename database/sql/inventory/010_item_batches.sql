DROP TABLE IF EXISTS item_batches;
CREATE TABLE item_batches(
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 item_id BIGINT UNSIGNED NOT NULL,
 batch_number VARCHAR(100) NOT NULL,
 manufacture_date DATE NULL,
 expiry_date DATE NULL,
 quantity DECIMAL(18,4) DEFAULT 0,
 UNIQUE KEY uk_batch(item_id,batch_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;