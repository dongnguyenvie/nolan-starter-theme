<?php
/**
 * Auto-setup required theme pages on theme activation or manual sync.
 * Creates missing pages and assigns correct templates idempotently.
 *
 * @package StarterTheme
 */

defined( 'ABSPATH' ) || exit;

/**
 * List of pages required by the theme.
 * Each entry: slug, title, page template file.
 *
 * Example:
 *   [ 'slug' => 'about', 'title' => 'About', 'template' => 'page-about.php' ],
 */
function starter_theme_required_pages(): array {
	return [
		// Add your pages here after creating page-*.php templates:
		// [ 'slug' => 'about',   'title' => 'About',   'template' => 'page-about.php' ],
		// [ 'slug' => 'contact', 'title' => 'Contact', 'template' => 'page-contact.php' ],
	];
}

/**
 * Ensure all required pages exist and have the correct template assigned.
 * Hooked to after_switch_theme — safe to run multiple times (idempotent).
 */
function starter_theme_setup_required_pages(): void {
	$pages = starter_theme_required_pages();
	if ( empty( $pages ) ) {
		return;
	}

	foreach ( $pages as $page ) {
		$existing = get_page_by_path( $page['slug'] );

		if ( ! $existing ) {
			$page_id = wp_insert_post( [
				'post_title'  => $page['title'],
				'post_name'   => $page['slug'],
				'post_status' => 'publish',
				'post_type'   => 'page',
				'post_author' => 1,
			] );

			if ( $page_id && ! is_wp_error( $page_id ) ) {
				update_post_meta( $page_id, '_wp_page_template', $page['template'] );
			}

			continue;
		}

		// Page exists — fix template only if wrong or missing
		$current = get_post_meta( $existing->ID, '_wp_page_template', true );
		if ( $current !== $page['template'] ) {
			update_post_meta( $existing->ID, '_wp_page_template', $page['template'] );
		}
	}
}
add_action( 'after_switch_theme', 'starter_theme_setup_required_pages' );

/* ── Admin UI: Appearance > Sync Pages ── */

add_action( 'admin_menu', function () {
	add_theme_page(
		'Sync Pages',
		'Sync Pages',
		'manage_options',
		'starter-sync-pages',
		'starter_theme_render_sync_pages_admin'
	);
} );

/** Handle POST sync action. */
add_action( 'admin_init', function () {
	if ( empty( $_POST['starter_sync_pages_nonce'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( $_POST['starter_sync_pages_nonce'], 'starter_sync_pages' ) || ! current_user_can( 'manage_options' ) ) {
		wp_die( 'Unauthorized' );
	}
	starter_theme_setup_required_pages();
	wp_safe_redirect( admin_url( 'themes.php?page=starter-sync-pages&synced=1' ) );
	exit;
} );

/** Render the admin page with page status table + sync button. */
function starter_theme_render_sync_pages_admin(): void {
	$synced = isset( $_GET['synced'] );
	$pages  = starter_theme_required_pages();
	?>
	<div class="wrap">
		<h1>Sync Pages</h1>

		<?php if ( $synced ) : ?>
			<div class="notice notice-success is-dismissible"><p>Pages synced successfully!</p></div>
		<?php endif; ?>

		<?php if ( empty( $pages ) ) : ?>
			<div class="notice notice-info"><p>No required pages defined yet. Edit <code>inc/theme-page-setup.php</code> → <code>starter_theme_required_pages()</code> to add pages.</p></div>
		<?php else : ?>
			<table class="widefat striped" style="max-width:700px;margin:20px 0">
				<thead>
					<tr><th>Slug</th><th>Title</th><th>Template</th><th>Status</th></tr>
				</thead>
				<tbody>
				<?php foreach ( $pages as $p ) :
					$existing = get_page_by_path( $p['slug'] );
					if ( ! $existing ) {
						$status = '<span style="color:red">&#10007; Not created</span>';
					} else {
						$tpl = get_post_meta( $existing->ID, '_wp_page_template', true );
						$status = ( $tpl === $p['template'] )
							? '<span style="color:green">&#10003; OK</span>'
							: '<span style="color:orange">&#9888; Wrong template (' . esc_html( $tpl ) . ')</span>';
					}
				?>
					<tr>
						<td><code>/<?php echo esc_html( $p['slug'] ); ?></code></td>
						<td><?php echo esc_html( $p['title'] ); ?></td>
						<td><code><?php echo esc_html( $p['template'] ); ?></code></td>
						<td><?php echo $status; ?></td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>

			<form method="post">
				<?php wp_nonce_field( 'starter_sync_pages', 'starter_sync_pages_nonce' ); ?>
				<button type="submit" class="button button-primary">Sync Pages</button>
				<p class="description">Creates missing pages &amp; fixes wrong templates. Safe to run multiple times.</p>
			</form>
		<?php endif; ?>
	</div>
	<?php
}
