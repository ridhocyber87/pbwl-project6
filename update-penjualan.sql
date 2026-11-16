-- Tambahan tabel penjualan untuk database dbproject5 yang sudah ada
-- (jalankan setelah database.sql versi sebelumnya sudah diimport)
USE dbproject5;

CREATE TABLE IF NOT EXISTS tb_penjualan (
    jual_id      INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    jual_no      VARCHAR(30)     NOT NULL,
    jual_tgl     DATE            NOT NULL,
    jual_pembeli VARCHAR(100)    NOT NULL,
    jual_catatan VARCHAR(255)    DEFAULT NULL,
    jual_total   BIGINT UNSIGNED NOT NULL DEFAULT 0,
    created_at   TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (jual_id),
    UNIQUE KEY uk_jual_no (jual_no),
    KEY idx_jual_tgl (jual_tgl)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS tb_penjualan_detail (
    detail_id        INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    detail_id_jual   INT UNSIGNED    NOT NULL,
    detail_id_produk INT UNSIGNED    NOT NULL,
    detail_kode      VARCHAR(50)     NOT NULL,
    detail_nama      VARCHAR(256)    NOT NULL,
    detail_hrg       INT UNSIGNED    NOT NULL,
    detail_qty       INT UNSIGNED    NOT NULL,
    detail_subtotal  BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (detail_id),
    KEY idx_detail_jual (detail_id_jual),
    KEY idx_detail_produk (detail_id_produk),
    CONSTRAINT fk_detail_penjualan FOREIGN KEY (detail_id_jual)
        REFERENCES tb_penjualan (jual_id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_detail_produk FOREIGN KEY (detail_id_produk)
        REFERENCES tb_produk (produk_id)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
