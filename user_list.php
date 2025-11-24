<?php
session_start();
require_once 'koneksi.php';

if (!isset($_SESSION['is_logged_in']) || $_SESSION['idrole'] != 1) {
    header("Location: login.php");
    exit;
}

$db = new DbConnection();

$query = "SELECT * FROM v_daftar_user ORDER BY iduser DESC"; // view
$respon = $db->send_query($query);
$data_user = $respon->data;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen User</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f7f6; padding: 20px; }
        .container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        h1 { color: #333; }
        .btn { padding: 8px 12px; text-decoration: none; border-radius: 4px; color: white; font-size: 14px; border: none; cursor: pointer;}
        .btn-add { background-color: #28a745; }
        .btn-edit { background-color: #ffc107; color: black; }
        .btn-delete { background-color: #c82333; }
        .btn-back { background-color: #6c757d; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #007bff; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <h1>👥 Manajemen User</h1>
        <a href="dashboard.php" class="btn btn-back">Kembali</a>
        <a href="user_form.php" class="btn btn-add">+ Tambah User Baru</a>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Password</th>
                    <th>Role</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($data_user as $row): ?>
                <tr>
                    <td><?php echo $row['iduser']; ?></td>
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                    <td><?php echo htmlspecialchars($row['password']); ?></td>
                    <td><strong><?php echo $row['nama_role']; ?></strong></td>
                    <td>
                        <a href="user_form.php?id=<?php echo $row['iduser']; ?>" class="btn btn-edit">Edit</a>
                        
                        <?php if($row['iduser'] != $_SESSION['iduser']): ?>
                            <a href="user_proses.php?aksi=hapus&id=<?php echo $row['iduser']; ?>" 
                               class="btn btn-delete" 
                               onclick="return confirm('Yakin ingin menghapus user ini?')">Hapus</a>
                        <?php else: ?>
                            <span style="color:#aaa; font-size:12px;"></span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>