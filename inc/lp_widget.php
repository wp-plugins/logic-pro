<?php

add_action( 'widgets_init', 'vll_reg_widget');

function vll_reg_widget()
{
	register_widget( 'lp_widget' );
}


class lp_widget extends WP_Widget {

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		// widget actual processes
		parent::__construct(
			'lp_widget', // Base ID
			__('Logic Lite Widget Area', 'lp'), // Name
			array( 'description' => __( 'Logic Lite Widget Area', 'lp' ), ) // Args
		);
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		// outputs the content of the widget
		
		extract($args);
		$widget_id = $args['widget_id'];
		$cntr = 0;
		$global_debug = get_option('lp_global_debug');
		$instance['lp_debug'] = $instance['lp_debug'] ? $instance['lp_debug'] : $global_debug;
		$global_bypass = get_option('lp_bypass_cache');

		if (isset($global_bypass) && $global_bypass && !defined('DOING_AJAX') )
		{
			global $post;
			$post_type = get_post_type($post);
			$data[$post_type] = $post->ID;
			$data['widg_class'] = 'lp_widget';
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
			$svar = "<div id='lp_aw_" . $widget_id . "'></div>
			   <script type='text/javascript'>
			    var lpd = " . json_encode($data) . ";
				jQuery.ajax({
				type : 'POST',
				data : lpd,
				url : '" . admin_url('admin-ajax.php') . "?action=lp_get_widg&id=" . $widget_id ."',
				async : true
				})
				.done( function(data) {
				if (data != 0)
				{
					jQuery('#lp_aw_" . $widget_id ."').html(data);
				}
				});
					 </script>
					 ";
			echo $svar;
			return;		 
		}
		else
		{
			global $lp_core;

			if (isset($instance['lp_block_id']) && !isset($instance['lp_selected_block'])  )
			{
				$instance['lp_selected_block'] = array($instance['lp_block_id']);
			}

			foreach ($instance['lp_selected_block'] as $sel_block)
			{
				if ($sel_block)
				{
					$block = $lp_core->process_logic($sel_block, $instance['lp_debug']);

					if ($block !== FALSE)
					{
						echo $args['before_widget'];
						if ( ! empty( $instance['title'] ) ) {
							echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
						}
						
						echo do_shortcode($block);
						if (!$instance['lp_debug'])
						{
							$cntr++;
							if ($cntr >= $instance['lp_max'])
							{
								break;
							}
						}
						else
						{
							echo "<br>";
						}
						echo $args['after_widget'];
					}
				}
			}
			
		}
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {
		// outputs the options form on admin
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'New Title', 'text_domain' );
		}
		if ( isset( $instance[ 'lp_selected_block' ] ) ) {
			$lp_selected_block = $instance[ 'lp_selected_block' ];
		}
		if ( isset( $instance[ 'lp_debug' ] ) ) {
			$lp_debug = $instance[ 'lp_debug' ];
		}
		
		if ( isset( $instance[ 'lp_max' ] ) ) {
			$lp_max = $instance[ 'lp_max' ];
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'lp_max' ); ?>"><?php _e( 'Limit blocks to display:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'lp_max' ); ?>" name="<?php echo $this->get_field_name( 'lp_max' ); ?>" type="text" value="<?php echo esc_attr( $lp_max ); ?>">
		</p>

        <p>
		<label for="<?php echo $this->get_field_id( 'lp_debug' ); ?>"><?php _e( 'Debug Mode:' ); ?></label> 
		<input type="checkbox" id="<?php echo $this->get_field_id( 'lp_debug' ); ?>" name="<?php echo $this->get_field_name( 'lp_debug' ); ?>" value="1" <?php echo  $lp_debug ? "checked" : "" ?> >
		</p>
		<?php 
	  		$args = array( 'post_type' => 'logicblock', 'posts_per_page'         => '-1');
	  		$loop = new WP_Query( $args );

		  	while ( $loop->have_posts() ) :
			    $loop->the_post();
				for($x=1; $x<=10; $x++)
				{
			   		$options[$x] .= "<option value='" .get_the_id(). "' " . ($lp_selected_block[$x] == get_the_id() ? " selected='selected' " : "" ). ">" .get_the_title() ."</option>";
				}
	  		endwhile;
	  	
		    for($x=1; $x<=10; $x++)
			{ ?>
		
              	<label for="<?php echo $this->get_field_id( 'lp_selected_block' )?>[<?php echo $x?>]"><?php echo  _e('Logic Block ' . $x) ?></label>
      	 		<select class="widefat"  id="<?php echo $this->get_field_id( 'lp_selected_block' )?>[<?php echo $x?>]"  name="<?php echo $this->get_field_name( 'lp_selected_block' )?>[<?php echo $x?>]" >
					<option value=''>    </option>
					<?php echo $options[$x];?>
      			</select>
      <?php
			}
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 */
	public function update( $new_instance, $old_instance ) {
		// processes widget options to be saved
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['lp_selected_block'] = ( ! empty( $new_instance['lp_selected_block'] ) ) ? ( $new_instance['lp_selected_block'] ) : '';
		$instance['lp_debug'] = ( ! empty( $new_instance['lp_debug'] ) ) ? ( $new_instance['lp_debug'] ) : '';
		$instance['lp_max'] = ( ! empty( $new_instance['lp_max'] ) ) ? ( $new_instance['lp_max'] ) : '1';
		
		return $instance;
		
	}
}

