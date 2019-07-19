<?php

$route['roles']['get']                = 'roles/setting';
$route['roles/form']['get']           = 'roles/form';
$route['roles/(:num)/delete']['post'] = 'roles/delete/$1'; 
$route['roles/submit']['post']        = 'roles/submit';