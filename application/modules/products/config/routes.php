<?php

$route['products']['get']           = 'products/search';
$route['products/cart']['get']      = 'products/cart';
$route['products/tryToPay']['post'] = 'products/tryToPay';

//default
$route['products/(:any)'] = "none";