<?php
/**
 * Search results page
 *
 * Methods for TimberHelper can be found in the /functions sub-directory
 *
 * @package 	WordPress
 * @subpackage 	Timber
 * @since 		Timber 0.1
 */


	$templates = array('search.twig', 'archive.twig', 'index.twig');
	$context = Timber::get_context();

	$context['title'] = get_search_query();
	$context['posts'] = Timber::get_posts();
    $context['pagination'] = Timber::get_pagination();

	Timber::render($templates, $context);
