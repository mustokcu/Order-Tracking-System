// Ana JavaScript Dosyası - Sipariş Takip Sistemi

$(document).ready(function() {
    
    // Sidebar Toggle
    $('#sidebarToggle').on('click', function() {
        $('#sidebar').toggleClass('sidebar-open');
        if ($('.sidebar-overlay').length === 0) {
            $('body').append('<div class="sidebar-overlay"></div>');
        }
        $('.sidebar-overlay').addClass('active');
    });
    
    // Sidebar Close
    $(document).on('click', '#sidebarClose, .sidebar-overlay', function() {
        $('#sidebar').removeClass('sidebar-open');
        $('.sidebar-overlay').removeClass('active');
        setTimeout(function() {
            $('.sidebar-overlay').remove();
        }, 300);
    });
    
    // Desktop'ta sidebar'ı her zaman göster
    $(window).on('resize', function() {
        if ($(window).width() > 768) {
            $('#sidebar').removeClass('sidebar-open');
            $('.sidebar-overlay').remove();
        }
    });
    
    // Alert otomatik kapatma
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
    
    // Tooltip aktifleştir
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Sayı formatla (Türkçe)
    window.numberFormat = function(number) {
        return new Intl.NumberFormat('tr-TR').format(number);
    };
    
    // Tarih formatla
    window.dateFormat = function(date) {
        return new Date(date).toLocaleDateString('tr-TR', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    };
    
    // Confirm dialog
    window.confirmDialog = function(message, callback) {
        if (confirm(message)) {
            callback();
        }
    };
    
    // Loading göster
    window.showLoading = function() {
        $('body').append('<div class="loading-overlay"><div class="spinner-border text-primary" role="status"></div></div>');
    };
    
    // Loading gizle
    window.hideLoading = function() {
        $('.loading-overlay').remove();
    };
    
    // Toast mesaj göster
    window.showToast = function(message, type = 'success') {
        const toastHtml = `
            <div class="toast align-items-center text-white bg-${type} border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;
        
        if ($('.toast-container').length === 0) {
            $('body').append('<div class="toast-container position-fixed top-0 end-0 p-3"></div>');
        }
        
        $('.toast-container').append(toastHtml);
        const toast = new bootstrap.Toast($('.toast').last()[0]);
        toast.show();
        
        setTimeout(function() {
            $('.toast').last().remove();
        }, 5000);
    };
    
    // Form validasyon
    window.validateForm = function(formId) {
        const form = document.getElementById(formId);
        if (!form) return false;
        
        let isValid = true;
        const inputs = form.querySelectorAll('[required]');
        
        inputs.forEach(function(input) {
            if (!input.value.trim()) {
                input.classList.add('is-invalid');
                isValid = false;
            } else {
                input.classList.remove('is-invalid');
            }
        });
        
        return isValid;
    };
    
    // Tablo arama
    window.searchTable = function(inputId, tableId) {
        const input = document.getElementById(inputId);
        const table = document.getElementById(tableId);
        
        if (!input || !table) return;
        
        input.addEventListener('keyup', function() {
            const filter = this.value.toUpperCase();
            const rows = table.getElementsByTagName('tr');
            
            for (let i = 1; i < rows.length; i++) {
                const cells = rows[i].getElementsByTagName('td');
                let found = false;
                
                for (let j = 0; j < cells.length; j++) {
                    if (cells[j].textContent.toUpperCase().indexOf(filter) > -1) {
                        found = true;
                        break;
                    }
                }
                
                rows[i].style.display = found ? '' : 'none';
            }
        });
    };
    
    // Sayfa yüklendiğinde tablo aramasını aktif et
    if (document.getElementById('aramaInput') && document.getElementById('urunTable')) {
        searchTable('aramaInput', 'urunTable');
    }
    
    // AJAX Request Helper
    window.ajaxRequest = function(url, method, data, successCallback, errorCallback) {
        $.ajax({
            url: url,
            type: method,
            data: data,
            dataType: 'json',
            success: function(response) {
                if (successCallback) successCallback(response);
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                if (errorCallback) {
                    errorCallback(error);
                } else {
                    showToast('Bir hata oluştu!', 'danger');
                }
            }
        });
    };
    
    // Input sadece rakam
    $('.number-only').on('keypress', function(e) {
        const charCode = (e.which) ? e.which : e.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        return true;
    });
    
    // Türkçe karakter düzeltme
    window.turkishToUpper = function(str) {
        const letters = { 'i': 'İ', 'ş': 'Ş', 'ğ': 'Ğ', 'ü': 'Ü', 'ö': 'Ö', 'ç': 'Ç', 'ı': 'I' };
        return str.replace(/[işğüöçı]/g, function(letter) { 
            return letters[letter]; 
        }).toUpperCase();
    };
    
    // Excel'den kopyala-yapıştır için hazırlık
    window.parseExcelData = function(text) {
        const lines = text.trim().split('\n');
        const data = [];
        
        lines.forEach(function(line) {
            const parts = line.split('\t').map(p => p.trim());
            if (parts.length >= 2 && parts[0] && parts[1]) {
                data.push({
                    kod: parts[0],
                    ad: parts[1]
                });
            }
        });
        
        return data;
    };
    
    console.log('Sipariş Takip Sistemi yüklendi!');
});

// Loading overlay CSS ekle
const style = document.createElement('style');
style.textContent = `
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }
    .loading-overlay .spinner-border {
        width: 3rem;
        height: 3rem;
    }
`;
document.head.appendChild(style);