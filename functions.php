<?php
	add_theme_support('post-formats');
	add_theme_support('post-thumbnails');
	add_theme_support('menus');

	//add_filter('get_twig', 'add_to_twig');
	add_filter('timber_context', 'add_to_context');
	add_filter('post_gallery', 'pressenza_gallery', 10, 2);

	add_action('wp_enqueue_scripts', 'load_scripts');

	define('THEME_URL', get_template_directory_uri());

	register_sidebar( array(
		'name'          => 'Primary Sidebar',
		'id'            => 'sidebar-1',
		'description'   => 'Main sidebar that appears on the right.',
		'before_widget' => '<div id="%1$s" class="box %2$s">',
		'after_widget'  => '</div></div>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3><div class="box-content">',
	) );
	register_sidebar( array(
		'name'          => 'Footer Sidebar',
		'id'            => 'sidebar-2',
		'description'   => 'Sidebar that appears on the bottom',
		'before_widget' => '<div class="col-xs-12 col-md-4">',
		'after_widget'  => '</div>'
	) );

	// Translatables in order to crste a po-file
	$e1 = __('Read articles by region', 'pressenza');
	$e1 = __('Read articles by section', 'pressenza');
	$e1 = __('Opinions', 'pressenza');
	$e1 = __('Interviews', 'pressenza');
	$e1 = __('Latest News', 'pressenza');
	$e1 = __('Posted by', 'pressenza');
	$e1 = __('Categories', 'pressenza');
	$e1 = __('About The Author', 'pressenza');
	$e1 = __('Number of Entries', 'pressenza');
	

	function add_to_context($data)
	{
		$data['THEME_URL'] = THEME_URL;
		$data['homeurl'] = get_site_url();
		if(ICL_LANGUAGE_CODE != 'en')
		{
			$data['homeurl'] .= '/'.ICL_LANGUAGE_CODE;
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

	function add_to_twig($twig)
	{
		/* this is where you can add your own fuctions to twig */
		$twig->addExtension(new Twig_Extension_StringLoader());
		$twig->addFilter('myfoo', new Twig_Filter_Function('myfoo'));
		return $twig;
	}

	function load_scripts()
	{
		wp_enqueue_style('bootstrap', THEME_URL . '/vendor/bootstrap/css/bootstrap.min.css', array(), '3.1.1');
		wp_enqueue_style('style', get_stylesheet_uri());

		wp_enqueue_script('bootstrap', THEME_URL . '/vendor/bootstrap/js/bootstrap.min.js', array(), false, true);
	}


	function pressenza_gallery($output, $attr)
	{
		global $post;

		if (isset($attr['orderby']))
		{
		    $attr['orderby'] = sanitize_sql_orderby($attr['orderby']);
		    if (!$attr['orderby'])
		    {
		        unset($attr['orderby']);
      		}
		}

		extract(shortcode_atts(array(
		    'order' => 'ASC',
		    'orderby' => 'menu_order ID',
		    'id' => $post->ID,
		    'itemtag' => 'dl',
		    'icontag' => 'dt',
		    'captiontag' => 'dd',
		    'columns' => 10,
		    'size' => 'thumbnail',
		    'include' => '',
		    'exclude' => ''
		), $attr));

		$id = intval($id);
		if ('RAND' == $order) $orderby = 'none';

		if (!empty($include))
		{
		    $include = preg_replace('/[^0-9,]+/', '', $include);
		    $_attachments = get_posts(array(
				'include' => $include,
				'post_status' => 'inherit',
				'post_type' => 'attachment',
				'post_mime_type' => 'image',
				'order' => $order,
				'orderby' => $orderby
			));

		    $attachments = array();
		    foreach ($_attachments as $key => $val)
			{
		        $attachments[$val->ID] = $_attachments[$key];
		    }
		}

		if (empty($attachments)) return '';

		// Here's your actual output, you may customize it to your need
		$output = '<div id="featured" class="carousel slide" data-ride="carousel">
			<div class="carousel-inner">';
			
		$counter = 1;
		// Now you loop through each attachment
		foreach ($attachments as $id => $attachment)
		{
		    // Fetch all data related to attachment
		    $img = wp_prepare_attachment_for_js($id);

		    // Store the caption
		    //$caption = $img['caption'].' - ES: '.$img['caption_es'];
		    $caption = $img['caption'];
		    
		    if($counter == 1)
		    {
		    	$output .= '<div class="item active">';
		    	$counter++;
		    } else {
		    	$output .= '<div class="item">';
		    }

		    $output .= '<img src="'.$img['sizes']['large']['url'].'" alt="'.$img['alt'].'" />'."\n";

		    // Output the caption if it exists
		    if ($caption)
			{
		        $output .= '<div class="carousel-caption">'.$caption.'</div>';
		    }
		    $output .= '</div>'."\n";
		}

		$output .= "</div>\n";
		$output .= '<a class="left carousel-control" href="#featured" data-slide="prev"><span class="glyphicon glyphicon-chevron-left"></span></a>
			<a class="right carousel-control" href="#featured" data-slide="next"><span class="glyphicon glyphicon-chevron-right"></span></a>
			</div>'."\n";

		return $output;
	}

