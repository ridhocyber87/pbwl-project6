-- Database Project 5 (ECOMMERCE)
CREATE DATABASE IF NOT EXISTS dbproject5
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE dbproject5;

CREATE TABLE IF NOT EXISTS tb_kategori (
    kat_id         TINYINT(3)   NOT NULL AUTO_INCREMENT,
    kat_nama       VARCHAR(50)  NOT NULL,
    kat_keterangan TEXT         DEFAULT NULL,
    created_at     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at     DATETIME     DEFAULT NULL,
    PRIMARY KEY (kat_id),
    UNIQUE KEY uk_kat_nama (kat_nama)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS tb_produk (
    produk_id         INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    produk_id_kat     TINYINT(3)    NOT NULL,
    produk_kode       VARCHAR(50)   NOT NULL,
    produk_nama       VARCHAR(256)  NOT NULL,
    produk_hrg        INT UNSIGNED  NOT NULL DEFAULT 0,
    produk_keterangan TEXT          DEFAULT NULL,
    produk_stock      INT UNSIGNED  NOT NULL DEFAULT 0,
    produk_photo      VARCHAR(40)   DEFAULT NULL,
    created_at        TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at        DATETIME      DEFAULT NULL,
    PRIMARY KEY (produk_id),
    UNIQUE KEY uk_produk_kode (produk_kode),
    KEY idx_produk_kat (produk_id_kat),
    CONSTRAINT fk_produk_kategori FOREIGN KEY (produk_id_kat)
        REFERENCES tb_kategori (kat_id)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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

-- Data contoh
INSERT INTO tb_kategori (kat_nama, kat_keterangan) VALUES
    ('Elektronik', 'Perangkat elektronik dan aksesorisnya'),
    ('Pakaian',    'Pakaian pria dan wanita'),
    ('Makanan',    'Makanan dan minuman kemasan');

INSERT INTO tb_produk (produk_id_kat, produk_kode, produk_nama, produk_hrg, produk_keterangan, produk_stock) VALUES
    (1, 'ELK-001', 'Headphone Bluetooth', 250000, 'Headphone nirkabel dengan baterai 20 jam', 15),
    (2, 'PKN-001', 'Kaos Polos Katun',     75000, 'Kaos katun combed 30s',                    40),
    (3, 'MKN-001', 'Kopi Arabika 250 gr', 65000, 'Kopi bubuk arabika Sidikalang',             25);
