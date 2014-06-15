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
 * @package 	WordPress
 * @subpackage 	Timber
 * @since 		Timber 0.1
 */

	if (!class_exists('Timber'))
	{
		echo 'Timber not activated. Make sure you activate the plugin in <a href="/wp-admin/plugins.php#timber">/wp-admin/plugins.php</a>';
	}

	$context = Timber::get_context();

	if (is_home())
	{
		// get Featured (Category 10)
		$context['featured'] = Timber::get_posts(array( 'numberposts' => '8', 'category' => 10, 'suppress_filters' => 0 ));

		// get Latest
		$context['latest'] = Timber::get_posts(array( 'numberposts' => '5', 'suppress_filters' => 0 ));

		// get Opinions (Category 160)
		$context['opinions'] = Timber::get_posts(array( 'numberposts' => '3', 'category' => 160, 'suppress_filters' => 0 ));

		// get Interviews (Category 154)
		$context['interviews'] = Timber::get_posts(array( 'numberposts' => '3', 'category' => 154, 'suppress_filters' => 0 ));

		// get Sections and recent posts
		$c = 0;
		$s = 0;
		$seccolumn = array(0 => '', 1 => '');
		$categories = get_categories(array('child_of' => get_cat_ID ("Section")));
		$percolumn = ceil(count($categories)/2);
		foreach($categories as $category)
		{
			$posts = wp_get_recent_posts(array('numberposts' => '3', 'category' => $category->cat_ID, 'suppress_filters' => 0));
			if(count($posts) > 0)
			{
				$seccolumn[$s].= '<div class="sections"><h5><a href="' . get_category_link( $category->term_id ) . '">' . $category->name . '</a></h5><ul>';
				foreach($posts as $post)
				{
					$seccolumn[$s].= '<li><a href="' . get_permalink($post["ID"]) . '">' . $post["post_title"] . '</a></li>';
				}
				$seccolumn[$s].= '</ul></div>';
				$c++;
				if($c == $percolumn)
				{
					$s = 1;
				}
			}
		}
		$context['seccolumn'] = $seccolumn;

		$exp['opinions'] = __('Opinions', 'pressenza');
		$exp['interviews'] = __('Interviews', 'pressenza');
		$exp['latest'] = __('Latest News', 'pressenza');
		$context['exp'] = $exp;

		$templates = array('home.twig');
	}
	else
	{
		/*$post = new TimberPost();
		$context['posts'] = $post;
		$templates = array('index.twig');
		*/
	}

	Timber::render($templates, $context);