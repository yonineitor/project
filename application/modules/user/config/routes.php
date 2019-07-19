<?php


$route['user/profile']['get'] = 'profile/form'; 

$route['user']['get']                        = 'user/search';
$route['user/search']['get']                 = 'user/search';
$route['user/create']['get']                 = 'user/create';
$route['user/(:num)']['get']                 = 'user/detail/$1';
$route['user/insert']['post']                = 'user_Post/insert';
$route['user/(:num)/disabled']['post']       = 'user_Post/disabled/$1';
$route['user/(:num)/enabled']['post']        = 'user_Post/enabled/$1';
$route['user/(:num)/updateRoles']['post']    = 'user_Post/updateRoles/$1';
$route['user/(:num)/updateBasic']['post']    = 'user_Post/updateBasic/$1';
$route['user/(:num)/changePassword']['post'] = 'user_Post/changePassword/$1';
$route['user/(:num)/delete']['post']         = 'user_Post/delete/$1';

$route['(:any)'] = 'none';