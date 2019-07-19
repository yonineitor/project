<?php

$route['profile']['get']                 = 'profile/form';
$route['profile/update']['post']         = 'profile/update';
$route['profile/updatePassword']['post'] = 'profile/updatePassword';
$route['profile/logOut']          		 = 'profile/logOut';

$route['(:any)'] = 'none';