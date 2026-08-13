DROP TABLE IF EXISTS item_stocks;
CREATE TABLE item_stocks(
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 item_id BIGINT UNSIGNED NOT NULL,
 warehouse_id BIGINT UNSIGNED NOT NULL,
 qty_on_hand DECIMAL(18,4) DEFAULT 0,
 qty_reserved DECIMAL(18,4) DEFAULT 0,
 qty_available DECIMAL(18,4) DEFAULT 0,
 last_stock_take DATE NULL,
 UNIQUE KEY uk_item_wh(item_id,warehouse_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;