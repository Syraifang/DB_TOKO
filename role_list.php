<?php
session_start();
require_once 'koneksi.php';

if (!isset($_SESSION['is_logged_in']) || $_SESSION['idrole'] != 1) {
    header("Location: login.php");
    exit;
}

$db = new DbConnection();
$query = "SELECT * FROM role ORDER BY idrole ASC";
$respon = $db->send_query($query);
$data_role = $respon->data;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Role</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f7f6; padding: 20px; }
        .container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .btn { padding: 8px 12px; text-decoration: none; border-radius: 4px; color: white; font-size: 14px; }
        .btn-add { background-color: #28a745; }
        .btn-edit { background-color: #ffc107; color: black; }
        .btn-back { background-color: #6c757d; }
        .btn-delete { background-color: #c82333; margin-left: 5px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #007bff; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔑 Manajemen Role</h1>
        <a href="dashboard.php" class="btn btn-back">Kembali</a>
        <a href="role_form.php" class="btn btn-add">Tambah Role</a>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Role</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($data_role as $row): ?>
                <tr>
                    <td><?php echo $row['idrole']; ?></td>
                    <td><strong><?php echo htmlspecialchars($row['nama_role']); ?></strong></td>
                    <td>
                        <a href="role_form.php?id=<?php echo $row['idrole']; ?>" class="btn btn-edit">Edit</a>
                        
                        <?php if($row['idrole'] != 1): ?>
                            <a href="role_proses.php?aksi=hapus&id=<?php echo $row['idrole']; ?>" 
                               class="btn btn-delete" 
                               onclick="return confirm('Hapus role ini? Pastikan tidak ada user yang menggunakan role ini.')">Hapus</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>