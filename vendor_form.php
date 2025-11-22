<?php
session_start();
require_once 'koneksi.php';
if ($_SESSION['idrole'] != 1) exit("Akses Ditolak");

$db = new DbConnection();

$idvendor = "";
$nama_vendor = "";
$badan_hukum = "N"; 
$is_edit = false;

if (isset($_GET['id'])) {
    $is_edit = true;
    $idvendor = $_GET['id'];
    
    $q = "SELECT * FROM vendor WHERE idvendor = ?";
    $res = $db->send_secure_query($q, [$idvendor], 'i');
    if ($res->sukses && count($res->data) > 0) {
        $data = $res->data[0];
        $nama_vendor = $data['nama_vendor'];
        $badan_hukum = $data['badan_hukum'];
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Form Vendor</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f7f6; padding: 20px; display: flex; justify-content: center; }
        .box { background: white; padding: 30px; width: 400px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        input, select { width: 100%; padding: 10px; margin: 10px 0; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #007bff; color: white; border: none; cursor: pointer; }
    </style>
</head>
<body>
    <div class="box">
        <h2><?php echo $is_edit ? "Edit Vendor" : "Tambah Vendor Baru"; ?></h2>
        
        <form action="vendor_proses.php" method="POST">
            <input type="hidden" name="aksi" value="<?php echo $is_edit ? 'update' : 'create'; ?>">
            <input type="hidden" name="idvendor" value="<?php echo $idvendor; ?>">

            <label>Nama Vendor</label>
            <input type="text" name="nama_vendor" value="<?php echo $nama_vendor; ?>" required placeholder="">

            <label>Badan Hukum</label>
            <select name="badan_hukum">
                <option value="P" <?php if($badan_hukum=='P') echo 'selected'; ?>>PT (Perseroan Terbatas)</option>
                <option value="C" <?php if($badan_hukum=='C') echo 'selected'; ?>>CV</option>
                <option value="N" <?php if($badan_hukum=='N') echo 'selected'; ?>>Perorangan / Lainnya</option>
            </select>

            <button type="submit">Simpan Data</button>
        </form>
        <br>
        <a href="vendor_list.php">Batal</a>
    </div>
</body>
</html>