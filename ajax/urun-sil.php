<?php
require_once '../config/database.php';
require_once '../config/config.php';

loginKontrol();

$urun_id = post('urun_id');

if (empty($urun_id)) {
    jsonResponse(false, 'Ürün ID gerekli!');
}

// Ürünü sil
$sql = "DELETE FROM urunler WHERE id = ?";
$result = query($sql, [$urun_id]);

if ($result) {
    jsonResponse(true, 'Ürün başarıyla silindi!');
} else {
    jsonResponse(false, 'Ürün silinirken bir hata oluştu!');
}
?>