<?php
require_once '../config/database.php';
require_once '../config/config.php';

loginKontrol();

// Otomatik sipariş no oluştur
$siparis_no = siparisNoOlustur();

// Merkezleri getir
$merkezler = fetchAll("SELECT * FROM dagitim_merkezleri WHERE durum = 1 ORDER BY merkez_adi");

$current_page = 'yeni-siparis';
include '../includes/header.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>
            <i class="fas fa-plus-circle"></i> Yeni Sipariş Ekle
        </h2>
    </div>

    <form id="siparisForm">
        <div class="row">
            <!-- Sol Taraf -->
            <div class="col-lg-8">
                <!-- Sipariş Bilgileri -->
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-info-circle"></i> Sipariş Bilgileri</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Sipariş No *</label>
                                <input type="text" name="siparis_no" class="form-control" value="<?php echo $siparis_no; ?>" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Sipariş Tarihi *</label>
                                <input type="date" name="siparis_tarihi" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- İRSALİYE BİLGİLERİ - YENİ EKLENEN BÖLÜM -->
                <div class="card mb-3">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-file-invoice"></i> İrsaliye Bilgileri</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">İrsaliye Tarihi</label>
                                <input type="date" name="irsaliye_tarihi" class="form-control">
                                <small class="text-muted">Opsiyonel</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">İrsaliye No</label>
                                <input type="text" name="irsaliye_no" class="form-control" placeholder="Örn: IRS-2024-001">
                                <small class="text-muted">Opsiyonel</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">İrsaliye Miktarı</label>
                                <input type="number" name="irsaliye_miktari" class="form-control" step="0.01" min="0" placeholder="0.00">
                                <small class="text-muted">Opsiyonel</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nakliye</label>
                                <input type="text" name="nakliye" class="form-control" placeholder="Nakliye şirketi veya bilgisi">
                                <small class="text-muted">Opsiyonel</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ürünler -->
                <div class="card mb-3">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-box"></i> Ürünler</h5>
                    </div>
                    <div class="card-body">
                        <!-- Ürün Arama -->
                        <div class="mb-3">
                            <label class="form-label">Ürün Ara</label>
                            <div class="search-box">
                                <input type="text" id="urunArama" class="form-control" placeholder="Ürün kodu veya adı ile ara...">
                                <div id="aramasonuclari" class="search-results" style="display:none;"></div>
                            </div>
                        </div>

                        <!-- Seçili Ürünler -->
                        <div id="seciliUrunler">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Yukarıdaki arama kutusundan ürün ekleyin
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sağ Taraf -->
            <div class="col-lg-4">
                <!-- Dağıtım Bilgileri -->
                <div class="card mb-3">
                    <div class="card-header bg-warning">
                        <h5 class="mb-0"><i class="fas fa-truck"></i> Dağıtım Bilgileri</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Dağıtım Merkezi *</label>
                            <select name="dagitim_merkezi_id" id="dagitimMerkezi" class="form-select" required>
                                <option value="">Seçiniz</option>
                                <?php foreach ($merkezler as $merkez): ?>
                                    <option value="<?php echo $merkez['id']; ?>"><?php echo $merkez['merkez_adi']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Dağıtım Bölgesi *</label>
                            <select name="dagitim_bolgesi_id" id="dagitimBolgesi" class="form-select" required disabled>
                                <option value="">Önce merkez seçin</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Dağıtım Yeri *</label>
                            <select name="dagitim_yeri_id" id="dagitimYeri" class="form-select" required disabled>
                                <option value="">Önce bölge seçin</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Kaydet Butonu -->
                <div class="card">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary w-100 btn-lg">
                            <i class="fas fa-save"></i> Siparişi Kaydet
                        </button>
                        <a href="siparis-listesi.php" class="btn btn-secondary w-100 mt-2">
                            <i class="fas fa-times"></i> İptal
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
let seciliUrunler = [];

// Ürün arama
let aramaTimer;
$('#urunArama').on('keyup', function() {
    clearTimeout(aramaTimer);
    const arama = $(this).val().trim();
    
    if (arama.length < 2) {
        $('#aramasonuclari').hide();
        return;
    }
    
    aramaTimer = setTimeout(function() {
        $.ajax({
            url: '../ajax/urun-ara.php',
            type: 'POST',
            data: { arama: arama },
            dataType: 'json',
            success: function(response) {
                if (response.success && response.data.length > 0) {
                    let html = '';
                    response.data.forEach(function(urun) {
                        html += `<div class="search-result-item" onclick="urunEkle(${urun.id}, '${urun.urun_kodu}', '${urun.urun_adi}')">
                            <strong>${urun.urun_kodu}</strong> - ${urun.urun_adi}
                        </div>`;
                    });
                    $('#aramasonuclari').html(html).show();
                } else {
                    $('#aramasonuclari').html('<div class="p-3 text-muted">Ürün bulunamadı</div>').show();
                }
            }
        });
    }, 300);
});

// Dokümana tıklandığında arama sonuçlarını kapat
$(document).on('click', function(e) {
    if (!$(e.target).closest('.search-box').length) {
        $('#aramasonuclari').hide();
    }
});

