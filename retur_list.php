<?php
session_start();
require_once 'koneksi.php';

if (!isset($_SESSION['is_logged_in']) || !in_array($_SESSION['idrole'], [1, 3])) {
    header("Location: login.php");
    exit;
}

$db = new DbConnection();

$query = "SELECT r.*, v.nama_vendor, u.username, p.created_at as tgl_terima
          FROM retur r
          JOIN penerimaan p ON r.idpenerimaan = p.idpenerimaan
          JOIN pengadaan pg ON p.idpengadaan = pg.idpengadaan
          JOIN vendor v ON pg.vendor_idvendor = v.idvendor
          JOIN user u ON r.iduser = u.iduser
          ORDER BY r.idretur DESC";

$respon = $db->send_query($query);
$data_retur = $respon->data;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Riwayat Retur</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f7f6; padding: 20px; }
        .container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .btn { padding: 8px 12px; text-decoration: none; border-radius: 4px; color: white; font-size: 14px; }
        .btn-add { background-color: #dc3545; } 
        .btn-back { background-color: #6c757d; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #3d668fff; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Riwayat Retur ke Vendor</h1>
        <a href="dashboard.php" class="btn btn-back">Kembali</a>
        <a href="retur_form.php" class="btn btn-add">Buat Retur Baru</a>

        <table>
            <thead>
                <tr>
                    <th>ID Retur</th>
                    <th>Tanggal Retur</th>
                    <th>Dari Penerimaan</th>
                    <th>Vendor</th>
                    <th>Staff</th>
                    <th>Info</th>
                </tr>
                </thead>
            <tbody>
                <?php foreach($data_retur as $row): ?>
                <tr>
                    <td>#RET-<?php echo $row['idretur']; ?></td>
                    <td><?php echo $row['created_at']; ?></td>
                    <td>
                        #RCV-<?php echo $row['idpenerimaan']; ?> <br>
                        <small>(Tgl Terima: <?php echo $row['tgl_terima']; ?>)</small>
                    </td>
                    <td><?php echo htmlspecialchars($row['nama_vendor']); ?></td>
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                    
                    <td>
                        <a href="retur_detail.php?id=<?php echo $row['idretur']; ?>" 
                           style="background-color: #17a2b8; color: white; padding: 5px 10px; text-decoration: none; border-radius: 4px; font-size: 12px;">
                           Lihat Detail
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>