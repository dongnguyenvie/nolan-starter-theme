<?php
/**
 * The template for displaying the footer
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 * @package StarterTheme
 */

?>

	<footer id="colophon" class="site-footer bg-gray-50 dark:bg-gray-950 border-t border-gray-200 dark:border-gray-800">

		<div class="max-w-6xl mx-auto px-4 py-12 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-10">

			<!-- Col 1: Brand -->
			<div>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" class="inline-flex mb-4">
					<?php if ( has_custom_logo() ) : ?>
						<?php the_custom_logo(); ?>
					<?php else : ?>
						<span class="text-lg font-bold text-gray-900 dark:text-white"><?php bloginfo( 'name' ); ?></span>
					<?php endif; ?>
				</a>
				<p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed">
					<?php bloginfo( 'description' ); ?>
				</p>
			</div>

			<!-- Col 2: Footer menu -->
			<div>
				<h3 class="text-xs font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-4"><?php esc_html_e( 'Links', 'starter-theme' ); ?></h3>
				<?php
				wp_nav_menu( [
					'theme_location' => 'footer',
					'container'      => false,
					'menu_class'     => 'space-y-2',
					'depth'          => 1,
					'fallback_cb'    => false,
				] );
				?>
			</div>

			<!-- Col 3: Contact placeholder -->
			<div>
				<h3 class="text-xs font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-4"><?php esc_html_e( 'Contact', 'starter-theme' ); ?></h3>
				<p class="text-sm text-gray-500 dark:text-gray-400">
					<?php esc_html_e( 'hello@your-site.com', 'starter-theme' ); ?>
				</p>
			</div>

		</div>

		<!-- Footer bottom bar -->
		<div class="border-t border-gray-200 dark:border-gray-800">
			<div class="max-w-6xl mx-auto px-4 py-4 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-gray-400 dark:text-gray-600">
				<span>&copy; <?php echo date( 'Y' ); ?> <?php bloginfo( 'name' ); ?>. <?php esc_html_e( 'All rights reserved.', 'starter-theme' ); ?></span>
			</div>
		</div>

	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
