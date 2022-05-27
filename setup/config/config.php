<?php
		
	/**
	 * Initiate and load dotenv
	 */
	$root_dir   = dirname( __DIR__ );
	$web_dir    = $root_dir . '/html';
	
	$dotenv = new Dotenv\Dotenv( $root_dir );
	if( file_exists( $root_dir . '/.env' ) ){
	    $dotenv->load();
	    $dotenv->required( [ 'DB_NAME', 'DB_USER', 'DB_PASSWORD', 'WP_HOME', 'WP_SITEURL' ] );
	}
	
	/**
	 * Set environment type
	 */
	define( 'WP_ENV', getenv( 'WP_ENV' ) ?: 'production' );

	/**
	 * Error handling on development environment
	 */
	if( WP_ENV == 'development' ) {
		define( 'WP_DEBUG_DISPLAY', true );
		define( 'WP_DEBUG_LOG', true );
		define( 'WP_DEBUG', true );
	} else {
		define( 'WP_DEBUG_DISPLAY', false );
		define( 'WP_DEBUG_LOG', false );
		define( 'WP_DEBUG', false );
	}

	/**
	 * Set ABSPATH to correct directory
	 */
	if (!defined('ABSPATH')) {
	    define('ABSPATH', $web_dir . '/wp/');
	}
	
	/**
	 * Set correct paths
	 */
	define( 'WP_CONTENT_DIR',   $web_dir . '/content' );
	define( 'WP_CONTENT_URL',   getenv( 'WP_HOME' ) . '/content' );
	define( 'WP_HOME',          getenv( 'WP_HOME' ) );
	define( 'WP_SITEURL',       getenv( 'WP_SITEURL' ) );
	
	/**
	 * Database settings
	 */
	define('DB_NAME',       getenv('DB_NAME'));
	define('DB_USER',       getenv('DB_USER'));
	define('DB_PASSWORD',   getenv('DB_PASSWORD'));
	define('DB_HOST',       getenv('DB_HOST') ?: 'localhost');
	define('DB_CHARSET',    'utf8mb4');
	define('DB_COLLATE',    '');
	$table_prefix  =        getenv('DB_PREFIX') ?: 'vmst_';
	
	/**
	 * Auth keys and Salts
	 */
	define('AUTH_KEY',          getenv('AUTH_KEY'));
	define('SECURE_AUTH_KEY',   getenv('SECURE_AUTH_KEY'));
	define('LOGGED_IN_KEY',     getenv('LOGGED_IN_KEY'));
	define('NONCE_KEY',         getenv('NONCE_KEY'));
	define('AUTH_SALT',         getenv('AUTH_SALT'));
	define('SECURE_AUTH_SALT',  getenv('SECURE_AUTH_SALT'));
	define('LOGGED_IN_SALT',    getenv('LOGGED_IN_SALT'));
	define('NONCE_SALT',        getenv('NONCE_SALT'));

	/**
	 * Set transfer method for plugins and updates
	 */
	if(!defined('FS_METHOD')) {
		define('FS_METHOD', 'direct');
	}

    /**
	 * Define licenses located on premise
	 */
    define( 'GF_LICENSE_KEY', getenv( 'LICENSE_GRAVITYFORMS' ) ?: '' );
    define( 'SHORTPIXEL_API_KEY', getenv( 'LICENSE_SHORTPIXEL' ) ?: '' );
    define( 'WPCOM_API_KEY', getenv( 'LICENSE_AKISMET' ) ?: '' );
    define( 'ACF_PRO_LICENSE', getenv( 'LICENSE_ACF' ) ?: '' );
    define( 'WP_ROCKET_KEY', getenv( 'LICENSE_WPROCKET' ) ?: '' );
    define( 'WP_ROCKET_EMAIL', getenv( 'LICENSE_WPROCKET_EMAIL' ) ?: '' );
    define( 'FACETWP_LICENSE_KEY', getenv( 'LICENSE_FACETWP' ) ?: '' );
