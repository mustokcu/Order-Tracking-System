<?php
require_once '../config/database.php';
require_once '../config/config.php';

loginKontrol();

$tip = post('tip');

// MERKEZ KAYDET
if ($tip === 'merkez') {
    $merkez_id = post('merkez_id');
    $merkez_adi = post('merkez_adi');
    
    if (empty($merkez_adi)) {
        jsonResponse(false, 'Merkez adı gerekli!');
    }
    
    if (!empty($merkez_id)) {
        // Güncelle
        $sql = "UPDATE dagitim_merkezleri SET merkez_adi = ? WHERE id = ?";
        $result = query($sql, [$merkez_adi, $merkez_id]);
        $mesaj = 'Merkez güncellendi!';
    } else {
        // Ekle
        $sql = "INSERT INTO dagitim_merkezleri (merkez_adi) VALUES (?)";
        $result = query($sql, [$merkez_adi]);
        $mesaj = 'Merkez eklendi!';
    }
    
    if ($result) {
        jsonResponse(true, $mesaj);
    } else {
        jsonResponse(false, 'İşlem başarısız!');
    }
}

// BÖLGE KAYDET
elseif ($tip === 'bolge') {
    $bolge_id = post('bolge_id');
    $merkez_id = post('merkez_id');
    $bolge_adi = post('bolge_adi');
    
    if (empty($merkez_id) || empty($bolge_adi)) {
        jsonResponse(false, 'Merkez ve bölge adı gerekli!');
    }
    
    if (!empty($bolge_id)) {
        // Güncelle
        $sql = "UPDATE dagitim_bolgeleri SET merkez_id = ?, bolge_adi = ? WHERE id = ?";
        $result = query($sql, [$merkez_id, $bolge_adi, $bolge_id]);
        $mesaj = 'Bölge güncellendi!';
    } else {
        // Ekle
        $sql = "INSERT INTO dagitim_bolgeleri (merkez_id, bolge_adi) VALUES (?, ?)";
        $result = query($sql, [$merkez_id, $bolge_adi]);
        $mesaj = 'Bölge eklendi!';
    }
    
    if ($result) {
        jsonResponse(true, $mesaj);
    } else {
        jsonResponse(false, 'İşlem başarısız!');
    }
}

// YER KAYDET
elseif ($tip === 'yer') {
    $yer_id = post('yer_id');
    $bolge_id = post('bolge_id');
    $yer_adi = post('yer_adi');
    
    if (empty($bolge_id) || empty($yer_adi)) {
        jsonResponse(false, 'Bölge ve yer adı gerekli!');
    }
    
    if (!empty($yer_id)) {
        // Güncelle
        $sql = "UPDATE dagitim_yerleri SET bolge_id = ?, yer_adi = ? WHERE id = ?";
        $result = query($sql, [$bolge_id, $yer_adi, $yer_id]);
        $mesaj = 'Yer güncellendi!';
    } else {
        // Ekle
        $sql = "INSERT INTO dagitim_yerleri (bolge_id, yer_adi) VALUES (?, ?)";
        $result = query($sql, [$bolge_id, $yer_adi]);
        $mesaj = 'Yer eklendi!';
    }
    
    if ($result) {
        jsonResponse(true, $mesaj);
    } else {
        jsonResponse(false, 'İşlem başarısız!');
    }
}

else {
    jsonResponse(false, 'Geçersiz işlem!');
}
?>