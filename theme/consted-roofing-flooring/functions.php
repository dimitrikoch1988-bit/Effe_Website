<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if ( !function_exists( 'consted_roofing_flooring_setup' ) ):

function consted_roofing_flooring_setup(){
   // Make theme available for translation.
   load_theme_textdomain( 'consted-roofing-flooring', get_stylesheet_directory_uri() . '/languages' );
   add_theme_support( 'custom-header', apply_filters( 'consted_custom_header_args', array(
        'default-image' => get_stylesheet_directory_uri() . '/assets/image/custom-header.jpg',
        'default-text-color'     => '000000',
        'width'                  => 1000,
        'height'                 => 350,
        'flex-height'            => true,
        'wp-head-callback'       => 'consted_header_style',
    ) ) );
    
    register_default_headers( array(
        'default-image' => array(
        'url' => '%s/assets/image/custom-header.jpg',
        'thumbnail_url' => '%s/assets/image/custom-header.jpg',
        'description' => esc_html__( 'Default Header Image', 'consted-roofing-flooring' ),
        ),
    ));

}
add_action( 'after_setup_theme', 'consted_roofing_flooring_setup' );
endif;

if ( !function_exists( 'consted_roofing_flooring_css' ) ):
    function consted_roofing_flooring_css() {
        wp_enqueue_style( 'consted_thm_parent', trailingslashit( get_template_directory_uri() ) . 'style.css', array( 'animate-2','icofont','bootstrap','consted-core-style' ) );
        wp_enqueue_script( 'consted-roofing-flooring-js', get_theme_file_uri( '/assets/consted-roofing-flooring.js'), array(),  wp_get_theme()->get('Version'), true);
    }
endif;
add_action( 'wp_enqueue_scripts', 'consted_roofing_flooring_css', 10 );


if( !function_exists('classic_construction_disable_from_parent') ):
    add_action('wp','classic_construction_disable_from_parent',50);
    function classic_construction_disable_from_parent(){
        global $consted_header_layout_class, $consted_body_layout, $consted_footer_layout_class, $consted_post_meta_class;
        remove_action('consted_shop_site_header', array( $consted_header_layout_class, 'header_container' ), 30 );
        remove_action('consted_container_wrap_start', array($consted_body_layout, 'container_wrap_column_start' ), 10 );
        remove_action('consted_container_wrap_end', array($consted_body_layout, 'get_sidebar' ), 10 );

        remove_action('consted_site_footer', array( $consted_footer_layout_class, 'site_footer_info' ), 80);
        if (is_single()) {
        remove_action( 'consted_site_content_type', array( $consted_post_meta_class, 'render_meta_list' ), 20, 1 );
        remove_action('consted_shop_site_header', array( $consted_header_layout_class, 'site_hero_sections' ), 999 );
        add_action('consted_shop_site_header', 'consted_roofing_flooring_banner', 999 );
        }
    
    }
endif;

if ( ! function_exists( 'consted_roofing_flooring_banner' ) ) :
     /*
     * Setup default theme options
     *
     * @return array $defaults
     */
    function consted_roofing_flooring_banner(){
    $header_image = get_header_image();
    ?>
    <div id="inner-hero" class="site-hero-section" <?php if( !empty( $header_image ) ):?> style="background-image: url(<?php echo esc_url( $header_image );?>); background-attachment: scroll; " <?php endif;?>>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-10 col-lg-10">
                    <div class="part-txt">
                        <div class="flex-wrap"> 
                        <?php 
                        echo '<h1 class="page-title-text">';
                        echo single_post_title( '', false );
                        echo '</h1>';
                        $meta = array( 'author', 'date','category','comments');
                        do_action('consted_meta_info',$meta);
                        ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    }
endif;

