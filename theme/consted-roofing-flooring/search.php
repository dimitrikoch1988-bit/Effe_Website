<?php
/**
 * Template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package Consted
 */
get_header();
// Container wrap start
do_action( 'consted_container_wrap_start', 'no-sidebar' );
?>
<div class="blog-wrap blog-inner">
	<?php if ( have_posts() ) : ?>
		<div class="row">
			<?php
			// Start the Loop
			while ( have_posts() ) :
				the_post();

				// Load content-search.php for search results
				get_template_part( 'template-parts/content', 'search' );

			endwhile;

			// Navigation
			do_action( 'consted_loop_navigation' );
			?>
		</div>
	<?php else : ?>
		<?php get_template_part( 'template-parts/content', 'none' ); ?>
	<?php endif; ?>
</div>
<?php
// Container wrap end
do_action( 'consted_container_wrap_end', 'no-sidebar');
get_footer();