<?php
session_start();
require_once 'koneksi.php';
if ($_SESSION['idrole'] != 1) exit("Akses Ditolak");

$db = new DbConnection();

$idmargin = "";
$persen = "";
$is_edit = false;

if (isset($_GET['id'])) {
    $is_edit = true;
    $idmargin = $_GET['id'];
    
    $q = "SELECT * FROM margin_penjualan WHERE idmargin_penjualan = ?";
    $res = $db->send_secure_query($q, [$idmargin], 'i');
    
    if ($res->sukses && count($res->data) > 0) {
        $data = $res->data[0];
        $persen = $data['persen'];
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Form Margin</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f7f6; padding: 20px; display: flex; justify-content: center; }
        .box { background: white; padding: 30px; width: 400px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        input { width: 100%; padding: 10px; margin: 10px 0; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #007bff; color: white; border: none; cursor: pointer; }
    </style>
</head>
<body>
    <div class="box">
        <h2><?php echo $is_edit ? "Edit Margin" : "Tambah Margin Baru"; ?></h2>
        
        <form action="margin_proses.php" method="POST">
            <input type="hidden" name="aksi" value="<?php echo $is_edit ? 'update' : 'create'; ?>">
            <input type="hidden" name="idmargin_penjualan" value="<?php echo $idmargin; ?>">

            <label>Persentase Keuntungan (%)</label>
            <input type="number" step="0.1" name="persen" value="<?php echo $persen; ?>" required placeholder="Contoh: 10 atau 15.5">

            <button type="submit">Simpan Data</button>
        </form>
        <br>
        <a href="margin_list.php">Batal</a>
    </div>
</body>
</html>