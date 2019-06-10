<?php
		
	// ========================
	// Initiate and load dotenv
	// ========================
	$root_dir   = dirname( __DIR__ );
	$web_dir    = $root_dir . '/public_html';
	
	//Env::init();
	$dotenv     = new Dotenv\Dotenv( $root_dir );
	if( file_exists( $root_dir . '/.env' ) ){
	    $dotenv->load();
	    $dotenv->required( [ 'DB_NAME', 'DB_USER', 'DB_PASSWORD', 'WP_HOME', 'WP_SITEURL' ] );
	}
	
	// ================
	// Define variables
	// ================
	define( 'WP_ENV', getenv( 'WP_ENV' ) ?: 'production' );
	
	// ===============
	// Error handling
	// ===============
	if( getenv('WP_DEBUG') == true ) {
		#ini_set( 'display_errors', E_ALL );
		#define( 'WP_DEBUG_DISPLAY', true );
		#define( 'WP_DEBUG', true );
	}
	
	// ===============================
	// Custom Content Directory & Home
	// ===============================
	define( 'WP_CONTENT_DIR',   $web_dir . '/content' );
	define( 'WP_CONTENT_URL',   getenv( 'WP_HOME' ) . '/content' );
	define( 'WP_HOME',          getenv( 'WP_HOME' ) );
	define( 'WP_SITEURL',       getenv( 'WP_SITEURL' ) );
	
	// ======================================================
	// Language
	// Leave blank for American English
	// WordPress sets this through database in newer versions
	// ======================================================
	define( 'WPLANG', '' );
	
	// =================
	// Database settings
	// =================
	define('DB_NAME',       getenv('DB_NAME'));
	define('DB_USER',       getenv('DB_USER'));
	define('DB_PASSWORD',   getenv('DB_PASSWORD'));
	define('DB_HOST',       getenv('DB_HOST') ?: 'localhost');
	define('DB_CHARSET',    'utf8mb4');
	define('DB_COLLATE',    '');
	$table_prefix  =        getenv('DB_PREFIX') ?: 'vm_';
	
	// ===================
	// Auth keys and Salts
	// ===================
	define('AUTH_KEY',          getenv('AUTH_KEY'));
	define('SECURE_AUTH_KEY',   getenv('SECURE_AUTH_KEY'));
	define('LOGGED_IN_KEY',     getenv('LOGGED_IN_KEY'));
	define('NONCE_KEY',         getenv('NONCE_KEY'));
	define('AUTH_SALT',         getenv('AUTH_SALT'));
	define('SECURE_AUTH_SALT',  getenv('SECURE_AUTH_SALT'));
	define('LOGGED_IN_SALT',    getenv('LOGGED_IN_SALT'));
	define('NONCE_SALT',        getenv('NONCE_SALT'));
	
	// =================================================================================
	// Set update methods. Disable all WordPress updates in favor of Composer
	// Also disable cron, in case you want to set it yourself, instead of at every visit
	// =================================================================================
	// WP UPDATE SETTINGS
	// Enable all automatic updates through WordPress ( if Composer isn't an option )
	// Use 'minor' for only minor updates, true for all, and false for none
	// Remove auto-update for plugins in the base-theme, if required.
	// =================================================================================
	define( 'WP_AUTO_UPDATE_CORE',          getenv('WP_AUTO_UPDATE_CORE') ?: false );
	define( 'DISABLE_WP_CRON',              getenv('DISABLE_WP_CRON') ?: false);
	define( 'AUTOMATIC_UPDATER_DISABLED',   getenv('AUTOMATIC_UPDATER_DISABLED') ?: false );
	
	// ========================
	// Set environment variable
	// ========================
	if (!defined('ABSPATH')) {
	    define('ABSPATH', $web_dir . '/wp/');
	}