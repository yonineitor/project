<?php
    $menuActive = isset($menuActive) ? $menuActive : ''; 
?>
<nav class="navbar navbar-expand navbar-dark bg-dark static-top">
    <a class="navbar-brand mr-1 bg-light" style="padding:2px;border-radius: 4px;" href="<?= site_url('panel')?>"><img width="100px" src="<?= base_url('/assets/img/800px-TAXI-2.png') ?>"></a>
    
    <button class="btn btn-link btn-sm text-white order-1 order-sm-0" id="sidebarToggle" href="#"><i class="fas fa-bars"></i></button>
    
    <!-- Navbar Search -->
    <div class="d-none d-md-inline-block form-inline ml-auto mr-0 mr-md-3 my-2 my-md-0"></div>
    
    <ul class="navbar-nav ml-auto ml-md-0">
        <li class="nav-item dropdown no-arrow mx-1">
            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-bell fa-fw"></i>
                <span class="badge badge-danger" id="header-notifications-count"></span>
            </a>
            <!--
            <div id="header-notifications" class="dropdown-menu dropdown-menu-right" aria-labelledby="alertsDropdown">
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="<?= site_url('avisos')?>">Ver mis avisos</a>
            </div>
        -->
        </li>
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="fas fa-user-circle fa-fw"></i> <?= \Lib\Auth::user('nombre') ?> <i class="fas fa-caret-down"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                <a class="dropdown-item <?= ($menuActive==='profile')? 'active' : '';?>" href="<?= site_url('perfil/detail');?>">Editar mi perfil</a>
                <a class="dropdown-item" href="<?= site_url('/perfil/logout');?>" >Cerrar sesión</a>
            </div>
        </li>
    </ul>
</nav>
