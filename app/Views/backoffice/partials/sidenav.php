<!-- Sidenav Menu Start -->
<div class="sidenav-menu">

    <!-- Brand Logo -->
    <a href="livraisons.php" class="logo">
        <span class="logo-light">
            <span class="logo-lg"><img src="/PROJET_WEB_NEXTGEN-main/public/assets/images/logo.png" alt="logo" style="height: 26px;"></span>
            <span class="logo-sm"><img src="/PROJET_WEB_NEXTGEN-main/public/assets/images/logo-sm.png" alt="small logo" style="height: 28px;"></span>
        </span>
    </a>

    <!-- Sidebar Hover Menu Toggle Button -->
    <button class="button-sm-hover">
        <i class="ri-circle-line align-middle"></i>
    </button>

    <!-- Full Sidebar Menu Close Button -->
    <button class="button-close-fullsidebar">
        <i class="ri-close-line align-middle"></i>
    </button>

    <div data-simplebar>

        <!-- User -->
        <div class="sidenav-user">
            <div class="dropdown-center">
                <a class="topbar-link dropdown-toggle text-reset drop-arrow-none px-2 d-flex align-items-center justify-content-center" data-bs-toggle="dropdown" data-bs-offset="0,19" type="button" aria-haspopup="false" aria-expanded="false">
                    <img src="/PROJET_WEB_NEXTGEN-main/public/assets/images/users/admin-profile.png" width="42" class="rounded-circle me-2 d-flex" alt="user-image">
                    <span class="d-flex flex-column gap-1 sidebar-user-name">
                        <h4 class="my-0 fw-bold fs-15">Admin</h4>
                        <h6 class="my-0">Administrateur</h6>
                    </span>
                    <i class="ri-arrow-down-s-line d-block sidebar-user-arrow align-middle ms-2"></i>
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

        <!--- Sidenav Menu -->
        <ul class="side-nav">
            <li class="side-nav-title">Navigation</li>

            <li class="side-nav-item">
                <a href="livraisons.php" class="side-nav-link active">
                    <span class="menu-icon"><i class="ri-truck-line"></i></span>
                    <span class="menu-text">Livraisons</span>
                    <?php if (isset($totalLivraisons) && $totalLivraisons > 0): ?>
                        <span class="badge bg-primary rounded-pill"><?= $totalLivraisons ?></span>
                    <?php endif; ?>
                </a>
            </li>


        </ul>

        <div class="clearfix"></div>
    </div>
</div>
<!-- Sidenav Menu End -->
