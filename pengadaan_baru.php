<?php
session_start();
require_once 'koneksi.php';
if ($_SESSION['idrole'] != 1) exit("Akses Ditolak"); // Hanya Admin

$db = new DbConnection();

if (!isset($_SESSION['cart_po'])) $_SESSION['cart_po'] = [];


if (isset($_POST['aksi']) && $_POST['aksi'] == 'tambah') {
    $idbarang = $_POST['idbarang'];
    $jumlah = $_POST['jumlah'];
    
    $res = $db->send_secure_query("SELECT * FROM barang WHERE idbarang=?", [$idbarang], 'i');
    if ($res->sukses && count($res->data) > 0) {
        $b = $res->data[0];

        $found = false;
        foreach ($_SESSION['cart_po'] as $k => $v) {
            if ($v['id'] == $idbarang) {
                $_SESSION['cart_po'][$k]['jumlah'] += $jumlah;
                $found = true;
                break;
            }
        }
        
        if (!$found) {
            $_SESSION['cart_po'][] = [
                'id' => $b['idbarang'],
                'nama' => $b['nama_barang'],
                'harga' => $b['harga'],
                'jumlah' => $jumlah
            ];
        }
    }
}

if (isset($_GET['reset'])) {
    unset($_SESSION['cart_po']);
    header("Location: pengadaan_baru.php");
    exit;
}

$vendors = $db->send_query("SELECT * FROM v_vendor_aktif")->data;
$barangs = $db->send_query("SELECT * FROM barang WHERE status=1")->data;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Buat PO Baru</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f4f7f6; }
        .container { background: white; padding: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); display: flex; gap: 20px;}
        .left, .right { flex: 1; }
        input, select, button { width: 100%; padding: 10px; margin-bottom: 10px; box-sizing: border-box;}
        .btn-add { background: #007bff; color: white; border: none; cursor: pointer;}
        .btn-save { background: #28a745; color: white; border: none; cursor: pointer; font-size: 18px;}
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; }
    </style>
</head>
<body>
    <h1>📝 Buat Pengadaan</h1>
    <a href="pengadaan_list.php" class="btn btn-back">Kembali</a>
    <br><br>

    <div class="container">
        <div class="left">
            <h3>Pilih Barang</h3>
            <form method="POST">
                <input type="hidden" name="aksi" value="tambah">
                
                <label>Nama Barang</label>
                <select name="idbarang" required>
                    <?php foreach($barangs as $b): ?>
                        <option value="<?php echo $b['idbarang']; ?>">
                            <?php echo $b['nama_barang']; ?> (Harga Beli: <?php echo $b['harga']; ?>)
                        </option>
                    <?php endforeach; ?>
                </select>

                <label>Jumlah Pesan</label>
                <input type="number" name="jumlah" value="10" min="1" required>

                <button type="submit" class="btn-add">Masukkan ke Daftar</button>
            </form>
        </div>

        <div class="right">
    <h3>Daftar Barang yang Dipesan</h3>
    
    <?php if(!empty($_SESSION['cart_po'])): ?>
        <form action="pengadaan_proses.php" method="POST">
            
            <label>Pilih Vendor (Supplier)</label>
            <select name="idvendor" required style="background: #fff3cd;">
                <option value="">-- Pilih Vendor --</option>
                <?php foreach($vendors as $v): ?>
                    <option value="<?php echo $v['idvendor']; ?>"><?php echo $v['nama_vendor']; ?></option>
                <?php endforeach; ?>
            </select>

            <table style="margin-bottom: 20px;">
                <thead>
                    <tr>
                        <th>Barang</th>
                        <th>Harga</th>
                        <th>Jml</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $total_subtotal = 0;
                    foreach($_SESSION['cart_po'] as $item): 
                        $sub = $item['harga'] * $item['jumlah'];
                        $total_subtotal += $sub;
                    ?>
                    <tr>
                        <td><?php echo $item['nama']; ?></td>
                        <td><?php echo number_format($item['harga']); ?></td>
                        <td><?php echo $item['jumlah']; ?></td>
                        <td><?php echo number_format($sub); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                
                <?php 
                    $ppn = $total_subtotal * 0.10;
                    $grand_total = $total_subtotal + $ppn;
                ?>
                <tfoot>
                    <tr>
                        <td colspan="3" style="text-align:right; font-weight:bold;">Subtotal:</td>
                        <td>Rp <?php echo number_format($total_subtotal); ?></td>
                    </tr>
                    <tr>
                        <td colspan="3" style="text-align:right; font-weight:bold; color:#666;">PPN (10%):</td>
                        <td>Rp <?php echo number_format($ppn); ?></td>
                    </tr>
                    <tr style="background-color: #d4edda;">
                        <td colspan="3" style="text-align:right; font-weight:bold; font-size:18px;">TOTAL EST.:</td>
                        <td style="font-weight:bold; font-size:18px; color:green;">Rp <?php echo number_format($grand_total); ?></td>
                    </tr>
                </tfoot>
            </table>
            
            <input type="hidden" name="subtotal" value="<?php echo $total_subtotal; ?>">
            
            <button type="submit" class="btn-save" onclick="return confirm('Buat PO ini senilai Rp <?php echo number_format($grand_total); ?>?')">SIMPAN</button>
            <a href="?reset=true" style="color:red; float:right; margin-top:10px;">Reset</a>
        </form>
    <?php else: ?>
        <p>Belum ada barang dipilih.</p>
    <?php endif; ?>
</div>
    </div>
</body>
</html>