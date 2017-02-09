<?php
// Texts in order to create a po-file
//$e1 = __('Read articles by region', 'pressenza');
//$e1 = __('Read articles by section', 'pressenza');
//$e1 = __('Sections', 'pressenza');
//$e1 = __('Opinions', 'pressenza');
//$e1 = __('Interviews', 'pressenza');
//$e1 = __('Latest News', 'pressenza');
//$e1 = __('Posted by', 'pressenza');
//$e1 = __('Categories', 'pressenza');
//$e1 = __('About The Author', 'pressenza');
//$e1 = __('Number of Entries', 'pressenza');
//$e1 = __('Image by', 'pressenza');
//$e1 = __('The original article can be found on our partner\'s website here', 'pressenza');
//$e1 = __('This post is also available in: %s', 'pressenza');
//$e1 = __('Search results for', 'pressenza');
//$e1 = __('Archives', 'pressenza');

/*
 * Settings
 */
add_theme_support('post-formats');
add_theme_support('post-thumbnails');
add_theme_support('menus');
// Add image size for Home slider
add_image_size('featured', 750, 422, true);
// deactivate WPML language selector includes
define('ICL_DONT_LOAD_LANGUAGE_SELECTOR_CSS', true);
define('ICL_DONT_LOAD_LANGUAGES_JS', true);
define('THEME_URL', get_template_directory_uri());
load_theme_textdomain('pressenza', get_template_directory());
global $shortcode_tags;

/*
 * Register Sidebars
 * Primary Sidebar for displaying the widgets in the right column
 * Footer Sidebar for the right textbox in the footer
 */
register_sidebar(array(
    'name' => 'Primary Sidebar',
    'id' => 'sidebar-1',
    'description' => 'Main sidebar that appears on the right.',
    'before_widget' => '<div id="%1$s" class="box %2$s">',
    'after_widget' => '</div></div>',
    'before_title' => '<h3 class="widget-title">',
    'after_title' => '</h3><div class="box-content">',
));
register_sidebar(array(
    'name' => 'Footer Sidebar',
    'id' => 'sidebar-2',
    'description' => 'Sidebar that appears on the bottom',
    'before_widget' => '<div class="footerwidget">',
    'after_widget' => '</div>'
));

/*
 * Add context for Timber
 */
add_filter('timber_context', 'add_to_context');
function add_to_context($data)
{
    $data['THEME_URL'] = THEME_URL;
    $data['homeurl'] = get_site_url();
    if (ICL_LANGUAGE_CODE != 'en') {
        $data['homeurl'] .= '/' . ICL_LANGUAGE_CODE;
    }
    // Menus
    $data['topmenu'] = new TimberMenu(36);
    $data['menu'] = new TimberMenu(35);
    $data['footer1'] = Timber::get_terms('category', array('parent' => 9)); // Region
    $data['footer2'] = Timber::get_terms('category', array('parent' => 11)); // Section
    // Sidebar
    $data['sidebar_right'] = Timber::get_widgets('sidebar-1');
    $data['sidebar_bottom'] = Timber::get_widgets('sidebar-2');
    // Languages WPML
    $data['languages'] = icl_get_languages('skip_missing=0');
    $data['language'] = $data['languages'][ICL_LANGUAGE_CODE];
    return $data;
}

/*
 * Load CSS and JS
 */
add_action('wp_enqueue_scripts', 'load_scripts');
function load_scripts()
{
    wp_enqueue_style('style', get_stylesheet_uri(), array(), '2.0');
    wp_enqueue_script('bootstrap', THEME_URL . '/js/bootstrap.min.js', array('jquery'), false, true);
    wp_enqueue_script('pressenza', THEME_URL . '/js/pressenza.js', array(), false, true);
}

/*
 * Custom gallery for bootstrap carousel - see views/gallery.twig
 */
