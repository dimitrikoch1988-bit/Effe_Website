<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package consted
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( array( 'col-xl-4', 'col-lg-4', 'col-md-12' ) ); ?>>
    <div class="single-box">

        <?php
        /**
         * Hook: consted_posts_blog_media
         *
         * @hooked consted_posts_formats_thumbnail - 10
         */
        do_action( 'consted_posts_blog_media' );
        ?>

        <div class="part-txt">

            <?php
            /**
             * Hook: consted_site_content_type
             *
             * @hooked site_loop_heading - 10
             * @hooked render_meta_list - 20
             * @hooked site_content_type - 30
             */
            $meta = array();

            if ( ! consted_get_option( 'blog_meta_hide' ) ) {
                $meta = array( 'author', 'date');
            }

            do_action( 'consted_site_content_type', $meta );
            ?>

        </div>
    </div>
</article><!-- #post-<?php the_ID(); ?> -->