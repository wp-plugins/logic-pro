<?php
add_Action ('init', 'lp_vc_maps');

function lp_vc_maps() {

if (function_exists('vc_map'))
{
vc_map( array(
  "name" => __("Logic Lite Snippet", "rch"),
  "base" => "logic_lite_snippet",
  "icon" => "icon-wpb-vc_engage",
  "category" => __('Logic Lite Plugin', "rch"),
  "params" => array(
    
	array(
		  "type" => "dropdown",
		  "heading" => __("Choose Snippets", "rch"),
		  "param_name" => "snippets",
		  "value" => array(__("3 - Default", "rch") => 3, 1, 2, 4),
		  "description" => __('', "rch")
		),
  )
) );

}
	
}