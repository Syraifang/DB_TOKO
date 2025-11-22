<?php
session_start();
require_once 'koneksi.php';
if ($_SESSION['idrole'] != 1) exit("Akses Ditolak");

$db = new DbConnection();

if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus') {
    $id = $_GET['id'];

    if ($id == $_SESSION['iduser']) {
        echo "<script>alert('Anda tidak bisa menghapus akun sendiri!'); window.location='user_list.php';</script>";
        exit;
    }

    $q = "DELETE FROM user WHERE iduser = ?";
    $db->send_secure_query($q, [$id], 'i');
    
    header("Location: user_list.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $aksi = $_POST['aksi'];
    $user = $_POST['username'];
    $pass = $_POST['password'];
    $role = $_POST['idrole'];

    if ($aksi == 'create') {
        $cek = $db->send_secure_query("SELECT iduser FROM user WHERE username=?", [$user], 's');
        if (count($cek->data) > 0) {
            echo "<script>alert('Username sudah dipakai!'); window.location='user_form.php';</script>";
            exit;
        }

        $q = "INSERT INTO user (username, password, idrole) VALUES (?, ?, ?)";
        $db->send_secure_query($q, [$user, $pass, $role], 'ssi');
    
    } elseif ($aksi == 'update') {
        $id = $_POST['iduser'];
        $q = "UPDATE user SET username=?, password=?, idrole=? WHERE iduser=?";
        $db->send_secure_query($q, [$user, $pass, $role, $id], 'ssii');
    }

    header("Location: user_list.php");
    exit;
}
?>