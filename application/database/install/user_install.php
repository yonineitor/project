<?php
$dbSetup['user'] = Array();
//prevent if exist
$dbSetup['user']['searchBy']  = Array('username');
//not update this columns
$dbSetup['user']['notUpdate'] = Array('username','created_at');
//insert data
$dbSetup['user']['insert'][] = Array(
	'username'     => 'jonathanq',
	'email'         => 'yonice.perez@gmail.com',
	'name'          => 'Jonathan',
	'last_name'     => 'Q',
	'date_of_birth' => '11/13/1987',
	'roles'         => '',
	'status'        => 1,//enable
	'password'      => password_hash( '123456' , PASSWORD_BCRYPT ), //set password
	'created_at'    => date('Y-m-d H:i:s'),
	'updated_at'    => date('Y-m-d H:i:s'),
	'is_admin'		=> 1,//full controll
);
return $dbSetup;