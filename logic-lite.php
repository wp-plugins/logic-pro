<?php
/*
  Plugin Name: Visitor Logic Lite
  Plugin URI: http://VisitorLogicPro.com
  Description: Visitor Logic Lite is a fully adaptable WordPress plugin that allows you to define an unlimited number of “Logic Blocks” or “group of settings” that allow you to determine when to display or to hide a block of content based on how a visitor is using your site.
  Author: RCH Tech Solutions 
  Version: 1.0.1
 */

define ('LP_DOMAIN','logicLite');	
define ('LP_ROOT', plugin_dir_path( __FILE__ ) );
define ('LP_ROOT_URL', plugin_dir_url( __FILE__ ) );

 
include( LP_ROOT . 'inc/lp_widget.php');
include( LP_ROOT . 'inc/lp_functions.php');
include( LP_ROOT . 'inc/lp_core.php');
	
add_action( 'admin_menu', 'logic_lite_plugin_admin_menu' );

add_action( 'admin_enqueue_scripts', 'logic_lite_plugin_scripts' );
add_action( 'admin_enqueue_scripts', 'logic_lite_plugin_styles' );

add_action( 'admin_init', 'logic_lite_register_settings');

function logic_lite_plugin_scripts() {
    global $post_type;
	{
		wp_register_script( 'logic_lite_plugin_js', plugins_url( 'js/logic-lite.js', __FILE__ ), array( 'jquery' ),NULL,true );
		wp_register_script( 'logic_lite_plugin_timepicker_js', plugins_url( 'js/timepicker.js', __FILE__ ), array( 'jquery', 'jquery-ui-datepicker' ),NULL,true );
		
		wp_enqueue_script( 'logic_lite_plugin_js' );
		wp_enqueue_script( 'jquery-ui-autocomplete');
		wp_enqueue_script( 'jquery-ui-datepicker');
		wp_enqueue_script( 'logic_lite_plugin_timepicker_js');
		
		add_action('admin_head', 'lp_wp_head');
	}
	
}

function logic_lite_plugin_styles($hook) {
    global $post_type;
	{
    	wp_enqueue_style( 'lp_style', plugins_url( '/css/lp_style.css', __FILE__ ));
	}
}

   
function logic_lite_plugin_admin_menu() {
       /* Register plugin page */
   $page = add_submenu_page( 'edit.php', 
                             __( 'Visitor Logic Lite', LP_DOMAIN ), 
                             __( 'Visitor Logic Lite', LP_DOMAIN ),
                             'administrator',
                             'LOGICLITEMAIN', 
                             'logic_lite_plugin_manage_menu' );

	add_action( 'admin_print_styles-' . $page, 'logic_lite_plugin_admin_styles' );

	if (is_admin())
	{
		$options_page = add_submenu_page('edit.php?post_type=logicblock', 'Logic Lite Options', 'Options',  'administrator', __FILE__, 'logic_lite_admin_options');
	}
		
}

function logic_lite_register_settings() { // whitelist options
	register_setting( 'logic_lite_group', 'lp_global_debug');
	register_setting( 'logic_lite_group', 'lp_bypass_cache');
}

function logic_lite_admin_options() {
       /* Output admin page */
	   include(plugin_dir_path( __FILE__ ) . 'inc/lp_options.php');
   }


function lp_wp_head() {
?>
<script type="text/javascript">
    var se_ajax_url = '<?php echo admin_url('admin-ajax.php'); ?>';

</script>
<?php
}

function lp_wp_footer() {
	
	global $lp_core;
	$lp_core->track_posts();
	$trackstr = urlencode(json_encode($lp_core->tracking));
	echo "<!-- Tracking " . print_r($lp_core->tracking,true) ." -->";
	$tracking = "
	<img width=1 height=1 style='position:absolute' src='" . plugins_url('images/lpt.php?s=' . $trackstr, __FILE__) . "' />";
	echo $tracking;
}

add_action ('wp_footer', 'lp_wp_footer', 20);


add_action('wp_ajax_lp_lookup', 'lp_lookup');
add_action('wp_ajax_nopriv_lp_lookup', 'lp_lookup');

function lp_lookup() {
	
    global $wpdb;

    $search = like_escape($_REQUEST['q']);
    $query = 'SELECT ID,post_title FROM ' . $wpdb->posts . '
        WHERE post_title LIKE \'%' . $search . '%\'
        AND post_type = \'post\'
        AND post_status = \'publish\'
        ORDER BY post_title ASC';
	$retArr = array();
	foreach ($wpdb->get_results($query) as $row) {
        $post_title = $row->post_title;
        $id = $row->ID;
		
		$retArr[] = array('value'=>$id, 'label'=>$post_title);
    }
	
	echo json_encode($retArr);
    die();
}

