<?php
session_start();
require_once 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_SESSION['keranjang'])) {
    $db = new DbConnection();

    $iduser = $_SESSION['iduser'];
    $idmargin = $_POST['idmargin'];

    $subtotal_nilai = $_POST['subtotal_nilai'];
    $ppn = $_POST['ppn'];
    $total_nilai = $_POST['total_nilai'];

    $q_header = "INSERT INTO penjualan (subtotal_nilai, ppn, total_nilai, iduser, idmargin_penjualan) 
                 VALUES (?, ?, ?, ?, ?)";
    
    $res_header = $db->send_secure_query($q_header, 
        [$subtotal_nilai, $ppn, $total_nilai, $iduser, $idmargin], 
        'dddii'
    );

    if ($res_header->sukses) {
        $idpenjualan = $db->get_last_insert_id();

        $q_detail = "INSERT INTO detail_penjualan (harga_satuan, jumlah, subtotal, idbarang, penjualan_idpenjualan) 
                     VALUES (?, ?, ?, ?, ?)";

        foreach ($_SESSION['keranjang'] as $item) {
            $harga_modal = $item['harga'];
            $jumlah = $item['jumlah'];

             $q_m = "SELECT persen FROM margin_penjualan WHERE idmargin_penjualan = $idmargin";
             $res_m = $db->send_query($q_m);
             $persen = $res_m->data[0]['persen'];

             $harga_jual_satuan = $harga_modal + ($harga_modal * $persen / 100);
             $subtotal_item = $harga_jual_satuan * $jumlah;

             $db->send_secure_query($q_detail, 
                [$harga_jual_satuan, $jumlah, $subtotal_item, $item['id'], $idpenjualan], 
                'didii'
             );
        }

        unset($_SESSION['keranjang']);

        echo "<script>alert('Transaksi Berhasil!'); window.location='dashboard.php';</script>";
        exit;

    } else {
        echo "Gagal membuat transaksi: " . $res_header->pesan;
    }

} else {
    header("Location: transaksi_baru.php");
    exit;
}
?>