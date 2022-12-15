<?php
		
	/**
	 * Initiate and load dotenv
	 */
	$root_dir   = dirname( __DIR__ );
	$web_dir    = $root_dir . '/html';
	
	$dotenv = Dotenv\Dotenv::createImmutable( $root_dir );
	if( file_exists( $root_dir . '/.env' ) ){
	    $dotenv->load();
	    $dotenv->required( [ 'DB_NAME', 'DB_USER', 'DB_PASSWORD', 'WP_HOME', 'WP_SITEURL' ] );
	}

	/**
	 * Set environment type
	 */
	define( 'WP_ENV', $_ENV['WP_ENV'] ?: 'production' );

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
	define( 'WP_CONTENT_URL',   $_ENV['WP_HOME'] . '/content' );
	define( 'WP_HOME',          $_ENV['WP_HOME'] );
	define( 'WP_SITEURL',       $_ENV['WP_SITEURL'] );

	/**
	 * Database settings
	 */
	define('DB_NAME',       $_ENV['DB_NAME']);
	define('DB_USER',       $_ENV['DB_USER']);
	define('DB_PASSWORD',   $_ENV['DB_PASSWORD']);
	define('DB_HOST',       $_ENV['DB_HOST'] ?: 'localhost');
	define('DB_CHARSET',    'utf8mb4');
	define('DB_COLLATE',    '');
	$table_prefix  =        $_ENV['DB_PREFIX'] ?: 'vmst_';
	
	/**
	 * Auth keys and Salts
	 */
	define('AUTH_KEY',          $_ENV['AUTH_KEY']);
	define('SECURE_AUTH_KEY',   $_ENV['SECURE_AUTH_KEY']);
	define('LOGGED_IN_KEY',     $_ENV['LOGGED_IN_KEY']);
	define('NONCE_KEY',         $_ENV['NONCE_KEY']);
	define('AUTH_SALT',         $_ENV['AUTH_SALT']);
	define('SECURE_AUTH_SALT',  $_ENV['SECURE_AUTH_SALT']);
	define('LOGGED_IN_SALT',    $_ENV['LOGGED_IN_SALT']);
	define('NONCE_SALT',        $_ENV['NONCE_SALT']);

	/**
	 * Set transfer method for plugins and updates
	 */
	if(!defined('FS_METHOD')) {
		define('FS_METHOD', 'direct');
	}

    /**
     * Disable default theme installs
     */
    define('CORE_UPGRADE_SKIP_NEW_BUNDLED', true);

    /**
	 * Define licenses located on premise
	 */
    define( 'ACF_PRO_LICENSE', 		$_ENV['LICENSE_ACF'] ?: '' );
    define( 'FACETWP_LICENSE_KEY', 	$_ENV['LICENSE_FACETWP'] ?: '' );
    define( 'GF_LICENSE_KEY', 		$_ENV['LICENSE_GRAVITYFORMS'] ?: '' );
	define( 'GPERKS_LICENSE_KEY', 	$_ENV['LICENSE_GRAVITYPERKS'] ?: '' );
    define( 'WPMDB_LICENCE',		$_ENV['LICENSE_WP_MIGRATE'] ?: '' );
    define( 'SHORTPIXEL_API_KEY', 	$_ENV['LICENSE_SHORTPIXEL'] ?: '' );
    define( 'WPCOM_API_KEY',		$_ENV['LICENSE_AKISMET'] ?: '' );
    define( 'WP_ROCKET_KEY', 		$_ENV['LICENSE_WPROCKET'] ?: '' );
    define( 'WP_ROCKET_EMAIL', 		$_ENV['LICENSE_WPROCKET_EMAIL'] ?: '' );
