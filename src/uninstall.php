<?php

// If uninstall not called from WordPress, then exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
  exit;
}

global $wpdb;

if ( is_multisite() ) {

  $blogs = $wpdb->get_results( "SELECT blog_id FROM {$wpdb->blogs}", ARRAY_A );
  if ( $blogs ) {
    foreach ( $blogs as $blog ) {
      switch_to_blog( $blog['blog_id'] );
      delete_option( 'language_redirect_default_redirect_location' );
      delete_option( 'language_redirect_redirect_mapping' );
      restore_current_blog();
    }
  }
} else {
  delete_option( 'language_redirect_default_redirect_location' );
  delete_option( 'language_redirect_redirect_mapping' );
}
