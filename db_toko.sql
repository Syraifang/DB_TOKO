CREATE DATABASE `db_toko`;
USE db_toko;
DROP DATABASE `db_toko`;

CREATE TABLE role (
   idrole INT AUTO_INCREMENT PRIMARY KEY,
   nama_role VARCHAR(100)
);

INSERT INTO role (nama_role) VALUES
('Administrator'),
('Kasir'),
('Staff Gudang');

CREATE TABLE satuan (
   idsatuan INT AUTO_INCREMENT PRIMARY KEY,
   nama_satuan VARCHAR(45),
   status TINYINT(1)
);

INSERT INTO satuan (idsatuan, nama_satuan, status) VALUES
(101,'Pcs', 1),
(102,'Box', 1),
(103,'Lusin', 1),
(104,'Kg', 1),
(105,'Gram', 1);

CREATE TABLE vendor (
   idvendor INT AUTO_INCREMENT PRIMARY KEY,
   nama_vendor VARCHAR(100) ,
   badan_hukum CHAR(1), 
   status CHAR(1) 
);

INSERT INTO vendor (nama_vendor, badan_hukum, status) VALUES
('PT Sinar Jaya Abadi', 'P', 'T'),
('PT Pencari Cinta Sejati', 'P', 'A'),
('Toko ATK Barokah', 'N', 'A'),
('Kari Dekado', 'N', 'A');

CREATE TABLE margin_penjualan (
   idmargin_penjualan INT AUTO_INCREMENT PRIMARY KEY,
   persen DOUBLE ,
   status TINYINT(1), 
   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
   iduser INT NOT NULL
);

CREATE TABLE user (
   iduser INT AUTO_INCREMENT PRIMARY KEY,
   username VARCHAR(45),
   password VARCHAR(100),
   idrole INT NOT NULL,
   FOREIGN KEY (idrole) REFERENCES role(idrole)
);

INSERT INTO user (iduser, username, password, idrole) VALUES
(77,'admin', 'admin123', 1),
(78,'kasir01', 'kasir123', 2),
(79,'gudang01', 'gudang123', 3);


ALTER TABLE margin_penjualan
ADD CONSTRAINT fk_margin_user
FOREIGN KEY (iduser) REFERENCES user(iduser);

CREATE TABLE barang (
   idbarang INT AUTO_INCREMENT PRIMARY KEY,
   jenis CHAR(1),
   nama_barang VARCHAR(45),
   harga INT,
   status TINYINT(1),
   idsatuan INT NOT NULL,
   FOREIGN KEY (idsatuan) REFERENCES satuan(idsatuan)
);

INSERT INTO barang (idbarang, jenis, nama_barang, harga, status, idsatuan) VALUES
(51,'B', 'Penggaris Kayu', 20000, 0, 101),
(52,'B', 'Pulpen Copilot', 15000, 1, 101),
(53,'B', 'Spidol Whiteboard Snowman', 100000, 0, 103),
(54,'B', 'Kertas A4', 55000, 1, 102);

CREATE TABLE kartu_stok (
   idkartu_stok INT AUTO_INCREMENT PRIMARY KEY,
   jenis_transaksi CHAR(1), 
   masuk INT,
   keluar INT,
   stok INT,
   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   idbarang INT NOT NULL,
   FOREIGN KEY (idbarang) REFERENCES barang(idbarang)
);

CREATE TABLE pengadaan (
   idpengadaan INT AUTO_INCREMENT PRIMARY KEY,
   timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   status CHAR(1), 
   subtotal_nilai INT,
   ppn INT,
   total_nilai INT,
   user_iduser INT NOT NULL,
   vendor_idvendor INT NOT NULL,
   FOREIGN KEY (user_iduser) REFERENCES user(iduser),
   FOREIGN KEY (vendor_idvendor) REFERENCES vendor(idvendor)
);


