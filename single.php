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
foreach($postcategories as $c)
{
	if($c != 9609)
	{
	$cat = get_category($c);
	$cats[] = '<a href="' . get_category_link( $cat->term_id ) . '">' . $cat->cat_name . '</a>';
	}
}
$context['postcats'] = implode(', ', $cats);
//$context['postcats'] = get_the_category_list(', ', '', $post->ID);
$context['posttags'] = get_the_tag_list('Tags: ', ', ');
$context['authorposts'] = count_user_posts($post->post_author);
$context['authoravatar'] = get_avatar($post->post_author, 70);
$context['wp_title'] .= ' - ' . $post->title();

$exp['postedby'] = __('Posted by', 'pressenza');
$exp['postedin'] = __('in', 'pressenza');
$exp['categories'] = __('Categories', 'pressenza');
$exp['ata'] = __('About The Author', 'pressenza');
$context['exp'] = $exp;

Timber::render('single.twig', $context);
