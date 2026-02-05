<?php
require_once '../config/database.php';
require_once '../config/config.php';

loginKontrol();

$merkez_id = post('merkez_id');

if (empty($merkez_id)) {
    jsonResponse(false, 'Merkez ID gerekli!');
}

$sql = "SELECT * FROM dagitim_bolgeleri WHERE merkez_id = ? AND durum = 1 ORDER BY bolge_adi ASC";
$bolgeler = fetchAll($sql, [$merkez_id]);

if ($bolgeler) {
    jsonResponse(true, 'Bölgeler bulundu', $bolgeler);
} else {
    jsonResponse(false, 'Bölge bulunamadı', []);
}
?>