<?php
session_start();
require_once 'koneksi.php';

// Cek Login & Role (Hanya Admin)
if (!isset($_SESSION['is_logged_in']) || $_SESSION['idrole'] != 1) {
    header("Location: login.php");
    exit;
}

$db = new DbConnection();

// Query: Ambil SEMUA barang (Aktif & Mati) beserta nama satuannya
// Kita pakai Raw SQL + JOIN manual agar bisa lihat status 0
$query = "SELECT b.*, s.nama_satuan 
          FROM barang b 
          JOIN satuan s ON b.idsatuan = s.idsatuan 
          ORDER BY b.idbarang DESC";
$respon = $db->send_query($query);
$data_barang = $respon->data;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Barang</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f7f6; padding: 20px; }
        .container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        h1 { color: #333; }
        .btn { padding: 8px 12px; text-decoration: none; border-radius: 4px; color: white; font-size: 14px; border: none; cursor: pointer;}
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
        .badge-inactive { background-color: #dc3545; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📦 Manajemen Barang</h1>
        <a href="dashboard.php" class="btn btn-back">Kembali ke Dashboard</a>
        <a href="barang_form.php" class="btn btn-add">+ Tambah Barang</a>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Barang</th>
                    <th>Satuan</th>
                    <th>Harga Modal</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($data_barang as $row): ?>
                <tr>
                    <td><?php echo $row['idbarang']; ?></td>
                    <td><?php echo htmlspecialchars($row['nama_barang']); ?></td>
                    <td><?php echo $row['nama_satuan']; ?></td>
                    <td>Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                    
                    <td>
                        <?php if($row['status'] == 1): ?>
                            <span class="badge badge-active">AKTIF</span>
                        <?php else: ?>
                            <span class="badge badge-inactive">NON-AKTIF</span>
                        <?php endif; ?>
                    </td>

                    <td>
                        <a href="barang_form.php?id=<?php echo $row['idbarang']; ?>" class="btn btn-edit">Edit</a>
                        
                        <?php if($row['status'] == 1): ?>
                            <a href="barang_proses.php?aksi=toggle&id=<?php echo $row['idbarang']; ?>&status=0" 
                               class="btn btn-on" onclick="return confirm('Matikan barang ini?')">Matikan</a>
                        <?php else: ?>
                            <a href="barang_proses.php?aksi=toggle&id=<?php echo $row['idbarang']; ?>&status=1" 
                               class="btn btn-off" onclick="return confirm('Aktifkan barang ini?')">Aktifkan</a>
                        <?php endif; ?>
                            <a href="barang_proses.php?aksi=hapus_permanen&id=<?php echo $row['idbarang']; ?>" 
                                class="btn btn-delete" onclick="return confirm('⚠️ PERINGATAN: Data akan hilang selamanya! \nJika barang ini pernah dijual, penghapusan akan GAGAL demi keamanan data.\n\nYakin ingin menghapus?')">Hapus</a>
                        <style>.btn-delete { background-color: #c82333; margin-left: 5px; }</style>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>