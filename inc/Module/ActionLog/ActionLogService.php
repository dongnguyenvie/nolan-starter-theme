<?php
/**
 * Action log service — writes and reads audit trail entries
 *
 * Usage:
 *   ActionLogService::log( get_current_user_id(), 'order_created', 'order', $order_id, 'User placed order' );
 *   ActionLogService::log( 0, 'cron_cleanup', '', 0, 'Expired records purged', ['count' => 5] );
 *
 * @package StarterTheme\Module\ActionLog
 */

namespace StarterTheme\Module\ActionLog;

defined( 'ABSPATH' ) || exit;

class ActionLogService {

	/**
	 * Record an action in the audit log
	 *
	 * @param int    $user_id     WP user ID (0 for system/cron)
	 * @param string $action      Machine-readable key (e.g. "user_login", "entry_created")
	 * @param string $object_type Entity type (e.g. "entry", "user", "order")
	 * @param int    $object_id   Entity ID
	 * @param string $message     Human-readable description
	 * @param array  $context     Extra structured data (serialised to JSON)
	 */
	public static function log(
		int    $user_id,
		string $action,
		string $object_type = '',
		int    $object_id   = 0,
		string $message     = '',
		array  $context     = []
	): void {
		$query = new Db\ActionLogQuery();
		$query->add_item( [
			'user_id'     => $user_id,
			'action'      => sanitize_key( $action ),
			'object_type' => sanitize_key( $object_type ),
			'object_id'   => $object_id,
			'message'     => sanitize_text_field( $message ),
			'context'     => wp_json_encode( $context ) ?: '',
			'ip_address'  => self::get_client_ip(),
			'created_at'  => current_time( 'mysql' ),
		] );
	}

	/**
	 * Get logs for a specific user
	 */
	public static function get_for_user( int $user_id, int $limit = 50 ): array {
		$query = new Db\ActionLogQuery();
		$rows  = $query->query( [
			'user_id' => $user_id,
			'number'  => $limit,
			'orderby' => 'created_at',
			'order'   => 'DESC',
		] );

		return array_map( [ static::class, 'format_row' ], $rows );
	}

	/**
	 * Get all logs (admin view) with optional filters
	 */
	public static function get_all( array $args = [] ): array {
		$defaults = [
			'number'  => 100,
			'orderby' => 'created_at',
			'order'   => 'DESC',
		];

		$query = new Db\ActionLogQuery();
		$rows  = $query->query( array_merge( $defaults, $args ) );

		return array_map( static function ( $row ) {
			$data = self::format_row( $row );
			$user = get_userdata( $row->user_id );
			$data['user_login'] = $user ? $user->user_login : 'system';
			return $data;
		}, $rows );
	}

	/**
	 * Format a row for API/display output
	 */
	private static function format_row( Db\ActionLogRow $row ): array {
		$ctx = [];
		if ( ! empty( $row->context ) ) {
			$decoded = json_decode( $row->context, true );
			if ( is_array( $decoded ) ) {
				$ctx = $decoded;
			}
		}

		return [
			'id'          => $row->id,
			'user_id'     => $row->user_id,
			'action'      => $row->action,
			'object_type' => $row->object_type,
			'object_id'   => $row->object_id,
			'message'     => $row->message,
			'context'     => $ctx,
			'ip_address'  => $row->ip_address,
			'created_at'  => $row->created_at,
		];
	}

	/**
	 * Get client IP address (supports proxies)
	 */
	private static function get_client_ip(): string {
		$headers = [ 'HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR' ];
		foreach ( $headers as $header ) {
			if ( ! empty( $_SERVER[ $header ] ) ) {
				$ip = explode( ',', $_SERVER[ $header ] )[0];
				$ip = trim( $ip );
				if ( filter_var( $ip, FILTER_VALIDATE_IP ) ) {
					return $ip;
				}
			}
		}
		return '';
	}
}
