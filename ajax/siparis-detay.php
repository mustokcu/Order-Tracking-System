<?php
require_once '../config/database.php';
require_once '../config/config.php';

loginKontrol();

$siparis_id = post('siparis_id');

if (empty($siparis_id)) {
    jsonResponse(false, 'Sipariş ID gerekli!');
}

// Sipariş bilgilerini getir - İRSALİYE BİLGİLERİ DAHİL
$siparis = fetch("
    SELECT s.*, 
           dm.merkez_adi,
           db.bolge_adi,
           dy.yer_adi,
           u.ad_soyad
    FROM siparisler s
    LEFT JOIN dagitim_merkezleri dm ON s.dagitim_merkezi_id = dm.id
    LEFT JOIN dagitim_bolgeleri db ON s.dagitim_bolgesi_id = db.id
    LEFT JOIN dagitim_yerleri dy ON s.dagitim_yeri_id = dy.id
    LEFT JOIN users u ON s.user_id = u.id
    WHERE s.id = ?
", [$siparis_id]);

if (!$siparis) {
    jsonResponse(false, 'Sipariş bulunamadı!');
}

// Sipariş tarihi formatla
$siparis['siparis_tarihi'] = date('d.m.Y', strtotime($siparis['siparis_tarihi']));

// İrsaliye tarihi formatla (eğer varsa)
if (!empty($siparis['irsaliye_tarihi'])) {
    $siparis['irsaliye_tarihi_formatted'] = date('d.m.Y', strtotime($siparis['irsaliye_tarihi']));
} else {
    $siparis['irsaliye_tarihi_formatted'] = '-';
}

// Sipariş ürünlerini getir
$urunler = fetchAll("
    SELECT sd.*, u.urun_kodu, u.urun_adi
    FROM siparis_detaylari sd
    LEFT JOIN urunler u ON sd.urun_id = u.id
    WHERE sd.siparis_id = ?
    ORDER BY u.urun_adi
", [$siparis_id]);

jsonResponse(true, 'Sipariş detayı bulundu', [
    'siparis' => $siparis,
    'urunler' => $urunler
]);
?>