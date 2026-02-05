<?php
require_once '../config/database.php';
require_once '../config/config.php';

loginKontrol();

$siparis_id = post('siparis_id');

if (empty($siparis_id)) {
    jsonResponse(false, 'Sipariş ID gerekli!');
}

// Siparişi sil (CASCADE ile detaylar da silinecek)
$sql = "DELETE FROM siparisler WHERE id = ?";
$result = query($sql, [$siparis_id]);

if ($result) {
    jsonResponse(true, 'Sipariş başarıyla silindi!');
} else {
    jsonResponse(false, 'Sipariş silinemedi!');
}
?>