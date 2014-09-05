<?php
add_theme_support('post-formats');
add_theme_support('post-thumbnails');
add_theme_support('menus');

add_filter('timber_context', 'add_to_context');

add_action('wp_enqueue_scripts', 'load_scripts');

define('THEME_URL', get_template_directory_uri());

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

// Translatables in order to create a po-file
/*$e1 = __('Read articles by region', 'pressenza');
$e1 = __('Read articles by section', 'pressenza');
$e1 = __('Opinions', 'pressenza');
$e1 = __('Interviews', 'pressenza');
$e1 = __('Latest News', 'pressenza');
$e1 = __('Posted by', 'pressenza');
$e1 = __('Categories', 'pressenza');
$e1 = __('About The Author', 'pressenza');
$e1 = __('Number of Entries', 'pressenza');
$e1 = __('Image by', 'pressenza');
$e1 = __('The original article can be found on our partner\'s website here', 'pressenza');
*/
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
    $data['footerregion'] = new TimberMenu(9606);
    $data['footersection'] = new TimberMenu(9607);
    // Sidebar
    $data['sidebar_right'] = Timber::get_widgets('sidebar-1');
    $data['sidebar_bottom'] = Timber::get_widgets('sidebar-2');
    // Languages WPML
    $data['languages'] = icl_get_languages('skip_missing=0&orderby=code');
    $data['language'] = $data['languages'][ICL_LANGUAGE_CODE];
    return $data;
}

function load_scripts()
{
    wp_enqueue_style('bootstrap', THEME_URL . '/vendor/bootstrap/css/bootstrap.min.css', array(), '3.2');
    wp_enqueue_style('style', get_stylesheet_uri());
    wp_enqueue_script('bootstrap', THEME_URL . '/vendor/bootstrap/js/bootstrap.min.js', array(), false, true);
    wp_enqueue_script('pressenza', THEME_URL . '/js/pressenza.js', array(), false, true);
}

// CUSTOM GALLERY
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
 * disable the [media-credit] shortcodes, as explained here http://wordpress.org/plugins/media-credit/faq/
 */
function ignore_media_credit_shortcode($atts, $content = null)
{
    return $content;
}

global $shortcode_tags;
if (!array_key_exists('media-credit', $shortcode_tags)) {
    add_shortcode('media-credit', 'ignore_media_credit_shortcode');
}
