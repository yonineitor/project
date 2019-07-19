
<?php
    $menuActive = isset($menuActive) ? $menuActive : ''; 

?>
<!-- Sidebar Menu-->
<ul class="sidebar navbar-nav">
    <li class="nav-item active">
        <a class="nav-link" href="<?= site_url('/products')?>">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Productos</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="<?= site_url('/products/cart')?>" >
            <i class="fas fa-database"></i>&nbsp;
            <span>Comprar</span>
        </a>
    </li>
</ul>