add_action('wp_ajax_lp_track', 'lp_track');
add_action('wp_ajax_nopriv_lp_track', 'lp_track');

function lp_track () {
	
	// ajax call to set cookie for display tracking purposes.
	$block_id = $_REQUEST['b'];
	if (!is_numeric($block_id) )
	{
		exit;
	}
	$block_tracker = unserialize($_COOKIE['lpblocks']);
	$block_tracker[$block_id] = $block_tracker[$block_id] + 1;
	setcookie('lpview', serialize($block_tracker), time()+86400 * 365);
	exit;
	
}

add_action('wp_ajax_lp_clearCache', 'lp_clearCache');
add_action('wp_ajax_nopriv_lp_clearCache', 'lp_clearCache');

function lp_clearCache() {
	global $wpdb;
$sql = "
		delete FROM wp_options WHERE option_name LIKE '_transient_%' and option_name like '%_lp_%'
	";

	$clean = $wpdb -> query( $sql );
	echo $clean;		
	die();
}

function lp_duplicate_post() {
	
	// Get access to the database
	global $wpdb;
	
	// Check the nonce
	check_ajax_referer( 'lp_ajax_file_nonce', 'security' );
	
	// Get variables
	$original_id  = $_POST['original_id'];
	
	// Get the post as an array
	$duplicate = get_post( $original_id, 'ARRAY_A' );
	
	// Modify some of the elements
	$duplicate['post_title'] = $duplicate['post_title'].' Copy';
	
	// Set the status
	$duplicate['post_status'] = 'draft';
	
	// Set the post date
	$timestamp = current_time('timestamp',0);
	
	$duplicate['post_date'] = date('Y-m-d H:i:s', $timestamp);

	// Remove some of the keys
	unset( $duplicate['ID'] );
	unset( $duplicate['guid'] );
	unset( $duplicate['comment_count'] );

	// Insert the post into the database
	$duplicate_id = wp_insert_post( $duplicate );
	
	// Duplicate all the taxonomies/terms
	$taxonomies = get_object_taxonomies( $duplicate['post_type'] );
	foreach( $taxonomies as $taxonomy ) {
		$terms = wp_get_post_terms( $original_id, $taxonomy, array('fields' => 'names') );
		wp_set_object_terms( $duplicate_id, $terms, $taxonomy );
	}

	// Duplicate all the custom fields
	$custom_fields = get_post_custom( $original_id );
  foreach ( $custom_fields as $key => $value ) {
		add_post_meta( $duplicate_id, $key, maybe_unserialize($value[0]) );
  }

	echo 'Duplicate Post Created!';

	die(); // this is required to return a proper result
}

add_filter( 'post_row_actions', 'lp_post_duplicator_action_row', 10, 2 );
add_filter( 'page_row_actions', 'lp_post_duplicator_action_row', 10, 2 );
/**
 * Add a duplicate post link.
 *
 * @since 1.0.0
 */
function lp_post_duplicator_action_row( $actions, $post ){

	// Get the post type object
	$post_type = get_post_type_object( $post->post_type );
	
	// Create a nonce & add an action
	$nonce = wp_create_nonce( 'lp_ajax_file_nonce' ); 
	$actions['duplicate_post'] = '<a class="lp-duplicate-post" rel="'.$nonce.'" href="'.$post->ID.'">Duplicate '.$post_type->labels->singular_name.'</a>';

	return $actions;
}


function init_custom_post_types() {
    $labels = array(
        'name' => _x('Logic Blocks', 'post type general name'),
        'singular_name' => _x('Logic Block', 'post type singular name'),
        'add_new' => _x('Add New Logic Block', 'Logic Blocks'),
        'add_new_item' => __('New Logic Block'),
        'edit_item' => __('Edit Logic Block'),
        'new_item' => __('New Logic Block'),
        'all_items' => __('All Logic Blocks'),
        'view_item' => __('View Logic Block'),
        'search_items' => __('Search Logic Blocks'),
        'not_found' => __('No Logic Blocks found'),
        'not_found_in_trash' => __('No Logic Blocks found in Trash'),
        'parent_item_colon' => '',
        'menu_name' => __('Visitor Logic Lite')
    );
    $args = array(
        'labels' => $labels,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'rewrite' => true,
        'capability_type' => 'post',
        'has_archive' => false,
        'hierarchical' => false,
        'menu_position' => 20,
		'menu_icon' => plugins_url('images/VLP-icon-26.png', __FILE__),
        'supports' => array('title', 'editor', 'revisions'),
		'taxonomies' => array('post_tag')
    );

register_post_type('Logic Block', $args);
}

