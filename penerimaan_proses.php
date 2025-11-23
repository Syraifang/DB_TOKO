<?php
session_start();
require_once 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $db = new DbConnection();

    $idpengadaan = $_POST['idpengadaan'];
    $iduser = $_SESSION['iduser'];

    $barang_ids = $_POST['idbarang'];
    $jml_terimas = $_POST['terima'];
    $hargas = $_POST['harga']; 

    $q_head = "INSERT INTO penerimaan (idpengadaan, iduser, status) VALUES (?, ?, 'S')";
    $res = $db->send_secure_query($q_head, [$idpengadaan, $iduser], 'ii');

    if ($res->sukses) {
        $idpenerimaan = $db->get_last_insert_id();

        $q_det = "INSERT INTO detail_penerimaan (idpenerimaan, barang_idbarang, jumlah_terima, harga_satuan_terima, sub_total_terima) 
                  VALUES (?, ?, ?, ?, ?)";

        for ($i = 0; $i < count($barang_ids); $i++) {
            $idb = $barang_ids[$i];
            $jml = $jml_terimas[$i];
            $hrg = $hargas[$i];
            $sub = $jml * $hrg;

            if ($jml > 0) {
                $db->send_secure_query($q_det, 
                    [$idpenerimaan, $idb, $jml, $hrg, $sub], 
                    'iiiii'
                );
            }
        }

        $db->send_secure_query("UPDATE pengadaan SET status='S' WHERE idpengadaan=?", [$idpengadaan], 'i');

        echo "<script>alert('Penerimaan Selesai! Stok barang otomatis bertambah.'); window.location='penerimaan_barang.php';</script>";
    } else {
        echo "Gagal: " . $res->pesan;
    }
}
?>