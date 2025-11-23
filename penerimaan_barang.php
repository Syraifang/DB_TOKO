<?php
session_start();
require_once 'koneksi.php';

if (!isset($_SESSION['is_logged_in']) || !in_array($_SESSION['idrole'], [1, 3])) {
    header("Location: login.php");
    exit;
}

$db = new DbConnection();

$query = "SELECT p.*, v.nama_vendor, u.username 
          FROM pengadaan p
          JOIN vendor v ON p.vendor_idvendor = v.idvendor
          JOIN user u ON p.user_iduser = u.iduser
          WHERE p.status = 'P'
          ORDER BY p.idpengadaan ASC";

$respon = $db->send_query($query);
$data_po = $respon->data;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Penerimaan Barang</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f7f6; padding: 20px; }
        .container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .btn { padding: 8px 12px; text-decoration: none; border-radius: 4px; color: white; font-size: 14px; }
        .btn-process { background-color: #17a2b8; }
        .btn-back { background-color: #6c757d; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #28a745; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚚 Penerimaan Barang Masuk</h1>
        <p>Daftar Pesanan (PO) yang belum diterima:</p>
        <a href="dashboard.php" class="btn btn-back">Kembali</a>

        <?php if(empty($data_po)): ?>
            <p style="text-align:center; margin-top:20px; color:#888;">Tidak ada barang yang perlu diterima saat ini.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID PO</th>
                        <th>Tanggal Pesan</th>
                        <th>Vendor</th>
                        <th>Staff Pemesan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($data_po as $row): ?>
                    <tr>
                        <td>#PO-<?php echo $row['idpengadaan']; ?></td>
                        <td><?php echo $row['timestamp']; ?></td>
                        <td><?php echo htmlspecialchars($row['nama_vendor']); ?></td>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td>
                            <a href="penerimaan_form.php?idpo=<?php echo $row['idpengadaan']; ?>" class="btn btn-process">Proses Terima</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>