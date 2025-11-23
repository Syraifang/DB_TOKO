<?php
session_start();
require_once 'koneksi.php';

if (!isset($_SESSION['is_logged_in']) || $_SESSION['idrole'] != 1) {
    header("Location: login.php");
    exit;
}

$db = new DbConnection();

$query = "SELECT p.*, v.nama_vendor, u.username 
          FROM pengadaan p
          JOIN vendor v ON p.vendor_idvendor = v.idvendor
          JOIN user u ON p.user_iduser = u.iduser
          ORDER BY p.idpengadaan DESC";

$respon = $db->send_query($query);
$data_po = $respon->data;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Pengadaan</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f7f6; padding: 20px; }
        .container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .btn { padding: 8px 12px; text-decoration: none; border-radius: 4px; color: white; font-size: 14px; }
        .btn-add { background-color: #28a745; }
        .btn-back { background-color: #6c757d; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #007bff; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📦 Pengadaan</h1>
        <a href="dashboard.php" class="btn btn-back">Kembali</a>
        <a href="pengadaan_baru.php" class="btn btn-add">+ Buat Baru</a>

        <table>
            <thead>
                <tr>
                    <th>ID PO</th>
                    <th>Tanggal</th>
                    <th>Vendor</th>
                    <th>Staff</th>
                    <th>Total Nilai</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($data_po as $row): ?>
                <tr>
                    <td>#PO-<?php echo $row['idpengadaan']; ?></td>
                    <td><?php echo $row['timestamp']; ?></td>
                    <td><?php echo htmlspecialchars($row['nama_vendor']); ?></td>
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                    <td>Rp <?php echo number_format($row['total_nilai'], 0, ',', '.'); ?></td>
                    <td>
                        <?php if($row['status'] == 'P'): ?>
                            <span style="color: orange; font-weight: bold;">Menunggu Barang</span>
                        <?php else: ?>
                            <span style="color: green; font-weight: bold;">Selesai</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>