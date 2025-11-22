<?php
session_start();
require_once 'koneksi.php';
if ($_SESSION['idrole'] != 1) exit("Akses Ditolak");

$db = new DbConnection();

if (isset($_GET['aksi'])) {
    $aksi = $_GET['aksi'];
    $id = $_GET['id'];

    if ($aksi == 'toggle') {
        $status_baru = $_GET['status'];
        $q = "UPDATE margin_penjualan SET status = ? WHERE idmargin_penjualan = ?";
        $db->send_secure_query($q, [$status_baru, $id], 'ii');
    } 
    elseif ($aksi == 'hapus') {
        $q = "DELETE FROM margin_penjualan WHERE idmargin_penjualan = ?";
        $respon = $db->send_secure_query($q, [$id], 'i');
        
        if (!$respon->sukses) {
            echo "<script>alert('GAGAL HAPUS! Margin ini sudah digunakan dalam riwayat penjualan.'); window.location='margin_list.php';</script>";
            exit;
        }
    }
    
    header("Location: margin_list.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $aksi = $_POST['aksi'];
    $persen = $_POST['persen'];
    $iduser = $_SESSION['iduser']; 

    if ($aksi == 'create') {

        $q = "INSERT INTO margin_penjualan (persen, status, iduser) VALUES (?, 1, ?)";
        $db->send_secure_query($q, [$persen, $iduser], 'di');
    
    } elseif ($aksi == 'update') {

        $id = $_POST['idmargin_penjualan'];
        $q = "UPDATE margin_penjualan SET persen=?, iduser=? WHERE idmargin_penjualan=?";
        $db->send_secure_query($q, [$persen, $iduser, $id], 'dii');
    }

    header("Location: margin_list.php");
    exit;
}
?>