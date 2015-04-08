<?php

function lp_get_post_meta($id)
{
	$settings = get_post_meta($id, 'lp_settings', true);
	return $settings;
}

function lp_post_type_tags($ret_as = 'array' ) {
    global $wpdb;

    $result_obj = $wpdb->get_results( "
        SELECT COUNT( DISTINCT tr.object_id ) 
            AS count, tt.taxonomy, tt.description, tt.term_taxonomy_id, t.name, t.slug, t.term_id 
        FROM {$wpdb->posts} p 
        INNER JOIN {$wpdb->term_relationships} tr 
            ON p.ID=tr.object_id 
        INNER JOIN {$wpdb->term_taxonomy} tt 
            ON tt.term_taxonomy_id=tr.term_taxonomy_id 
        INNER JOIN {$wpdb->terms} t 
            ON t.term_id=tt.term_taxonomy_id 
        WHERE p.post_type= 'logicblock' 
            AND tt.taxonomy='post_tag' 
        GROUP BY tt.term_taxonomy_id 
        ORDER BY count DESC
    ");
	
	
	
	if ($ret_as == 'object')
	{
		return $result_obj;
	}
	else
	{
		$result_arr = array();
		foreach( $result_obj as $tag ) {
 	   		 $result_arr[] = $tag->name;
		}
		return $result_arr;
	}
	
}

function lp_log($logmsg)
{
   $curlog = substr(get_option('lp_debug_log'),-60000);
   list($msec , $sec) = explode(" " , microtime());
   $curlog .= date('n/d/y H:i:s',$sec) . ' : ' . trim($logmsg) .'<br>';
   update_option('lp_debug_log',$curlog);
}


if ( !class_exists( 'Walker_PageDropdown_Multiple' ) ) {
  /** Create HTML dropdown list of pages. */
  class Walker_PageDropdown_Multiple extends Walker_PageDropdown {

    function start_el(&$output, $page, $depth=0, $args=Array(), $id = 0) {
      $pad = str_repeat( isset( $args['pad'] ) ? $args['pad'] : '--', $depth );
 
      $output .= "\t<option class=\"level-$depth\" value=\"$page->ID\"";
      if ( in_array( $page->ID, (array) $args['selected'] ) )
        $output .= ' selected="selected"';
      $output .= '>';
      $pagetitle = apply_filters( 'list_pages', $page->post_title, $page );
      $pagetitle = apply_filters( 'pagedropdown_multiple_title', $pagetitle, $page, $args );
      $output .= $pad . ' ' . esc_html( $pagetitle );
      $output .= "</option>\n";
    }
  }
}
if ( !class_exists( 'Walker_CategoryDropdown_Custom' ) ) {
	/** Create HTML dropdown list of categories. */
class Walker_CategoryDropdown_Custom extends Walker_CategoryDropdown {

    function start_el(&$output, $category, $depth=0, $args=Array(), $id = 0) {
      $pad = str_repeat( isset( $args['pad'] ) ? $args['pad'] : '--', $depth );
 
      $output .= "\t<option class=\"level-$depth\" value=\"$category->term_taxonomy_id\"";
      if ( in_array( $category->term_taxonomy_id, (array) $args['selected'] ) )
        $output .= ' selected="selected"';
      $output .= '>';
      $cattitle = apply_filters( 'list_cats', $category->name, $category );
      $cattitle = apply_filters( 'cat_dropdown_multiple', $cattitle, $category, $args );
      $output .= $pad . ' ' . esc_html( $cattitle );
      $output .= "</option>\n";
    }
	
  }
}

function lp_upgrade_msg()
{
	return '
<div>
		<a href="#close" title="Close" class="close">X</a>
		<h2>You Need To Upgrade To Pro For This Feature!</h2>
            <p>We hope you have found the basic Visitor Logic Lite features helpful. If you would like to unlock the advanced features and integrations of Visitor Logic Lite, please visit <a href="http://VisitorLogicPro.com" target="_blank">VisitorLogicPro.com</a> to obtain a license code for the advanced features.</p>
</div>
';
}
?>