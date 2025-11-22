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
        $q = "UPDATE satuan SET status = ? WHERE idsatuan = ?";
        $db->send_secure_query($q, [$status_baru, $id], 'ii');
    } 
    elseif ($aksi == 'hapus') {
        $q = "DELETE FROM satuan WHERE idsatuan = ?";
        $respon = $db->send_secure_query($q, [$id], 'i');
        
        if (!$respon->sukses) {
            echo "<script>alert('GAGAL HAPUS! Satuan ini sedang digunakan oleh Barang.'); window.location='satuan_list.php';</script>";
            exit;
        }
    }
    
    header("Location: satuan_list.php");
    exit;
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $aksi = $_POST['aksi'];
    $nama = $_POST['nama_satuan'];

    if ($aksi == 'create') {
        $q = "INSERT INTO satuan (nama_satuan, status) VALUES (?, 1)";
        $db->send_secure_query($q, [$nama], 's');
    
    } elseif ($aksi == 'update') {
        $id = $_POST['idsatuan'];
        $q = "UPDATE satuan SET nama_satuan=? WHERE idsatuan=?";
        $db->send_secure_query($q, [$nama, $id], 'si');
    }

    header("Location: satuan_list.php");
    exit;
}
?>