<?php
session_start();
require_once 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $db = new DbConnection();

    $username = $_POST['username'];
    $password = $_POST['password'];
    $idrole   = $_POST['idrole'];

    $cek_query = "SELECT iduser FROM user WHERE username = ?";
    $cek_respon = $db->send_secure_query($cek_query, [$username], 's');

    if ($cek_respon->sukses && count($cek_respon->data) > 0) {
        echo "<script>alert('Username sudah terpakai!'); window.location='register.php';</script>";
        exit;
    }

    $insert_query = "INSERT INTO user (username, password, idrole) VALUES (?, ?, ?)";

    $insert_respon = $db->send_secure_query($insert_query, [$username, $password, $idrole], 'ssi');

    if ($insert_respon->sukses) {
        echo "<script>alert('Registrasi Berhasil! Silakan Login.'); window.location='login.php';</script>";
    } else {
        echo "Gagal mendaftar: " . $insert_respon->pesan;
    }
    
    $db->close_connection();
}
?>