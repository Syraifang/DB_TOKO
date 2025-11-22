<?php
session_start();
require_once 'koneksi.php';

// Cek Login (Admin & Kasir boleh masuk)
if (!isset($_SESSION['is_logged_in']) || !in_array($_SESSION['idrole'], [1, 2])) {
    header("Location: login.php");
    exit;
}

$db = new DbConnection();

// --- 1. LOGIKA KERANJANG BELANJA (SESSION) ---

// Jika belum ada keranjang, buat array kosong
if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
}

// Tambah Barang ke Keranjang
if (isset($_POST['aksi']) && $_POST['aksi'] == 'tambah') {
    $idbarang = $_POST['idbarang'];
    $jumlah = $_POST['jumlah'];

    // Ambil Detail Barang dari Database (untuk nama & harga)
    // Kita pakai VIEW v_daftar_barang agar sesuai harga & status
    $q = "SELECT * FROM v_daftar_barang WHERE idbarang = ?";
    $res = $db->send_secure_query($q, [$idbarang], 'i');

    if ($res->sukses && count($res->data) > 0) {
        $barang = $res->data[0];
        
        // Masukkan ke session array
        $item = [
            'id' => $barang['idbarang'],
            'nama' => $barang['nama_barang'],
            'harga' => $barang['harga'], // Ini harga modal/dasar
            'satuan' => $barang['nama_satuan'],
            'jumlah' => $jumlah
        ];
        
        // Cek apakah barang sudah ada di keranjang? Kalau ada, tambahkan jumlahnya
        $found = false;
        foreach ($_SESSION['keranjang'] as $key => $val) {
            if ($val['id'] == $idbarang) {
                $_SESSION['keranjang'][$key]['jumlah'] += $jumlah;
                $found = true;
                break;
            }
        }
        
        if (!$found) {
            $_SESSION['keranjang'][] = $item;
        }
    }
}

// Hapus Barang dari Keranjang
if (isset($_GET['hapus'])) {
    $index = $_GET['hapus'];
    unset($_SESSION['keranjang'][$index]);
    // Reset urutan array
    $_SESSION['keranjang'] = array_values($_SESSION['keranjang']);
}

// Reset Keranjang (Batal)
if (isset($_GET['reset'])) {
    unset($_SESSION['keranjang']);
    header("Location: transaksi_baru.php");
    exit;
}

// --- 2. AMBIL DATA PENDUKUNG ---

// Ambil Daftar Barang Aktif untuk Dropdown
$q_barang = "SELECT * FROM v_daftar_barang ORDER BY nama_barang ASC";
$res_barang = $db->send_query($q_barang);
$list_barang = $res_barang->data;

