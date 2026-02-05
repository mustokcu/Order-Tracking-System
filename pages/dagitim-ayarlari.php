<?php
require_once '../config/database.php';
require_once '../config/config.php';

loginKontrol();

// Merkezleri getir
$merkezler = fetchAll("SELECT * FROM dagitim_merkezleri ORDER BY merkez_adi ASC");

// Bölgeleri getir
$bolgeler = fetchAll("
    SELECT db.*, dm.merkez_adi 
    FROM dagitim_bolgeleri db
    LEFT JOIN dagitim_merkezleri dm ON db.merkez_id = dm.id
    ORDER BY dm.merkez_adi, db.bolge_adi ASC
");

// Yerleri getir
$yerler = fetchAll("
    SELECT dy.*, db.bolge_adi, dm.merkez_adi
    FROM dagitim_yerleri dy
    LEFT JOIN dagitim_bolgeleri db ON dy.bolge_id = db.id
    LEFT JOIN dagitim_merkezleri dm ON db.merkez_id = dm.id
    ORDER BY dm.merkez_adi, db.bolge_adi, dy.yer_adi ASC
");

$current_page = 'dagitim-ayarlari';
include '../includes/header.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>
            <i class="fas fa-cog"></i> Dağıtım Ayarları
        </h2>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4" id="dagitimTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#merkezler">
                🏢 Merkezler
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#bolgeler">
                🗺️ Bölgeler
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#yerler">
                📍 Yerler
            </button>
        </li>
    </ul>

    <div class="tab-content">
        <!-- MERKEZLER -->
        <div class="tab-pane fade show active" id="merkezler">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h5 class="mb-0">Dağıtım Merkezleri</h5>
                    <button class="btn btn-primary btn-sm" onclick="merkezEkleModal()">
                        <i class="fas fa-plus"></i> Yeni Merkez
                    </button>
                </div>
                <div class="card-body">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Merkez Adı</th>
                                <th>Durum</th>
                                <th>İşlem</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($merkezler as $index => $merkez): ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td><strong><?php echo $merkez['merkez_adi']; ?></strong></td>
                                    <td>
                                        <span class="badge bg-<?php echo $merkez['durum'] ? 'success' : 'secondary'; ?>">
                                            <?php echo $merkez['durum'] ? 'Aktif' : 'Pasif'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-warning" onclick="merkezDuzenle(<?php echo $merkez['id']; ?>, '<?php echo addslashes($merkez['merkez_adi']); ?>')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="merkezSil(<?php echo $merkez['id']; ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- BÖLGELER -->
        <div class="tab-pane fade" id="bolgeler">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h5 class="mb-0">Dağıtım Bölgeleri</h5>
                    <button class="btn btn-primary btn-sm" onclick="bolgeEkleModal()">
                        <i class="fas fa-plus"></i> Yeni Bölge
                    </button>
                </div>
                <div class="card-body">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Merkez</th>
                                <th>Bölge Adı</th>
                                <th>Durum</th>
                                <th>İşlem</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($bolgeler as $index => $bolge): ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td><?php echo $bolge['merkez_adi']; ?></td>
                                    <td><strong><?php echo $bolge['bolge_adi']; ?></strong></td>
                                    <td>
                                        <span class="badge bg-<?php echo $bolge['durum'] ? 'success' : 'secondary'; ?>">
                                            <?php echo $bolge['durum'] ? 'Aktif' : 'Pasif'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-warning" onclick="bolgeDuzenle(<?php echo $bolge['id']; ?>, <?php echo $bolge['merkez_id']; ?>, '<?php echo addslashes($bolge['bolge_adi']); ?>')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="bolgeSil(<?php echo $bolge['id']; ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- YERLER -->
        <div class="tab-pane fade" id="yerler">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h5 class="mb-0">Dağıtım Yerleri</h5>
                    <button class="btn btn-primary btn-sm" onclick="yerEkleModal()">
                        <i class="fas fa-plus"></i> Yeni Yer
                    </button>
                </div>
                <div class="card-body">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Merkez</th>
                                <th>Bölge</th>
                                <th>Yer Adı</th>
                                <th>Durum</th>
                                <th>İşlem</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($yerler as $index => $yer): ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td><?php echo $yer['merkez_adi']; ?></td>
                                    <td><?php echo $yer['bolge_adi']; ?></td>
                                    <td><strong><?php echo $yer['yer_adi']; ?></strong></td>
                                    <td>
                                        <span class="badge bg-<?php echo $yer['durum'] ? 'success' : 'secondary'; ?>">
                                            <?php echo $yer['durum'] ? 'Aktif' : 'Pasif'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-warning" onclick="yerDuzenle(<?php echo $yer['id']; ?>, <?php echo $yer['bolge_id']; ?>, '<?php echo addslashes($yer['yer_adi']); ?>')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="yerSil(<?php echo $yer['id']; ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Merkez Modal -->
<div class="modal fade" id="merkezModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="merkezModalTitle">Merkez Ekle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="merkezForm">
                <input type="hidden" name="merkez_id" id="merkez_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Merkez Adı *</label>
                        <input type="text" name="merkez_adi" id="merkez_adi" class="form-control" required>
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

<!-- Bölge Modal -->
<div class="modal fade" id="bolgeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bolgeModalTitle">Bölge Ekle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="bolgeForm">
                <input type="hidden" name="bolge_id" id="bolge_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Merkez *</label>
                        <select name="merkez_id" id="bolge_merkez_id" class="form-select" required>
                            <option value="">Seçiniz</option>
                            <?php foreach ($merkezler as $merkez): ?>
                                <option value="<?php echo $merkez['id']; ?>"><?php echo $merkez['merkez_adi']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bölge Adı *</label>
                        <input type="text" name="bolge_adi" id="bolge_adi" class="form-control" required>
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

<!-- Yer Modal -->
<div class="modal fade" id="yerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="yerModalTitle">Yer Ekle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="yerForm">
                <input type="hidden" name="yer_id" id="yer_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Bölge *</label>
                        <select name="bolge_id" id="yer_bolge_id" class="form-select" required>
                            <option value="">Seçiniz</option>
                            <?php foreach ($bolgeler as $bolge): ?>
                                <option value="<?php echo $bolge['id']; ?>"><?php echo $bolge['merkez_adi']; ?> - <?php echo $bolge['bolge_adi']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Yer Adı *</label>
                        <input type="text" name="yer_adi" id="yer_adi" class="form-control" required>
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

<script src="../assets/js/dagitim-ayarlari.js"></script>

<?php include '../includes/footer.php'; ?>