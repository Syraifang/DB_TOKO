<?php
session_start();
require_once 'koneksi.php';

if (!isset($_SESSION['is_logged_in']) || !in_array($_SESSION['idrole'], [1, 2, 4])) {
    header("Location: login.php");
    exit;
}

$db = new DbConnection();

$query = "SELECT p.*, u.username 
          FROM penjualan p 
          JOIN user u ON p.iduser = u.iduser 
          ORDER BY p.created_at DESC";
$respon = $db->send_query($query);
$data_transaksi = $respon->data;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Riwayat Transaksi</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f7f6; padding: 20px; }
        .container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .btn { padding: 8px 12px; text-decoration: none; border-radius: 4px; color: white; font-size: 14px; }
        .btn-detail { background-color: #17a2b8; }
        .btn-back { background-color: #6c757d; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #007bff; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📄 Riwayat Transaksi</h1>
        <a href="dashboard.php" class="btn btn-back">Kembali ke Dashboard</a>
        
        <table>
            <thead>
                <tr>
                    <th>No Nota</th>
                    <th>Tanggal & Jam</th>
                    <th>User</th>
                    <th>Total Belanja</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($data_transaksi as $row): ?>
                <tr>
                    <td>#TRX-<?php echo $row['idpenjualan']; ?></td>
                    <td><?php echo $row['created_at']; ?></td>
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                    <td style="font-weight: bold;">Rp <?php echo number_format($row['total_nilai'], 0, ',', '.'); ?></td>
                    <td>
                        <a href="transaksi_detail.php?id=<?php echo $row['idpenjualan']; ?>" class="btn btn-detail">Lihat Detail</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>