<?php
require_once '../config/database.php';
require_once '../config/config.php';

loginKontrol();

$tip = post('tip');
$id = post('id');

if (empty($tip) || empty($id)) {
    jsonResponse(false, 'Geçersiz parametreler!');
}

// MERKEZ SİL
if ($tip === 'merkez') {
    $sql = "DELETE FROM dagitim_merkezleri WHERE id = ?";
    $result = query($sql, [$id]);
    $mesaj = 'Merkez silindi!';
}

// BÖLGE SİL
elseif ($tip === 'bolge') {
    $sql = "DELETE FROM dagitim_bolgeleri WHERE id = ?";
    $result = query($sql, [$id]);
    $mesaj = 'Bölge silindi!';
}

// YER SİL
elseif ($tip === 'yer') {
    $sql = "DELETE FROM dagitim_yerleri WHERE id = ?";
    $result = query($sql, [$id]);
    $mesaj = 'Yer silindi!';
}

else {
    jsonResponse(false, 'Geçersiz işlem!');
}

if ($result) {
    jsonResponse(true, $mesaj);
} else {
    jsonResponse(false, 'Silme işlemi başarısız!');
}
?>