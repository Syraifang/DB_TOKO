<?php
session_start();
require_once 'koneksi.php';
if (!isset($_SESSION['is_logged_in']) || !in_array($_SESSION['idrole'], [1, 3])) exit("Akses Ditolak");

$db = new DbConnection();
$idpo = $_GET['idpo'];

$q_po = "SELECT p.*, v.nama_vendor FROM pengadaan p 
         JOIN vendor v ON p.vendor_idvendor = v.idvendor 
         WHERE p.idpengadaan = ?";
$res_po = $db->send_secure_query($q_po, [$idpo], 'i');
$po = $res_po->data[0];

$q_item = "SELECT dp.*, b.nama_barang 
           FROM detail_pengadaan dp 
           JOIN barang b ON dp.idbarang = b.idbarang 
           WHERE dp.idpengadaan = ?";
$res_item = $db->send_secure_query($q_item, [$idpo], 'i');
$items = $res_item->data;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Form Penerimaan</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f4f7f6; }
        .container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); width: 800px; margin: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        input[type="number"] { width: 80px; padding: 5px; }
        .btn-save { background: #28a745; color: white; padding: 10px 20px; border: none; cursor: pointer; font-size: 16px; margin-top: 20px;}
    </style>
</head>
<body>
    <div class="container">
        <h2>📦 Cek Barang Datang</h2>
        <p><strong>No PO:</strong> #PO-<?php echo $po['idpengadaan']; ?> | <strong>Vendor:</strong> <?php echo $po['nama_vendor']; ?></p>
        
        <form action="penerimaan_proses.php" method="POST">
            <input type="hidden" name="idpengadaan" value="<?php echo $idpo; ?>">

            <table>
                <thead>
                    <tr>
                        <th>Nama Barang</th>
                        <th>Jml Pesan</th>
                        <th>Jml Diterima (Input)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($items as $item): ?>
                    <tr>
                        <td>
                            <?php echo $item['nama_barang']; ?>
                            <input type="hidden" name="idbarang[]" value="<?php echo $item['idbarang']; ?>">
                            <input type="hidden" name="harga[]" value="<?php echo $item['harga_satuan']; ?>">
                        </td>
                        <td><?php echo $item['jumlah']; ?></td>
                        <td>
                            <input type="number" name="terima[]" value="<?php echo $item['jumlah']; ?>" min="0" required>
                        </td>
                        
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <button type="submit" class="btn-save" onclick="return confirm('Yakin data sudah benar? Stok akan bertambah.')">Simpan Penerimaan</button>
        </form>
    </div>
</body>
</html>