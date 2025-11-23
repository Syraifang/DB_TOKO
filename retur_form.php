<?php
session_start();
require_once 'koneksi.php';
if (!isset($_SESSION['is_logged_in']) || !in_array($_SESSION['idrole'], [1, 3])) exit("Akses Ditolak");

$db = new DbConnection();
$step = 1;
$items = [];
$idpenerimaan = "";

if (isset($_GET['idpenerimaan'])) {
    $step = 2;
    $idpenerimaan = $_GET['idpenerimaan'];

    $q_items = "SELECT dp.*, b.nama_barang 
                FROM detail_penerimaan dp 
                JOIN barang b ON dp.barang_idbarang = b.idbarang 
                WHERE dp.idpenerimaan = ?";
    $res_items = $db->send_secure_query($q_items, [$idpenerimaan], 'i');
    $items = $res_items->data;
}

$q_rcv = "SELECT p.idpenerimaan, p.created_at, v.nama_vendor 
          FROM penerimaan p
          JOIN pengadaan pg ON p.idpengadaan = pg.idpengadaan
          JOIN vendor v ON pg.vendor_idvendor = v.idvendor
          ORDER BY p.idpenerimaan DESC LIMIT 20";
$list_rcv = $db->send_query($q_rcv)->data;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Form Retur</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f4f7f6; }
        .container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); width: 800px; margin: auto; }
        select, input[type=text], button { padding: 10px; margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        .btn-next { background: #007bff; color: white; border: none; cursor: pointer; }
        .btn-save { background: #dc3545; color: white; border: none; cursor: pointer; font-size: 16px; margin-top: 20px; width: 100%; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Buat Retur Pembelian</h2>
        <a href="retur_list.php">Kembali</a>
        <hr>

        <form method="GET">
            <label>Pilih Sumber Penerimaan:</label>
            <select name="idpenerimaan" required>
                <option value="">-- Pilih Transaksi Penerimaan --</option>
                <?php foreach($list_rcv as $rcv): ?>
                    <option value="<?php echo $rcv['idpenerimaan']; ?>" <?php if($idpenerimaan == $rcv['idpenerimaan']) echo 'selected'; ?>>
                        #RCV-<?php echo $rcv['idpenerimaan']; ?> | <?php echo $rcv['nama_vendor']; ?> (<?php echo $rcv['created_at']; ?>)
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn-next">Lihat Barang</button>
        </form>

        <?php if($step == 2): ?>
            <form action="retur_proses.php" method="POST">
                <input type="hidden" name="idpenerimaan" value="<?php echo $idpenerimaan; ?>">
                
                <h3>Daftar Barang di Penerimaan #<?php echo $idpenerimaan; ?></h3>
                <p style="color: #666; font-size: 14px;">Isi jumlah pada barang yang ingin diretur. Kosongkan atau isi 0 jika barang baik.</p>

                <table>
                    <thead>
                        <tr>
                            <th>Nama Barang</th>
                            <th>Jml Diterima</th>
                            <th>Jml Retur</th>
                            <th>Alasan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($items as $item): ?>
                        <tr>
                            <td>
                                <?php echo $item['nama_barang']; ?>
                                <input type="hidden" name="iddetail_penerimaan[]" value="<?php echo $item['iddetail_penerimaan']; ?>">
                            </td>
                            <td><?php echo $item['jumlah_terima']; ?></td>
                            <td>
                                <input type="number" name="jml_retur[]" min="0" max="<?php echo $item['jumlah_terima']; ?>" value="0" style="width: 60px;">
                            </td>
                            <td>
                                <input type="text" name="alasan[]" placeholder="Cth: Rusak/Basi" style="width: 100%;">
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <button type="submit" class="btn-save" onclick="return confirm('Yakin retur barang ini? Stok akan berkurang otomatis.')">PROSES RETUR</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>