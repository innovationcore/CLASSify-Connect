<?php
/** @var UserSession $userSession */
/** @var string $page */
global $rootURL;
?>
<header class="col-12 col-md-1 bg-light sidebar sidebar-sticky">
    <ul class="flex-row flex-md-column navbar-nav justify-content-between sidebar-menu sidebar-sticky">
        <li class="nav-item">
            <a class="nav-link" href="/" data-toggle="tooltip" data-placement="bottom" title="Dashboard Overview">
                <i class="fas fa-desktop"></i>
                <span class="d-none d-md-inline">Dashboard</span>
            </a>
        </li>
        <li class="nav-item d-none d-md-inline">
            <span class="nav-link sidebar-heading pl-0 text-nowrap">Data</span>
        </li>
        <li class="nav-item">
            <a class="nav-link<?php if (!is_null($page) && $page == "home") { echo " active"; } ?>" href="<?= $rootURL ?>/" data-toggle="tooltip" data-placement="bottom" title="Dashboard Overview">
                <i class="fas fa-upload"></i>
                <span class="d-none d-md-inline">Upload<?php if (!is_null($page) && $page == "home") { echo' <span class="sr-only">(current)</span>'; } ?></span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link<?php if (!is_null($page) && $page == "results") { echo " active"; } ?>" href="<?= $rootURL ?>/result" data-toggle="tooltip" data-placement="bottom" title="View results">
                <i class="fas fa-file-alt"></i>
                <span class="d-none d-md-inline">Results<?php if (!is_null($page) && $page == "results") { echo' <span class="sr-only">(current)</span>'; } ?></span>
            </a>
        </li>
        <li class="nav-item d-none d-md-inline">
            <span class="nav-link sidebar-heading pl-0 text-nowrap">Resources</span>
        </li>
        <li class="nav-item">
            <a class="nav-link<?php if (!is_null($page) && $page == "user-guide") { echo " active"; } ?>" href="<?= $rootURL ?>/user-guide" data-toggle="tooltip" data-placement="bottom" title="View User Guide">
                <i class="fas fa-book"></i>
                <span class="d-none d-md-inline">User Guide<?php if (!is_null($page) && $page == "user-guide") { echo' <span class="sr-only">(current)</span>'; } ?></span>
            </a>
        </li>
    </ul>
</header>

