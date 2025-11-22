<?php
session_start();
require_once 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $db = new DbConnection();

    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM user WHERE username = ?";
    
    $respon = $db->send_secure_query($query, [$username], 's');

    if ($respon->sukses && count($respon->data) > 0) {
        $user_data = $respon->data[0];

        if ($password === $user_data['password']) {
            
            $_SESSION['is_logged_in'] = true;
            $_SESSION['iduser'] = $user_data['iduser'];
            $_SESSION['username'] = $user_data['username'];
            $_SESSION['idrole'] = $user_data['idrole'];

            header("Location: dashboard.php");
            exit;
        } else {
            $_SESSION['error_message'] = "Password salah!";
            header("Location: login.php");
            exit;
        }
    } else {
        $_SESSION['error_message'] = "Username tidak ditemukan!";
        header("Location: login.php");
        exit;
    }
}
?>