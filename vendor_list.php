<?php
session_start();
require_once 'koneksi.php';

if (!isset($_SESSION['is_logged_in']) || $_SESSION['idrole'] != 1) {
    header("Location: login.php");
    exit;
}

$db = new DbConnection();

$query = "SELECT * FROM vendor ORDER BY idvendor DESC";
$respon = $db->send_query($query);
$data_vendor = $respon->data;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Vendor</title>
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
        <h1>🏢 Manajemen Vendor</h1>
        <a href="dashboard.php" class="btn btn-back">Kembali</a>
        <a href="vendor_form.php" class="btn btn-add">+ Tambah Vendor</a>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Vendor</th>
                    <th>Badan Hukum</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($data_vendor as $row): ?>
                <tr>
                    <td><?php echo $row['idvendor']; ?></td>
                    <td><?php echo htmlspecialchars($row['nama_vendor']); ?></td>
                    <td>
                        <?php 
                            if ($row['badan_hukum'] == 'P') echo 'PT';
                            elseif ($row['badan_hukum'] == 'C') echo 'CV';
                            else echo 'Perorangan/Lainnya';
                        ?>
                    </td>
                    
                    <td>
                        <?php if($row['status'] == 'A'): ?>
                            <span class="badge badge-active">AKTIF</span>
                        <?php else: ?>
                            <span class="badge badge-inactive">NON-AKTIF</span>
                        <?php endif; ?>
                    </td>

                    <td>
                        <a href="vendor_form.php?id=<?php echo $row['idvendor']; ?>" class="btn btn-edit">Edit</a>
                        
                        <?php if($row['status'] == 'A'): ?>
                            <a href="vendor_proses.php?aksi=toggle&id=<?php echo $row['idvendor']; ?>&status=T" 
                               class="btn btn-on" onclick="return confirm('Non-aktifkan vendor ini?')">Non-Aktifkan</a>
                        <?php else: ?>
                            <a href="vendor_proses.php?aksi=toggle&id=<?php echo $row['idvendor']; ?>&status=A" 
                               class="btn btn-off" onclick="return confirm('Aktifkan vendor ini?')">Aktifkan</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>