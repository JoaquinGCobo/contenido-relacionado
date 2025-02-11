<?php
/*
Plugin Name: Contenido Relacionado
Plugin URI: 
Description: Plugin para WordPress creado a partir del código de Jhon Marreros Guzman, autor de la web Decodecms.com
Version: 0.1
Author: Jhon Marreros Guzman
Author URI: https://github.com/jmarreros/posts-relacionados#posts-relacionados
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
*/
function cr_css(){
	wp_register_style('plugin-styles', plugins_url('plugin-styles.css', __FILE__));
	wp_enqueue_style('plugin-styles');
}
add_action('wp_enqueue_scripts','cr_css');
  
 function dc_related_after_content( $content ) 
 { 
    
    if ( !is_singular('post') ) return $content;	
	
	$cad			= "";
	$template_li 	= '<li>
							<a class="thumb_rel" href="{url}">{thumb}</a>
							<a class="title_rel" href="{url}">{title}</a>
						</li>';
	$template_rel	= '<div class="rel_posts">
							<h3>Artículos Relacionados</h3>
							<ul>
								{list}
							</ul>
					   </div>';

    $terms = get_the_terms( get_the_ID(), 'category');
    $categ = array();
    
    if ( $terms )
    {
    	foreach ($terms as $term) 
    	{
    		$categ[] = $term->term_id;
    	}
    }
    else{
    	return $content;
    }

    $loop	= new WP_QUERY(array(
    				'category__in'		=> $categ,
    				'posts_per_page'	=> 4,
    				'post__not_in'		=>array(get_the_ID()),
    				'orderby'			=>'rand'
    				));

    if ( $loop->have_posts() )
    {

    	while ( $loop->have_posts() )
    	{
    		$loop->the_post();

    		$search	 = Array('{url}','{thumb}','{title}');
	  		$replace = Array(get_permalink(),get_the_post_thumbnail(),get_the_title());
    	
    		$cad .= str_replace($search,$replace, $template_li);
    	}

    	if ( $cad ) 
    	{
		  	$content .= str_replace('{list}', $cad, $template_rel);
    	}

    }
   	wp_reset_query();

    return $content;
}

add_filter( 'the_content', 'dc_related_after_content'); 	
