<?php
session_start();
require_once 'koneksi.php';
if (!isset($_SESSION['is_logged_in']) || $_SESSION['idrole'] != 1) exit("Akses Ditolak");

$db = new DbConnection();

$query = "SELECT m.*, u.username 
          FROM margin_penjualan m 
          JOIN user u ON m.iduser = u.iduser 
          ORDER BY m.idmargin_penjualan DESC";
$respon = $db->send_query($query);
$data_margin = $respon->data;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Margin</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f7f6; padding: 20px; }
        .container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        h1 { color: #333; }
        .btn { padding: 8px 12px; text-decoration: none; border-radius: 4px; color: white; font-size: 14px; }
        .btn-add { background-color: #28a745; }
        .btn-back { background-color: #6c757d; }
        .btn-on { background-color: #dc3545; } 
        .btn-off { background-color: #28a745; } 

        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #007bff; color: white; }
        .badge { padding: 5px 10px; border-radius: 10px; font-size: 12px; font-weight: bold; color: white;}
        .badge-active { background-color: #28a745; }
        .badge-inactive { background-color: #6c757d; }
        .btn-delete { background-color: #c82333; margin-left: 5px; }
        .btn-edit { background-color: #ffc107; color: black; }
</style>
    </style>
</head>
<body>
    <div class="container">
        <h1>💰 Manajemen Margin Keuntungan</h1>
        <a href="dashboard.php" class="btn btn-back">Kembali</a>
        <a href="margin_form.php" class="btn btn-add">+ Atur Margin Baru</a>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Persentase Profit</th>
                    <th>Dibuat Oleh</th>
                    <th>Tanggal Dibuat</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($data_margin as $row): ?>
            <tr>
                <td><?php echo $row['idmargin_penjualan']; ?></td>
                <td><strong><?php echo $row['persen']; ?>%</strong></td>
                <td><?php echo htmlspecialchars($row['username']); ?></td>
                <td><?php echo $row['created_at']; ?></td>
        
                <td>
                    <?php if($row['status'] == 1): ?>
                        <span class="badge badge-active">AKTIF</span>
                    <?php else: ?>
                        <span class="badge badge-inactive">NON-AKTIF</span>
                    <?php endif; ?>
                </td>

                <td>
                    <a href="margin_form.php?id=<?php echo $row['idmargin_penjualan']; ?>" class="btn btn-edit">Edit</a>
                    
                    <?php if($row['status'] == 1): ?>
                        <a href="margin_proses.php?aksi=toggle&id=<?php echo $row['idmargin_penjualan']; ?>&status=0" 
                        class="btn btn-on" onclick="return confirm('Non-aktifkan margin ini?')">Matikan</a>
                    <?php else: ?>
                        <a href="margin_proses.php?aksi=toggle&id=<?php echo $row['idmargin_penjualan']; ?>&status=1" 
                        class="btn btn-off" onclick="return confirm('Aktifkan margin ini?')">Aktifkan</a>
                    <?php endif; ?>

                    <a href="margin_proses.php?aksi=hapus&id=<?php echo $row['idmargin_penjualan']; ?>" 
                    class="btn btn-delete" 
                    onclick="return confirm('Yakin hapus permanen? \nJika margin ini sudah dipakai di penjualan, penghapusan akan GAGAL.')">Hapus</a>
                </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>