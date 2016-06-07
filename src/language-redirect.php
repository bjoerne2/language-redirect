<?php
/*
Plugin Name: Language Redirect
Plugin URI: http://www.bjoerne.com
Description: Redirects from the root site of a multisite project to a language specific network site.
Author: Björn Weinbrenner
Version: 1.0.1
Author URI: http://www.bjoerne.com
*/

include_once dirname( __FILE__ ).'/options.php';

if ( is_admin() ) {
	add_action( 'admin_init', 'language_redirect_register_settings' );
	add_action( 'admin_menu', 'language_redirect_add_config_page' );
} else {
	add_action( 'plugins_loaded', 'language_redirect_plugins_loaded' );
}

function language_redirect_register_settings() {
	register_setting( 'language_redirect_group', 'language_redirect_default_redirect_location' );
	register_setting( 'language_redirect_group', 'language_redirect_redirect_mapping' );
}

function language_redirect_add_config_page() {
	add_options_page( __( 'Language Redirect' ), __( 'Language Redirect' ), 'manage_options', basename( __FILE__ ), 'language_redirect_show_settings_page' );
}

function language_redirect_plugins_loaded() {
	if ( language_redirect_is_login() ) {
		return;
	}
	if ( array_key_exists( 'HTTP_ACCEPT_LANGUAGE', $_SERVER ) ) {
		$language = explode( ',', $_SERVER['HTTP_ACCEPT_LANGUAGE'] );
		$language = strtolower( substr( chop( $language[0] ), 0, 2 ) );
		$redirect_location = language_redirect_get_redirect_location( $language );
	} else {
		$redirect_location = language_redirect_get_default_redirect_location();
	}
	if ( $redirect_location == null ) {
		return;
	}
	if ( $redirect_location[0] == '/' ) {
		header( 'Location: ' . site_url( $redirect_location ) );
	} else {
		header( 'Location: ' . $redirect_location );
	}
	exit;
}

function language_redirect_is_login() {
	$request_protocol = array_key_exists( 'HTTPS', $_SERVER ) && $_SERVER['HTTPS'] ? 'https' : 'http';
	$request_port     = $_SERVER['SERVER_PORT'] == '80' ? '' : ':'.$_SERVER['SERVER_PORT'];
	$request_url      = $request_protocol.'://'.$_SERVER['HTTP_HOST'].$request_port.$_SERVER['PHP_SELF'];
	if ( strpos( $request_url, site_url() ) !== 0 ) {
		return false;
	}
	$relative_url = substr( $request_url, strlen( site_url() ) );
	if ( $relative_url == '/wp-login.php' ) {
		return true;
	}
	return false;
}

function language_redirect_get_redirect_location( $language ) {
	$redirect_location = language_redirect_get_redirect_location_from_mapping( $language );
	if ( null != $redirect_location ) {
		return $redirect_location;
	}
	return language_redirect_get_default_redirect_location();
}

function language_redirect_get_redirect_location_from_mapping( $language ) {
	$mapping = get_option( 'language_redirect_redirect_mapping' );
	if ( null == $mapping ) {
		return null;
	}
	foreach ( preg_split( "/((\r?\n)|(\r\n?))/ ", $mapping ) as $line )	{
		$pos_of_equals = strpos( $line, '=' );
		if ( ! $pos_of_equals ) {
			continue;
		}
		$mapping_language = substr( $line, 0, $pos_of_equals );
		if ( $mapping_language == $language ) {
			return substr( $line, $pos_of_equals + 1 );
		}
	}
	return null;
}

function language_redirect_get_default_redirect_location() {
	return get_option( 'language_redirect_default_redirect_location' );
}