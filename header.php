<?php
/**
 * The header template
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 * @package StarterTheme
 */

?>
<!doctype html>
<html <?php language_attributes(); ?> class="">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<!-- Dark mode: apply localStorage preference before first paint to avoid flash -->
	<script>(function(){var s=localStorage.getItem('starter-theme');if(s==='dark'){document.documentElement.classList.add('dark');}})();</script>
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#main-content"><?php esc_html_e( 'Skip to content', 'starter-theme' ); ?></a>

	<header id="masthead" style="position:sticky;top:0;z-index:100;border-bottom:1px solid rgba(0,0,0,.08);"
	        class="bg-white dark:bg-gray-950">
		<div style="max-width:1200px;margin:0 auto;padding:0 16px;height:60px;display:flex;align-items:center;justify-content:space-between;gap:16px;">

			<!-- Logo / site name -->
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"
			   style="text-decoration:none;flex-shrink:0;display:inline-flex;align-items:center;">
				<?php if ( has_custom_logo() ) : ?>
					<?php the_custom_logo(); ?>
				<?php else : ?>
					<span class="text-lg font-bold text-gray-900 dark:text-white"><?php bloginfo( 'name' ); ?></span>
				<?php endif; ?>
			</a>

			<!-- Nav links — uses wp_nav_menu for easy customization -->
			<nav id="starter-main-nav" style="display:flex;align-items:center;gap:4px;flex:1;justify-content:center;flex-wrap:wrap;">
				<?php
				wp_nav_menu( [
					'theme_location' => 'primary',
					'container'      => false,
					'items_wrap'     => '%3$s',
					'depth'          => 1,
					'fallback_cb'    => function () {
						printf(
							'<a href="%s" style="padding:6px 12px;border-radius:6px;font-size:13px;font-weight:500;text-decoration:none;" class="text-gray-600 dark:text-gray-300 hover:text-brand dark:hover:text-brand-light">%s</a>',
							esc_url( home_url( '/' ) ),
							esc_html__( 'Home', 'starter-theme' )
						);
					},
					'walker'         => new class extends \Walker_Nav_Menu {
						public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
							$classes = 'text-gray-600 dark:text-gray-300 hover:text-brand dark:hover:text-brand-light';
							if ( $item->current ) {
								$classes .= ' text-brand dark:text-brand-light font-semibold';
							}
							$output .= sprintf(
								'<a href="%s" style="padding:6px 12px;border-radius:6px;font-size:13px;font-weight:500;text-decoration:none;transition:color .15s;" class="%s">%s</a>',
								esc_url( $item->url ),
								esc_attr( $classes ),
								esc_html( $item->title )
							);
						}
						public function end_el( &$output, $item, $depth = 0, $args = null ) {}
						public function start_lvl( &$output, $depth = 0, $args = null ) {}
						public function end_lvl( &$output, $depth = 0, $args = null ) {}
					},
				] );
				?>
			</nav>

			<!-- Dark/light toggle -->
			<button id="starter-theme-toggle" aria-label="Toggle dark/light mode"
			        style="background:none;border:none;cursor:pointer;font-size:18px;padding:4px 8px;border-radius:8px;flex-shrink:0;"
			        class="text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800">
				<span id="starter-theme-icon"></span>
			</button>
		</div>
		<script>
		(function(){
			var btn=document.getElementById('starter-theme-toggle'),icon=document.getElementById('starter-theme-icon'),h=document.documentElement;
			function sync(){icon.textContent=h.classList.contains('dark')?'☀️':'🌙';}
			sync();
			btn.addEventListener('click',function(){var d=h.classList.toggle('dark');localStorage.setItem('starter-theme',d?'dark':'light');sync();});
		})();
		</script>
	</header><!-- #masthead -->
