<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container">
        <a class="navbar-brand" href="./"><?php echo htmlspecialchars((string)$loginsystem->getMainData('site_title'), ENT_QUOTES, 'UTF-8'); ?></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#bs-navbar" aria-controls="bs-navbar" aria-expanded="false" aria-label="Navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="bs-navbar">
            <ul class="navbar-nav ms-auto">
                <?php echo $menu->getMenu(); ?>
            </ul>
            <button class="btn btn-link nav-link text-white ms-2 px-2" id="theme-toggle" type="button" title="Dark Mode umschalten" aria-label="Dark Mode umschalten">
                <i class="fa fa-moon" id="theme-icon"></i>
            </button>
        </div>
    </div>
</nav>
