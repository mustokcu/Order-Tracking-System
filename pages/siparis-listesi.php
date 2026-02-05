<?php
require_once '../config/database.php';
require_once '../config/config.php';

loginKontrol();

// Filtreleme
$tarih_baslangic = get('tarih_baslangic', date('Y-m-d', strtotime('-30 days')));
$tarih_bitis = get('tarih_bitis', date('Y-m-d'));
$durum_filtre = get('durum', '');
$merkez_filtre = get('merkez', '');

// SQL sorgusu oluştur
$where = ["siparis_tarihi BETWEEN ? AND ?"];
$params = [$tarih_baslangic, $tarih_bitis];

if (!empty($durum_filtre)) {
    $where[] = "s.durum = ?";
    $params[] = $durum_filtre;
}

if (!empty($merkez_filtre)) {
    $where[] = "s.dagitim_merkezi_id = ?";
    $params[] = $merkez_filtre;
}

$where_sql = implode(' AND ', $where);

// Siparişleri getir
$siparisler = fetchAll("
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
    WHERE $where_sql
    ORDER BY s.created_at DESC
", $params);

// Merkezleri getir (filtre için)
$merkezler = fetchAll("SELECT * FROM dagitim_merkezleri ORDER BY merkez_adi");

$current_page = 'siparis-listesi';
include '../includes/header.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>
            <i class="fas fa-list-alt"></i> Sipariş Listesi
        </h2>
        <a href="yeni-siparis.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> Yeni Sipariş
        </a>
    </div>

    <!-- Filtreler -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Başlangıç Tarihi</label>
                    <input type="date" name="tarih_baslangic" class="form-control" value="<?php echo $tarih_baslangic; ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Bitiş Tarihi</label>
                    <input type="date" name="tarih_bitis" class="form-control" value="<?php echo $tarih_bitis; ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Durum</label>
                    <select name="durum" class="form-select">
                        <option value="">Tümü</option>
                        <?php foreach (SIPARIS_DURUMLARI as $durum): ?>
                            <option value="<?php echo $durum; ?>" <?php echo $durum_filtre == $durum ? 'selected' : ''; ?>>
                                <?php echo $durum; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Merkez</label>
                    <select name="merkez" class="form-select">
                        <option value="">Tümü</option>
                        <?php foreach ($merkezler as $merkez): ?>
                            <option value="<?php echo $merkez['id']; ?>" <?php echo $merkez_filtre == $merkez['id'] ? 'selected' : ''; ?>>
                                <?php echo $merkez['merkez_adi']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Filtrele
                    </button>
                    <a href="siparis-listesi.php" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Temizle
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Sipariş Listesi -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-list"></i> Siparişler 
                <span class="badge bg-primary"><?php echo count($siparisler); ?> Adet</span>
            </h5>
        </div>
        <div class="card-body">
            <?php if ($siparisler && count($siparisler) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Sipariş No</th>
                                <th>Tarih</th>
                                <th>Merkez</th>
                                <th>Bölge</th>
                                <th>Yer</th>
                                <th>Durum</th>
                                <th>Oluşturan</th>
                                <th>İşlem</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($siparisler as $siparis): ?>
                                <tr>
                                    <td><strong><?php echo $siparis['siparis_no']; ?></strong></td>
                                    <td><?php echo date('d.m.Y', strtotime($siparis['siparis_tarihi'])); ?></td>
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
                                        <span class="badge <?php echo $durum_class; ?> cursor-pointer" 
                                              onclick="durumDegistir(<?php echo $siparis['id']; ?>, '<?php echo $siparis['durum']; ?>')" 
                                              style="cursor: pointer;">
                                            <?php echo $siparis['durum']; ?> <i class="fas fa-edit ms-1"></i>
                                        </span>
                                    </td>
                                    <td><?php echo $siparis['ad_soyad']; ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" onclick="siparisDetay(<?php echo $siparis['id']; ?>)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="siparisSil(<?php echo $siparis['id']; ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center text-muted py-5">
                    <i class="fas fa-inbox fa-3x mb-3"></i>
                    <p>Sipariş bulunamadı</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Durum Değiştir Modal -->
<div class="modal fade" id="durumModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Sipariş Durumu Değiştir</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="durumForm">
                <input type="hidden" name="siparis_id" id="durum_siparis_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Yeni Durum *</label>
                        <select name="yeni_durum" id="yeni_durum" class="form-select" required>
                            <?php foreach (SIPARIS_DURUMLARI as $durum): ?>
                                <option value="<?php echo $durum; ?>"><?php echo $durum; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Güncelle
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Sipariş Detay Modal -->
<div class="modal fade" id="detayModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Sipariş Detayı</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detayIcerik">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Durum değiştir
function durumDegistir(siparis_id, mevcut_durum) {
    $('#durum_siparis_id').val(siparis_id);
    $('#yeni_durum').val(mevcut_durum);
    const modal = new bootstrap.Modal(document.getElementById('durumModal'));
    modal.show();
}

$('#durumForm').on('submit', function(e) {
    e.preventDefault();
    
    const siparis_id = $('#durum_siparis_id').val();
    const yeni_durum = $('#yeni_durum').val();
    
    if (!siparis_id || !yeni_durum) {
        showToast('Lütfen tüm alanları doldurun!', 'warning');
        return;
    }
    
    $.ajax({
        url: '../ajax/siparis-durum.php',
        type: 'POST',
        data: { 
            siparis_id: siparis_id, 
            yeni_durum: yeni_durum 
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showToast(response.message, 'success');
                $('#durumModal').modal('hide');
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast(response.message, 'danger');
            }
        },
        error: function() {
            showToast('Bir hata oluştu!', 'danger');
        }
    });
});

// Sipariş detay - İRSALİYE BİLGİLERİ DAHİL
function siparisDetay(siparis_id) {
    $('#detayIcerik').html('<div class="text-center py-4"><div class="spinner-border text-primary"></div><p class="mt-2">Yükleniyor...</p></div>');
    const modal = new bootstrap.Modal(document.getElementById('detayModal'));
    modal.show();
    
    $.ajax({
        url: '../ajax/siparis-detay.php',
        type: 'POST',
        data: { siparis_id: siparis_id },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const siparis = response.data.siparis;
                const urunler = response.data.urunler;
                
                let html = `
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p><strong>Sipariş No:</strong> ${siparis.siparis_no}</p>
                            <p><strong>Tarih:</strong> ${siparis.siparis_tarihi}</p>
                            <p><strong>Durum:</strong> <span class="badge bg-primary">${siparis.durum}</span></p>
                            <p><strong>Oluşturan:</strong> ${siparis.ad_soyad}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Merkez:</strong> ${siparis.merkez_adi}</p>
                            <p><strong>Bölge:</strong> ${siparis.bolge_adi}</p>
                            <p><strong>Yer:</strong> ${siparis.yer_adi}</p>
                        </div>
                    </div>
                    
                    <!-- İRSALİYE BİLGİLERİ BÖLÜMÜ -->
                    <div class="card mb-4 bg-light">
                        <div class="card-body">
                            <h6 class="mb-3"><i class="fas fa-file-invoice text-info"></i> İrsaliye Bilgileri</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>İrsaliye Tarihi:</strong> ${siparis.irsaliye_tarihi_formatted || '-'}</p>
                                    <p><strong>İrsaliye No:</strong> ${siparis.irsaliye_no || '-'}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>İrsaliye Miktarı:</strong> ${siparis.irsaliye_miktari || '-'}</p>
                                    <p><strong>Nakliye:</strong> ${siparis.nakliye || '-'}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <h6 class="mb-3"><i class="fas fa-box"></i> Ürünler</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Ürün Kodu</th>
                                    <th>Ürün Adı</th>
                                    <th>Koli</th>
                                    <th>Adet</th>
                                    <th>Palet</th>
                                </tr>
                            </thead>
                            <tbody>
                `;
                
                urunler.forEach(function(urun) {
                    html += `
                        <tr>
                            <td><strong>${urun.urun_kodu}</strong></td>
                            <td>${urun.urun_adi}</td>
                            <td><span class="badge bg-info">${urun.koli_miktari}</span></td>
                            <td><span class="badge bg-warning text-dark">${urun.adet_miktari}</span></td>
                            <td><span class="badge bg-success">${urun.palet_sayisi}</span></td>
                        </tr>
                    `;
                });
                
                html += `
                            </tbody>
                        </table>
                    </div>
                `;
                
                $('#detayIcerik').html(html);
            } else {
                $('#detayIcerik').html('<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> ' + response.message + '</div>');
            }
        },
        error: function() {
            $('#detayIcerik').html('<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Bir hata oluştu!</div>');
        }
    });
}

// Sipariş sil
function siparisSil(siparis_id) {
    if (confirm('⚠️ Bu siparişi silmek istediğinize emin misiniz?\n\nBu işlem geri alınamaz!')) {
        showLoading();
        $.ajax({
            url: '../ajax/siparis-sil.php',
            type: 'POST',
            data: { siparis_id: siparis_id },
            dataType: 'json',
            success: function(response) {
                hideLoading();
                if (response.success) {
                    showToast(response.message, 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showToast(response.message, 'danger');
                }
            },
            error: function() {
                hideLoading();
                showToast('Bir hata oluştu!', 'danger');
            }
        });
    }
}
</script>

<style>
.cursor-pointer {
    cursor: pointer;
    transition: all 0.2s ease;
}
.cursor-pointer:hover {
    opacity: 0.8;
    transform: scale(1.05);
}
</style>

<?php include '../includes/footer.php'; ?>