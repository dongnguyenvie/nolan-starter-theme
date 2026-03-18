<?php
/**
 * Abstract base controller for all REST handlers
 *
 * @package StarterTheme
 */

namespace StarterTheme\Shared;

defined( 'ABSPATH' ) || exit;

abstract class BaseController {

	/**
	 * Send JSON success response and exit
	 */
	public static function send_json_success( array $data = [], int $status = 200 ): void {
		wp_send_json_success( $data, $status );
	}

	/**
	 * Send JSON error response and exit
	 */
	public static function send_json_error( string $message, int $status = 400 ): void {
		wp_send_json_error( [ 'message' => $message ], $status );
	}

	/**
	 * Verify WordPress nonce — calls send_json_error on failure
	 */
	public static function verify_nonce( string $nonce, string $action ): void {
		if ( ! wp_verify_nonce( $nonce, $action ) ) {
			static::send_json_error( 'Invalid security token', 403 );
		}
	}

	/**
	 * Require logged-in user
	 */
	public static function require_login(): void {
		if ( ! is_user_logged_in() ) {
			static::send_json_error( 'Authentication required', 401 );
		}
	}

	/**
	 * Require admin capability
	 */
	public static function require_admin(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			static::send_json_error( 'Unauthorized', 403 );
		}
	}
}
