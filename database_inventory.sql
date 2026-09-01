CREATE DATABASE IF NOT EXISTS db_inventory;
USE db_inventory;
DROP TABLE IF EXISTS inventory;
DROP TABLE IF EXISTS storage_unit;
DROP TABLE IF EXISTS vendor_supplier;
DROP TABLE IF EXISTS ADMIN;

CREATE TABLE ADMIN (
    nomor_id VARCHAR(50) PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    kontak VARCHAR(20),
    email VARCHAR(100),
    password VARCHAR(255) NOT NULL
) ENGINE=INNODB;

CREATE TABLE storage_unit (
    id_gudang INT PRIMARY KEY AUTO_INCREMENT,
    nama_gudang VARCHAR(100) NOT NULL,
    lokasi VARCHAR(255) NOT NULL
) ENGINE=INNODB;

CREATE TABLE vendor_supplier (
    id_vendor INT PRIMARY KEY AUTO_INCREMENT,
    nama VARCHAR(100) NOT NULL,
    kontak VARCHAR(20)
) ENGINE=INNODB;

CREATE TABLE inventory (
    serial_number VARCHAR(100) PRIMARY KEY,
    nama_barang VARCHAR(100) NOT NULL,
    jenis_barang VARCHAR(50),
    kuantitas_stok INT DEFAULT 0,
    harga DECIMAL(15,2),
    id_gudang INT,
    id_vendor INT,
    FOREIGN KEY (id_gudang) REFERENCES storage_unit(id_gudang) ON DELETE SET NULL,
    FOREIGN KEY (id_vendor) REFERENCES vendor_supplier(id_vendor) ON DELETE SET NULL
) ENGINE=INNODB;

-- Contoh data gudang & vendor (opsional, boleh dihapus)
INSERT INTO storage_unit (nama_gudang, lokasi) VALUES
('Gudang Pusat', 'Surabaya'),
('Gudang Cabang', 'Sidoarjo');

INSERT INTO vendor_supplier (nama, kontak) VALUES
('CV Sumber Makmur', '081234567890'),
('PT Jaya Abadi', '081298765432');
