<!-- Custom Navbar with Unique Design -->
<nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme custom-navbar"
    id="layout-navbar">

    <!-- Mobile Menu Toggle -->
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
        <a class="nav-item nav-link px-0 me-xl-4 mobile-menu-btn" href="javascript:void(0)">
            <i class="bx bx-menu bx-sm"></i>
        </a>
    </div>

    <!-- Main Navbar Content -->
    <div class="navbar-nav-right d-flex align-items-center justify-content-between w-100" id="navbar-collapse">

        <!-- Title Section with Custom Styling -->
        <div class="navbar-title-section">
            <h3 class="navbar-title mb-0" style="background: linear-gradient(135deg, #FFA500 0%, #FF6B35 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; font-weight: 700; text-shadow: 0 2px 4px rgba(255, 165, 0, 0.3);">
                <i class="bx bx-home-alt me-2" style="color: #FFA500;"></i>
                <span class="d-none d-md-inline"><?= $title ?></span>
                <span class="d-md-none title-short"><?= substr($title, 0, 15) ?>...</span>
            </h3>
        </div>

        <!-- Right Side Navigation -->
        <ul class="navbar-nav flex-row align-items-center">



            <!-- User Profile Dropdown -->
            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a class="nav-link dropdown-toggle hide-arrow d-flex align-items-center user-profile-btn"
                    href="javascript:void(0);" data-bs-toggle="dropdown">
                    <div class="user-info-container d-flex align-items-center">
                        <!-- Avatar with Online Indicator -->
                        <div class="avatar avatar-online me-2 position-relative">
                            <img src="<?= base_url('assets/img/') ?><?= session()->get('jenis_kelamin') == 'P' ? 'ustadzah.webp' : 'ustad.webp' ?>"
                                alt="User Avatar" class="w-px-40 h-auto rounded-circle avatar-shadow" />
                            <span class="avatar-status-indicator"></span>
                        </div>

                        <!-- User Details (Hidden on small screens) -->
                        <div class="d-none d-lg-flex flex-column user-details">
                            <span class="user-name"><?= session()->get('nama') ?></span>
                            <small class="text-muted user-role"><?= session()->get('jabatan') ?></small>
                        </div>

                        <!-- Dropdown Arrow -->
                        <i class="bx bx-chevron-down ms-2 d-none d-sm-inline"></i>
                    </div>
                </a>

                <!-- Enhanced Dropdown Menu -->
                <ul class="dropdown-menu dropdown-menu-end user-dropdown-menu">
                    <!-- User Info Header -->
                    <li class="dropdown-item-header">
                        <div class="d-flex">
                            <div class="flex-shrink-0 me-3">
                                <div class="avatar avatar-online">
                                    <img src="<?= base_url('assets/img/') ?><?= session()->get('jenis_kelamin') == 'P' ? 'ustadzah.webp' : 'ustad.webp' ?>"
                                        alt="User Avatar" class="w-px-40 h-auto rounded-circle" />
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <span class="fw-semibold d-block user-name-dropdown"><?= session()->get('nama') ?></span>
                                <small class="text-muted">Super Admin</small>
                                <div class="user-status mt-1">
                                    <span class="badge bg-success">Online</span>
                                </div>
                            </div>
                        </div>
                    </li>

                    <li>
                        <div class="dropdown-divider"></div>
                    </li>

                    <!-- Menu Items with Icons -->
                    <li>
                        <a class="dropdown-item dropdown-item-custom" href="#">
                            <i class="bx bx-user me-2 text-primary"></i>
                            <span class="align-middle">Profil Saya</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item dropdown-item-custom" href="#">
                            <i class="bx bx-cog me-2 text-secondary"></i>
                            <span class="align-middle">Pengaturan</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item dropdown-item-custom" href="#">
                            <span class="d-flex align-items-center align-middle">
                                <i class="flex-shrink-0 bx bx-chart me-2 text-info"></i>
                                <span class="flex-grow-1 align-middle">Aktivitas</span>
                                <span class="flex-shrink-0 badge badge-center rounded-pill bg-danger w-px-20 h-px-20">0</span>
                            </span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item dropdown-item-custom" href="#">
                            <i class="bx bx-help-circle me-2 text-warning"></i>
                            <span class="align-middle">Bantuan</span>
                        </a>
                    </li>

                    <li>
                        <div class="dropdown-divider"></div>
                    </li>

                    <!-- Logout Button -->
                    <li>
                        <a class="dropdown-item dropdown-item-logout" href="<?= base_url('logout') ?>">
                            <i class="bx bx-power-off me-2 text-danger"></i>
                            <span class="align-middle">Keluar</span>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</nav>

