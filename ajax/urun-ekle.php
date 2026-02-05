<?php
require_once '../config/database.php';
require_once '../config/config.php';

loginKontrol();

$urun_kodu = post('urun_kodu');
$urun_adi = post('urun_adi');

if (empty($urun_kodu) || empty($urun_adi)) {
    jsonResponse(false, 'Ürün kodu ve adı gerekli!');
}

// Ürün kodu kontrolü
$kontrol = fetch("SELECT id FROM urunler WHERE urun_kodu = ?", [$urun_kodu]);
if ($kontrol) {
    jsonResponse(false, 'Bu ürün kodu zaten mevcut!');
}

// Ürün ekle
$sql = "INSERT INTO urunler (urun_kodu, urun_adi) VALUES (?, ?)";
$result = query($sql, [$urun_kodu, $urun_adi]);

if ($result) {
    jsonResponse(true, 'Ürün başarıyla eklendi!');
} else {
    jsonResponse(false, 'Ürün eklenirken bir hata oluştu!');
}
?>