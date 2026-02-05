<div class="sidebar bg-dark text-white">
    <div class="sidebar-header p-3 border-bottom border-secondary">
        <h5 class="mb-0">
            <i class="fas fa-bars"></i> Menü
        </h5>
    </div>
    
    <ul class="sidebar-menu list-unstyled">
        <li>
            <a href="<?php echo $base_url; ?>index.php" class="sidebar-link <?php echo ($current_page == 'index' || $current_page == 'dashboard') ? 'active' : ''; ?>">
                <i class="fas fa-chart-line"></i>
                <span>Dashboard</span>
            </a>
        </li>
        
        <li>
            <a href="<?php echo $base_url; ?>pages/yeni-siparis.php" class="sidebar-link <?php echo $current_page == 'yeni-siparis' ? 'active' : ''; ?>">
                <i class="fas fa-plus-circle"></i>
                <span>Yeni Sipariş</span>
            </a>
        </li>
        
        <li>
            <a href="<?php echo $base_url; ?>pages/siparis-listesi.php" class="sidebar-link <?php echo $current_page == 'siparis-listesi' ? 'active' : ''; ?>">
                <i class="fas fa-list-alt"></i>
                <span>Sipariş Listesi</span>
            </a>
        </li>
        
        <li>
            <a href="<?php echo $base_url; ?>pages/urun-yonetimi.php" class="sidebar-link <?php echo $current_page == 'urun-yonetimi' ? 'active' : ''; ?>">
                <i class="fas fa-box-open"></i>
                <span>Ürün Yönetimi</span>
            </a>
        </li>
        
        <li>
            <a href="<?php echo $base_url; ?>pages/dagitim-ayarlari.php" class="sidebar-link <?php echo $current_page == 'dagitim-ayarlari' ? 'active' : ''; ?>">
                <i class="fas fa-cog"></i>
                <span>Dağıtım Ayarları</span>
            </a>
        </li>
    </ul>
</div>