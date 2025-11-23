<?php
session_start();
require_once 'koneksi.php';

if (!isset($_SESSION['is_logged_in']) || !in_array($_SESSION['idrole'], [1, 2])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) exit("ID Transaksi tidak ditemukan.");

$db = new DbConnection();
$idpenjualan = $_GET['id'];

$q_header = "SELECT p.*, u.username 
             FROM penjualan p 
             JOIN user u ON p.iduser = u.iduser 
             WHERE p.idpenjualan = ?";
$res_header = $db->send_secure_query($q_header, [$idpenjualan], 'i');

if (!$res_header->sukses || count($res_header->data) == 0) exit("Data tidak ditemukan.");
$header = $res_header->data[0];

$q_detail = "SELECT dp.*, b.nama_barang 
             FROM detail_penjualan dp 
             JOIN barang b ON dp.idbarang = b.idbarang 
             WHERE dp.penjualan_idpenjualan = ?";
$res_detail = $db->send_secure_query($q_detail, [$idpenjualan], 'i');
$items = $res_detail->data;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Detail Transaksi #<?php echo $idpenjualan; ?></title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f7f6; padding: 20px; display: flex; justify-content: center; }
        .invoice-box { background: white; padding: 30px; width: 600px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { text-align: center; margin-top: 0; color: #333; }
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { padding: 5px; }
        
        .item-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .item-table th, .item-table td { border-bottom: 1px solid #ddd; padding: 10px; text-align: left; }
        .item-table th { background-color: #f8f9fa; }
        
        .total-section { margin-top: 20px; text-align: right; }
        .total-row { font-size: 18px; font-weight: bold; color: #28a745; }
        
        .btn-print { background: #007bff; color: white; padding: 10px 20px; border: none; cursor: pointer; text-decoration: none; display: inline-block; margin-top: 20px;}
        .btn-back { color: #666; text-decoration: none; margin-right: 15px; }
        
        @media print {
            .no-print { display: none; }
            body { background: white; }
            .invoice-box { box-shadow: none; width: 100%; }
        }
    </style>
</head>
<body>

    <div class="invoice-box">
        <h2>TOKOKU</h2>
        <p style="text-align: center;">Tidak Menerima Keluhan</p>
        <hr>

        <table class="info-table">
            <tr>
                <td><strong>No Nota:</strong> #TRX-<?php echo $header['idpenjualan']; ?></td>
                <td style="text-align: right;"><strong>Tanggal:</strong> <?php echo $header['created_at']; ?></td>
            </tr>
            <tr>
                <td><strong>Kasir:</strong> <?php echo htmlspecialchars($header['username']); ?></td>
                <td></td>
            </tr>
        </table>

        <table class="item-table">
            <thead>
                <tr>
                    <th>Barang</th>
                    <th>Harga</th>
                    <th>Qty</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($items as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['nama_barang']); ?></td>
                    <td><?php echo number_format($item['harga_satuan']); ?></td>
                    <td><?php echo $item['jumlah']; ?></td>
                    <td><?php echo number_format($item['subtotal']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="total-section">
            <p>Subtotal: Rp <?php echo number_format($header['subtotal_nilai']); ?></p>
            <p>PPN (10%): Rp <?php echo number_format($header['ppn']); ?></p>
            <p class="total-row">TOTAL: Rp <?php echo number_format($header['total_nilai']); ?></p>
        </div>

        <div class="no-print" style="text-align: center;">
            <a href="riwayat_transaksi.php" class="btn-back">Kembali</a>
            <button onclick="window.print()" class="btn-print">Cetak Struk 🖨️</button>
        </div>
    </div>

</body>
</html>