<!-- Custom CSS for Enhanced Styling -->
<style>
    /* Custom Navbar Styling */
    .custom-navbar {
        backdrop-filter: blur(10px);
        border-bottom: 1px solid rgba(255, 165, 0, 0.2);
        box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    /* Mobile Menu Button */
    .mobile-menu-btn {
        background: linear-gradient(135deg, #FFA500, #FF6B35);
        border-radius: 8px;
        color: white !important;
        transition: all 0.3s ease;
    }

    .mobile-menu-btn:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 15px rgba(255, 165, 0, 0.3);
    }

    /* Title Styling */
    .navbar-title-section {
        flex-grow: 1;
    }

    .navbar-title {
        position: relative;
        font-size: 1.5rem;
    }

    @media (max-width: 768px) {
        .navbar-title {
            font-size: 1.2rem;
        }

        .title-short {
            display: inline !important;
        }
    }

    /* Notification Bell */
    .notification-bell {
        background: rgba(255, 165, 0, 0.1);
        border-radius: 50%;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .notification-bell:hover {
        background: rgba(255, 165, 0, 0.2);
        transform: scale(1.1);
    }

    /* User Profile Button */
    .user-profile-btn {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 25px;
        padding: 8px 15px;
        transition: all 0.3s ease;
        border: 1px solid rgba(255, 165, 0, 0.2);
    }

    .user-profile-btn:hover {
        background: rgba(255, 165, 0, 0.1);
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    /* Avatar Styling */
    .avatar-shadow {
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        transition: all 0.3s ease;
    }

    .avatar-status-indicator {
        position: absolute;
        bottom: 0;
        right: 0;
        width: 12px;
        height: 12px;
        background: #28a745;
        border: 2px solid white;
        border-radius: 50%;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.2);
        }

        100% {
            transform: scale(1);
        }
    }

    /* User Details */
    .user-name {
        font-weight: 600;
        color: #333;
        font-size: 0.9rem;
    }

    .user-role {
        font-size: 0.75rem;
        opacity: 0.8;
    }

    /* Dropdown Menu Styling */
    .user-dropdown-menu {
        min-width: 280px;
        border: none;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        border-radius: 15px;
        padding: 0;
        overflow: hidden;
        margin-top: 10px;
    }

    .dropdown-item-header {
        background: linear-gradient(135deg, #FFA500, #FF6B35);
        color: white;
        padding: 20px;
        margin: 0;
    }

    .dropdown-item-header .user-name-dropdown {
        color: white;
        font-weight: 700;
    }

    .dropdown-item-custom {
        padding: 12px 20px;
        transition: all 0.3s ease;
        border-left: 3px solid transparent;
    }

    .dropdown-item-custom:hover {
        background: rgba(255, 165, 0, 0.1);
        border-left-color: #FFA500;
        transform: translateX(5px);
    }

    .dropdown-item-logout {
        padding: 12px 20px;
        transition: all 0.3s ease;
        border-left: 3px solid transparent;
        background: rgba(220, 53, 69, 0.05);
    }

    .dropdown-item-logout:hover {
        background: rgba(220, 53, 69, 0.1);
        border-left-color: #dc3545;
        transform: translateX(5px);
    }

    /* User Status Badge */
    .user-status .badge {
        font-size: 0.6rem;
        padding: 2px 8px;
    }

    /* Responsive Design */
    @media (max-width: 576px) {
        .user-info-container {
            padding: 5px;
        }

        .user-dropdown-menu {
            min-width: 250px;
            margin-right: 10px;
        }

        .dropdown-item-header {
            padding: 15px;
        }
    }

    @media (min-width: 1200px) {
        .custom-navbar {
            border-radius: 15px;
            margin: 10px auto;
            max-width: calc(100% - 40px);
        }
    }

    /* Dark mode support */
    @media (prefers-color-scheme: dark) {
        .user-name {
            color: #fff;
        }

        .custom-navbar {
            background: rgba(0, 0, 0, 0.8) !important;
            border-bottom-color: rgba(255, 165, 0, 0.3);
        }
    }
</style>