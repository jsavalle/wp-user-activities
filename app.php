<?php
/**
 * @package Logs_login
 * @version 0.1
 */
/*
Plugin Name: Logs Login
Plugin URI: NA
Description: Log users login in a persistant forms for reporting
Author: Johann Savalle
Version: 0.1
Author URI: https://jsavalle.com
Text Domain: logs-login
*/


if ( !defined('ABSPATH') ) exit;

function BB_Logs_login_activate() {

    // Activation code here...
		global $wpdb;

		$req = "CREATE TABLE bbloginlogs (
			id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
			firstname VARCHAR(30),
			lastname VARCHAR(30),
			username VARCHAR(30),
			email VARCHAR(100),
			event VARCHAR(30),
			site VARCHAR(30),
			loggedon timestamp
		)";

		$wpdb->query($req);

}
register_activation_hook( __FILE__, 'BB_Logs_login_activate' );

function BB_Logs_event_record( $event_name ) {

    // Activation code here...
		global $wpdb;

		if (is_user_logged_in()){
			error_log('JSLOG - Getting current user');
			$current_user = wp_get_current_user();
			$log_login = $current_user->user_login;
			$log_email = $current_user->user_email;
			$log_fname = $current_user->user_firstname;
			$log_lname = $current_user->user_lastname;
			$log_event = $event_name;
			$log_site = get_bloginfo( 'url' );

			$req = "INSERT INTO bbloginlogs (
					firstname,
					lastname,
					username,
					email,
					event,
					site,
					loggedon
				)
				values (
					'$log_fname',
					'$log_lname',
					'$log_login',
					'$log_email',
					'$log_event',
					'$log_site',
					now()
				);";

			$wpdb->query($req);
		}
		else {
			error_log('JSLOG - user was not loggedin');

		}
}

function BB_Logs_login_record (){
	BB_Logs_event_record( "init" );
}

add_action('init', 'BB_Logs_login_record');

function BB_Logs_test_record (){
	BB_Logs_event_record( "test" );
}

add_action('set_current_user', 'BB_Logs_test_record');

function BB_Logs_logout_record (){
	BB_Logs_event_record( "logout" );
}

add_action('wp_logout', 'BB_Logs_logout_record');
