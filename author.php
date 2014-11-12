<?php
/**
 * The template for displaying Author Archive pages
 *
 * Methods for TimberHelper can be found in the /functions sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */
global $wp_query;

$data = Timber::get_context();
$data['posts'] = Timber::get_posts();
$data['pagination'] = Timber::get_pagination();

//$curauth = (get_query_var('author_name')) ? get_user_by('slug', get_query_var('author_name')) : get_userdata(get_query_var('author'));
//print_r($curauth);
//$user = get_userdata( get_the_author_meta('ID') );
//print_r($user);
//exit;
$author = new TimberUser($wp_query->query_vars['author']);
$data['author'] = $author;
$data['pagetitle'] = ' : ' . $author->name();
$data['title'] = $author->name();

Timber::render(array('author.twig', 'archive.twig'), $data);
