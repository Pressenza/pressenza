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
$context['postcats'] = get_the_category_list(', ', '', $post->ID);
$context['posttags'] = get_the_tag_list('Tags', ',');
$context['authorposts'] = count_user_posts($post->post_author);
$context['authoravatar'] = get_avatar($post->post_author, 70);
$context['wp_title'] .= ' - ' . $post->title();

$exp['postedby'] = __('Posted by', 'pressenza');
$exp['postedin'] = __('in', 'pressenza');
$exp['previous'] = __('Previous', 'pressenza');
$exp['next'] = __('Next', 'pressenza');
$exp['ata'] = __('About The Author', 'pressenza');
$context['exp'] = $exp;

Timber::render('single.twig', $context);
