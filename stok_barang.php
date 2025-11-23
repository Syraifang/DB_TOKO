<?php
session_start();
require_once 'koneksi.php';

if (!isset($_SESSION['is_logged_in']) || !in_array($_SESSION['idrole'], [1, 3])) {
    header("Location: login.php");
    exit;
}

$db = new DbConnection();


$query = "SELECT * FROM v_stok_barang_terkini ORDER BY nama_barang ASC";
$respon = $db->send_query($query);
$data_stok = $respon->data;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Cek Stok Gudang</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f7f6; padding: 20px; }
        .container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .btn-back { background-color: #6c757d; color: white; padding: 8px 12px; text-decoration: none; border-radius: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #17a2b8; color: white; }
        .warning { background-color: #f8d7da; color: #721c24; font-weight: bold; } 
        .safe { background-color: #d4edda; color: #155724; } 
    </style>
</head>
<body>
    <div class="container">
        <h1>📦 Laporan Stok Gudang</h1>
        <a href="dashboard.php" class="btn-back">Kembali ke Dashboard</a>

        <table>
            <thead>
                <tr>
                    <th>ID Barang</th>
                    <th>Nama Barang</th>
                    <th>Sisa Stok Fisik</th>
                    <th>Status Stok</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($data_stok as $row): ?>
                <tr>
                    <td><?php echo $row['idbarang']; ?></td>
                    <td><?php echo htmlspecialchars($row['nama_barang']); ?></td>
                    
                    <td style="font-size: 18px; font-weight: bold;">
                        <?php echo $row['stok_terkini']; ?>
                    </td>

                    <?php if ($row['stok_terkini'] == 0): ?>
                        <td style="background: #9e1c1cff; color: #ffffffff;">⚠️ HABIS</td>
                    <?php elseif ($row['stok_terkini'] > 0 && $row['stok_terkini'] <= 5): ?>
                        <td style="background: #ce3c3cff; color: #ffffffff;">⚠️ MAU HABIS</td>
                    <?php elseif ($row['stok_terkini'] <= 10): ?>
                        <td style="background: #fff3cd; color: #856404;">⚠️ MENIPIS</td>
                    <?php else: ?>
                        <td class="safe">✅ AMAN</td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>