add_shortcode('gallery', 'pressenza_gallery_shortcode');
function pressenza_gallery_shortcode($attr)
{
    $post = get_post();

    if (!empty($attr['ids'])) {
        if (empty($attr['orderby'])) {
            $attr['orderby'] = 'post__in';
        }
        $attr['include'] = $attr['ids'];
    }

    extract(shortcode_atts(array(
        'order' => 'ASC',
        'orderby' => 'menu_order ID',
        'id' => $post ? $post->ID : 0,
        'itemtag' => 'dl',
        'icontag' => 'dt',
        'captiontag' => 'dd',
        'columns' => 3,
        'size' => 'thumbnail',
        'include' => '',
        'exclude' => '',
        'link' => ''
    ), $attr, 'gallery'));

    $id = intval($id);
    if ('RAND' == $order) {
        $orderby = 'none';
    }

    if (!empty($include)) {
        $_attachments = get_posts(array(
            'include' => $include,
            'post_status' => 'inherit',
            'post_type' => 'attachment',
            'post_mime_type' => 'image',
            'order' => $order,
            'orderby' => $orderby));

        $attachments = array();
        foreach ($_attachments as $key => $val) {
            $attachments[$val->ID] = $_attachments[$key];
        }
    } elseif (!empty($exclude)) {
        $attachments = get_children(array(
            'post_parent' => $id,
            'exclude' => $exclude,
            'post_status' => 'inherit',
            'post_type' => 'attachment',
            'post_mime_type' => 'image',
            'order' => $order,
            'orderby' => $orderby));
    } else {
        $attachments = get_children(array(
            'post_parent' => $id,
            'post_status' => 'inherit',
            'post_type' => 'attachment',
            'post_mime_type' => 'image',
            'order' => $order,
            'orderby' => $orderby));
    }

    if (empty($attachments)) {
        return '';
    }

    $context = Timber::get_context();
    $context['attachments'] = $attachments;

    return Timber::compile('gallery.twig', $context);
}

/*
 * Shortcodes
 */

// disable the [media-credit] shortcodes, as explained here http://wordpress.org/plugins/media-credit/faq/
function ignore_media_credit_shortcode($atts, $content = null)
{
    return $content;
}

// Media credit shortcode for old articles
function media_credit_shortcode($atts, $content = null)
{
    extract(shortcode_atts(array('name' => ''), $atts));
    $output = '<div class="wp-caption alignnone">' . $content . '<p class="wp-caption-text">' . $name . '</p></div>';
    return $output;
}

if (!array_key_exists('media-credit', $shortcode_tags)) {
    add_shortcode('media-credit', 'media_credit_shortcode');
}

// Image shortcode for old articles
function image_shortcode($atts, $content = null)
{
    extract(shortcode_atts(array('src' => '', 'width' => '', 'height' => ''), $atts));
    return '<img class="alignnone size-large" src="' . $src . '" alt="" width="' . $width . '" height="' . $height . '" />';
}

if (!array_key_exists('image', $shortcode_tags)) {
    add_shortcode('image', 'image_shortcode');
}

/*
 * Tag Cloud Settings
 */
function pressenza_tag_cloud_args($args)
{
    $args['largest'] = 24;
    $args['smallest'] = 10;
    $args['unit'] = 'px';
    return $args;
}

add_filter('widget_tag_cloud_args', 'pressenza_tag_cloud_args');

/*
 * Translate Widget Title
 * Only for the term Archives ...
 */
function translate_title($title)
{
    if ($title == 'Archives') {
        return __($title);
    } else {
        return $title;
    }
}

add_filter('widget_title', 'translate_title');

/*
 * Add Thumbnails for RSS
 */
function insertThumbnailRSS($content)
{
    global $post;
    if (has_post_thumbnail($post->ID)) {
        $content = '' . get_the_post_thumbnail($post->ID, 'thumbnail') . '' . $content;
    }
    return $content;
}

add_filter('the_excerpt_rss', 'insertThumbnailRSS');
add_filter('the_content_feed', 'insertThumbnailRSS');


