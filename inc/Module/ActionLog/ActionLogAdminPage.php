<?php
/**
 * ActionLog admin page — view audit trail logs in WP Admin
 *
 * Shows a filterable table of all action log entries with user, action, IP, timestamp.
 *
 * @package StarterTheme\Module\ActionLog
 */

namespace StarterTheme\Module\ActionLog;

defined( 'ABSPATH' ) || exit;

class ActionLogAdminPage {

	public static function init(): void {
		add_action( 'admin_menu', [ static::class, 'add_menu_page' ] );
	}

	public static function add_menu_page(): void {
		add_menu_page(
			__( 'Action Logs', 'starter-theme' ),
			__( 'Action Logs', 'starter-theme' ),
			'manage_options',
			'starter-action-logs',
			[ static::class, 'render_page' ],
			'dashicons-list-view',
			75
		);
	}

	public static function render_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Filter params
		$filter_action  = sanitize_key( $_GET['log_action'] ?? '' );
		$filter_user_id = absint( $_GET['log_user_id'] ?? 0 );

		$args = [ 'number' => 100 ];
		if ( $filter_action )  $args['action']  = $filter_action;
		if ( $filter_user_id ) $args['user_id'] = $filter_user_id;

		$logs = ActionLogService::get_all( $args );
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

			<!-- Filters -->
			<form method="get" style="margin:16px 0;display:flex;gap:8px;align-items:center;">
				<input type="hidden" name="page" value="starter-action-logs">
				<input type="text" name="log_action" value="<?php echo esc_attr( $filter_action ); ?>"
				       placeholder="<?php esc_attr_e( 'Filter by action...', 'starter-theme' ); ?>"
				       class="regular-text" style="max-width:200px;">
				<input type="number" name="log_user_id" value="<?php echo $filter_user_id ?: ''; ?>"
				       placeholder="<?php esc_attr_e( 'User ID', 'starter-theme' ); ?>"
				       class="small-text" style="width:100px;">
				<?php submit_button( __( 'Filter', 'starter-theme' ), 'secondary', 'submit', false ); ?>
				<?php if ( $filter_action || $filter_user_id ) : ?>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=starter-action-logs' ) ); ?>"
					   class="button"><?php esc_html_e( 'Clear', 'starter-theme' ); ?></a>
				<?php endif; ?>
			</form>

			<!-- Log count -->
			<p class="description">
				<?php printf( esc_html__( 'Showing %d entries', 'starter-theme' ), count( $logs ) ); ?>
			</p>

			<!-- Logs table -->
			<table class="widefat striped" style="margin-top:8px;">
				<thead>
					<tr>
						<th style="width:50px;">ID</th>
						<th style="width:120px;"><?php esc_html_e( 'User', 'starter-theme' ); ?></th>
						<th style="width:150px;"><?php esc_html_e( 'Action', 'starter-theme' ); ?></th>
						<th style="width:100px;"><?php esc_html_e( 'Object', 'starter-theme' ); ?></th>
						<th><?php esc_html_e( 'Message', 'starter-theme' ); ?></th>
						<th style="width:120px;"><?php esc_html_e( 'IP', 'starter-theme' ); ?></th>
						<th style="width:160px;"><?php esc_html_e( 'Date', 'starter-theme' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php if ( empty( $logs ) ) : ?>
						<tr>
							<td colspan="7"><?php esc_html_e( 'No logs found.', 'starter-theme' ); ?></td>
						</tr>
					<?php else : ?>
						<?php foreach ( $logs as $log ) : ?>
							<tr>
								<td><?php echo esc_html( $log['id'] ); ?></td>
								<td>
									<?php if ( $log['user_id'] ) : ?>
										<a href="<?php echo esc_url( admin_url( 'admin.php?page=starter-action-logs&log_user_id=' . $log['user_id'] ) ); ?>">
											<?php echo esc_html( $log['user_login'] ); ?>
										</a>
										<br><small>#<?php echo esc_html( $log['user_id'] ); ?></small>
									<?php else : ?>
										<em>system</em>
									<?php endif; ?>
								</td>
								<td>
									<a href="<?php echo esc_url( admin_url( 'admin.php?page=starter-action-logs&log_action=' . $log['action'] ) ); ?>">
										<code><?php echo esc_html( $log['action'] ); ?></code>
									</a>
								</td>
								<td>
									<?php if ( $log['object_type'] ) : ?>
										<?php echo esc_html( $log['object_type'] ); ?>#<?php echo esc_html( $log['object_id'] ); ?>
									<?php else : ?>
										—
									<?php endif; ?>
								</td>
								<td><?php echo esc_html( $log['message'] ); ?></td>
								<td><code style="font-size:11px;"><?php echo esc_html( $log['ip_address'] ?: '—' ); ?></code></td>
								<td style="font-size:12px;"><?php echo esc_html( $log['created_at'] ); ?></td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>

			<!-- REST API info -->
			<div style="margin-top:24px;padding:12px;background:#f0f0f1;border-radius:4px;">
				<strong><?php esc_html_e( 'REST API Endpoints:', 'starter-theme' ); ?></strong>
				<ul style="margin:8px 0 0 16px;">
					<li><code>GET <?php echo esc_html( rest_url( 'starter/v1/logs' ) ); ?></code> — <?php esc_html_e( 'All logs (admin)', 'starter-theme' ); ?></li>
					<li><code>GET <?php echo esc_html( rest_url( 'starter/v1/logs/me' ) ); ?></code> — <?php esc_html_e( 'Current user logs', 'starter-theme' ); ?></li>
					<li><code>POST <?php echo esc_html( rest_url( 'starter/v1/logs' ) ); ?></code> — <?php esc_html_e( 'Create log entry', 'starter-theme' ); ?></li>
				</ul>
			</div>
		</div>
		<?php
	}
}