add_action('in_widget_form', 'lp_in_widget_form',5,3);
add_filter('widget_update_callback', 'lp_in_widget_form_update',5,3);
add_filter('widget_display_callback', 'lp_widget_wrap',10,3);

function lp_in_widget_form($t,$return,$instance){
	if ($t->id_base == 'lp_widget')
	{
		return array($t,$return,$instance);
	}
    $instance = wp_parse_args( (array) $instance, array( 'title' => '', 'lp_block_id' => 'none') );
    if ( !isset($instance['lp_block_id']) )
        $instance['lp_block_id'] = null;
		
		$args = array( 'post_type' => 'logicblock', 'posts_per_page' => '-1' );
	  	$loop = new WP_Query( $args );

		  	while ( $loop->have_posts() ) :
			    $loop->the_post();
			   		$options .= "<option value='" .get_the_id(). "' " . ($instance['lp_block_id'] == get_the_id() ? " selected='selected' " : "" ). ">" .get_the_title() ."</option>";
			endwhile;
		
    ?>
    <p>
        <input id="<?php echo $t->get_field_id('lp_debug'); ?>" name="<?php echo $t->get_field_name('lp_debug'); ?>" type="checkbox" <?php checked(isset($instance['lp_debug']) ? $instance['lp_debug'] : 0); ?> />
        <label for="<?php echo $t->get_field_id('lp_debug'); ?>"><?php _e('VLP Debug Mode'); ?></label>
    </p>
    <p>
        <label for="<?php echo $t->get_field_id('lp_block_id'); ?>">Apply this Logic Block's logic to this widget:</label>
        <select id="<?php echo $t->get_field_id('lp_block_id'); ?>" name="<?php echo $t->get_field_name('lp_block_id'); ?>">
            <option value=''>    </option>
					<?php echo $options;?>
        </select>
    </p>
    <?php
    $retrun = null;
    return array($t,$return,$instance);
}

function lp_in_widget_form_update($instance, $new_instance, $old_instance){
    $instance['lp_debug'] = isset($new_instance['lp_debug']);
    $instance['lp_block_id'] = $new_instance['lp_block_id'];
    return $instance;
}

function lp_widget_wrap($instance, $widget_class, $args){

   
	if (empty($instance['lp_block_id']) )
	{
		return $instance;
	}
	else
	{
		$global_debug = get_option('lp_global_debug');
		$debug = $instance['lp_debug'] ? $instance['lp_debug'] : $global_debug;
		global $lp_core;
		$global_bypass = get_option('lp_bypass_cache');
		
		if (isset($global_bypass) && $global_bypass && !defined('DOING_AJAX') )
		{
			global $post;
			$post_type = get_post_type($post);
			$data[$post_type] = $post->ID;
			$data['widg_class'] = get_class($widget_class);
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
			$svar = "<div id='lp_aw_" . $args['widget_id'] . "'></div>
			   <script type='text/javascript'>
			    var lpd = " . json_encode($data) . ";
				jQuery.ajax({
				type : 'POST',
				data : lpd,
				url : '" . admin_url('admin-ajax.php') . "?action=lp_get_widg&id=" . $args['widget_id'] ."',
				async : true
				})
				.done( function(data) {
				if (data != 0)
				{
					jQuery('#lp_aw_" . $args['widget_id'] ."').html(data);
				}
				});
					 </script>
					 ";
			echo $svar;
			return false;		 
		}
		else
		{
			$block = $lp_core->process_logic($instance['lp_block_id'], $debug, false);
			//echo "<pre>" . ($block === FALSE ? " its FALSE " : " it's not ") . "</pre>";
			if ($block !== FALSE)
			{
				$result = explode('-',$block);
				$result = trim($result[0]);
				if ($result == $instance['lp_block_id'])  //  we have a debug message
				{
					if ($debug)
					{
    					echo $block;
					}
				}
				return $instance;

			}
			return false;
		}
	}
	
	return false;
}

add_action('wp_ajax_lp_get_widg', 'lp_get_widg');
add_action('wp_ajax_nopriv_lp_get_widg', 'lp_get_widg');

function lp_get_widg() {
	if (empty($_POST['widg_class']))
	{
		die();
	}
		
	$widget_id = explode('-',$_GET['id']);
	$options = get_option("widget_".$widget_id[0]);
	$instance = $options[$widget_id[1]];
	
	$widget_class = $_POST['widg_class'];
	$new_widg = new $widget_class($widget_id[0], "");
	
	// the_widget is not firing the lp_widget_wrap
	$instance = lp_widget_wrap($instance, "", "");
	if (is_array($instance) )
	{
		the_widget($_POST['widg_class'],$instance);
	}
	die();
}


?>