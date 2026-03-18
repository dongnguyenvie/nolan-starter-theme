<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 * @package StarterTheme
 */

get_header();
?>

	<main id="main-content" class="site-main">

		<section class="error-404 not-found max-w-2xl mx-auto px-4 py-16 text-center">
			<header class="page-header">
				<h1 class="page-title text-6xl font-bold text-gray-300 dark:text-gray-700 mb-4">404</h1>
			</header>

			<div class="page-content">
				<p class="text-lg text-gray-600 dark:text-gray-400 mb-8"><?php esc_html_e( 'Oops! That page can\'t be found.', 'starter-theme' ); ?></p>
				<?php get_search_form(); ?>
			</div>
		</section>

	</main><!-- #main -->

<?php
get_footer();
