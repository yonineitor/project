
<?php
    $menuActive = isset($menuActive) ? $menuActive : ''; 
    $toggled = ($userSetting['sidebarToggle']==1) ? "toggled" : "";
?>
<!-- Sidebar Menu-->
<ul class="sidebar navbar-nav">
    <li class="nav-item active">
        <a class="nav-link" href="<?= site_url('/dashboard')?>">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span><?= _l('dashboard')?></span>
        </a>
    </li>
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="pagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-fw fa-folder"></i>
            <span>Fake link</span>
        </a>
        <div class="dropdown-menu" aria-labelledby="pagesDropdown">
            <h6 class="dropdown-header">ABCDEF</h6>
            <a class="dropdown-item" href="#">F1</a>
            <a class="dropdown-item" href="#">F2</a>
            <a class="dropdown-item" href="#">F3</a>
        </div>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#">
            <i class="fas fa-fw fa-table"></i>
            <span>Fake link 2</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="<?= site_url('/dashboard/code')?>" >
            <i class="fas fa-code"></i>
            <span>Code</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="<?= site_url('/migration')?>" target="_blank">
            <i class="fas fa-database"></i>&nbsp;
            <span>Database schema</span>
        </a>
    </li>
</ul>