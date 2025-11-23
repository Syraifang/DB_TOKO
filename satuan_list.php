<?php
session_start();
require_once 'koneksi.php';

// Cek Role Admin
if (!isset($_SESSION['is_logged_in']) || $_SESSION['idrole'] != 1) {
    header("Location: login.php");
    exit;
}

$db = new DbConnection();

$query = "SELECT * FROM satuan ORDER BY idsatuan DESC";
$respon = $db->send_query($query);
$data_satuan = $respon->data;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Satuan</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f7f6; padding: 20px; }
        .container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        h1 { color: #333; }
        .btn { padding: 8px 12px; text-decoration: none; border-radius: 4px; color: white; font-size: 14px; }
        .btn-add { background-color: #28a745; }
        .btn-edit { background-color: #ffc107; color: black; }
        .btn-back { background-color: #6c757d; }
        .btn-on { background-color: #dc3545; } 
        .btn-off { background-color: #28a745; } 
        .btn-delete { background-color: #c82333; margin-left: 5px; }

        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #007bff; color: white; }
        
        .badge { padding: 5px 10px; border-radius: 10px; font-size: 12px; font-weight: bold; color: white;}
        .badge-active { background-color: #28a745; }
        .badge-inactive { background-color: #6c757d; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📏 Manajemen Satuan</h1>
        <a href="dashboard.php" class="btn btn-back">Kembali</a>
        <a href="satuan_form.php" class="btn btn-add">+ Tambah Satuan</a>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Satuan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($data_satuan as $row): ?>
                <tr>
                    <td><?php echo $row['idsatuan']; ?></td>
                    <td><strong><?php echo htmlspecialchars($row['nama_satuan']); ?></strong></td>
                    
                    <td>
                        <?php if($row['status'] == 1): ?>
                            <span class="badge badge-active">AKTIF</span>
                        <?php else: ?>
                            <span class="badge badge-inactive">NON-AKTIF</span>
                        <?php endif; ?>
                    </td>

                    <td>
                        <a href="satuan_form.php?id=<?php echo $row['idsatuan']; ?>" class="btn btn-edit">Edit</a>
                        
                        <?php if($row['status'] == 1): ?>
                            <a href="satuan_proses.php?aksi=toggle&id=<?php echo $row['idsatuan']; ?>&status=0" 
                               class="btn btn-on" onclick="return confirm('Matikan satuan ini?')">Off</a>
                        <?php else: ?>
                            <a href="satuan_proses.php?aksi=toggle&id=<?php echo $row['idsatuan']; ?>&status=1" 
                               class="btn btn-off" onclick="return confirm('Aktifkan satuan ini?')">On</a>
                        <?php endif; ?>

                        <a href="satuan_proses.php?aksi=hapus&id=<?php echo $row['idsatuan']; ?>" 
                           class="btn btn-delete" 
                           onclick="return confirm('Yakin hapus permanen? \nJika satuan ini dipakai oleh Barang, penghapusan akan GAGAL.')">Hapus</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>