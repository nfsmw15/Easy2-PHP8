<header class="navbar sticky-top bg-dark flex-md-nowrap p-0 shadow">
    <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3 fs-6 text-white" href="./">
        <?php echo htmlspecialchars((string)$loginsystem->getMainData('site_title'), ENT_QUOTES, 'UTF-8'); ?>
    </a>
    <button class="navbar-toggler d-md-none collapsed text-white border-0 px-3" type="button"
            data-bs-toggle="collapse" data-bs-target="#sidebarMenu"
            aria-controls="sidebarMenu" aria-expanded="false" aria-label="Navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="navbar-nav ms-auto me-3">
        <span class="nav-item text-white small">
            <?php echo htmlspecialchars((string)$loginsystem->getData('username'), ENT_QUOTES, 'UTF-8'); ?>
        </span>
    </div>
</header>

<div class="container-fluid">
    <div class="row">
        <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse">
            <div class="position-sticky pt-3 sidebar-sticky">
                <ul class="nav flex-column">
                    <?php echo $menu->getMenu(1, 'dashboard'); ?>
                </ul>
            </div>
            <div class="sidebar-footer px-3 py-2">
                <small class="text-muted">Copyright &copy; <?php echo date('Y'); ?></small>
            </div>
        </nav>
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
