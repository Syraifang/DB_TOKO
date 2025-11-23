<?php
session_start();
require_once 'koneksi.php';
if ($_SESSION['idrole'] != 1) exit("Akses Ditolak");

$db = new DbConnection();

if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus') {
    $id = $_GET['id'];

    if ($id == 1) {
        echo "<script>alert('Role Administrator Utama tidak boleh dihapus!'); window.location='role_list.php';</script>";
        exit;
    }

    $q = "DELETE FROM role WHERE idrole = ?";
    $res = $db->send_secure_query($q, [$id], 'i');
    
    if (!$res->sukses) {
        echo "<script>alert('GAGAL HAPUS! Role ini sedang dipakai oleh User.'); window.location='role_list.php';</script>";
        exit;
    }
    header("Location: role_list.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $aksi = $_POST['aksi'];
    $nama = $_POST['nama_role'];

    if ($aksi == 'create') {
        $q = "INSERT INTO role (nama_role) VALUES (?)";
        $db->send_secure_query($q, [$nama], 's');
    } elseif ($aksi == 'update') {
        $id = $_POST['idrole'];
        $q = "UPDATE role SET nama_role=? WHERE idrole=?";
        $db->send_secure_query($q, [$nama, $id], 'si');
    }

    header("Location: role_list.php");
    exit;
}
?>