// Ürün ekle
function urunEkle(id, kod, ad) {
    $('#aramasonuclari').hide();
    $('#urunArama').val('');
    
    // Daha önce eklendi mi kontrol et
    if (seciliUrunler.find(u => u.id === id)) {
        showToast('Bu ürün zaten eklenmiş!', 'warning');
        return;
    }
    
    seciliUrunler.push({ id, kod, ad, koli: 0, adet: 0, palet: 0 });
    urunListesiGuncelle();
}

// Ürün listesini güncelle
function urunListesiGuncelle() {
    if (seciliUrunler.length === 0) {
        $('#seciliUrunler').html(`
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Yukarıdaki arama kutusundan ürün ekleyin
            </div>
        `);
        return;
    }
    
    let html = '<div class="table-responsive"><table class="table table-bordered"><thead><tr><th>Ürün Kodu</th><th>Ürün Adı</th><th>Koli</th><th>Adet</th><th>Palet</th><th></th></tr></thead><tbody>';
    
    seciliUrunler.forEach(function(urun, index) {
        html += `
            <tr>
                <td><strong>${urun.kod}</strong></td>
                <td>${urun.ad}</td>
                <td><input type="number" class="form-control form-control-sm number-only" value="${urun.koli}" onchange="miktarGuncelle(${index}, 'koli', this.value)" min="0"></td>
                <td><input type="number" class="form-control form-control-sm number-only" value="${urun.adet}" onchange="miktarGuncelle(${index}, 'adet', this.value)" min="0"></td>
                <td><input type="number" class="form-control form-control-sm number-only" value="${urun.palet}" onchange="miktarGuncelle(${index}, 'palet', this.value)" min="0"></td>
                <td><button type="button" class="btn btn-sm btn-danger" onclick="urunSil(${index})"><i class="fas fa-trash"></i></button></td>
            </tr>
        `;
    });
    
    html += '</tbody></table></div>';
    $('#seciliUrunler').html(html);
}

// Miktar güncelle
function miktarGuncelle(index, tip, deger) {
    seciliUrunler[index][tip] = parseInt(deger) || 0;
}

// Ürün sil
function urunSil(index) {
    seciliUrunler.splice(index, 1);
    urunListesiGuncelle();
}

// Merkez değiştiğinde bölgeleri getir
$('#dagitimMerkezi').on('change', function() {
    const merkez_id = $(this).val();
    
    if (!merkez_id) {
        $('#dagitimBolgesi').html('<option value="">Önce merkez seçin</option>').prop('disabled', true);
        $('#dagitimYeri').html('<option value="">Önce bölge seçin</option>').prop('disabled', true);
        return;
    }
    
    $.ajax({
        url: '../ajax/bolge-getir.php',
        type: 'POST',
        data: { merkez_id: merkez_id },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                let html = '<option value="">Seçiniz</option>';
                response.data.forEach(function(bolge) {
                    html += `<option value="${bolge.id}">${bolge.bolge_adi}</option>`;
                });
                $('#dagitimBolgesi').html(html).prop('disabled', false);
                $('#dagitimYeri').html('<option value="">Önce bölge seçin</option>').prop('disabled', true);
            }
        }
    });
});

// Bölge değiştiğinde yerleri getir
$('#dagitimBolgesi').on('change', function() {
    const bolge_id = $(this).val();
    
    if (!bolge_id) {
        $('#dagitimYeri').html('<option value="">Önce bölge seçin</option>').prop('disabled', true);
        return;
    }
    
    $.ajax({
        url: '../ajax/yer-getir.php',
        type: 'POST',
        data: { bolge_id: bolge_id },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                let html = '<option value="">Seçiniz</option>';
                response.data.forEach(function(yer) {
                    html += `<option value="${yer.id}">${yer.yer_adi}</option>`;
                });
                $('#dagitimYeri').html(html).prop('disabled', false);
            }
        }
    });
});

// Form gönder - İRSALİYE BİLGİLERİ EKLENDI
$('#siparisForm').on('submit', function(e) {
    e.preventDefault();
    
    if (seciliUrunler.length === 0) {
        showToast('En az bir ürün eklemelisiniz!', 'warning');
        return;
    }
    
    // Form verilerini topla
    const formData = {
        siparis_no: $('input[name="siparis_no"]').val(),
        siparis_tarihi: $('input[name="siparis_tarihi"]').val(),
        dagitim_merkezi_id: $('select[name="dagitim_merkezi_id"]').val(),
        dagitim_bolgesi_id: $('select[name="dagitim_bolgesi_id"]').val(),
        dagitim_yeri_id: $('select[name="dagitim_yeri_id"]').val(),
        // İRSALİYE BİLGİLERİ
        irsaliye_tarihi: $('input[name="irsaliye_tarihi"]').val(),
        irsaliye_no: $('input[name="irsaliye_no"]').val(),
        irsaliye_miktari: $('input[name="irsaliye_miktari"]').val(),
        nakliye: $('input[name="nakliye"]').val(),
        urunler: JSON.stringify(seciliUrunler)
    };
    
    showLoading();
    
    $.ajax({
        url: '../ajax/siparis-kaydet.php',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            hideLoading();
            if (response.success) {
                showToast(response.message, 'success');
                setTimeout(() => {
                    window.location.href = 'siparis-listesi.php';
                }, 1500);
            } else {
                showToast(response.message, 'danger');
            }
        },
        error: function() {
            hideLoading();
            showToast('Bir hata oluştu!', 'danger');
        }
    });
});
</script>

<?php include '../includes/footer.php'; ?>