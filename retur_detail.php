<?php
session_start();
require_once 'koneksi.php';

if (!isset($_SESSION['is_logged_in']) || !in_array($_SESSION['idrole'], [1, 3])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) exit("ID Retur tidak ditemukan.");

$db = new DbConnection();
$idretur = $_GET['id'];

$q_header = "SELECT r.*, v.nama_vendor, v.badan_hukum, u.username, p.idpenerimaan 
             FROM retur r
             JOIN penerimaan p ON r.idpenerimaan = p.idpenerimaan
             JOIN pengadaan pg ON p.idpengadaan = pg.idpengadaan
             JOIN vendor v ON pg.vendor_idvendor = v.idvendor
             JOIN user u ON r.iduser = u.iduser
             WHERE r.idretur = ?";
             
$res_header = $db->send_secure_query($q_header, [$idretur], 'i');

if (!$res_header->sukses || count($res_header->data) == 0) exit("Data tidak ditemukan.");
$header = $res_header->data[0];

$q_detail = "SELECT dr.*, b.nama_barang 
             FROM detail_retur dr
             JOIN detail_penerimaan dp ON dr.iddetail_penerimaan = dp.iddetail_penerimaan
             JOIN barang b ON dp.barang_idbarang = b.idbarang
             WHERE dr.idretur = ?";
             
$res_detail = $db->send_secure_query($q_detail, [$idretur], 'i');
$items = $res_detail->data;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Detail Retur #RET-<?php echo $idretur; ?></title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f7f6; padding: 20px; display: flex; justify-content: center; }
        .invoice-box { background: white; padding: 30px; width: 700px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { text-align: center; margin-top: 0; color: #dc3545; } /* Merah untuk menandakan Barang Keluar */
        
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { padding: 5px; vertical-align: top; }
        
        .item-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .item-table th, .item-table td { border-bottom: 1px solid #ddd; padding: 10px; text-align: left; }
        .item-table th { background-color: #f8f9fa; }
        
        .btn-back { background: #6c757d; color: white; padding: 10px 20px; border: none; cursor: pointer; text-decoration: none; display: inline-block; margin-top: 20px; border-radius: 4px;}
        
        @media print {
            .no-print { display: none; }
            body { background: white; }
            .invoice-box { box-shadow: none; width: 100%; }
        }
    </style>
</head>
<body>

    <div class="invoice-box">
        <h2>BUKTI PENGEMBALIAN BARANG (RETUR)</h2>
        <hr>

        <table class="info-table">
            <tr>
                <td width="60%">
                    <strong>Kepada Vendor:</strong><br>
                    <?php echo htmlspecialchars($header['nama_vendor']); ?> (<?php echo $header['badan_hukum']; ?>)<br>
                    <small>Referensi: Penerimaan #RCV-<?php echo $header['idpenerimaan']; ?></small>
                </td>
                <td width="40%" style="text-align: right;">
                    <strong>No Retur:</strong> #RET-<?php echo $header['idretur']; ?><br>
                    <strong>Tanggal:</strong> <?php echo $header['created_at']; ?><br>
                    <strong>Staff:</strong> <?php echo htmlspecialchars($header['username']); ?>
                </td>
            </tr>
        </table>

        <table class="item-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Barang</th>
                    <th>Jumlah Retur</th>
                    <th>Alasan Pengembalian</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                foreach($items as $item): 
                ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><strong><?php echo htmlspecialchars($item['nama_barang']); ?></strong></td>
                    <td style="color: red; font-weight: bold;">- <?php echo $item['jumlah']; ?></td>
                    <td><?php echo htmlspecialchars($item['alasan']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="no-print" style="text-align: center;">
            <a href="retur_list.php" class="btn-back">Kembali</a>
            <button onclick="window.print()" class="btn-back" style="background-color: #007bff; margin-left: 10px;">Cetak Bukti 🖨️</button>
        </div>
    </div>

</body>
</html>