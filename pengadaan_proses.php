<?php
session_start();
require_once 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_SESSION['cart_po'])) {
    $db = new DbConnection();

    $idvendor = $_POST['idvendor'];
    $subtotal = $_POST['subtotal'];
    $ppn = $subtotal * 0.10;
    $total = $subtotal + $ppn;
    $iduser = $_SESSION['iduser'];


    $q = "INSERT INTO pengadaan (user_iduser, vendor_idvendor, subtotal_nilai, ppn, total_nilai, status) 
          VALUES (?, ?, ?, ?, ?, 'P')";
    
    $res = $db->send_secure_query($q, [$iduser, $idvendor, $subtotal, $ppn, $total], 'iidii');

    if ($res->sukses) {
        $idpengadaan = $db->get_last_insert_id();

        $q_det = "INSERT INTO detail_pengadaan (idpengadaan, idbarang, harga_satuan, jumlah, sub_total) 
                  VALUES (?, ?, ?, ?, ?)";

        foreach ($_SESSION['cart_po'] as $item) {
            $sub_item = $item['harga'] * $item['jumlah'];
            $db->send_secure_query($q_det, 
                [$idpengadaan, $item['id'], $item['harga'], $item['jumlah'], $sub_item], 
                'iiiii'
            );
        }

        unset($_SESSION['cart_po']);
        echo "<script>alert('PO Berhasil Dibuat! Silakan informasikan ke Vendor.'); window.location='pengadaan_list.php';</script>";
    } else {
        echo "Error: " . $res->pesan;
    }
}
?>