/*
 *  Cleanup Frontend output and remove unneeded CSS and JS
 */

// Remove from WP-View Plugin
function wsis_remove_wpv_frontend_enqueue_scripts()
{
    wp_dequeue_script('views-pagination-script');
    wp_dequeue_style('views-pagination-style');
    wp_dequeue_style('wptoolset-field-datepicker');
}

add_action('wp_enqueue_scripts', 'wsis_remove_wpv_frontend_enqueue_scripts', 20);

// Remove Emoji CSS and JS
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');

// Remove meta generator tag
remove_action('wp_head', 'wp_generator');
global $sitepress;
remove_action('wp_head', array($sitepress, 'meta_generator_tag'));

/*
* Remove wp-embed.js
*/
function my_deregister_scripts()
{
    wp_deregister_script('wp-embed');
}

add_action('wp_footer', 'my_deregister_scripts');

/*
* Add defer to JavaScript for non-blocking loading
* see https://wpshout.com/make-site-faster-async-deferred-javascript-introducing-script_loader_tag/
*/
add_filter('script_loader_tag', 'wsds_defer_scripts', 10, 3);
function wsds_defer_scripts($tag, $handle, $src)
{
    $defer_scripts = array(
        'bootstrap',
        'pressenza'
    );

    if (in_array($handle, $defer_scripts)) {
        return '<script src="' . $src . '" defer="defer" type="text/javascript"></script>' . "\n";
    }

    return $tag;
}

/*
 * Remove unneeded CSS from Jetpack
 */
// First, make sure Jetpack doesn't concatenate all its CSS
//add_filter( 'jetpack_implode_frontend_css', '__return_false' );
// Then, remove each CSS file, one at a time
//function remove_jetpack_styles() {
//  wp_deregister_style( 'AtD_style' ); // After the Deadline
//  wp_deregister_style( 'jetpack_likes' ); // Likes
//  wp_deregister_style( 'jetpack_related-posts' ); //Related Posts
//  wp_deregister_style( 'jetpack-carousel' ); // Carousel
//  wp_deregister_style( 'grunion.css' ); // Grunion contact form
//  wp_deregister_style( 'the-neverending-homepage' ); // Infinite Scroll
//  wp_deregister_style( 'infinity-twentyten' ); // Infinite Scroll - Twentyten Theme
//  wp_deregister_style( 'infinity-twentyeleven' ); // Infinite Scroll - Twentyeleven Theme
//  wp_deregister_style( 'infinity-twentytwelve' ); // Infinite Scroll - Twentytwelve Theme
//  wp_deregister_style( 'noticons' ); // Notes
//  wp_deregister_style( 'post-by-email' ); // Post by Email
//  wp_deregister_style( 'publicize' ); // Publicize
//  wp_deregister_style( 'sharedaddy' ); // Sharedaddy
//  wp_deregister_style( 'sharing' ); // Sharedaddy Sharing
//  wp_deregister_style( 'stats_reports_css' ); // Stats
//  wp_deregister_style( 'jetpack-widgets' ); // Widgets
//  wp_deregister_style( 'jetpack-slideshow' ); // Slideshows
//  wp_deregister_style( 'presentations' ); // Presentation shortcode
//  wp_deregister_style( 'jetpack-subscriptions' ); // Subscriptions
//  wp_deregister_style( 'tiled-gallery' ); // Tiled Galleries
//  wp_deregister_style( 'widget-conditions' ); // Widget Visibility
//  wp_deregister_style( 'jetpack_display_posts_widget' ); // Display Posts Widget
//  wp_deregister_style( 'gravatar-profile-widget' ); // Gravatar Widget
//  wp_deregister_style( 'widget-grid-and-list' ); // Top Posts widget
//  wp_deregister_style( 'jetpack-widgets' ); // Widgets
//}
//add_action('wp_print_styles', 'remove_jetpack_styles' );

?>