CREATE TABLE penerimaan (
   idpenerimaan INT AUTO_INCREMENT PRIMARY KEY,
   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   status CHAR(1),
   idpengadaan INT NOT NULL,
   iduser INT NOT NULL,
   FOREIGN KEY (idpengadaan) REFERENCES pengadaan(idpengadaan),
   FOREIGN KEY (iduser) REFERENCES user(iduser)
);

CREATE TABLE penjualan (
   idpenjualan INT AUTO_INCREMENT PRIMARY KEY,
   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   subtotal_nilai INT,
   ppn INT,
   total_nilai INT,
   iduser INT NOT NULL,
   idmargin_penjualan INT NOT NULL,
   FOREIGN KEY (iduser) REFERENCES user(iduser),
   FOREIGN KEY (idmargin_penjualan) REFERENCES margin_penjualan(idmargin_penjualan)
);

CREATE TABLE retur (
   idretur INT AUTO_INCREMENT PRIMARY KEY,
   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   idpenerimaan INT NOT null,
   iduser INT NOT null,
   FOREIGN KEY (idpenerimaan) REFERENCES penerimaan(idpenerimaan),
   FOREIGN KEY (iduser) REFERENCES user(iduser)
);

CREATE TABLE detail_pengadaan (
   iddetail_pengadaan INT AUTO_INCREMENT PRIMARY KEY,
   harga_satuan INT,
   jumlah INT,
   sub_total INT,
   idbarang INT NOT NULL,
   idpengadaan INT NOT NULL,
   FOREIGN KEY (idbarang) REFERENCES barang(idbarang),
   FOREIGN KEY (idpengadaan) REFERENCES pengadaan(idpengadaan)
);

CREATE TABLE detail_penerimaan (
   iddetail_penerimaan INT AUTO_INCREMENT PRIMARY KEY,
   barang_idbarang INT NOT NULL,
   idpenerimaan INT NOT NULL,
   jumlah_terima INT,
   harga_satuan_terima INT,
   sub_total_terima INT ,
   FOREIGN KEY (barang_idbarang) REFERENCES barang(idbarang),
   FOREIGN KEY (idpenerimaan) REFERENCES penerimaan(idpenerimaan)
);

CREATE TABLE detail_penjualan (
   iddetail_penjualan INT AUTO_INCREMENT PRIMARY KEY,
   harga_satuan INT,
   jumlah INT,
   subtotal INT,
   idbarang INT NOT NULL,
   penjualan_idpenjualan INT NOT NULL,
   FOREIGN KEY (idbarang) REFERENCES barang(idbarang),
   FOREIGN KEY (penjualan_idpenjualan) REFERENCES penjualan(idpenjualan)
);

CREATE TABLE detail_retur (
   iddetail_retur INT AUTO_INCREMENT PRIMARY KEY,
   jumlah INT,
   alasan VARCHAR(200),
   idretur INT NOT NULL,
   iddetail_penerimaan INT NOT NULL,
   FOREIGN KEY (idretur) REFERENCES retur(idretur),
   FOREIGN KEY (iddetail_penerimaan) REFERENCES detail_penerimaan(iddetail_penerimaan)
);

-- VIEW

CREATE OR REPLACE VIEW v_daftar_user AS
SELECT 
   u.iduser,
   u.username,
   r.nama_role
FROM user u
JOIN role r ON u.idrole = r.idrole;
   
CREATE OR REPLACE VIEW v_daftar_barang AS
SELECT 
   b.idbarang,
   b.nama_barang,
   b.harga,
   s.nama_satuan,
   b.jenis
FROM barang b
JOIN satuan s ON b.idsatuan = s.idsatuan
WHERE b.status= 1;

CREATE OR REPLACE VIEW v_stok_barang_terkini AS
SELECT 
   b.idbarang,
   b.nama_barang,
   IFNULL((SELECT ks.stok 
   FROM kartu_stok ks 
   WHERE ks.idbarang = b.idbarang 
   ORDER BY ks.idkartu_stok DESC LIMIT 1), 0) AS stok_terkini
