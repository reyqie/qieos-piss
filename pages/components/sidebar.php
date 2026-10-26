<?php
    if (!defined('BASE_URL')) {
        require_once __DIR__ . '/../../script/connection.php';
    }

    $current_page = basename($_SERVER['PHP_SELF']);
    $menu = isset($_GET['menu']) ? $_GET['menu'] : '';

    // Ambil versi update terbaru dari database jika koneksi $conn tersedia
    $latest_version = 'v1.0.0';
    if (isset($conn) && $conn) {
        $q_ver = mysqli_query($conn, "SELECT update_version FROM updates WHERE deleted_at IS NULL ORDER BY id DESC LIMIT 1");
        if ($q_ver && mysqli_num_rows($q_ver) > 0) {
            $row_ver = mysqli_fetch_assoc($q_ver);
            $latest_version = 'v' . ltrim($row_ver['update_version'], 'v');
        }
    }
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/sidebar.css?v=<?php echo filemtime(__DIR__ . '/../../css/pages/sidebar.css'); ?>">

<!-- Mobile Overlay Backdrop -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- Floating Tooltip Container for Collapsed State -->
<div class="sidebar-tooltip" id="sidebarTooltip"></div>

<!-- Mobile Top Navbar -->
<nav class="navbar navbar-dark navbar-theme-primary px-3 d-lg-none" style="gap:6px;flex-wrap:nowrap;">
    <a class="navbar-brand d-flex align-items-center gap-2" href="<?php echo BASE_URL; ?>/pages/dashboard.php">
        <img src="<?php echo BASE_URL; ?>/assets/img/brand/qieos.png" alt="Qieos Logo" style="height: 32px; width: auto;" />
    </a>
    </a>
    <div class="d-flex align-items-center gap-1 ms-auto">
        <button class="mobile-nav-btn" onclick="openSearch()" aria-label="Search" style="width:38px;height:38px;border-radius:10px;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.1);color:#fff;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:14px;">
            <i class="fas fa-search"></i>
        </button>
        <button class="mobile-nav-btn" id="whatsNewBtnMobile" aria-label="What's New" style="width:38px;height:38px;border-radius:10px;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.1);color:#fff;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:14px;position:relative;">
            <i class="fas fa-bullhorn"></i>
            <span style="position:absolute;top:-3px;right:-3px;background:#ef4444;color:#fff;font-size:7px;font-weight:700;padding:2px 4px;border-radius:4px;">NEW</span>
        </button>
        <a href="<?php echo BASE_URL; ?>/pages/chat/chat.php" id="chatNavBtnMobile" class="mobile-nav-btn" aria-label="Chat" style="width:38px;height:38px;border-radius:10px;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.1);color:#fff;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:14px;position:relative;text-decoration:none;">
            <i class="fas fa-comments"></i>
            <span class="cart-badge chat-unread-badge d-none" style="min-width:16px;height:16px;font-size:9px;padding:2px 4px;top:-4px;right:-4px;">0</span>
        </a>
        <button type="button" class="mobile-nav-btn stock-notif-btn stock-notif-btn-mobile" id="stockNotifBtnMobile" aria-label="Notifikasi Stok" style="width:38px;height:38px;border-radius:10px;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.1);color:#fff;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:14px;position:relative;">
            <i class="fas fa-bell"></i>
            <span class="cart-badge stock-notif-badge d-none" style="min-width:16px;height:16px;font-size:9px;padding:2px 4px;top:-4px;right:-4px;">0</span>
            <span class="stock-notif-ring d-none"></span>
        </button>
        <span style="width:1px;height:22px;background:rgba(255,255,255,.18);margin:0 10px;"></span>
        <button
            class="navbar-toggler"
            type="button"
            id="mobileSidebarToggle"
            aria-label="Toggle navigation"
            style="width:38px;height:38px;padding:0;border-radius:10px;border:1px solid rgba(255,255,255,.1);display:flex;align-items:center;justify-content:center;">
            <span class="navbar-toggler-icon"></span>
        </button>
    </div>
