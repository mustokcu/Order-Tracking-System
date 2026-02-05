<?php
require_once '../config/database.php';
require_once '../config/config.php';

loginKontrol();

// Ürünleri getir
$urunler = fetchAll("SELECT * FROM urunler ORDER BY urun_adi ASC");

$current_page = 'urun-yonetimi';
include '../includes/header.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>
            <i class="fas fa-box-open"></i> Ürün Yönetimi
        </h2>
        <div>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#topluEkleModal">
                <i class="fas fa-file-excel"></i> Toplu Ekle
            </button>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#urunEkleModal">
                <i class="fas fa-plus"></i> Yeni Ürün
            </button>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="mb-0">Ürün Listesi</h5>
                </div>
                <div class="col-md-6">
                    <input type="text" id="aramaInput" class="form-control" placeholder="Ürün ara...">
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="urunTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Ürün Kodu</th>
                            <th>Ürün Adı</th>
                            <th>Eklenme Tarihi</th>
                            <th>İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($urunler && count($urunler) > 0): ?>
                            <?php foreach ($urunler as $index => $urun): ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td><strong><?php echo $urun['urun_kodu']; ?></strong></td>
                                    <td><?php echo $urun['urun_adi']; ?></td>
                                    <td><?php echo date('d.m.Y H:i', strtotime($urun['created_at'])); ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-warning" onclick="urunDuzenle(<?php echo $urun['id']; ?>, '<?php echo addslashes($urun['urun_kodu']); ?>', '<?php echo addslashes($urun['urun_adi']); ?>')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="urunSil(<?php echo $urun['id']; ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x mb-2"></i>
                                    <p>Henüz ürün eklenmemiş</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Yeni Ürün Modal -->
<div class="modal fade" id="urunEkleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus"></i> Yeni Ürün Ekle
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="urunEkleForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Ürün Kodu *</label>
                        <input type="text" name="urun_kodu" class="form-control" required>
                        <small class="text-muted">Örnek: 11000075</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ürün Adı *</label>
                        <input type="text" name="urun_adi" class="form-control" required>
                        <small class="text-muted">Örnek: DOND.SOMON DİLİM 440 GR</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Kaydet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Toplu Ürün Ekle Modal -->
<div class="modal fade" id="topluEkleModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-file-excel"></i> Toplu Ürün Ekle (Excel'den Yapıştır)
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="topluEkleForm">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Nasıl kullanılır?</strong><br>
                        Excel'den ürün kodları ve isimleri kopyalayıp aşağıdaki alana yapıştırın.<br>
                        Format: <code>ÜRÜN_KODU [TAB] ÜRÜN_ADI</code>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Excel Verilerini Yapıştırın *</label>
                        <textarea name="excel_data" class="form-control" rows="10" required placeholder="11000075    DOND.SOMON DİLİM 440 GR
11000300    DOND.MİDYE ETİ 500 GR
11001936    DOND.HALKA KALAMAR 500 GR"></textarea>
                    </div>
                    
                    <div id="onizleme" class="d-none">
                        <h6>Önizleme:</h6>
                        <div id="onizlemeIcerik" class="border p-3 bg-light" style="max-height: 200px; overflow-y: auto;"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="button" class="btn btn-info" onclick="onizlemeGoster()">
                        <i class="fas fa-eye"></i> Önizleme
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Toplu Kaydet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Ürün Düzenle Modal -->
<div class="modal fade" id="urunDuzenleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-edit"></i> Ürün Düzenle
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="urunDuzenleForm">
                <input type="hidden" name="urun_id" id="duzenle_urun_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Ürün Kodu *</label>
                        <input type="text" name="urun_kodu" id="duzenle_urun_kodu" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ürün Adı *</label>
                        <input type="text" name="urun_adi" id="duzenle_urun_adi" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save"></i> Güncelle
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Sayfa yüklendiğinde arama fonksiyonunu aktif et
$(document).ready(function() {
    // Arama fonksiyonu
    if (typeof searchTable === 'function') {
        searchTable('aramaInput', 'urunTable');
    }
});

// Tek ürün ekle
$('#urunEkleForm').on('submit', function(e) {
    e.preventDefault();
    
    $.ajax({
        url: '../ajax/urun-ekle.php',
        type: 'POST',
        data: $(this).serialize(),
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showToast(response.message, 'success');
                $('#urunEkleModal').modal('hide');
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

// Toplu ürün ekle
$('#topluEkleForm').on('submit', function(e) {
    e.preventDefault();
    
    const excelData = $('textarea[name="excel_data"]').val().trim();
    
    if (!excelData) {
        showToast('Lütfen ürün verilerini yapıştırın!', 'warning');
        return;
    }
    
    showLoading();
    
    $.ajax({
        url: '../ajax/toplu-urun-ekle.php',
        type: 'POST',
        data: { excel_data: excelData },
        dataType: 'json',
        success: function(response) {
            hideLoading();
            if (response.success) {
                let mesaj = response.message;
                if (response.data.atlanan > 0) {
                    mesaj += ' (' + response.data.atlanan + ' ürün zaten mevcuttu)';
                }
                showToast(mesaj, 'success');
                $('#topluEkleModal').modal('hide');
                setTimeout(() => location.reload(), 1500);
            } else {
                showToast(response.message, 'danger');
            }
        },
        error: function(xhr, status, error) {
            hideLoading();
            console.error('AJAX Error:', error);
            showToast('Bir hata oluştu! Lütfen tekrar deneyin.', 'danger');
        }
    });
});

// Önizleme göster
function onizlemeGoster() {
    const data = $('textarea[name="excel_data"]').val();
    const parsed = parseExcelData(data);
    
    if (parsed.length === 0) {
        showToast('Geçerli veri bulunamadı!', 'warning');
        return;
    }
    
    let html = '<table class="table table-sm"><thead><tr><th>Ürün Kodu</th><th>Ürün Adı</th></tr></thead><tbody>';
    parsed.forEach(item => {
        html += `<tr><td>${item.kod}</td><td>${item.ad}</td></tr>`;
    });
    html += '</tbody></table>';
    html += `<p class="text-success"><strong>${parsed.length} ürün bulundu</strong></p>`;
    
    $('#onizlemeIcerik').html(html);
    $('#onizleme').removeClass('d-none');
}

// Ürün düzenle
function urunDuzenle(id, kod, ad) {
    $('#duzenle_urun_id').val(id);
    $('#duzenle_urun_kodu').val(kod);
    $('#duzenle_urun_adi').val(ad);
    new bootstrap.Modal($('#urunDuzenleModal')).show();
}

$('#urunDuzenleForm').on('submit', function(e) {
    e.preventDefault();
    
    $.ajax({
        url: '../ajax/urun-guncelle.php',
        type: 'POST',
        data: $(this).serialize(),
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showToast(response.message, 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast(response.message, 'danger');
            }
        }
    });
});

// Ürün sil
function urunSil(id) {
    if (confirm('Bu ürünü silmek istediğinize emin misiniz?')) {
        $.ajax({
            url: '../ajax/urun-sil.php',
            type: 'POST',
            data: { urun_id: id },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showToast(response.message, 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showToast(response.message, 'danger');
                }
            }
        });
    }
}
</script>

<?php include '../includes/footer.php'; ?>