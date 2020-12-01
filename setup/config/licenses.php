<?php

    $dotenv = new Dotenv\Dotenv( $root_dir );
	if( file_exists( $root_dir . '/.env' ) ){
	    $dotenv->load();
    }
    

    // ===============
	// Define licenses
	// ===============
    define( 'GF_LICENSE_KEY', getenv( 'LICENSE_GRAVITYFORMS' ) ?: '' );
    define( 'SHORTPIXEL_API_KEY', getenv( 'LICENSE_AKISMET' ) ?: '' );
    define( 'WPCOM_API_KEY', getenv( 'LICENSE_SHORTPIXEL' ) ?: '' );
    define( 'ACF_PRO_LICENSE', getenv( 'LICENSE_ACF' ) ?: '' );
    define( 'WP_ROCKET_KEY', getenv( 'LICENSE_WPROCKET' ) ?: '' );
    define( 'WP_ROCKET_EMAIL', getenv( 'LICENSE_WPROCKET_EMAIL' ) ?: '' );
