<?php
session_start();
require_once 'koneksi.php';
if ($_SESSION['idrole'] != 1) exit("Akses Ditolak");

$db = new DbConnection();

$idbarang = "";
$nama_barang = "";
$harga = "";
$idsatuan = "";
$jenis = "B";
$is_edit = false;


if (isset($_GET['id'])) {
    $is_edit = true;
    $idbarang = $_GET['id'];

    $q = "SELECT * FROM barang WHERE idbarang = ?";
    $res = $db->send_secure_query($q, [$idbarang], 'i');
    if ($res->sukses && count($res->data) > 0) {
        $data = $res->data[0];
        $nama_barang = $data['nama_barang'];
        $harga = $data['harga'];
        $idsatuan = $data['idsatuan'];
        $jenis = $data['jenis'];
    }
}

$q_satuan = "SELECT * FROM v_satuan_aktif";
$res_satuan = $db->send_query($q_satuan);
$list_satuan = $res_satuan->data;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Form Barang</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f7f6; padding: 20px; display: flex; justify-content: center; }
        .box { background: white; padding: 30px; width: 400px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        input, select { width: 100%; padding: 10px; margin: 10px 0; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #007bff; color: white; border: none; cursor: pointer; }
    </style>
</head>
<body>
    <div class="box">
        <h2><?php echo $is_edit ? "Edit Barang" : "Tambah Barang Baru"; ?></h2>
        
        <form action="barang_proses.php" method="POST">
            <input type="hidden" name="aksi" value="<?php echo $is_edit ? 'update' : 'create'; ?>">
            <input type="hidden" name="idbarang" value="<?php echo $idbarang; ?>">

            <label>Nama Barang</label>
            <input type="text" name="nama_barang" value="<?php echo $nama_barang; ?>" required>

            <label>Jenis</label>
            <select name="jenis">
                <option value="B" <?php if($jenis=='B') echo 'selected'; ?>>Barang</option>
                <option value="J" <?php if($jenis=='J') echo 'selected'; ?>>Jasa</option>
            </select>

            <label>Satuan</label>
            <select name="idsatuan">
                <?php foreach($list_satuan as $sat): ?>
                    <option value="<?php echo $sat['idsatuan']; ?>" 
                        <?php if($sat['idsatuan'] == $idsatuan) echo 'selected'; ?>>
                        <?php echo $sat['nama_satuan']; ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Harga Modal</label>
            <input type="number" name="harga" value="<?php echo $harga; ?>" required>

            <button type="submit">Simpan Data</button>
        </form>
        <br>
        <a href="barang_list.php">Batal</a>
    </div>
</body>
</html>