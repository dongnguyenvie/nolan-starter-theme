<?php
/**
 * ActionLog REST controller
 *
 * Endpoints:
 *   GET  /wp-json/starter/v1/logs              — list logs (admin only)
 *   GET  /wp-json/starter/v1/logs/me           — current user's logs
 *   POST /wp-json/starter/v1/logs              — create log entry (authenticated)
 *
 * @package StarterTheme\Module\ActionLog
 */

namespace StarterTheme\Module\ActionLog;

use StarterTheme\Shared\BaseController;

defined( 'ABSPATH' ) || exit;

class ActionLogController extends BaseController {

	public static function register_routes(): void {
		add_action( 'rest_api_init', function () {

			// Admin: list all logs with filters
			register_rest_route( 'starter/v1', '/logs', [
				'methods'             => 'GET',
				'callback'            => [ static::class, 'list_logs' ],
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
			] );

			// User: my own logs
			register_rest_route( 'starter/v1', '/logs/me', [
				'methods'             => 'GET',
				'callback'            => [ static::class, 'my_logs' ],
				'permission_callback' => function () {
					return is_user_logged_in();
				},
			] );

			// Create a log entry (for other modules calling via REST)
			register_rest_route( 'starter/v1', '/logs', [
				'methods'             => 'POST',
				'callback'            => [ static::class, 'create_log' ],
				'permission_callback' => function () {
					return is_user_logged_in();
				},
			] );
		} );
	}

	/**
	 * GET /logs — admin list with filters
	 * Query params: action, user_id, object_type, per_page, offset
	 */
	public static function list_logs( \WP_REST_Request $request ): void {
		$args = [
			'number' => absint( $request->get_param( 'per_page' ) ?: 50 ),
			'offset' => absint( $request->get_param( 'offset' ) ?: 0 ),
		];

		if ( $request->get_param( 'action' ) ) {
			$args['action'] = sanitize_key( $request->get_param( 'action' ) );
		}
		if ( $request->get_param( 'user_id' ) ) {
			$args['user_id'] = absint( $request->get_param( 'user_id' ) );
		}
		if ( $request->get_param( 'object_type' ) ) {
			$args['object_type'] = sanitize_key( $request->get_param( 'object_type' ) );
		}

		$logs = ActionLogService::get_all( $args );
		static::send_json_success( [ 'items' => $logs, 'count' => count( $logs ) ] );
	}

	/**
	 * GET /logs/me — current user's logs
	 */
	public static function my_logs( \WP_REST_Request $request ): void {
		$limit = absint( $request->get_param( 'per_page' ) ?: 50 );
		$logs  = ActionLogService::get_for_user( get_current_user_id(), $limit );
		static::send_json_success( [ 'items' => $logs, 'count' => count( $logs ) ] );
	}

	/**
	 * POST /logs — create a log entry
	 * Body: action (required), object_type, object_id, message, context
	 */
	public static function create_log( \WP_REST_Request $request ): void {
		$action = sanitize_key( $request->get_param( 'action' ) ?? '' );
		if ( empty( $action ) ) {
			static::send_json_error( 'Action is required', 400 );
		}

		ActionLogService::log(
			get_current_user_id(),
			$action,
			sanitize_key( $request->get_param( 'object_type' ) ?? '' ),
			absint( $request->get_param( 'object_id' ) ?? 0 ),
			sanitize_text_field( $request->get_param( 'message' ) ?? '' ),
			is_array( $request->get_param( 'context' ) ) ? $request->get_param( 'context' ) : []
		);

		static::send_json_success( [ 'logged' => true ], 201 );
	}
}
