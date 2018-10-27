<?php
/**
 * @package Logs_login
 * @version 0.1
 */
/*
Plugin Name: Plop Login
Plugin URI: NA
Description: Log users login in a persistant forms for reporting
Author: Johann Savalle
Version: 0.1
Author URI: https://jsavalle.com
Text Domain: logs-login
*/


if ( !defined('ABSPATH') ) exit;

function WUA_Logs_login_activate() {

    // Activation code here...
		global $wpdb;
		global $wp;

		$req = "CREATE TABLE wua_loginlogs (
			id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
			firstname VARCHAR(30),
			lastname VARCHAR(30),
			username VARCHAR(30),
			email VARCHAR(100),
			event VARCHAR(30),
			site VARCHAR(100),
			page VARCHAR(150),
			loggedon timestamp
		)";

		$wpdb->query($req);

}
register_activation_hook( __FILE__, 'WUA_Logs_login_activate' );

function WUA_Logs_event_record( $event_name ) {

    // Activation code here...
		global $wpdb;
		global $wp;

		if (is_user_logged_in()){
			error_log('WUALOG - Getting current user');

			$current_slug = add_query_arg( array(), $wp->request );

			$current_user = wp_get_current_user();
			$log_login = $current_user->user_login;
			$log_email = $current_user->user_email;
			$log_fname = $current_user->user_firstname;
			$log_lname = $current_user->user_lastname;
			$log_event = $event_name;
			$log_page = $current_slug;
			$log_site = get_bloginfo( 'url' );

			$req = "INSERT INTO wua_loginlogs (
					firstname,
					lastname,
					username,
					email,
					event,
					site,
					page,
					loggedon
				)
				values (
					'$log_fname',
					'$log_lname',
					'$log_login',
					'$log_email',
					'$log_event',
					'$log_site',
					'$log_page',
					now()
				);";

			$wpdb->query($req);
		}
		else {
			error_log('WUALOG - user was not loggedin');

		}
}

function WUA_Logs_login_record (){
	WUA_Logs_event_record( "init" );
}

add_action('init', 'WUA_Logs_login_record');

function WUA_Logs_test_record (){
	WUA_Logs_event_record( "test" );
}

add_action('set_current_user', 'WUA_Logs_test_record');

function WUA_Logs_logout_record (){
	WUA_Logs_event_record( "logout" );
}

add_action('wp_logout', 'WUA_Logs_logout_record');
