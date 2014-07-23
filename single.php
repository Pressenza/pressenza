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

$postcategories = wp_get_post_categories($post->ID);
$cats = array();
$catexlude = array(9609, 9610, 9611, 9612, 9613, 9614);
$needles = array('@de','@es','@fr','@pt');
foreach($postcategories as $c)
{
	if(!in_array($c, $catexlude))
	{
	$cat = get_category($c);
	$cats[] = '<a href="' . get_category_link($cat->term_id) . '">' . str_replace($needles, NULL, $cat->name) . '</a>';
	}
}
$context['postcats'] = implode(', ', $cats);
//$context['postcats'] = get_the_category_list(', ', '', $post->ID);
$context['posttags'] = get_the_tag_list('Tags: ', ', ');
$context['authorposts'] = count_user_posts($post->post_author);
$context['authoravatar'] = get_avatar($post->post_author, 70);
$context['wp_title'] = 'Pressenza - ' . $post->title();

Timber::render('single.twig', $context);