add_filter( 'manage_edit-logicblock_columns', 'lp_set_custom_edit_logicblock_columns' );
add_action( 'manage_logicblock_posts_custom_column' , 'custom_logicblock_column', 10, 2 );


function lp_set_custom_edit_logicblock_columns($columns) {

    $new_column = array('id' => 'ID', 'shortcode'=>'ShortCodes');
	
	$columns = array_slice( $columns, 0, 1, true ) + $new_column + array_slice( $columns, 1, NULL, true );
    return $columns;
}

function custom_logicblock_column( $column, $post_id ) {
    switch ( $column ) {
        case 'id' :
		case 'ID' :
            echo $post_id;
            break;
		case 'shortcode':
			echo '[lp-logic ids="' . $post_id . '" limit=1]<br>[lp-logic ids="' . $post_id . '" limit=1 use_content=1] Content to use [/lp-logic]';
			break;	

    }
}

add_filter("manage_edit-logicblock_sortable_columns", 'logicblock_sort');
function logicblock_sort($columns) {
	$custom = array(
		'id' 	=> 'id'
	);
	return wp_parse_args($custom, $columns);
}

function lp_custom_settings() {
    global $post;
	global $post_ID;
	
	wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css');
	
	$lp_settings = lp_get_post_meta($post->ID);
?>
<style>
.ba-3way {
 background: transparent url("<?php echo plugins_url("images/3-way.png", __FILE__) ?>") left top no-repeat;
	width: 70px;
	height: 70px;
}
.ba-3way-on {
	background-position: 0px 0px;
}

.ba-3way-off {
	background-position: -151.5px 0px;
}

.ba-3way-logic {
	background-position: -75.5px 0px;
}

.lp_row {
    float: left;
}

.lp_cell {
    float: left;
	padding: 2px 5px;
}

.clear{
    content: "";
	display: block;
	height: 0;
	clear: both;
	visibility: hidden;
}

</style>

<p>Shortcode to use for this Logic Block - [lp-logic ids='<?php echo $post_ID ?>']</p>

<div class="lp-tabs hide">
    <ul class="tab-links">
        <li class="first active" tab="1"><a href="#main">Main</a></li>
        <?php 
  global $lp_core;
  foreach ($lp_core->reg_modules as $lp_order => $lp_module)
  {

	 $classname = 'lp_' . $lp_module;
	 if (class_exists($classname))
	  { 
  	 	$lp_class = new $classname;
	 	echo "<li tab=". $lp_order ."><a class='" . ($lp_class->is_used($lp_settings) ? "lp_checked" : "") . "' href=#" . $classname .">" . $lp_class->title ."</a></li>";
	 }
 }

?>
    </ul>
</div>

<div id="lp_settings_area">

<div id="main" class="tabcontent lp_instructions active">
  <h3 >Main Menu</h3>
  <div class="inside">
  <ul class="home_links">
<li class="main_btn"><a href="#lp_general">General <img src="<?php echo plugins_url('images/general-icon.png', __FILE__); ?>" /></a></li>
<li class="main_btn"><a href="#lp_pages">Pages <img src="<?php echo plugins_url('images/pages-icon.png', __FILE__); ?>" /></a></li>
<li class="main_btn"><a href="#lp_posts">Posts <img src="<?php echo plugins_url('images/post-icon.png', __FILE__); ?>" /></a></li>
<li class="main_btn"><a href="#lp_categories">Categories <img src="<?php echo plugins_url('images/cat-icon.png', __FILE__); ?>" /></a></li>
<li class="main_btn"><a href="#lp_authors">Authors <img src="<?php echo plugins_url('images/author-icon.png', __FILE__); ?>" /></a></li>
<li class="main_btn"><a href="#lp_cookie">Cookies <img src="<?php echo plugins_url('images/cookie-icon.png', __FILE__); ?>" /></a></li>
<li class="main_btn no_float"><a href="#lp_wlm"><img src="<?php echo plugins_url('images/wishlist.png', __FILE__); ?>" /></li></a>
<li class="main_btn no_float"><a href="#lp_aweber"><img src="<?php echo plugins_url('images/aweber.png', __FILE__); ?>" /></li></a>
<li class="main_btn no_float"><a href="#lp_infusion"><img src="<?php echo plugins_url('images/infusionsoft.png', __FILE__); ?>" /></li></a>
<li class="main_btn no_float"><a href="#lp_getresponse"><img src="<?php echo plugins_url('images/get-response.png', __FILE__); ?>" /></li></a>
  </ul>

<div style="float:left">
    <p>Use these logic settings to define the conditions when this content block will and will not be displayed.</p>
    <p>With no settings selected, this content will always be displayed. The best way set up each content block is to start at the top setting, and work your way down the list of settings. Each setting is evaluated in this order, top to bottom. The first setting that is not met will cause the content block not to be displayed.</p>
    <p>Example: If you set the Login Status to &quot;Logged In&quot; and select a number of include pages, the logged in setting is evaluated first. Therefore, if the visitor is not logged in, the content block will not be displayed regardless of the rest of the settings. </p>
    <p>If a setting does not have a value set, (Example: Post fields are blank) they will not be used to determine if the content will be shown or not. </p>
    </div>
</div>
</div>

<div id="openUpsell" class="lp_modal">
<?php echo lp_upgrade_msg();?>
</div>

<input type="hidden" name="lp_hidden" value="true" />
<?php 
 global $lp_core;
 foreach ($lp_core->reg_modules as $lp_module)
 {
	 $classname = 'lp_' . strtoupper($lp_module);
	 if (class_exists($classname))
	  { 
  	 	$lp_class = new $classname;
		if (method_exists($lp_class, 'settings'))
		{
		 	$lp_class->settings($lp_settings);
		}
	 }
 }
 ?>
 </div>
<?php
 
}

