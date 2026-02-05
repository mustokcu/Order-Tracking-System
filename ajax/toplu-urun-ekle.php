<?php
require_once '../config/database.php';
require_once '../config/config.php';

loginKontrol();

$excel_data = post('excel_data');

if (empty($excel_data)) {
    jsonResponse(false, 'Excel verisi gerekli!');
}

// Satırları ayır
$lines = explode("\n", trim($excel_data));
$eklenen = 0;
$atlanan = 0;
$hatalar = [];
$eklenen_kodlar = []; // Aynı istekte tekrar eden kodları takip et

foreach ($lines as $line) {
    $line = trim($line);
    if (empty($line)) continue;
    
    // Tab veya birden fazla boşlukla ayır
    $parts = preg_split('/\s{2,}|\t/', $line, 2);
    
    if (count($parts) < 2) {
        continue; // Hatalı formatı sessizce atla
    }
    
    $urun_kodu = trim($parts[0]);
    $urun_adi = trim($parts[1]);
    
    if (empty($urun_kodu) || empty($urun_adi)) {
        continue; // Eksik bilgiyi sessizce atla
    }
    
    // Bu istekte daha önce eklendi mi? (Aynı Excel'de tekrar eden)
    if (in_array($urun_kodu, $eklenen_kodlar)) {
        $atlanan++;
        continue;
    }
    
    // Veritabanında var mı kontrol et
    $kontrol = fetch("SELECT id FROM urunler WHERE urun_kodu = ?", [$urun_kodu]);
    if ($kontrol) {
        $atlanan++;
        continue; // Zaten var, sessizce atla
    }
    
    // Ürün ekle
    $sql = "INSERT INTO urunler (urun_kodu, urun_adi) VALUES (?, ?)";
    $result = query($sql, [$urun_kodu, $urun_adi]);
    
    if ($result) {
        $eklenen++;
        $eklenen_kodlar[] = $urun_kodu; // Eklenenler listesine ekle
    }
}

if ($eklenen > 0) {
    $mesaj = "$eklenen ürün başarıyla eklendi";
    if ($atlanan > 0) {
        $mesaj .= ", $atlanan ürün zaten mevcut (atlandı)";
    }
    jsonResponse(true, $mesaj, ['eklenen' => $eklenen, 'atlanan' => $atlanan]);
} else {
    if ($atlanan > 0) {
        jsonResponse(true, "Tüm ürünler zaten mevcut ($atlanan ürün)", ['eklenen' => 0, 'atlanan' => $atlanan]);
    } else {
        jsonResponse(false, 'Hiçbir ürün eklenemedi! Lütfen format kontrolü yapın.', ['eklenen' => 0, 'atlanan' => 0]);
    }
}
?>