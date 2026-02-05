<?php
require_once '../config/database.php';
require_once '../config/config.php';

loginKontrol();

$siparis_no = post('siparis_no');
$siparis_tarihi = post('siparis_tarihi');
$dagitim_merkezi_id = post('dagitim_merkezi_id');
$dagitim_bolgesi_id = post('dagitim_bolgesi_id');
$dagitim_yeri_id = post('dagitim_yeri_id');
$urunler_json = post('urunler');

// İRSALİYE BİLGİLERİ
$irsaliye_tarihi = post('irsaliye_tarihi');
$irsaliye_no = post('irsaliye_no');
$irsaliye_miktari = post('irsaliye_miktari');
$nakliye = post('nakliye');

// Validasyon
if (empty($siparis_no) || empty($siparis_tarihi) || empty($dagitim_merkezi_id) || 
    empty($dagitim_bolgesi_id) || empty($dagitim_yeri_id) || empty($urunler_json)) {
    jsonResponse(false, 'Tüm zorunlu alanlar gerekli!');
}

// Ürünleri parse et
$urunler = json_decode($urunler_json, true);

if (empty($urunler) || !is_array($urunler)) {
    jsonResponse(false, 'Geçerli ürün listesi bulunamadı!');
}

// Sipariş numarası kontrolü
$kontrol = fetch("SELECT id FROM siparisler WHERE siparis_no = ?", [$siparis_no]);
if ($kontrol) {
    jsonResponse(false, 'Bu sipariş numarası zaten kullanılıyor!');
}

try {
    global $pdo;
    $pdo->beginTransaction();
    
    // Siparişi ekle - İRSALİYE BİLGİLERİ DAHİL
    $sql = "INSERT INTO siparisler (
                siparis_no, 
                siparis_tarihi, 
                dagitim_merkezi_id, 
                dagitim_bolgesi_id, 
                dagitim_yeri_id, 
                user_id, 
                durum,
                irsaliye_tarihi,
                irsaliye_no,
                irsaliye_miktari,
                nakliye
            ) VALUES (?, ?, ?, ?, ?, ?, 'Bekliyor', ?, ?, ?, ?)";
    
    // İrsaliye tarihi boşsa NULL olarak kaydet
    $irsaliye_tarihi_value = !empty($irsaliye_tarihi) ? $irsaliye_tarihi : null;
    $irsaliye_no_value = !empty($irsaliye_no) ? $irsaliye_no : null;
    $irsaliye_miktari_value = !empty($irsaliye_miktari) ? $irsaliye_miktari : null;
    $nakliye_value = !empty($nakliye) ? $nakliye : null;
    
    $result = query($sql, [
        $siparis_no, 
        $siparis_tarihi, 
        $dagitim_merkezi_id, 
        $dagitim_bolgesi_id, 
        $dagitim_yeri_id,
        $_SESSION['user_id'],
        $irsaliye_tarihi_value,
        $irsaliye_no_value,
        $irsaliye_miktari_value,
        $nakliye_value
    ]);
    
    if (!$result) {
        throw new Exception('Sipariş eklenemedi');
    }
    
    $siparis_id = lastInsertId();
    
    // DEBUG: Sipariş ID'yi kontrol et
    if (!$siparis_id || $siparis_id <= 0) {
        throw new Exception('Geçersiz sipariş ID: ' . $siparis_id);
    }
    
    // Ürünleri ekle
    $urun_sql = "INSERT INTO siparis_detaylari (siparis_id, urun_id, koli_miktari, adet_miktari, palet_sayisi) 
                 VALUES (?, ?, ?, ?, ?)";
    
    $eklenen_urun_sayisi = 0;
    foreach ($urunler as $urun) {
        $urun_result = query($urun_sql, [
            $siparis_id,
            $urun['id'],
            isset($urun['koli']) ? intval($urun['koli']) : 0,
            isset($urun['adet']) ? intval($urun['adet']) : 0,
            isset($urun['palet']) ? intval($urun['palet']) : 0
        ]);
        
        if ($urun_result) {
            $eklenen_urun_sayisi++;
        }
    }
    
    // Hiç ürün eklenemediyse hata ver
    if ($eklenen_urun_sayisi == 0) {
        throw new Exception('Hiçbir ürün eklenemedi');
    }
    
    $pdo->commit();
    
    jsonResponse(true, "Sipariş başarıyla kaydedildi! ($eklenen_urun_sayisi ürün eklendi)", [
        'siparis_id' => $siparis_id,
        'urun_sayisi' => $eklenen_urun_sayisi
    ]);
    
} catch (Exception $e) {
    $pdo->rollBack();
    
    // Detaylı hata mesajı
    $hata_mesaji = 'Sipariş kaydedilemedi: ' . $e->getMessage();
    error_log($hata_mesaji); // Sunucu loguna yaz
    
    jsonResponse(false, $hata_mesaji);
}
?>