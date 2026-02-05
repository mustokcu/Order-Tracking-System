<?php
require_once '../config/database.php';
require_once '../config/config.php';

header('Content-Type: application/json; charset=utf-8');

// Login kontrolü
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Oturum süreniz dolmuş'
    ]);
    exit;
}

$siparis_id = isset($_POST['siparis_id']) ? intval($_POST['siparis_id']) : 0;

if ($siparis_id <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Geçersiz sipariş ID'
    ]);
    exit;
}

// Sipariş ürünlerini getir (siparis_detaylari tablosundan)
$urunler = fetchAll("
    SELECT 
        sd.*,
        u.urun_kodu,
        u.urun_adi
    FROM siparis_detaylari sd
    INNER JOIN urunler u ON sd.urun_id = u.id
    WHERE sd.siparis_id = ?
    ORDER BY u.urun_adi ASC
", [$siparis_id]);

if ($urunler === false) {
    echo json_encode([
        'success' => false,
        'message' => 'Ürünler getirilirken bir hata oluştu'
    ]);
    exit;
}

echo json_encode([
    'success' => true,
    'data' => $urunler,
    'message' => count($urunler) . ' ürün bulundu'
]);