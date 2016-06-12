<?php
/**
 * The Template for displaying all single posts
 *
 * Methods for TimberHelper can be found in the /functions sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */

$context = Timber::get_context();
$post = new TimberPost();
$context['post'] = $post;

// add page title
$context['wp_title'] = 'Pressenza - ' . $post->title();

// get author informations
$context['authorposts'] = count_user_posts($post->post_author);
$context['authoravatar'] = get_avatar($post->post_author, 70);

// get other languages of this post
$otherlanguages = '';
$languages = icl_get_languages('skip_missing=1');
if (1 < count($languages)) {
    $langs = array();
    foreach ($languages as $l) {
        if (!$l['active']) {
            $langs[] = '<a href="' . $l['url'] . '">' . $l['translated_name'] . '</a>';
        }
    }
    $otherlanguages = implode(', ', $langs);
    unset($langs);
}
$context['otherlanguages'] = $otherlanguages;

// get post categories
$postcategories = wp_get_post_categories($post->ID);
$cats = array();
$catexlude = array(11385, 11386, 11387, 11388, 11389, 11390, 19112);
$needles = array('@de', '@es', '@fr', '@pt');
foreach ($postcategories as $c) {
    if (!in_array($c, $catexlude)) {
        $cat = get_category($c);
        $cats[] = '<a href="' . get_category_link($cat->term_id) . '">' . str_replace($needles, NULL, $cat->name) . '</a>';
    }
}
$context['postcats'] = implode(', ', $cats);

// get post tags
$context['posttags'] = get_the_tag_list('Tags: ', ', ');

Timber::render('single.twig', $context);
