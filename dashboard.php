<?php
session_start();
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

require_once 'koneksi.php';
$db = new DbConnection();

$username = $_SESSION['username'];
$idrole   = $_SESSION['idrole'];

$total_penjualan = 0;
$barang_habis = 0;
$vendor_aktif = 0;

if ($idrole == 1) {
    $q_jual = "SELECT IFNULL(SUM(total_nilai), 0) AS total FROM penjualan WHERE MONTH(created_at) = MONTH(NOW())";
    $resp_jual = $db->send_query($q_jual);
    if ($resp_jual->sukses && count($resp_jual->data) > 0) {
        $total_penjualan = $resp_jual->data[0]['total'];
    }

    $q_stok = "SELECT IFNULL(SUM(stok_terkini), 0) AS total FROM v_stok_barang_terkini";
    $resp_stok = $db->send_query($q_stok);
    if ($resp_stok->sukses && count($resp_stok->data) > 0) {
        $barang_habis = $resp_stok->data[0]['total'];
    }

    $q_vendor = "SELECT COUNT(*) AS total FROM v_vendor_aktif";
    $resp_vendor = $db->send_query($q_vendor);
    if ($resp_vendor->sukses && count($resp_vendor->data) > 0) {
        $vendor_aktif = $resp_vendor->data[0]['total'];
    }
}

$db->close_connection();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - TOKOKU</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background-color: #f4f7f6; display: flex; height: 100vh; }
        
        .sidebar {
            width: 250px;
            background-color: #fff;
            border-right: 1px solid #ddd;
            display: flex;
            flex-direction: column;
        }
        .sidebar-header {
            padding: 20px;
            background-color: #007bff;
            color: white;
            font-weight: bold;
            font-size: 20px;
            text-align: center;
        }
        .menu { list-style: none; padding: 0; margin: 0; }
        .menu li a {
            display: block;
            padding: 15px 20px;
            text-decoration: none;
            color: #333;
            border-bottom: 1px solid #f0f0f0;
        }
        .menu li a:hover { background-color: #e9ecef; color: #007bff; }
        .menu-label {
            padding: 10px 20px;
            font-size: 12px;
            color: #888;
            font-weight: bold;
            background-color: #f9f9f9;
            margin-top: 10px;
        }

        .main-content { flex: 1; display: flex; flex-direction: column; }
        
        .topbar {
            background-color: #fff;
            padding: 15px 30px;
            border-bottom: 1px solid #ddd;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logout-btn {
            color: #dc3545;
            text-decoration: none;
            font-weight: bold;
            border: 1px solid #dc3545;
            padding: 5px 15px;
            border-radius: 5px;
        }
        .logout-btn:hover { background-color: #dc3545; color: white; }
        .content { padding: 30px; overflow-y: auto; }

        .kpi-container { display: flex; gap: 20px; margin-bottom: 30px; }
        .kpi-card {
            flex: 1;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            border-top: 4px solid #007bff;
        }
        .kpi-card h3 { margin: 0 0 10px 0; font-size: 14px; color: #666; }
        .kpi-card .value { font-size: 28px; font-weight: bold; color: #333; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-header">TOKOKU</div>
            <ul class="menu">
                <?php 

                // sidebarnya admin
                if ($idrole == 1): 
                ?>
                    <div class="menu-label">DATA MASTER</div>
                    <li><a href="user_list.php">👥 Manajemen User</a></li>
                    <li><a href="barang_list.php">📦 Manajemen Barang</a></li>
                    <li><a href="vendor_list.php">🏢 Manajemen Vendor</a></li>
                    <li><a href="margin_list.php">💰 Manajemen Margin</a></li>
                    <li><a href="satuan_list.php">📏 Manajemen Satuan</a></li>
                    <li><a href="role_list.php">🔑 Manajemen Role</a></li>

                    <div class="menu-label">TRANSAKSI</div>
                    <li><a href="transaksi_baru.php">🛒 Pembelian</a></li>
                    <li><a href="pengadaan_list.php">📦 Pengadaan</a></li>
                    <li><a href="retur_list.php">🚚 Retur Barang</a></li>
                    

                    <div class="menu-label">INVENTORI</div>
                    <li><a href="penerimaan_barang.php">🚚 Penerimaan Barang</a></li>
                    <li><a href="stok_barang.php">📦 Cek Stok</a></li>

                    <div class="menu-label">LAPORAN</div>
                    <li><a href="laporan_penjualan.php">📈 Laporan Penjualan</a></li>
                    <li><a href="riwayat_transaksi.php">📄 Riwayat Transaksi</a></li>

                <?php 

                // MENU KASIR
                elseif ($idrole == 2): 
                ?>
                    <div class="menu-label">TRANSAKSI</div>
                    <li><a href="transaksi_baru.php">🛒 Pembelian</a></li>
                    <li><a href="riwayat_transaksi.php">📄 Riwayat Transaksi</a></li>
                    <li><a href="laporan_pribadi.php">📈 Laporan Saya</a></li>

                <?php 
                // MENU GUDANG 
                elseif ($idrole == 3): 
                ?>
                    <div class="menu-label">INVENTORI</div>
                    <li><a href="penerimaan_barang.php">🚚 Penerimaan Barang</a></li>
                    <li><a href="stok_barang.php">📦 Cek Stok</a></li>
                <?php 
                // pembeli
                elseif ($idrole > 3): 
                ?>
                    <div class="menu-label">TRANSAKSI</div>
                    <li><a href="transaksi_baru.php">🛒 Pembelian</a></li>
                    
                <?php endif; ?>
            </ul>
    </div>

    <div class="main-content">
        <div class="topbar">
            <div>Halo, <strong><?php echo htmlspecialchars($username); ?></strong></div>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>

        <div class="content">
            <h1>Selamat Datang!</h1>
            
            <?php if ($idrole == 1):?>
                <p>Ringkasan Toko Anda Bulan Ini:</p>
                <div class="kpi-container">
                    <div class="kpi-card" style="border-color: #28a745;">
                        <h3>Total Penjualan</h3>
                        <div class="value">Rp <?php echo number_format($total_penjualan, 0, ',', '.'); ?></div>
                    </div>
                    <div class="kpi-card" style="border-color: #17a2b8;"> <h3>Total Stok Gudang</h3>
                        <div class="value">
                            <?php echo number_format($barang_habis, 0, ',', '.'); ?> Unit
                        </div>
                    </div>
                    <div class="kpi-card" style="border-color: #ffc107;">
                        <h3>Vendor Aktif</h3>
                        <div class="value"> <?php echo $vendor_aktif; ?> Vendor</div>
                    </div>
                </div>

            <?php else:?>
                <p>Silakan pilih menu di sebelah kiri untuk mulai bekerja.</p>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>