</nav>

<!-- Main Collapsible Sidebar -->
<div class="sidebar" id="sidebarMenu">
    <!-- Meteor Shower Background -->
    <div class="sidebar-meteors" aria-hidden="true">
        <span class="meteor"></span>
        <span class="meteor"></span>
        <span class="meteor"></span>
        <span class="meteor"></span>
        <span class="meteor"></span>
    </div>

    <!-- Scroll Parallax Layers -->
    <div class="sidebar-parallax" aria-hidden="true">
        <div class="plx-depth plx-far">
            <span class="plx-glow"></span>
            <span class="pstar" style="top:16%;left:12%;--s:1.5px;--d:.2s"></span>
            <span class="pstar" style="top:26%;left:78%;--s:2px;--d:1.1s"></span>
            <span class="pstar" style="top:44%;left:20%;--s:1.5px;--d:1.8s"></span>
            <span class="pstar" style="top:56%;left:85%;--s:2px;--d:.6s"></span>
            <span class="pstar" style="top:72%;left:38%;--s:1.5px;--d:2.3s"></span>
        </div>
        <div class="plx-depth plx-mid">
            <span class="plx-moon" style="top:30%;left:58%"></span>
            <span class="pstar" style="top:12%;left:34%;--s:2.5px;--d:0s"></span>
            <span class="pstar" style="top:22%;left:90%;--s:2px;--d:1.4s"></span>
            <span class="pstar" style="top:38%;left:8%;--s:2.5px;--d:2.1s"></span>
            <span class="pstar" style="top:52%;left:64%;--s:2px;--d:.8s"></span>
            <span class="pstar" style="top:68%;left:26%;--s:2.5px;--d:1.7s"></span>
            <span class="pstar" style="top:82%;left:72%;--s:2px;--d:2.6s"></span>
        </div>
        <div class="plx-depth plx-near">
            <span class="pstar" style="top:20%;left:48%;--s:4px;--d:0s"></span>
            <span class="pstar" style="top:34%;left:14%;--s:3px;--d:1.2s"></span>
            <span class="pstar" style="top:50%;left:80%;--s:4px;--d:2s"></span>
            <span class="pstar" style="top:62%;left:40%;--s:3px;--d:.5s"></span>
            <span class="pstar" style="top:76%;left:88%;--s:3.5px;--d:1.6s"></span>
        </div>
    </div>

    <!-- Header -->
    <div class="sidebar-header">
        <a href="<?php echo BASE_URL; ?>/pages/dashboard.php" class="sidebar-logo">
            <div class="sidebar-logo-icon">
                <img src="<?php echo BASE_URL; ?>/assets/img/brand/qieos2.png" alt="Qieos Logo" />
            </div>
            <div class="sidebar-logo-text">
                <h4>Qieos</h4>
                <span>POS Management</span>
            </div>
        </a>
        <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle Sidebar">
            <i class="fas fa-chevron-left"></i>
        </button>
    </div>

    <!-- Mobile User Profile Block -->
    <div class="mobile-user-card d-lg-none mx-3 mt-3">
        <div class="d-flex align-items-center gap-3">
            <img
                src="<?php echo $user['photo'] ? BASE_URL . '/assets/img/uploads/' . $user['photo'] : BASE_URL . '/assets/img/default-avatar.jpg'; ?>"
                class="rounded-circle"
                style="width: 38px; height: 38px; object-fit: cover;"
                alt="User" />
            <div class="overflow-hidden">
                <h6 class="mb-0 text-white text-truncate" style="font-size: 13px; font-weight: 700;">
                    <?php echo $user['fullname'] != '' ? $user['fullname'] : $_SESSION['username']; ?>
                </h6>
                <small class="text-secondary" style="font-size: 11px;">
                    <?php echo ucwords(strtolower($user['role'])); ?>
                </small>
            </div>
        </div>
        <a href="<?php echo BASE_URL; ?>/sessions/logout.php" class="btn btn-sm btn-danger w-100 mt-2 py-1" style="background: #ef4444; border: none; font-size: 12px; font-weight: 600;">
            <i class="fas fa-sign-out-alt me-1"></i> Logout
        </a>
    </div>

    <!-- Menu Items -->
    <div class="sidebar-menu" id="sidebarMenuContainer">
        <ul class="nav flex-column">
            <li class="nav-item <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>/pages/dashboard.php" class="nav-link" data-tooltip="Dashboard">
                    <span class="sidebar-icon"><i class="fas fa-th-large"></i></span>
                    <span class="sidebar-text">Dashboard</span>
                </a>
            </li>

            <?php if ($user['role'] == 'developer') { ?>
                <?php include 'sidebar_developer.php' ?>
            <?php } ?>

            <?php if ($user['role'] == 'administrator') { ?>

                <!-- MASTER DATA -->
                <li class="nav-title">MASTER</li>

                <li class="nav-item <?= ($current_page == 'master-product.php') ? 'active' : ''; ?>">
                    <a href="<?php echo BASE_URL; ?>/pages/master/master-product.php" class="nav-link" data-tooltip="Master Produk">
                        <span class="sidebar-icon"><i class="fas fa-boxes-stacked"></i></span>
                        <span class="sidebar-text">Master Produk</span>
                    </a>
                </li>

                <li class="nav-item <?= ($current_page == 'master-unit.php') ? 'active' : ''; ?>">
                    <a href="<?php echo BASE_URL; ?>/pages/master/master-unit.php" class="nav-link" data-tooltip="Master Unit">
                        <span class="sidebar-icon"><i class="fas fa-ruler-combined"></i></span>
                        <span class="sidebar-text">Master Unit</span>
                    </a>
                </li>

                <li class="nav-item <?= ($current_page == 'master-supplier.php') ? 'active' : ''; ?>">
                    <a href="<?php echo BASE_URL; ?>/pages/master/master-supplier.php" class="nav-link" data-tooltip="Master Supplier">
                        <span class="sidebar-icon"><i class="fas fa-truck"></i></span>
                        <span class="sidebar-text">Master Supplier</span>
                    </a>
                </li>

                <li class="nav-item <?= ($current_page == 'master-customer.php') ? 'active' : ''; ?>">
                    <a href="<?php echo BASE_URL; ?>/pages/master/master-customer.php" class="nav-link" data-tooltip="Master Customer">
                        <span class="sidebar-icon"><i class="fas fa-users"></i></span>
                        <span class="sidebar-text">Master Customer</span>
                    </a>
                </li>

                <li class="nav-item <?= ($current_page == 'master-user.php') ? 'active' : ''; ?>">
                    <a href="<?php echo BASE_URL; ?>/pages/master/master-user.php" class="nav-link" data-tooltip="Master User">
                        <span class="sidebar-icon"><i class="fas fa-users-cog"></i></span>
                        <span class="sidebar-text">Master User</span>
                    </a>
                </li>

                <!-- STOK ACTION (SUMBER BARANG / FIFO) -->
                <li class="nav-title">STOK ACTION</li>

                <li class="nav-item <?= ($current_page == 'restock.php') ? 'active' : ''; ?>">
                    <a href="<?php echo BASE_URL; ?>/pages/stock/restock.php" class="nav-link" data-tooltip="Belanja &amp; Restock">
                        <span class="sidebar-icon"><i class="fas fa-cart-shopping"></i></span>
                        <span class="sidebar-text">Belanja &amp; Restock</span>
                    </a>
                </li>

                <li class="nav-item <?= ($current_page == 'transfer.php') ? 'active' : ''; ?>">
                    <a href="<?php echo BASE_URL; ?>/pages/stock/transfer.php" class="nav-link" data-tooltip="Transfer Stok">
                        <span class="sidebar-icon"><i class="fas fa-exchange-alt"></i></span>
                        <span class="sidebar-text">Transfer Stok</span>
                    </a>
                </li>

                <li class="nav-item <?= ($current_page == 'stock.php') ? 'active' : ''; ?>">
                    <a href="<?php echo BASE_URL; ?>/pages/stock/stock.php" class="nav-link" data-tooltip="Detail Stok">
                        <span class="sidebar-icon"><i class="fas fa-warehouse"></i></span>
                        <span class="sidebar-text">Detail Stok</span>
                    </a>
                </li>

                <!-- TENANT -->
                <li class="nav-title">TENANT</li>

                <li class="nav-item <?= ($current_page == 'tenant.php' || $current_page == 'tenant-detail.php') ? 'active' : ''; ?>">
                    <a href="<?php echo BASE_URL; ?>/pages/tenant/tenant.php" class="nav-link" data-tooltip="Tenant">
                        <span class="sidebar-icon"><i class="fas fa-store"></i></span>
                        <span class="sidebar-text">Tenant</span>
                    </a>
                </li>

                <!-- LAPORAN -->
                <li class="nav-title">LAPORAN</li>

                <li class="nav-item <?= ($current_page == 'report-sales.php') ? 'active' : ''; ?>">
                    <a href="<?php echo BASE_URL; ?>/pages/report/report-sales.php" class="nav-link" data-tooltip="Laporan Penjualan">
                        <span class="sidebar-icon"><i class="fas fa-chart-line"></i></span>
                        <span class="sidebar-text">Laporan Penjualan</span>
                    </a>
                </li>

                <li class="nav-item <?= ($current_page == 'report-tenant.php') ? 'active' : ''; ?>">
                    <a href="<?php echo BASE_URL; ?>/pages/report/report-tenant.php" class="nav-link" data-tooltip="Laporan Tenant">
                        <span class="sidebar-icon"><i class="fas fa-chart-line"></i></span>
                        <span class="sidebar-text">Laporan Tenant</span>
                    </a>
                </li>

                <!-- LAINNYA -->
                <li class="nav-title">LAINNYA</li>

                <li class="nav-item <?= ($current_page == 'update.php') ? 'active' : ''; ?>">
                    <a href="<?php echo BASE_URL; ?>/pages/other/update.php" class="nav-link" data-tooltip="Update">
                        <span class="sidebar-icon"><i class="fas fa-rocket"></i></span>
                        <span class="sidebar-text">Update</span>
                    </a>
                </li>

                <li class="nav-item <?= ($current_page == 'guide.php') ? 'active' : ''; ?>">
                    <a href="<?php echo BASE_URL; ?>/pages/other/guide.php" class="nav-link" data-tooltip="Guide">
                        <span class="sidebar-icon"><i class="fas fa-book"></i></span>
                        <span class="sidebar-text">Guide</span>
                    </a>
                </li>

                <li class="nav-item <?= ($current_page == 'database-schema.php') ? 'active' : ''; ?>">
                    <a href="<?php echo BASE_URL; ?>/pages/other/database-schema.php" class="nav-link" data-tooltip="Database Schema">
                        <span class="sidebar-icon"><i class="fas fa-database"></i></span>
                        <span class="sidebar-text">Database Schema</span>
                    </a>
                </li>
            <?php } ?>

            <?php if ($user['role'] == 'staff kasir') { ?>
                <!-- MASTER DATA -->
                <li class="nav-title">MASTER</li>

                <li class="nav-item <?= ($current_page == 'master-customer.php') ? 'active' : ''; ?>">
                    <a href="<?php echo BASE_URL; ?>/pages/master/master-customer.php" class="nav-link" data-tooltip="Master Customer">
                        <span class="sidebar-icon"><i class="fas fa-users"></i></span>
                        <span class="sidebar-text">Master Customer</span>
                    </a>
                </li>

                <!-- GUDANG KANTIN -->
                <li class="nav-title">KANTIN</li>

                <li class="nav-item <?= ($current_page == 'sales-stock.php') ? 'active' : ''; ?>">
                    <a href="<?php echo BASE_URL; ?>/pages/sales/sales-stock.php" class="nav-link" data-tooltip="Stok Kantin">
                        <span class="sidebar-icon"><i class="fas fa-store"></i></span>
                        <span class="sidebar-text">Stok Kantin</span>
                    </a>
                </li>

                <li class="nav-item <?= ($current_page == 'catalog.php') ? 'active' : ''; ?>">
                    <a href="<?php echo BASE_URL; ?>/pages/sales/catalog.php" class="nav-link" data-tooltip="Katalog Produk">
                        <span class="sidebar-icon"><i class="fas fa-book-open"></i></span>
                        <span class="sidebar-text">Katalog Produk</span>
                    </a>
                </li>

                <li class="nav-item <?= ($current_page == 'order.php') ? 'active' : ''; ?>">
                    <a href="<?php echo BASE_URL; ?>/pages/sales/order.php" class="nav-link" data-tooltip="Pesanan">
                        <span class="sidebar-icon"><i class="fas fa-receipt"></i></span>
                        <span class="sidebar-text">Pesanan</span>
                    </a>
                </li>

                <!-- TENANT -->
                <li class="nav-title">TENANT</li>

                <li class="nav-item <?= ($current_page == 'tenant.php' || $current_page == 'tenant-detail.php') ? 'active' : ''; ?>">
                    <a href="<?php echo BASE_URL; ?>/pages/tenant/tenant.php" class="nav-link" data-tooltip="Daftar Tenant">
                        <span class="sidebar-icon"><i class="fas fa-store"></i></span>
                        <span class="sidebar-text">Daftar Tenant</span>
                    </a>
                </li>

                <!-- REKAP -->
                <li class="nav-title">REKAP</li>

                <li class="nav-item <?= ($current_page == 'recap.php') ? 'active' : ''; ?>">
                    <a href="<?php echo BASE_URL; ?>/pages/recap/recap.php" class="nav-link" data-tooltip="Penjualan & Tenant">
                        <span class="sidebar-icon"><i class="fas fa-chart-bar"></i></span>
                        <span class="sidebar-text">Penjualan & Tenant</span>
                    </a>
                </li>

                <!-- LAINNYA -->
                <li class="nav-title">LAINNYA</li>

                <li class="nav-item <?= ($current_page == 'update.php') ? 'active' : ''; ?>">
                    <a href="<?php echo BASE_URL; ?>/pages/other/update.php" class="nav-link" data-tooltip="Update">
                        <span class="sidebar-icon"><i class="fas fa-rocket"></i></span>
                        <span class="sidebar-text">Update</span>
                    </a>
                </li>

                <li class="nav-item <?= ($current_page == 'guide.php') ? 'active' : ''; ?>">
                    <a href="<?php echo BASE_URL; ?>/pages/other/guide.php" class="nav-link" data-tooltip="Guide">
                        <span class="sidebar-icon"><i class="fas fa-book"></i></span>
                        <span class="sidebar-text">Guide</span>
                    </a>
                </li>

                <li class="nav-item <?= ($current_page == 'database-schema.php') ? 'active' : ''; ?>">
                    <a href="<?php echo BASE_URL; ?>/pages/other/database-schema.php" class="nav-link" data-tooltip="Database Schema">
                        <span class="sidebar-icon"><i class="fas fa-database"></i></span>
                        <span class="sidebar-text">Database Schema</span>
                    </a>
                </li>
            <?php } ?>
        
        </ul>
    </div>

    <!-- Footer -->
    <div class="sidebar-footer">
        <div class="version-badge">
            <i class="fas fa-code-branch version-icon"></i>
            <div class="version-text">
                <span>Qieos</span>
                <small><?php echo htmlspecialchars($latest_version); ?></small>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('sidebarMenu');
        const overlay = document.getElementById('sidebarOverlay');
        const desktopToggle = document.getElementById('sidebarToggle');
        const mobileToggle = document.getElementById('mobileSidebarToggle');
        const tooltip = document.getElementById('sidebarTooltip');
        const menuContainer = document.getElementById('sidebarMenuContainer');

        // Restore collapsed state on desktop from localStorage
        if (window.innerWidth >= 992) {
            const isCollapsed = localStorage.getItem('sidebar_collapsed') === 'true';
            if (isCollapsed) {
                sidebar.classList.add('collapsed');
            }
        }

        // Staggered entrance animation (first load only, once per session)
        if (menuContainer && sessionStorage.getItem('sidebar_animate_in') !== '1') {
            menuContainer.querySelectorAll(':scope > ul > li').forEach(function(item, index) {
                item.style.setProperty('--i', index);
            });
            sidebar.classList.add('animate-in');
            sessionStorage.setItem('sidebar_animate_in', '1');
        }

        // Auto Scroll to Active Menu Item
        const activeItem = sidebar.querySelector('.nav-item.active');
        if (activeItem && menuContainer) {
            setTimeout(function() {
                activeItem.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, 100);
        }

        // Desktop Toggle Action
        if (desktopToggle) {
            desktopToggle.addEventListener('click', function() {
                const content = document.querySelector('.content');
                if (content) content.classList.add('sidebar-transition');
                sidebar.classList.toggle('collapsed');
                localStorage.setItem('sidebar_collapsed', sidebar.classList.contains('collapsed'));
                if (tooltip) tooltip.classList.remove('show');
                setTimeout(function() {
                    if (content) content.classList.remove('sidebar-transition');
                }, 320);
            });
        }

        // Mobile Toggle Action
        if (mobileToggle) {
            mobileToggle.addEventListener('click', function() {
                sidebar.classList.toggle('show');
                overlay.classList.toggle('show');
            });
        }

        // Mobile Overlay Click Close
        if (overlay) {
            overlay.addEventListener('click', function() {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
            });
        }

        // Floating Tooltip for Collapsed Sidebar
        const navLinks = sidebar.querySelectorAll('.nav-link');
        navLinks.forEach(function(link) {
            link.addEventListener('mouseenter', function() {
                if (sidebar.classList.contains('collapsed') && window.innerWidth >= 992) {
                    const text = this.getAttribute('data-tooltip') || this.querySelector('.sidebar-text')?.textContent;
                    if (text && tooltip) {
                        const rect = this.getBoundingClientRect();
                        tooltip.textContent = text.trim();
                        tooltip.style.top = (rect.top + rect.height / 2) + 'px';
                        tooltip.classList.add('show');
                    }
                }
            });

            link.addEventListener('mouseleave', function() {
                if (tooltip) tooltip.classList.remove('show');
            });
        });

        if (menuContainer) {
            menuContainer.addEventListener('scroll', function() {
                if (tooltip) tooltip.classList.remove('show');
            });
        }

        // Scroll Parallax Background (GPU-only, single CSS var per frame)
        if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            let targetY = 0, currentY = 0, rafId = null;

            // Konten ter-scroll di dalam .content (bukan window) — ambil nilai terbesar
            const scrollEl = document.querySelector('.content');

            function getScrollTop() {
                const c = scrollEl ? scrollEl.scrollTop || 0 : 0;
                const w = window.pageYOffset || document.documentElement.scrollTop || 0;
                return Math.max(c, w);
            }

            function parallaxFrame() {
                currentY += (targetY - currentY) * 0.08;
                sidebar.style.setProperty('--py', Math.round(currentY * 10) / 10 + 'px');
                rafId = null;
                if (Math.abs(targetY - currentY) > 0.5) {
                    rafId = requestAnimationFrame(parallaxFrame);
                }
            }

            function parallaxScroll() {
                // Sidebar tersembunyi di mobile → tidak perlu hitung parallax
                if (window.innerWidth < 992 && !sidebar.classList.contains('show')) {
                    currentY = 0;
                    return;
                }
                targetY = getScrollTop();
                if (rafId === null) {
                    rafId = requestAnimationFrame(parallaxFrame);
                }
            }

            document.addEventListener('scroll', parallaxScroll, { passive: true, capture: true });
            window.addEventListener('scroll', parallaxScroll, { passive: true });
            window.addEventListener('resize', parallaxScroll);
            parallaxScroll();
        }
    });
</script>
