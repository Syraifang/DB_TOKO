<?php
session_start();
require_once 'koneksi.php';
if ($_SESSION['idrole'] != 1) exit("Akses Ditolak");

$db = new DbConnection();

if (isset($_GET['aksi']) && $_GET['aksi'] == 'toggle') {
    $id = $_GET['id'];
    $status_baru = $_GET['status'];
    $q = "UPDATE vendor SET status = ? WHERE idvendor = ?";
    $db->send_secure_query($q, [$status_baru, $id], 'si');

    header("Location: vendor_list.php");
    exit;
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $aksi = $_POST['aksi'];
    $nama = $_POST['nama_vendor'];
    $badan = $_POST['badan_hukum'];

    if ($aksi == 'create') {
        $q = "INSERT INTO vendor (nama_vendor, badan_hukum, status) VALUES (?, ?, 'A')";
        $db->send_secure_query($q, [$nama, $badan], 'ss');
    
    } elseif ($aksi == 'update') {
        $id = $_POST['idvendor'];
        $q = "UPDATE vendor SET nama_vendor=?, badan_hukum=? WHERE idvendor=?";
        $db->send_secure_query($q, [$nama, $badan, $id], 'ssi');
    }

    header("Location: vendor_list.php");
    exit;
}
?>