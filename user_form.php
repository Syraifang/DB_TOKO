<?php
session_start();
require_once 'koneksi.php';
if ($_SESSION['idrole'] != 1) exit("Akses Ditolak");

$db = new DbConnection();

$iduser = "";
$username = "";
$password = "";
$idrole = 2;
$is_edit = false;

if (isset($_GET['id'])) {
    $is_edit = true;
    $iduser = $_GET['id'];
    
    $q = "SELECT * FROM user WHERE iduser = ?";
    $res = $db->send_secure_query($q, [$iduser], 'i');
    if ($res->sukses && count($res->data) > 0) {
        $data = $res->data[0];
        $username = $data['username'];
        $password = $data['password'];
        $idrole = $data['idrole'];
    }
}

$q_role = "SELECT * FROM role";
$res_role = $db->send_query($q_role);
$list_role = $res_role->data;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Form User</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f7f6; padding: 20px; display: flex; justify-content: center; }
        .box { background: white; padding: 30px; width: 400px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        input, select { width: 100%; padding: 10px; margin: 10px 0; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #007bff; color: white; border: none; cursor: pointer; }
    </style>
</head>
<body>
    <div class="box">
        <h2><?php echo $is_edit ? "Edit User" : "Tambah User Baru"; ?></h2>
        
        <form action="user_proses.php" method="POST">
            <input type="hidden" name="aksi" value="<?php echo $is_edit ? 'update' : 'create'; ?>">
            <input type="hidden" name="iduser" value="<?php echo $iduser; ?>">

            <label>Username</label>
            <input type="text" name="username" value="<?php echo $username; ?>" required placeholder="Masukkan username">

            <label>Password</label>
            <input type="text" name="password" value="<?php echo $password; ?>" required placeholder="Masukkan password">

            <label>Role</label>
            <select name="idrole">
                <?php foreach($list_role as $role): ?>
                    <option value="<?php echo $role['idrole']; ?>" 
                        <?php if($role['idrole'] == $idrole) echo 'selected'; ?>>
                        <?php echo $role['nama_role']; ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Simpan Data</button>
        </form>
        <br>
        <a href="user_list.php">Batal</a>
    </div>
</body>
</html>