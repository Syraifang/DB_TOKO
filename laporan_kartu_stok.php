<?php
session_start();
require_once 'koneksi.php';

if (!isset($_SESSION['is_logged_in']) || !in_array($_SESSION['idrole'], [1, 3])) {
    header("Location: login.php");
    exit;
}

$db = new DbConnection();

// view 4
$query = "SELECT * FROM v_kartu_stok_lengkap ORDER BY tanggal DESC";
$respon = $db->send_query($query);
$data_kartu = $respon->data;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Riwayat Kartu Stok</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f7f6; padding: 20px; }
        .container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .btn-back { background-color: #6c757d; color: white; padding: 8px 12px; text-decoration: none; border-radius: 4px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #17a2b8; color: white; }
        
        .badge { padding: 5px 10px; border-radius: 4px; font-weight: bold; font-size: 12px; color: white; }
        .masuk { background-color: #28a745; }
        .keluar { background-color: #dc3545; } 
    </style>
</head>
<body>
    <div class="container">
        <h1>📜 Riwayat Keluar Masuk Barang</h1>
        <a href="dashboard.php" class="btn-back">Kembali ke Dashboard</a>

        <table>
            <thead>
                <tr>
                    <th>Tanggal & Jam</th>
                    <th>Nama Barang</th>
                    <th>Aktifitas</th>
                    <th>Masuk</th>
                    <th>Keluar</th>
                    <th>Sisa Stok</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($data_kartu as $row): ?>
                <tr>
                    <td><?php echo $row['tanggal']; ?></td>
                    <td><?php echo htmlspecialchars($row['nama_barang']); ?></td>
                    
                    <td>
                        <?php if($row['jenis_transaksi'] == 'M'): ?>
                            <span class="badge masuk">MASUK</span>
                        <?php else: ?>
                            <span class="badge keluar">KELUAR</span>
                        <?php endif; ?>
                    </td>

                    <td style="color: green; font-weight: bold;">
                        <?php echo ($row['masuk'] > 0) ? "+".$row['masuk'] : "-"; ?>
                    </td>
                    
                    <td style="color: red; font-weight: bold;">
                        <?php echo ($row['keluar'] > 0) ? "-".$row['keluar'] : "-"; ?>
                    </td>

                    <td style="font-weight: bold; background-color: #f8f9fa;">
                        <?php echo $row['sisa_stok']; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>