<?php
/*
 * WPGear. New Users Monitor
 * uninstall.php
*/

	// if uninstall.php is not called by WordPress, die
	if (!defined('WP_UNINSTALL_PLUGIN')) {
		die;
	}
	
	// Удаляем настройки Плагина
	delete_option('num_dashboard_newusers');
	delete_option('num_scan_newusers');
	delete_option('num_first_run');
	delete_option('num_option_adminonly');
	delete_option('num_disable_login');	
	
	// Удаляем метаполя Плагина у всех Пользователей
	global $wpdb;
	$num_usermeta_table = $wpdb->prefix .'usermeta';
	$meta_key = 'num_confirm';
	$Query = "DELETE FROM $num_usermeta_table WHERE meta_key = '$meta_key'";
	
	$wpdb->query($Query);