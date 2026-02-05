<?php
require_once '../config/database.php';
require_once '../config/config.php';

loginKontrol();

$arama = post('arama');

if (empty($arama)) {
    jsonResponse(false, 'Arama kelimesi gerekli!');
}

$sql = "SELECT * FROM urunler 
        WHERE urun_kodu LIKE ? OR urun_adi LIKE ? 
        ORDER BY urun_adi ASC 
        LIMIT 20";

$arama_param = "%$arama%";
$urunler = fetchAll($sql, [$arama_param, $arama_param]);

if ($urunler) {
    jsonResponse(true, 'Ürünler bulundu', $urunler);
} else {
    jsonResponse(false, 'Ürün bulunamadı', []);
}
?>