<?php

// prevent direct access
if ( ! defined('WP_UNINSTALL_PLUGIN') ) {
    die;
}

delete_option( 'addsearch_settings' );
delete_option( 'addsearch_prexisting' );
delete_option( 'addsearch_currentversion' );