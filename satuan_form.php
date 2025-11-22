<?php
session_start();
require_once 'koneksi.php';
if ($_SESSION['idrole'] != 1) exit("Akses Ditolak");

$db = new DbConnection();

$idsatuan = "";
$nama_satuan = "";
$is_edit = false;

// Cek Mode Edit
if (isset($_GET['id'])) {
    $is_edit = true;
    $idsatuan = $_GET['id'];
    
    $q = "SELECT * FROM satuan WHERE idsatuan = ?";
    $res = $db->send_secure_query($q, [$idsatuan], 'i');
    if ($res->sukses && count($res->data) > 0) {
        $data = $res->data[0];
        $nama_satuan = $data['nama_satuan'];
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Form Satuan</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f7f6; padding: 20px; display: flex; justify-content: center; }
        .box { background: white; padding: 30px; width: 400px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        input { width: 100%; padding: 10px; margin: 10px 0; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #007bff; color: white; border: none; cursor: pointer; }
    </style>
</head>
<body>
    <div class="box">
        <h2><?php echo $is_edit ? "Edit Satuan" : "Tambah Satuan Baru"; ?></h2>
        
        <form action="satuan_proses.php" method="POST">
            <input type="hidden" name="aksi" value="<?php echo $is_edit ? 'update' : 'create'; ?>">
            <input type="hidden" name="idsatuan" value="<?php echo $idsatuan; ?>">

            <label>Nama Satuan</label>
            <input type="text" name="nama_satuan" value="<?php echo $nama_satuan; ?>" required placeholder="Contoh: Pcs, Box, Kg">

            <button type="submit">Simpan Data</button>
        </form>
        <br>
        <a href="satuan_list.php">Batal</a>
    </div>
</body>
</html>