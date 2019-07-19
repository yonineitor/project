<?php
$privileges = Array();

//Menu
$privileges['Settings']['priv_setting_user_view']   = 'View users';
$privileges['Settings']['priv_setting_user_create'] = 'Create users';
$privileges['Settings']['priv_setting_user_edit']   = 'Edit users';
$privileges['Settings']['priv_setting_user_delete'] = 'Delete users';

$privileges['Settings']['priv_setting_roles_manager']   = 'Roles manager';

//FAKE Permissions
$privileges['Products']['priv_product_search'] = 'Search';
$privileges['Products']['priv_product_view']   = 'View';
$privileges['Products']['priv_product_edit']   = 'Edit';
$privileges['Products']['priv_product_create'] = 'Create';
$privileges['Products']['priv_product_delete'] = 'Delete';


return $privileges;