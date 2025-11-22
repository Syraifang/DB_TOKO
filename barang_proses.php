<?php
session_start();
require_once 'koneksi.php';
if ($_SESSION['idrole'] != 1) exit("Akses Ditolak");

$db = new DbConnection();

if (isset($_GET['aksi']) && $_GET['aksi'] == 'toggle') {
    $id = $_GET['id'];
    $status_baru = $_GET['status'];

    if ($status_baru == 0) {
        $q = "CALL sp_hapus_barang_logis(?)";
        $db->send_secure_query($q, [$id], 'i');
    } else {
        $q = "UPDATE barang SET status = 1 WHERE idbarang = ?";
        $db->send_secure_query($q, [$id], 'i');
    }

    header("Location: barang_list.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $aksi = $_POST['aksi'];
    $nama = $_POST['nama_barang'];
    $jenis = $_POST['jenis'];
    $idsatuan = $_POST['idsatuan'];
    $harga = $_POST['harga'];

    if ($aksi == 'create') {
        $q = "INSERT INTO barang (nama_barang, jenis, idsatuan, harga, status) VALUES (?, ?, ?, ?, 1)";
        $db->send_secure_query($q, [$nama, $jenis, $idsatuan, $harga], 'ssid');
    
    } elseif ($aksi == 'update') {
        $id = $_POST['idbarang'];
        $q = "UPDATE barang SET nama_barang=?, jenis=?, idsatuan=?, harga=? WHERE idbarang=?";
        $db->send_secure_query($q, [$nama, $jenis, $idsatuan, $harga, $id], 'ssidi');
    }

    header("Location: barang_list.php");
    exit;
}
?>