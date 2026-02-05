<?php
require_once '../config/database.php';
require_once '../config/config.php';

loginKontrol();

$siparis_id = post('siparis_id');
$yeni_durum = post('yeni_durum');

if (empty($siparis_id) || empty($yeni_durum)) {
    jsonResponse(false, 'Sipariş ID ve durum gerekli!');
}

// Durum kontrolü
if (!in_array($yeni_durum, SIPARIS_DURUMLARI)) {
    jsonResponse(false, 'Geçersiz durum!');
}

// Durumu güncelle
$sql = "UPDATE siparisler SET durum = ? WHERE id = ?";
$result = query($sql, [$yeni_durum, $siparis_id]);

if ($result) {
    jsonResponse(true, 'Sipariş durumu güncellendi!');
} else {
    jsonResponse(false, 'Durum güncellenemedi!');
}
?>