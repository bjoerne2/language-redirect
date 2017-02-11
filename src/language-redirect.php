<?php
/*
Plugin Name: Language Redirect
Plugin URI: http://www.bjoerne.com
Description: Redirects from the root site of a multisite project to a language specific network site.
Author: Björn Weinbrenner
Version: 1.0.4
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
	if ( ! defined( 'WP_USE_THEMES' ) ) {
		return;
	}
	if ( language_redirect_is_robots_txt() ) {
		return;
	}
	if ( array_key_exists( 'HTTP_ACCEPT_LANGUAGE', $_SERVER ) ) {
		// Thanks to http://www.thefutureoftheweb.com/blog/use-accept-language-header for the following lines of code
		preg_match_all('/([a-z]{1,8}(-[a-z]{1,8})?)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $header_matchings);
		if (count($header_matchings[1])) {
			// create a list like "en" => 0.8
			$language_to_priority_map = array_combine( $header_matchings[1], $header_matchings[4] );
			// set default to 1 for any without q factor
			foreach ($language_to_priority_map as $lang => $val) {
				if ( $val === '' ) $language_to_priority_map[$lang] = 1;
			}
			// sort list based on value
			arsort( $language_to_priority_map, SORT_NUMERIC );
		}
		$redirect_location = language_redirect_get_redirect_location( array_keys( $language_to_priority_map ) );
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

function language_redirect_is_robots_txt() {
	return false; // TODO
}

function language_redirect_get_redirect_location( $languages ) {
	foreach ( $languages as $language ) {
		$redirect_location = language_redirect_get_redirect_location_from_mapping( $language );
		if ( null != $redirect_location ) {
			return $redirect_location;
		}
	}
	foreach ( $languages as $language ) {
		$language_prefix = substr( $language, 0, 2 );
		$redirect_location = language_redirect_get_redirect_location_from_mapping( $language_prefix );
		if ( null != $redirect_location ) {
			return $redirect_location;
		}
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
		if ( 0 === strcasecmp( $mapping_language, $language ) ) {
			return substr( $line, $pos_of_equals + 1 );
		}
	}
	return null;
}

function language_redirect_get_default_redirect_location() {
	return get_option( 'language_redirect_default_redirect_location' );
}