FROM barang b;
   
CREATE OR REPLACE VIEW v_laporan_pengadaan_detail AS
SELECT 
   p.idpengadaan ,
   p.timestamp AS tanggal_pengadaan,
   v.nama_vendor,
   u.username AS nama_staff,
   b.nama_barang,
   dp.harga_satuan,
   dp.jumlah,
   dp.sub_total
FROM pengadaan p
JOIN detail_pengadaan dp ON p.idpengadaan = dp.idpengadaan
JOIN barang b ON dp.idbarang = b.idbarang
JOIN user u ON p.user_iduser = u.iduser
JOIN vendor v ON p.vendor_idvendor = v.idvendor;
   
CREATE OR REPLACE VIEW v_laporan_penerimaan_detail AS
SELECT 
   p.idpenerimaan,
   p.created_at AS tanggal_terima,
   p.idpengadaan,
   u.username AS nama_staff,
   b.nama_barang,
   dp.jumlah_terima,
   dp.harga_satuan_terima,
   dp.sub_total_terima
FROM penerimaan p
JOIN detail_penerimaan dp ON p.idpenerimaan = dp.idpenerimaan
JOIN barang b ON dp.barang_idbarang = b.idbarang
JOIN user u ON p.iduser = u.iduser;
   
CREATE OR REPLACE VIEW v_laporan_penjualan_detail AS
SELECT 
   p.idpenjualan,
   p.created_at AS tanggal_transaksi,
   u.username AS nama_kasir,
   b.nama_barang,
   dp.harga_satuan,
   dp.jumlah,
   dp.subtotal
FROM penjualan p
JOIN detail_penjualan dp ON p.idpenjualan = dp.penjualan_idpenjualan
JOIN barang b ON dp.idbarang = b.idbarang
JOIN user u ON p.iduser = u.iduser;

CREATE OR REPLACE VIEW v_vendor_aktif AS
SELECT 
   idvendor,
   nama_vendor,
   badan_hukum
FROM vendor
WHERE status = 'A';

CREATE OR REPLACE VIEW v_satuan_aktif AS
SELECT 
   idsatuan,
   nama_satuan
FROM satuan
WHERE status = 1;

CREATE OR REPLACE VIEW v_margin_aktif AS
SELECT 
   idmargin_penjualan,
   persen,
   iduser
FROM margin_penjualan
WHERE status = 1;

CREATE OR REPLACE VIEW v_barang_tidak_aktif AS
SELECT 
   b.idbarang,
   b.nama_barang,
   b.harga,
   s.nama_satuan,
   b.jenis
FROM barang b
JOIN satuan s ON b.idsatuan = s.idsatuan
WHERE b.status = 0;
   
-- Procedure

DELIMITER $$

CREATE PROCEDURE sp_hapus_barang_logis(IN p_idbarang INT)
BEGIN
UPDATE barang
SET status = 0
WHERE idbarang = p_idbarang;
END$$

DELIMITER ;

DELIMITER $$

CREATE PROCEDURE sp_update_kartu_stok(
   IN p_idbarang INT,
   IN p_masuk INT,
   IN p_keluar INT,
   IN p_jenis_transaksi CHAR(1))
BEGIN
   DECLARE v_stok_terakhir INT DEFAULT 0;
   DECLARE v_stok_baru INT DEFAULT 0;
   SET v_stok_terakhir = f_get_stok_terkini(p_idbarang);
   SET v_stok_baru = v_stok_terakhir + p_masuk - p_keluar;
   INSERT INTO kartu_stok (idbarang, jenis_transaksi, masuk, keluar, stok)
   VALUES (p_idbarang, p_jenis_transaksi, p_masuk, p_keluar, v_stok_baru);
END$$

DELIMITER ;

