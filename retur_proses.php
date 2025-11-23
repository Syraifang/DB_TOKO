<?php
session_start();
require_once 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $db = new DbConnection();

    $idpenerimaan = $_POST['idpenerimaan'];
    $iduser = $_SESSION['iduser'];

    $ids_detail_terima = $_POST['iddetail_penerimaan'];
    $jml_returs = $_POST['jml_retur'];
    $alasans = $_POST['alasan'];

    $ada_retur = false;
    foreach ($jml_returs as $jml) {
        if ($jml > 0) $ada_retur = true;
    }

    if (!$ada_retur) {
        echo "<script>alert('Tidak ada barang yang diretur (Jumlah 0 semua).'); window.history.back();</script>";
        exit;
    }

    $q_head = "INSERT INTO retur (idpenerimaan, iduser) VALUES (?, ?)";
    $res = $db->send_secure_query($q_head, [$idpenerimaan, $iduser], 'ii');

    if ($res->sukses) {
        $idretur = $db->get_last_insert_id();

        $q_det = "INSERT INTO detail_retur (idretur, iddetail_penerimaan, jumlah, alasan) 
                  VALUES (?, ?, ?, ?)";

        for ($i = 0; $i < count($ids_detail_terima); $i++) {
            $id_det_terima = $ids_detail_terima[$i];
            $jml = $jml_returs[$i];
            $alasan = $alasans[$i];

            if ($jml > 0) {
                $db->send_secure_query($q_det, 
                    [$idretur, $id_det_terima, $jml, $alasan], 
                    'iiis'
                );
            }
        }

        echo "<script>alert('Retur Berhasil Disimpan!'); window.location='retur_list.php';</script>";
    } else {
        echo "Gagal: " . $res->pesan;
    }
}
?>