if ( ! function_exists( 'consted_roofing_flooring_navbar' ) ) :
    /**
     * Display the bottom header navbar for the Consted Roofing Flooring theme.
     *
     * Hooks into 'consted_shop_site_header' with priority 30.
     *
     * @developer aThemeArt (https://athemeart.com)
     */
    add_action( 'consted_shop_site_header', 'consted_roofing_flooring_navbar', 30 );

    function consted_roofing_flooring_navbar() {
        $padding = empty(array_filter([
        consted_get_option('__topbar_phone'),
        consted_get_option('__topbar_address'),
        consted_get_option('__topbar_email')
        ])) ? 'padding-remove' : '';
        ?>
        <div class="bottom-header layout-3 <?php echo esc_attr($padding);?>">
            <div class="container">
                <div class="rows justify-content-end d-flex align-items-center relative">
                    <!-- Site logo -->
                    <div class="logo">
                        <?php do_action('consted_site_branding');?>
                    </div> 
                    <!-- Navbar toggle button for responsive view -->
                    <button 
                        class="navbar-toggle collapsed"
                        type="button"
                        data-toggle="collapse"
                        data-target="#navbar"
                        aria-controls="navbar"
                        aria-expanded="false"
                    >
                        <i class="icofont-close"></i>
                    </button>

                    <!-- Primary navigation menu -->
                    <nav class="navbar navbar-expand-lg navbar-light ow-navigation underline">
                        <div id="navbar" class="collapse navbar-collapse navbar-left">
                            <button type="button" class="nav-close" aria-expanded="false">
                                <i class="fa fa-window-close"></i>
                            </button>
                            <?php
                            wp_nav_menu(
                                array(
                                    'theme_location' => 'primary',
                                    'depth'          => 3,
                                    'container'      => '',
                                    'menu_class'     => 'nav navbar-nav menubar',
                                    'fallback_cb'    => 'WP_Bootstrap_Navwalker::fallback',
                                    'walker'         => new WP_Bootstrap_Navwalker(),
                                )
                            );
                            ?>
                        </div>
                    </nav>

                    <!-- Sidebar toggle button or empty space -->
                    <?php if ( is_active_sidebar( 'sidebar-1' )  ) : ?>
                        <button class="side-bar-show">
                            <img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/image/btn-img.png' ); ?>" alt="<?php esc_attr_e( 'Sidebar', 'consted-roofing-flooring' ); ?>">
                        </button>
                    <?php else : ?>
                        <div class="empty-space"></div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
        <?php
    }
endif;


if( !function_exists('consted_roofing_flooring_options') ):
    /*
     * Setup default theme options
     *
     * @return array $defaults
     */
    function consted_roofing_flooring_options( $defaults ) {
        $defaults['blog_layout']        = 'no-sidebar';
        $defaults['single_post_layout'] = 'without-sidebar';
        $defaults['page_layout']        = 'without-sidebar';

        return $defaults;
    }
    add_filter( 'consted_filter_default_theme_options', 'consted_roofing_flooring_options',99);
endif;


if ( ! function_exists( 'consted_roofing_flooring_customize' ) ) :
/**
 * Customize and extend default theme layout options.
 *
 * Hooks into 'customize_register' to override or extend the layout
 * choices for single posts and pages in the Customizer.
 *
 * @since 1.0.0
 * @developer aThemeArt (https://athemeart.com)
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 * @return void
 */
add_action( 'customize_register', 'consted_roofing_flooring_customize', 20 );
function consted_roofing_flooring_customize( $wp_customize ) {

    $page_layout_control = $wp_customize->get_control( 'single_post_layout' );

    if ( null !== $page_layout_control ) {
        // Override choices for single post layout
        $page_layout_control->choices = array(
            'left-sidebar'     => esc_html__( 'Primary Sidebar / Content', 'consted-roofing-flooring'),
            'right-sidebar'    => esc_html__( 'Content / Primary Sidebar', 'consted-roofing-flooring'),
            'without-sidebar'  => esc_html__( 'No Sidebar', 'consted-roofing-flooring'),
            'no-sidebar'       => esc_html__( 'Container', 'consted-roofing-flooring'),
            'full-container'   => esc_html__( 'Full Container', 'consted-roofing-flooring'),
        );
    }

    $page_layout_control = $wp_customize->get_control( 'page_layout' );

    if ( null !== $page_layout_control ) {
        // Override choices for page layout
        $page_layout_control->choices = array(
            'left-sidebar'     => esc_html__( 'Primary Sidebar / Content', 'consted-roofing-flooring'),
            'right-sidebar'    => esc_html__( 'Content / Primary Sidebar', 'consted-roofing-flooring'),
            'without-sidebar'  => esc_html__( 'No Sidebar', 'consted-roofing-flooring'),
            'no-sidebar'       => esc_html__( 'Container', 'consted-roofing-flooring'),
            'full-container'   => esc_html__( 'Full Container', 'consted-roofing-flooring'),
        );
    }
}
endif;

if ( ! function_exists( 'consted_roofing_flooring_column_repalce' ) ) :
/**
 * Main Content Column before
 *
 * @param string $layout
 * @return void
 */