DELIMITER $$

CREATE PROCEDURE sp_cari_barang(
   IN p_nama_barang VARCHAR(45))
BEGIN
   SELECT 
   b.idbarang,
   b.nama_barang,
   b.harga,
   s.nama_satuan,
   b.status
FROM barang b
JOIN satuan s ON b.idsatuan = s.idsatuan
WHERE 
   b.nama_barang LIKE CONCAT('%', p_nama_barang, '%');
END$$

DELIMITER ;

CALL sp_cari_barang('pul');

DELIMITER $$

CREATE PROCEDURE sp_cari_vendor(
   IN p_nama_vendor VARCHAR(100))
BEGIN
   SELECT 
   idvendor,
   nama_vendor,
   badan_hukum,
   status
FROM vendor
WHERE nama_vendor LIKE CONCAT('%', p_nama_vendor, '%');
END$$

DELIMITER ;

-- Function

DELIMITER $$

CREATE FUNCTION f_hitung_harga_jual(
   p_idbarang INT,
   p_idmargin INT)
RETURNS DOUBLE
READS SQL DATA
BEGIN
   DECLARE v_harga_dasar INT;
   DECLARE v_persen_margin DOUBLE;
   DECLARE v_harga_jual DOUBLE;

SELECT harga INTO v_harga_dasar 
FROM barang 
WHERE idbarang = p_idbarang;

SELECT persen INTO v_persen_margin 
FROM margin_penjualan 
WHERE idmargin_penjualan = p_idmargin;
SET v_harga_jual = v_harga_dasar + (v_harga_dasar * (v_persen_margin / 100));
RETURN v_harga_jual;
END$$

DELIMITER ;

DELIMITER $$

CREATE FUNCTION f_get_stok_terkini(
   p_idbarang INT) 
RETURNS INT
READS SQL DATA
BEGIN
   DECLARE v_stok INT DEFAULT 0;

SELECT stok INTO v_stok 
FROM kartu_stok 
WHERE idbarang = p_idbarang
ORDER BY idkartu_stok DESC 
   LIMIT 1;
RETURN v_stok;
END$$

DELIMITER ;

DELIMITER $$

CREATE FUNCTION f_hitung_subtotal(
   p_idbarang INT,
   p_idmargin INT,
   p_jumlah INT) 
RETURNS DOUBLE
READS SQL DATA
BEGIN
   DECLARE v_harga_jual_satuan DOUBLE;
   DECLARE v_subtotal_final DOUBLE;

SET v_harga_jual_satuan = f_hitung_harga_jual(p_idbarang, p_idmargin);
-- menghitung harga
SET v_subtotal_final = v_harga_jual_satuan * p_jumlah;
RETURN v_subtotal_final;
END$$

DELIMITER ;

DELIMITER $$

CREATE FUNCTION f_hitung_total_nilai(
   p_subtotal INT)
RETURNS DOUBLE
DETERMINISTIC
BEGIN
   DECLARE v_ppn DOUBLE;
   DECLARE v_total_nilai DOUBLE;
    
SET v_ppn = p_subtotal * 0.10; 
SET v_total_nilai = p_subtotal + v_ppn;
RETURN v_total_nilai;
END$$

DELIMITER ;

-- triger

DELIMITER $$

CREATE TRIGGER trg_detail_penjualan AFTER INSERT ON detail_penjualan
FOR EACH ROW
BEGIN
CALL sp_update_kartu_stok(
   NEW.idbarang,
   0,
   NEW.jumlah,
	'K');
END$$

DELIMITER ;

DELIMITER $$

CREATE TRIGGER trg_detail_penerimaan AFTER INSERT ON detail_penerimaan
FOR EACH ROW
BEGIN
CALL sp_update_kartu_stok(
   NEW.barang_idbarang,
   NEW.jumlah_terima,
   0,
   'M');
END$$

DELIMITER ;