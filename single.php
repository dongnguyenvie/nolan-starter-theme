<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 * @package StarterTheme
 */

get_header();
?>

	<main id="main-content" class="site-main">

		<?php
		while ( have_posts() ) :
			the_post();

			get_template_part( 'template-parts/content', get_post_type() );

			the_post_navigation( [
				'prev_text' => '<span class="nav-subtitle">' . esc_html__( 'Previous:', 'starter-theme' ) . '</span> <span class="nav-title">%title</span>',
				'next_text' => '<span class="nav-subtitle">' . esc_html__( 'Next:', 'starter-theme' ) . '</span> <span class="nav-title">%title</span>',
			] );

			if ( comments_open() || get_comments_number() ) :
				comments_template();
			endif;

		endwhile;
		?>

	</main><!-- #main -->

<?php
get_sidebar();
get_footer();