add_action('consted_container_wrap_start', 'consted_roofing_flooring_column_repalce', 10 );

function consted_roofing_flooring_column_repalce( $layout ) {
    if ( get_post_meta( get_the_ID(), '_wp_page_template', true ) == 'elementor_theme' ) {
        return false;
    }
    switch ( $layout ) {
        case 'right-sidebar':
            $layout = 'col-xl-8 col-md-8 col-12 order-1';
            break;
        case 'left-sidebar':
            $layout = 'col-xl-8 col-md-8 col-12 order-2';
            break;      
        case 'no-sidebar':
            $layout = 'col-md-12';
            break;
        case 'without-sidebar':
            $layout = 'col-md-10 offset-md-1 col-12';
            break;    
        case 'full-container':
            $layout = 'col-12';
            break;  
        default:
            $layout = 'col-12';
    } 
    $html = '<div class="' . esc_attr( $layout ) . '">
                <main id="main" class="site-main">';
    echo wp_kses( $html, consted_alowed_tags() );
}
endif;

if ( ! function_exists( 'consted_roofing_flooring_sidebar' ) ) :
/**
 * Sidebar wrapper output based on layout
 *
 * @param string $layout
 * @return void|false
 */
add_action( 'consted_container_wrap_end', 'consted_roofing_flooring_sidebar', 10 );
function consted_roofing_flooring_sidebar( $layout = '' ) { 
    // Skip if theme canvas or no sidebar needed
    if ( $layout === 'theme-canvas' ) {
        return false;
    }
    switch ( $layout ) {
        case 'right-sidebar':
            $classes = 'col-xl-4 col-md-4 col-12 order-2 consted-sidebar';
            break;
        case 'left-sidebar':
            $classes = 'col-xl-4 col-md-4 col-12 order-1 consted-sidebar';
            break;  
        default:
            return false; // no-sidebar, without-sidebar, full-container, etc.
    }
    ?>
    <div id="blog-sidebar" class="<?php echo esc_attr( $classes ); ?>">
        <?php if ( is_active_sidebar( 'sidebar-2' ) ) {
            dynamic_sidebar( 'sidebar-2' );
        }?>
    </div>
    <?php
}
endif;

if ( ! function_exists( 'consted_roofing_flooring_backtotop' ) ) :
/**
 * Display Back to Top button.
 *
 * @return void
 * @developer aThemeArt (https://athemeart.com)
 */
function consted_roofing_flooring_backtotop() {
    echo '<a id="back_to_top" class="ui-to-top"><i class="icofont-arrow-up"></i></a>';
}
add_action( 'wp_footer', 'consted_roofing_flooring_backtotop', 10 );
endif;


if ( ! function_exists( 'consted_roofing_flooring_footer' ) ) :
/**
 * Display the footer copyright section.
 *
 * Outputs the site copyright text and developer credit.
 *
 * @return void
 * @developer aThemeArt (https://athemeart.com)
 */
add_action('consted_site_footer', 'consted_roofing_flooring_footer', 80);
function consted_roofing_flooring_footer() {
    $html  = '<div class="copyright">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-xl-6 col-lg-6 col-md-6 text-wrap">';
    if ( get_theme_mod( 'copyright_text' ) != '' ) {
        $html .=esc_html( get_theme_mod( 'copyright_text' ) );
    } else {
        $html .= sprintf( /* translators: 1: Current Year, 2: Blog Name */
            esc_html__( 'Copyright &copy; %1$s %2$s. All Rights Reserved.', 'consted-roofing-flooring' ),
            date_i18n(
                _x( 'Y', 'copyright date format', 'consted-roofing-flooring' )
            ),
            esc_html( get_bloginfo( 'name' ) )
        );
    }

    $html .= '<span class="dev_info">' . sprintf(  /* translators: 1: developer website, 2: WordPress url */
        esc_html__( ' %1$s theme by aThemeArt - Proudly Powered by WordPress.', 'consted-roofing-flooring' ),
        '<a href="' . esc_url( 'https://athemeart.com/downloads/consted-roofing-flooring/' ) . '" target="_blank" rel="nofollow">' . esc_html_x( 'Consted', 'credit - theme', 'consted-roofing-flooring' ) . '</a>'
    ) . '</span>';

    $html .= '        </div>
                    </div>
                </div>
              </div>';

    echo wp_kses( $html, consted_alowed_tags() );
}
endif;