// Ambil Margin Aktif (Kita ambil margin aktif terakhir yang dibuat)
$q_margin = "SELECT * FROM margin_penjualan WHERE status = 1 ORDER BY idmargin_penjualan DESC LIMIT 1";
$res_margin = $db->send_query($q_margin);
$margin_aktif = null;
if ($res_margin->sukses && count($res_margin->data) > 0) {
    $margin_aktif = $res_margin->data[0];
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Kasir TOKOKU</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f7f6; padding: 20px; }
        .container { display: flex; gap: 20px; }
        .box { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .left { flex: 1; }
        .right { flex: 2; }
        
        input, select, button { width: 100%; padding: 10px; margin-bottom: 10px; box-sizing: border-box; }
        .btn-add { background-color: #007bff; color: white; border: none; cursor: pointer; }
        .btn-pay { background-color: #28a745; color: white; border: none; cursor: pointer; font-size: 18px; font-weight: bold;}
        .btn-danger { background-color: #dc3545; color: white; text-decoration: none; padding: 5px 10px; border-radius: 4px; font-size: 12px;}
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #eee; }
        .total-row { font-weight: bold; font-size: 18px; background-color: #fff3cd; }
    </style>
</head>
<body>

    <h1>🛒 Kasir (Point of Sales)</h1>
    <a href="dashboard.php" style="text-decoration: none;">&larr; Kembali ke Dashboard</a>
    <br><br>

    <div class="container">
        
        <div class="box left">
            <h3>Tambah Barang</h3>
            
            <?php if($margin_aktif): ?>
                <div style="background: #d4edda; color: #155724; padding: 10px; margin-bottom: 10px; border-radius: 4px;">
                    Margin Aktif: <strong><?php echo $margin_aktif['persen']; ?>%</strong>
                </div>

                <form method="POST">
                    <input type="hidden" name="aksi" value="tambah">
                    
                    <label>Pilih Barang</label>
                    <select name="idbarang" required autofocus>
                        <option value="">-- Cari Barang --</option>
                        <?php foreach($list_barang as $b): ?>
                            <option value="<?php echo $b['idbarang']; ?>">
                                <?php echo $b['nama_barang']; ?> (Stok: ?) - Rp <?php echo number_format($b['harga']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    
                    <label>Jumlah</label>
                    <input type="number" name="jumlah" value="1" min="1" required>
                    
                    <button type="submit" class="btn-add">+ Masukkan Keranjang</button>
                </form>
            <?php else: ?>
                <div style="background: #f8d7da; padding: 10px; color: #721c24;">
                    ⚠️ ERROR: Tidak ada Margin Keuntungan yang Aktif! <br>
                    Admin harus membuat/mengaktifkan Margin dulu.
                </div>
            <?php endif; ?>
        </div>

        <div class="box right">
            <h3>Daftar Belanjaan</h3>
            
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Barang</th>
                        <th>Harga Satuan (Jual)</th>
                        <th>Qty</th>
                        <th>Subtotal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $grand_total = 0;
                    $no = 1;
                    
                    if (!empty($_SESSION['keranjang'])):
                        foreach($_SESSION['keranjang'] as $key => $item): 
                            // HITUNG HARGA JUAL & SUBTOTAL DI SINI (Memakai Logika Function)
                            
                            // 1. Hitung Harga Jual (Modal + Margin)
                            // Rumus: Harga + (Harga * Persen / 100)
                            $persen = $margin_aktif['persen'];
                            $harga_jual = $item['harga'] + ($item['harga'] * $persen / 100);
                            
                            // 2. Hitung Subtotal
                            $subtotal = $harga_jual * $item['jumlah'];
                            
                            $grand_total += $subtotal;
                    ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo $item['nama']; ?></td>
                            <td>Rp <?php echo number_format($harga_jual, 0, ',', '.'); ?></td>
                            <td><?php echo $item['jumlah']; ?> <?php echo $item['satuan']; ?></td>
                            <td>Rp <?php echo number_format($subtotal, 0, ',', '.'); ?></td>
                            <td><a href="?hapus=<?php echo $key; ?>" class="btn-danger">Hapus</a></td>
                        </tr>
                    <?php 
                        endforeach; 
                    else:
                    ?>
                        <tr><td colspan="6" style="text-align:center;">Keranjang masih kosong...</td></tr>
                    <?php endif; ?>
                </tbody>
                
                <?php if($grand_total > 0): ?>
                <tfoot>
                    <?php 
                        $ppn = $grand_total * 0.11; // PPN 11%
                        $total_bayar = $grand_total + $ppn;
                    ?>
                    <tr>
                        <td colspan="4" style="text-align: right;">Total Subtotal</td>
                        <td colspan="2">Rp <?php echo number_format($grand_total, 0, ',', '.'); ?></td>
                    </tr>
                    <tr>
                        <td colspan="4" style="text-align: right;">PPN (11%)</td>
                        <td colspan="2">Rp <?php echo number_format($ppn, 0, ',', '.'); ?></td>
                    </tr>
                    <tr class="total-row">
                        <td colspan="4" style="text-align: right;">TOTAL BAYAR</td>
                        <td colspan="2" style="color: green;">Rp <?php echo number_format($total_bayar, 0, ',', '.'); ?></td>
                    </tr>
                </tfoot>
                <?php endif; ?>
            </table>

            <br>
            
            <?php if(!empty($_SESSION['keranjang'])): ?>
                <form action="transaksi_proses.php" method="POST" onsubmit="return confirm('Proses pembayaran ini?');">
                    <input type="hidden" name="idmargin" value="<?php echo $margin_aktif['idmargin_penjualan']; ?>">
                    <input type="hidden" name="subtotal_nilai" value="<?php echo $grand_total; ?>">
                    <input type="hidden" name="ppn" value="<?php echo $ppn; ?>">
                    <input type="hidden" name="total_nilai" value="<?php echo $total_bayar; ?>">
                    
                    <button type="submit" class="btn-pay">💰 BAYAR SEKARANG</button>
                </form>
                
                <br>
                <a href="?reset=true" style="color: red; font-size: 12px;">Kosongkan Keranjang</a>
            <?php endif; ?>

        </div>
    </div>

</body>
</html>