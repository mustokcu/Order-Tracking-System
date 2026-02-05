// Dağıtım Ayarları JavaScript

// MERKEZ FONKSİYONLARI
function merkezEkleModal() {
    $('#merkezModalTitle').text('Yeni Merkez Ekle');
    $('#merkez_id').val('');
    $('#merkez_adi').val('');
    new bootstrap.Modal($('#merkezModal')).show();
}

function merkezDuzenle(id, adi) {
    $('#merkezModalTitle').text('Merkez Düzenle');
    $('#merkez_id').val(id);
    $('#merkez_adi').val(adi);
    new bootstrap.Modal($('#merkezModal')).show();
}

function merkezSil(id) {
    if (confirm('Bu merkezi silmek istediğinize emin misiniz?')) {
        $.ajax({
            url: '../ajax/dagitim-sil.php',
            type: 'POST',
            data: { tip: 'merkez', id: id },
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

$('#merkezForm').on('submit', function(e) {
    e.preventDefault();
    
    $.ajax({
        url: '../ajax/dagitim-kaydet.php',
        type: 'POST',
        data: $(this).serialize() + '&tip=merkez',
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

// BÖLGE FONKSİYONLARI
function bolgeEkleModal() {
    $('#bolgeModalTitle').text('Yeni Bölge Ekle');
    $('#bolge_id').val('');
    $('#bolge_merkez_id').val('');
    $('#bolge_adi').val('');
    new bootstrap.Modal($('#bolgeModal')).show();
}

function bolgeDuzenle(id, merkez_id, adi) {
    $('#bolgeModalTitle').text('Bölge Düzenle');
    $('#bolge_id').val(id);
    $('#bolge_merkez_id').val(merkez_id);
    $('#bolge_adi').val(adi);
    new bootstrap.Modal($('#bolgeModal')).show();
}

function bolgeSil(id) {
    if (confirm('Bu bölgeyi silmek istediğinize emin misiniz?')) {
        $.ajax({
            url: '../ajax/dagitim-sil.php',
            type: 'POST',
            data: { tip: 'bolge', id: id },
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

$('#bolgeForm').on('submit', function(e) {
    e.preventDefault();
    
    $.ajax({
        url: '../ajax/dagitim-kaydet.php',
        type: 'POST',
        data: $(this).serialize() + '&tip=bolge',
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

// YER FONKSİYONLARI
function yerEkleModal() {
    $('#yerModalTitle').text('Yeni Yer Ekle');
    $('#yer_id').val('');
    $('#yer_bolge_id').val('');
    $('#yer_adi').val('');
    new bootstrap.Modal($('#yerModal')).show();
}

function yerDuzenle(id, bolge_id, adi) {
    $('#yerModalTitle').text('Yer Düzenle');
    $('#yer_id').val(id);
    $('#yer_bolge_id').val(bolge_id);
    $('#yer_adi').val(adi);
    new bootstrap.Modal($('#yerModal')).show();
}

function yerSil(id) {
    if (confirm('Bu yeri silmek istediğinize emin misiniz?')) {
        $.ajax({
            url: '../ajax/dagitim-sil.php',
            type: 'POST',
            data: { tip: 'yer', id: id },
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

$('#yerForm').on('submit', function(e) {
    e.preventDefault();
    
    $.ajax({
        url: '../ajax/dagitim-kaydet.php',
        type: 'POST',
        data: $(this).serialize() + '&tip=yer',
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