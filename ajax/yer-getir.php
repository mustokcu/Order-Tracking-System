<?php
require_once '../config/database.php';
require_once '../config/config.php';

loginKontrol();

$bolge_id = post('bolge_id');

if (empty($bolge_id)) {
    jsonResponse(false, 'Bölge ID gerekli!');
}

$sql = "SELECT * FROM dagitim_yerleri WHERE bolge_id = ? AND durum = 1 ORDER BY yer_adi ASC";
$yerler = fetchAll($sql, [$bolge_id]);

if ($yerler) {
    jsonResponse(true, 'Yerler bulundu', $yerler);
} else {
    jsonResponse(false, 'Yer bulunamadı', []);
}
?>