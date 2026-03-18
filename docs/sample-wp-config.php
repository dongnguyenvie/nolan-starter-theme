<?php
/**
 * Sample wp-config.php — env-driven WordPress configuration
 *
 * Copy this to your WP root as wp-config.php.
 * Create a .env file alongside it with your actual values.
 * Optionally create .env.local for local overrides (gitignored).
 *
 * Priority: OS env > .env.local > .env
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 * @package WordPress
 */

// -- Load environment variables: .env first, .env.local overrides, OS env wins. --
( function () {
	$os_env = getenv();
	$vars   = [];

	$parse = function ( $path ) use ( &$vars ) {
		if ( ! file_exists( $path ) ) {
			return;
		}
		foreach ( file( $path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES ) as $line ) {
			$line = trim( $line );
			if ( '' === $line || '#' === $line[0] ) {
				continue;
			}
			if ( strpos( $line, '=' ) === false ) {
				continue;
			}
			[ $key, $value ] = explode( '=', $line, 2 );
			$key   = trim( $key );
			$value = trim( $value );
			// Strip surrounding quotes.
			if ( preg_match( '/^(["\'])(.*)\\1$/', $value, $m ) ) {
				$value = $m[2];
			}
			$vars[ $key ] = $value;
		}
	};

	$parse( __DIR__ . '/.env' );
	$parse( __DIR__ . '/.env.local' );

	// Apply collected vars; OS environment takes precedence.
	foreach ( $vars as $key => $value ) {
		if ( isset( $os_env[ $key ] ) ) {
			continue;
		}
		putenv( "$key=$value" );
		$_ENV[ $key ] = $value;
	}
} )();

// -- Database settings (from .env) --
define( 'DB_NAME', getenv( 'DB_NAME' ) ?: 'wordpress' );
define( 'DB_USER', getenv( 'DB_USER' ) ?: 'root' );
define( 'DB_PASSWORD', getenv( 'DB_PASSWORD' ) ?: '' );
define( 'DB_HOST', getenv( 'DB_HOST' ) ?: 'localhost' );
define( 'DB_CHARSET', 'utf8mb4' );
define( 'DB_COLLATE', '' );

/**
 * Authentication unique keys and salts.
 * Generate yours: https://api.wordpress.org/secret-key/1.1/salt/
 */
define( 'AUTH_KEY',         'put unique phrase here' );
define( 'SECURE_AUTH_KEY',  'put unique phrase here' );
define( 'LOGGED_IN_KEY',    'put unique phrase here' );
define( 'NONCE_KEY',        'put unique phrase here' );
define( 'AUTH_SALT',        'put unique phrase here' );
define( 'SECURE_AUTH_SALT', 'put unique phrase here' );
define( 'LOGGED_IN_SALT',   'put unique phrase here' );
define( 'NONCE_SALT',       'put unique phrase here' );

/**
 * WordPress database table prefix.
 */
$table_prefix = 'wp_';

/**
 * Debugging — controlled by APP_ENV environment variable.
 * Set APP_ENV=development in .env for local dev.
 */
$is_dev = getenv( 'APP_ENV' ) === 'development';
define( 'WP_DEBUG', $is_dev );
define( 'WP_DEBUG_LOG', $is_dev );
define( 'WP_DEBUG_DISPLAY', $is_dev );

// Disable auto-updates on local dev.
if ( $is_dev ) {
	define( 'AUTOMATIC_UPDATER_DISABLED', true );
	define( 'WP_AUTO_UPDATE_CORE', false );
	define( 'FS_METHOD', 'direct' );
}

// Detect HTTPS behind reverse proxy (Traefik, Nginx, Cloudflare).
if ( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ) {
	$_SERVER['HTTPS'] = 'on';
}

// Dynamic site URL from env (useful for Docker/staging).
// Set WP_HOME_URL in .env to override WordPress Settings > General.
if ( getenv( 'WP_HOME_URL' ) ) {
	define( 'WP_HOME', getenv( 'WP_HOME_URL' ) );
	define( 'WP_SITEURL', getenv( 'WP_HOME_URL' ) );
	define( 'FORCE_SSL_ADMIN', strpos( getenv( 'WP_HOME_URL' ), 'https' ) === 0 );
}

/* ── Add your project-specific constants below ─────────────────────── */

// Example: SMTP
// define( 'SMTP_HOST', getenv( 'SMTP_HOST' ) ?: '' );
// define( 'SMTP_PORT', getenv( 'SMTP_PORT' ) ?: 465 );
// define( 'SMTP_USER', getenv( 'SMTP_USER' ) ?: '' );
// define( 'SMTP_PASS', getenv( 'SMTP_PASS' ) ?: '' );

// Example: Payment gateway
// define( 'SEPAY_WEBHOOK_API_KEY', getenv( 'SEPAY_WEBHOOK_API_KEY' ) ?: '' );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
