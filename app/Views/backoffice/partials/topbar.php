<!-- Color Top line -->
<div class="color-line"></div>

<!-- Topbar Start -->
<header class="app-topbar">
    <div class="page-container topbar-menu">
        <div class="d-flex align-items-center gap-2">

            <!-- Brand Logo -->
            <a href="livraisons.php" class="logo">
                <span class="logo-light">
                    <span class="logo-lg"><img src="/PROJET_WEB_NEXTGEN-main/public/assets/images/logo.png" alt="logo" style="height: 26px;"></span>
                    <span class="logo-sm"><img src="/PROJET_WEB_NEXTGEN-main/public/assets/images/logo-sm.png" alt="small logo" style="height: 28px;"></span>
                </span>
            </a>

            <!-- Sidebar Menu Toggle Button -->
            <button class="sidenav-toggle-button px-2">
                <i class="ri-menu-5-line fs-24"></i>
            </button>

            <!-- Topbar Page Title -->
            <div class="topbar-item d-none d-md-flex">
                <?php if (isset($title)): ?>
                    <div>
                        <h4 class="page-title fs-18 fw-bold mb-0"><?= $title ?></h4>
                        <ol class="breadcrumb m-0 mt-1 py-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">NextGen</a></li>
                            <li class="breadcrumb-item active"><?= $subtitle ?? 'Admin' ?></li>
                        </ol>
                    </div>
                <?php endif; ?>

                <?php if (!isset($title)): ?>
                    <div>
                        <h4 class="page-title fs-18 fw-bold mb-0">Bienvenue Admin!</h4>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">

            <!-- PWA Install Button -->
            <div class="topbar-item d-none d-sm-flex">
                <button class="topbar-link" id="pwa-install-btn" onclick="installPWA()" type="button" style="display: none;" title="Installer l'application">
                    <i class="ri-download-line fs-22"></i>
                </button>
            </div>

            <!-- Light/Dark Mode Button -->
            <div class="topbar-item d-none d-sm-flex">
                <button class="topbar-link" id="light-dark-mode" type="button">
                    <i class="ri-moon-line fs-22"></i>
                </button>
            </div>

            <!-- User Dropdown -->
            <div class="topbar-item nav-user">
                <div class="dropdown">
                    <a class="topbar-link dropdown-toggle drop-arrow-none px-2" data-bs-toggle="dropdown"
                        data-bs-offset="0,25" type="button" aria-haspopup="false" aria-expanded="false">
                        <img src="/PROJET_WEB_NEXTGEN-main/public/assets/images/users/admin-profile.png" width="32" class="rounded-circle me-lg-2 d-flex"
                            alt="user-image">
                        <span class="d-lg-flex flex-column gap-1 d-none">
                            <h5 class="my-0">Admin</h5>
                        </span>
                        <i class="ri-arrow-down-s-line d-none d-lg-block align-middle ms-2"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <!-- item-->
                        <div class="dropdown-header noti-title">
                            <h6 class="text-overflow m-0">Bienvenue !</h6>
                        </div>

                        <!-- item-->
                        <a href="javascript:void(0);" class="dropdown-item">
                            <i class="ri-account-circle-line me-1 fs-16 align-middle"></i>
                            <span class="align-middle">Mon Compte</span>
                        </a>

                        <!-- item-->
                        <a href="javascript:void(0);" class="dropdown-item">
                            <i class="ri-settings-2-line me-1 fs-16 align-middle"></i>
                            <span class="align-middle">Paramètres</span>
                        </a>

                        <div class="dropdown-divider"></div>

                        <!-- item-->
                        <a href="javascript:void(0);" class="dropdown-item active fw-semibold text-danger">
                            <i class="ri-logout-box-line me-1 fs-16 align-middle"></i>
                            <span class="align-middle">Déconnexion</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
<!-- Topbar End -->