function lp_add_meta_boxes() {
    add_meta_box('lp_meta_id', '<img class="lp-logo" src="'.plugins_url('images/Visitor-Logic-Lite.png', __FILE__).'" alt="'. __("Visitor Logic Lite Options", LP_DOMAIN) .'" />', 'lp_custom_settings', 'Logic Block', 'normal');
}

add_action('add_meta_boxes', 'lp_add_meta_boxes');
add_action('init', 'init_custom_post_types');


// Create Shortcode
function lp_logic_shortcode( $atts, $content ) {
	$options = shortcode_atts( array(
		'ids' => '',
		'limit' => 1,
		'debug' => false,
		'use_content' => false
        ), $atts ) ;

	$ids = explode(',' , str_replace(" ", "" ,$options['ids']));
    $cntr = 0;
	$use_content = $options['use_content'] ? $content : NULL;
	$content = "";
	$global_debug = get_option('lp_global_debug');
	$global_bypass = get_option('lp_bypass_cache');
	$options['debug'] = $options['debug'] ? $options['debug'] : $global_debug;
	global $lp_core;

	if (isset($global_bypass) && $global_bypass && !defined('DOING_AJAX') )
	{
		$uid = uniqid('lp_block_');
		$data = array();
		global $post;
		$post_type = get_post_type($post);
		$data[$post_type] = $post->ID;
		$cats = get_the_category($post->ID);
		foreach($cats as $cat)
		{
			$catlist[] = $cat->cat_ID;
		}
		
		$data['cats'] = $catlist;
		if (is_author() || is_single() || is_page())
		{
			$data['auth'] = get_the_author_meta('ID');
		}

		// Just echo a script to use ajax to try and display the block.
		$svar = "<div id='" . $uid . "'></div><script type='text/javascript'>
			var lpd = " . json_encode($data) . ";
			jQuery.ajax({
			type : 'POST',
			data : lpd,
			url : '" . admin_url('admin-ajax.php') . "?action=lp_get_block&ids=" . $options['ids'] ."',
			async : true
			})
		 	.done( function(data) {
			if (data)
		 	{
				jQuery('#$uid').html(data);
		 	}
       		});
				 </script>
				 ";
		return $svar;		 
	}

	foreach ($ids as $sel_block)
		{
			if ($sel_block)
			{
				$block = $lp_core->process_logic($sel_block, $options['debug'], $use_content);
				
				if ($block !== FALSE)
				{
					$content .= do_shortcode($block);
					$cntr++;
					
					if (!$options['debug'])
					{
						if ($cntr >= $options['limit'] || $use_content != NULL)
						{
							break;
						}
					}
				}
			}
		}
		
	return $content;	

}
add_shortcode( 'lp-logic', 'lp_logic_shortcode' );


add_action('wp_ajax_lp_get_block', 'lp_get_block');
add_action('wp_ajax_nopriv_lp_get_block', 'lp_get_block');

function lp_get_block() {
	$ids = $_GET['ids'];
	$content = lp_logic_shortcode(array('ids' => $ids), NULL);
	echo $content;
	die();
}

?>