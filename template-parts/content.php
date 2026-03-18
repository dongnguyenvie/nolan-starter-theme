<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 * @package StarterTheme
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<?php
		if ( is_singular() ) :
			the_title( '<h1 class="entry-title">', '</h1>' );
		else :
			the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
		endif;

		if ( 'post' === get_post_type() ) :
			?>
			<div class="entry-meta">
				<?php
				starter_theme_posted_on();
				starter_theme_posted_by();
				?>
			</div>
		<?php endif; ?>
	</header>

	<?php starter_theme_post_thumbnail(); ?>

	<div class="entry-content">
		<?php
		the_content( sprintf(
			wp_kses( __( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'starter-theme' ), [ 'span' => [ 'class' => [] ] ] ),
			wp_kses_post( get_the_title() )
		) );

		wp_link_pages( [
			'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'starter-theme' ),
			'after'  => '</div>',
		] );
		?>
	</div>

	<footer class="entry-footer">
		<?php starter_theme_entry_footer(); ?>
	</footer>
</article>
