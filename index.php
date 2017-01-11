<?php
/**
 * The main template file
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file
 *
 * Methods for TimberHelper can be found in the /functions sub-directory
 *
 * @package    WordPress
 * @subpackage    Timber
 * @since        Timber 0.1
 */

if (!class_exists('Timber')) {
    echo 'Timber not activated. Make sure you activate the plugin in <a href="/wp-admin/plugins.php#timber">/wp-admin/plugins.php</a>';
}

//var_dump( get_intermediate_image_sizes() );

$context = Timber::get_context();

// get Featured articles for image slider from category 11385
// check if image size is a least 750 x 415 pixel
$featuredposts = get_posts(array('numberposts' => '15', 'cat' => 11385, 'suppress_filters' => 0));
$featured = array();
$counter = 1;
foreach ($featuredposts as $fp) {
    if ($counter < 8) {
        $img = wp_get_attachment_image_src(get_post_thumbnail_id($fp->ID), 'featured');
        if (is_array($img)) {
            if ($img[1] >= 750 && $img[2] >= 415) {
                //print_r($fp);
                $featured[] = array(
                    'id' => $fp->ID,
                    'title' => $fp->post_title,
                    'text' => substr(strip_tags($fp->post_content), 0 , 180) .'…',
                    'url' => get_permalink($fp),
                    'img' => $img[0]
                );
                $counter++;
            }
        }
    } else {
        break;
    }
}
//$context['featured'] = Timber::get_posts(array('numberposts' => '8', 'cat' => 11385, 'suppress_filters' => 0));
$context['featured'] = $featured;

// get Latest articles
$context['latest'] = Timber::get_posts(array('numberposts' => '5', 'suppress_filters' => 0));

// get Opinions (Category 160)
$context['opinions'] = Timber::get_posts(array('numberposts' => '3', 'cat' => 160, 'suppress_filters' => 0));

// get Interviews (Category 154)
$context['interviews'] = Timber::get_posts(array('numberposts' => '3', 'cat' => 19316, 'suppress_filters' => 0));

// get Sections and recent posts
$transient = 'latest_by_sections_' . ICL_LANGUAGE_CODE;
if (false === ($seccolumn = get_transient($transient))) {
    $c = 0;
    $s = 0;
    $seccolumn = array(0 => '', 1 => '');
    $categories = get_categories(array('child_of' => get_cat_ID("Section")));
    $percolumn = ceil(count($categories) / 2);
    foreach ($categories as $category) {
        $posts = wp_get_recent_posts(array('numberposts' => '3', 'category' => $category->cat_ID, 'post_status' => 'publish', 'suppress_filters' => 0));
        if (count($posts) > 0) {
            $seccolumn[$s] .= '<div class="sections"><h2><a href="' . get_category_link($category->term_id) . '">' . $category->name . '</a></h2><ul>';
            foreach ($posts as $post) {
                $seccolumn[$s] .= '<li><a href="' . get_permalink($post["ID"]) . '">' . $post["post_title"] . '</a></li>';
            }
            $seccolumn[$s] .= '</ul></div>';
            $c++;
            if ($c == $percolumn) {
                $s = 1;
            }
        }
    }
    set_transient($transient, $seccolumn, 600);
}
$context['seccolumn'] = $seccolumn;
$context['wp_title'] = 'Pressenza';

Timber::render('home.twig', $context);