<?php
require_once 'config/database.php';
require_once 'config/config.php';

// Login kontrolü
loginKontrol();

// Dashboard istatistikleri
$toplam_siparis = fetch("SELECT COUNT(*) as toplam FROM siparisler")['toplam'] ?? 0;
$bekleyen_siparis = fetch("SELECT COUNT(*) as toplam FROM siparisler WHERE durum = 'Bekliyor'")['toplam'] ?? 0;
$hazirlanan_siparis = fetch("SELECT COUNT(*) as toplam FROM siparisler WHERE durum = 'Hazırlanıyor'")['toplam'] ?? 0;
$teslim_edilen = fetch("SELECT COUNT(*) as toplam FROM siparisler WHERE durum = 'Teslim Edildi'")['toplam'] ?? 0;

// Son siparişler
$son_siparisler = fetchAll("
    SELECT s.*, 
           dm.merkez_adi,
           db.bolge_adi,
           dy.yer_adi
    FROM siparisler s
    LEFT JOIN dagitim_merkezleri dm ON s.dagitim_merkezi_id = dm.id
    LEFT JOIN dagitim_bolgeleri db ON s.dagitim_bolgesi_id = db.id
    LEFT JOIN dagitim_yerleri dy ON s.dagitim_yeri_id = dy.id
    ORDER BY s.created_at DESC
    LIMIT 10
");

$current_page = 'dashboard';
include 'includes/header.php';
?>

<style>
.siparis-row {
    cursor: pointer;
    transition: all 0.2s;
    background: linear-gradient(135deg, #FFE4E1 0%, #FFC0CB 100%);
}
.siparis-row:hover {
    background: linear-gradient(135deg, #FFD6D6 0%, #FFB3BA 100%);
    transform: translateX(2px);
}
/* Bekliyor durumu için sarı-turuncu */
.siparis-row.durum-bekliyor {
    background: linear-gradient(135deg, #FFF9E6 0%, #FFE4B3 100%);
}
.siparis-row.durum-bekliyor:hover {
    background: linear-gradient(135deg, #FFF4D6 0%, #FFD699 100%);
}
/* Hazırlanıyor için açık mavi */
.siparis-row.durum-hazirlaniyor {
    background: linear-gradient(135deg, #E3F2FD 0%, #BBDEFB 100%);
}
.siparis-row.durum-hazirlaniyor:hover {
    background: linear-gradient(135deg, #D1E9FF 0%, #A3D5FF 100%);
}
/* Yolda için turkuaz */
.siparis-row.durum-yolda {
    background: linear-gradient(135deg, #E0F2F1 0%, #B2DFDB 100%);
}
.siparis-row.durum-yolda:hover {
    background: linear-gradient(135deg, #C8E8E6 0%, #9FD4CF 100%);
}
/* Teslim Edildi için açık yeşil */
.siparis-row.durum-teslim {
    background: linear-gradient(135deg, #E8F5E9 0%, #C8E6C9 100%);
}
.siparis-row.durum-teslim:hover {
    background: linear-gradient(135deg, #D4EDD6 0%, #B2DDB4 100%);
}
.urun-detay-row {
    background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
    display: none;
    border-left: 4px solid #2196F3;
}
.urun-detay-row.show {
    display: table-row;
}
.urun-liste {
    padding: 20px;
}
.urun-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 15px;
    margin-bottom: 10px;
    background: white;
    border-radius: 8px;
    border-left: 4px solid #2196F3;
    box-shadow: 0 2px 4px rgba(0,0,0,0.08);
    transition: all 0.2s;
}
/* Alternatif renkler - Her satır farklı renk */
.urun-item:nth-child(1) { 
    background: linear-gradient(135deg, #E3F2FD 0%, #BBDEFB 100%); 
    border-left-color: #2196F3; 
}
.urun-item:nth-child(2) { 
    background: linear-gradient(135deg, #F3E5F5 0%, #E1BEE7 100%); 
    border-left-color: #9C27B0; 
}
.urun-item:nth-child(3) { 
    background: linear-gradient(135deg, #E8F5E9 0%, #C8E6C9 100%); 
    border-left-color: #4CAF50; 
}
.urun-item:nth-child(4) { 
    background: linear-gradient(135deg, #FFF3E0 0%, #FFE0B2 100%); 
    border-left-color: #FF9800; 
}
.urun-item:nth-child(5) { 
    background: linear-gradient(135deg, #FCE4EC 0%, #F8BBD0 100%); 
    border-left-color: #E91E63; 
}
.urun-item:nth-child(6) { 
    background: linear-gradient(135deg, #E0F2F1 0%, #B2DFDB 100%); 
    border-left-color: #009688; 
}
.urun-item:nth-child(7) { 
    background: linear-gradient(135deg, #FFF9C4 0%, #FFF59D 100%); 
    border-left-color: #FBC02D; 
}
.urun-item:nth-child(8) { 
    background: linear-gradient(135deg, #E1F5FE 0%, #B3E5FC 100%); 
    border-left-color: #03A9F4; 
}
.urun-item:nth-child(9) { 
    background: linear-gradient(135deg, #F1F8E9 0%, #DCEDC8 100%); 
    border-left-color: #8BC34A; 
}
.urun-item:nth-child(10) { 
    background: linear-gradient(135deg, #EDE7F6 0%, #D1C4E9 100%); 
    border-left-color: #673AB7; 
}
/* 10'dan sonra tekrar başa dön */
.urun-item:nth-child(n+11):nth-child(odd) { 
    background: linear-gradient(135deg, #E3F2FD 0%, #BBDEFB 100%); 
    border-left-color: #2196F3; 
}
.urun-item:nth-child(n+11):nth-child(even) { 
    background: linear-gradient(135deg, #F3E5F5 0%, #E1BEE7 100%); 
    border-left-color: #9C27B0; 
}
.urun-item:hover {
    transform: translateX(3px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.12);
}
.urun-item:last-child {
    margin-bottom: 0;
}
.urun-kod {
    font-weight: 700;
    color: #2196F3;
    font-size: 0.95em;
    min-width: 100px;
}
.urun-ad {
    flex: 1;
    margin: 0 15px;
    color: #37474F;
    font-weight: 500;
}
.urun-miktarlar {
    display: flex;
    gap: 8px;
}
.miktar-badge {
    padding: 8px 14px;
    border-radius: 6px;
    font-size: 0.85em;
    font-weight: 600;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    text-align: center;
    line-height: 1.3;
}
.miktar-koli {
    background: linear-gradient(135deg, #2196F3 0%, #1976D2 100%);
    color: white;
}
.miktar-adet {
    background: linear-gradient(135deg, #FFC107 0%, #FFA000 100%);
    color: white;
}
.miktar-palet {
    background: linear-gradient(135deg, #4CAF50 0%, #388E3C 100%);
    color: white;
}
.toggle-icon {
    transition: transform 0.3s;
    color: #2196F3;
}
.toggle-icon.rotated {
    transform: rotate(180deg);
}
.urun-detay-empty {
    text-align: center;
    padding: 30px;
    color: #6c757d;
    font-style: italic;
    background: white;
    border-radius: 8px;
}
.btn-teslim-et {
    background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
    border: none;
    color: white;
    padding: 6px 16px;
    border-radius: 6px;
    font-weight: 600;
    transition: all 0.2s;
    box-shadow: 0 2px 4px rgba(76, 175, 80, 0.3);
}
.btn-teslim-et:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(76, 175, 80, 0.4);
    background: linear-gradient(135deg, #45a049 0%, #4CAF50 100%);
}
.btn-teslim-et:active {
    transform: translateY(0);
}
.btn-teslim-et i {
    margin-right: 5px;
}
</style>

<div class="container-fluid">
    <!-- Başlık -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>
            <i class="fas fa-chart-line"></i> Dashboard
        </h2>
        <div class="text-muted">
            <i class="fas fa-calendar"></i>
            <?php echo tarihFormatla(date('Y-m-d')); ?>
        </div>
    </div>

    <!-- İstatistik Kartları -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stat-card border-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Toplam Sipariş</h6>
                            <h2 class="mb-0"><?php echo $toplam_siparis; ?></h2>
                        </div>
                        <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stat-card border-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Bekleyen</h6>
                            <h2 class="mb-0"><?php echo $bekleyen_siparis; ?></h2>
                        </div>
                        <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stat-card border-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Hazırlanan</h6>
                            <h2 class="mb-0"><?php echo $hazirlanan_siparis; ?></h2>
                        </div>
                        <div class="stat-icon bg-info bg-opacity-10 text-info">
                            <i class="fas fa-box"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stat-card border-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Teslim Edilen</h6>
                            <h2 class="mb-0"><?php echo $teslim_edilen; ?></h2>
                        </div>
                        <div class="stat-icon bg-success bg-opacity-10 text-success">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Son Siparişler -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-list"></i> Son Siparişler
            </h5>
        </div>
        <div class="card-body">
            <?php if ($son_siparisler && count($son_siparisler) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="30"></th>
                                <th>Sipariş No</th>
                                <th>Tarih</th>
                                <th>Merkez</th>
                                <th>Bölge</th>
                                <th>Yer</th>
                                <th>Durum</th>
                                <th>İşlem</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($son_siparisler as $siparis): ?>
                                <?php
                                // Durum class'ı belirle
                                $durum_row_class = match($siparis['durum']) {
                                    'Bekliyor' => 'durum-bekliyor',
                                    'Hazırlanıyor' => 'durum-hazirlaniyor',
                                    'Yolda' => 'durum-yolda',
                                    'Teslim Edildi' => 'durum-teslim',
                                    default => ''
                                };
                                ?>
                                <tr class="siparis-row <?php echo $durum_row_class; ?>" onclick="toggleUrunler(<?php echo $siparis['id']; ?>)">
                                    <td>
                                        <i class="fas fa-chevron-down toggle-icon" id="icon-<?php echo $siparis['id']; ?>"></i>
                                    </td>
                                    <td><strong><?php echo $siparis['siparis_no']; ?></strong></td>
                                    <td><?php echo tarihFormatla($siparis['siparis_tarihi']); ?></td>
                                    <td><?php echo $siparis['merkez_adi']; ?></td>
                                    <td><?php echo $siparis['bolge_adi']; ?></td>
                                    <td><?php echo $siparis['yer_adi']; ?></td>
                                    <td>
                                        <?php
                                        $durum_class = match($siparis['durum']) {
                                            'Bekliyor' => 'status-bekliyor',
                                            'Hazırlanıyor' => 'status-hazirlaniyor',
                                            'Yolda' => 'status-yolda',
                                            'Teslim Edildi' => 'status-teslim',
                                            default => 'bg-secondary'
                                        };
                                        ?>
                                        <span class="badge <?php echo $durum_class; ?>">
                                            <?php echo $siparis['durum']; ?>
                                        </span>
                                    </td>
                                    <td onclick="event.stopPropagation()">
                                        <button class="btn btn-teslim-et btn-sm me-2" 
                                                onclick="hizliTeslim(<?php echo $siparis['id']; ?>, '<?php echo $siparis['siparis_no']; ?>')"
                                                <?php echo $siparis['durum'] == 'Teslim Edildi' ? 'disabled' : ''; ?>>
                                            <i class="fas fa-check-circle"></i> Teslim Et
                                        </button>
                                        <a href="pages/siparis-listesi.php?id=<?php echo $siparis['id']; ?>" 
                                           class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i> Detay
                                        </a>
                                    </td>
                                </tr>
                                <tr class="urun-detay-row" id="urunler-<?php echo $siparis['id']; ?>">
                                    <td colspan="8">
                                        <div class="urun-liste" id="urun-liste-<?php echo $siparis['id']; ?>">
                                            <div class="text-center text-muted">
                                                <i class="fas fa-spinner fa-spin"></i> Ürünler yükleniyor...
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center text-muted py-5">
                    <i class="fas fa-inbox fa-3x mb-3"></i>
                    <p>Henüz sipariş bulunmuyor</p>
                    <a href="pages/yeni-siparis.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Yeni Sipariş Ekle
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
let yuklenenSiparisler = {};

// Sayfa yüklendiğinde tüm ürünleri otomatik yükle
$(document).ready(function() {
    <?php if ($son_siparisler && count($son_siparisler) > 0): ?>
        <?php foreach ($son_siparisler as $siparis): ?>
            // Her sipariş için ürünleri yükle ve aç
            urunleriYukle(<?php echo $siparis['id']; ?>, true);
        <?php endforeach; ?>
    <?php endif; ?>
});

function toggleUrunler(siparisId) {
    const detayRow = document.getElementById('urunler-' + siparisId);
    const icon = document.getElementById('icon-' + siparisId);
    
    if (detayRow.classList.contains('show')) {
        // Kapat
        detayRow.classList.remove('show');
        icon.classList.remove('rotated');
    } else {
        // Aç
        detayRow.classList.add('show');
        icon.classList.add('rotated');
        
        // Eğer daha önce yüklenmediyse ürünleri getir
        if (!yuklenenSiparisler[siparisId]) {
            urunleriYukle(siparisId);
        }
    }
}

function urunleriYukle(siparisId, autoOpen = false) {
    $.ajax({
        url: 'ajax/siparis-urunleri.php',
        type: 'POST',
        data: { siparis_id: siparisId },
        dataType: 'json',
        success: function(response) {
            if (response.success && response.data.length > 0) {
                let html = '';
                response.data.forEach(function(urun) {
                    html += `
                        <div class="urun-item">
                            <div class="urun-kod">${urun.urun_kodu}</div>
                            <div class="urun-ad">${urun.urun_adi}</div>
                            <div class="urun-miktarlar">
                                <span class="miktar-badge miktar-koli">
                                    <small style="opacity: 0.8; font-size: 0.7em;">Koli</small><br>
                                    <i class="fas fa-box"></i> ${urun.koli_miktari}
                                </span>
                                <span class="miktar-badge miktar-adet">
                                    <small style="opacity: 0.8; font-size: 0.7em;">Adet</small><br>
                                    <i class="fas fa-cubes"></i> ${urun.adet_miktari}
                                </span>
                                <span class="miktar-badge miktar-palet">
                                    <small style="opacity: 0.8; font-size: 0.7em;">Palet</small><br>
                                    <i class="fas fa-pallet"></i> ${urun.palet_sayisi}
                                </span>
                            </div>
                        </div>
                    `;
                });
                $('#urun-liste-' + siparisId).html(html);
                yuklenenSiparisler[siparisId] = true;
                
                // Otomatik açma özelliği
                if (autoOpen) {
                    const detayRow = document.getElementById('urunler-' + siparisId);
                    const icon = document.getElementById('icon-' + siparisId);
                    detayRow.classList.add('show');
                    icon.classList.add('rotated');
                }
            } else {
                $('#urun-liste-' + siparisId).html('<div class="urun-detay-empty"><i class="fas fa-box-open"></i> Bu siparişte ürün bulunmuyor</div>');
            }
        },
        error: function() {
            $('#urun-liste-' + siparisId).html('<div class="alert alert-danger mb-0"><i class="fas fa-exclamation-circle"></i> Ürünler yüklenirken bir hata oluştu</div>');
        }
    });
}

// Hızlı teslim et fonksiyonu
function hizliTeslim(siparisId, siparisNo) {
    if (!confirm(`${siparisNo} numaralı siparişi "Teslim Edildi" olarak işaretlemek istediğinize emin misiniz?`)) {
        return;
    }
    
    $.ajax({
        url: 'ajax/siparis-durum.php',
        type: 'POST',
        data: { 
            siparis_id: siparisId, 
            yeni_durum: 'Teslim Edildi' 
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Toast mesajı göster (eğer showToast fonksiyonu varsa)
                if (typeof showToast === 'function') {
                    showToast(response.message, 'success');
                } else {
                    alert(response.message);
                }
                // Sayfayı yenile
                setTimeout(() => location.reload(), 1000);
            } else {
                if (typeof showToast === 'function') {
                    showToast(response.message, 'danger');
                } else {
                    alert(response.message);
                }
            }
        },
        error: function() {
            if (typeof showToast === 'function') {
                showToast('Bir hata oluştu!', 'danger');
            } else {
                alert('Bir hata oluştu!');
            }
        }
    });
}
</script>

<?php include 'includes/footer.php'; ?>