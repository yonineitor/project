
<?php
    $menuActive = isset($menuActive) ? $menuActive : ''; 
?>
<ul class="sidebar navbar-nav toggled">
    <li class="nav-item  <?= ($menuActive==='panel')? 'active' : '';?>">
        <a class="nav-link " href="<?= site_url('/panel');?>">
            <i class="fas fa-tachometer-alt"></i>
            <span>Panel</span>
        </a>
    </li>
    <li class="nav-item  <?= ($menuActive==='auto')? 'active' : '';?>">
        <a class="nav-link " href="<?= site_url('/auto');?>">
            <i class="fas fa-car"></i>
            <span>Autos</span>
        </a>
    </li>
    <li class="nav-item  <?= ($menuActive==='usuario')? 'active' : '';?>">
        <a class="nav-link " href="<?= site_url('/user');?>">
            <i class="fas fa-user"></i>
            <span>Usuarios</span>
        </a>
    </li>
    <li class="nav-item  <?= ($menuActive==='turno')? 'active' : '';?>" >
        <a class="nav-link " href="<?= site_url('/turno');?>">
            <i class="fas fa-globe"></i>
            <span>Turnos</span>
        </a>
    </li>
    <li class="nav-item  <?= ($menuActive==='horario')? 'active' : '';?>" >
        <a class="nav-link " href="<?= site_url('/horario');?>">
            <i class="far fa-clock"></i>
            <span>Horarios</span>
        </a>
    </li>
</ul>