<?php
$configTemplate = [];
/**
 * VIEWPATH is home layouts
 */
$configTemplate['layout_template_path']	= __DIR__ . '/layouts/';

/**
 * RETURN DEFAULT VALUES IN JSON
 */
$configTemplate['return_data'] = [
	'status' => 0,
	'message' => ''
];

/**
 * Assets prevent cache
 */
$configTemplate['regenerate_assets'] = FALSE;

/**
 * Save `table` time(seconds) and memory(megabytes) information about render
 * 
 * +------------+---------------+------+-----+---------+----------------+
 * | Field      | Type          | Null | Key | Default | Extra          |
 * +------------+---------------+------+-----+---------+----------------+
 * | id         | bigint(20)    | NO   | PRI | NULL    | auto_increment |
 * | date       | datetime      | NO   |     | NULL    |                |
 * | controller | varchar(100)  | NO   |     |         |                |
 * | action     | varchar(100)  | NO   |     |         |                |
 * | time       | decimal(10,4) | NO   |     | 0.0000  |                |
 * | params     | varchar(250)  | NO   |     |         |                |
 * | memory_mb  | decimal(10,2) | NO   |     | 0.00    |                |
 * +------------+---------------+------+-----+---------+----------------+
 *
 */
$configTemplate['render_template'] = '';

/**
 * minify HTML
 */
$configTemplate['minify_output']  = FALSE;

/**
 * @example
 * $configTemplate[layouts][@name_template][js]
 * $configTemplate[layouts][@name_template][css]
 * 
 * Only for local url
 */
$configTemplate['layouts']['*'] 	= [
	'css' => [
		'/assets/fonts/fontawesome-5.9.0/css/all.min.css',
		'/assets/libs/bootstrap/bootstrap-4.3.1/css/bootstrap.min.css',
		'/assets/libs/toastr/toastr.css',
		'/assets/css/styles.css?refresh='.uniqid(),
	],
	'js' => [
		'/assets/libs/jquery/jquery-3.3.1.min.js',
		'/assets/libs/popper/popper.min.js',
		'/assets/libs/bootstrap/bootstrap-4.3.1/js/bootstrap.min.js',
		'/assets/libs/sweetalert/polyfill.min.js',
		'/assets/libs/toastr/toastr.js',
		'/assets/libs/moment/moment.min.js',
		'/assets/libs/moment/moment-with-locales.min.js',
		'/assets/libs/sweetalert/sweetalert.min.js',
		
		'/assets/js/scripts.js?refresh='.uniqid(),
	]
];

$configTemplate['layouts']['admin_layout'] = [
	'css' => [
		'/assets/libs/bootstrap/sb-admin/css/sb-admin.min.css',
		'/assets/libs/toastr/toastr.css',
	],
	'js' => [
		'/assets/libs/bootstrap/sb-admin/js/sb-admin.min.js',
		'/assets/libs/vuejs/vue.js',
		'/assets/libs/vuejs/vue-resource.js',
		'/assets/js/vue-config.js',
	]
];

$configTemplate['layouts']['demo_layout'] = $configTemplate['layouts']['admin_layout'];
/**
 * don't remove this line
 */
return $configTemplate;