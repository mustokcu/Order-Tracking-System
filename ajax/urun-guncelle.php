<?php
require_once '../config/database.php';
require_once '../config/config.php';

loginKontrol();

$urun_id = post('urun_id');
$urun_kodu = post('urun_kodu');
$urun_adi = post('urun_adi');

if (empty($urun_id) || empty($urun_kodu) || empty($urun_adi)) {
    jsonResponse(false, 'Tüm alanlar gerekli!');
}

// Ürün kodu kontrolü (başka üründe var mı?)
$kontrol = fetch("SELECT id FROM urunler WHERE urun_kodu = ? AND id != ?", [$urun_kodu, $urun_id]);
if ($kontrol) {
    jsonResponse(false, 'Bu ürün kodu başka bir üründe kullanılıyor!');
}

// Ürün güncelle
$sql = "UPDATE urunler SET urun_kodu = ?, urun_adi = ? WHERE id = ?";
$result = query($sql, [$urun_kodu, $urun_adi, $urun_id]);

if ($result) {
    jsonResponse(true, 'Ürün başarıyla güncellendi!');
} else {
    jsonResponse(false, 'Ürün güncellenirken bir hata oluştu!');
}
?>