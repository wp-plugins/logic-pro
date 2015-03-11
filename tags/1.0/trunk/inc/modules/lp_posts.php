<?php
if (!class_exists('lp_posts') )
{
	
class lp_posts
{
	public $title = 'Posts';
	public $defaults = array(
						'lp_inc_post_ids'=>'',
						'lp_exc_post_ids'=>''
						);
						
	public function __construct()
	{
		global $lp_core;
		if (empty($lp_core) )
		{
			return;
		}
		$lp_core->register(array(4=>'posts'));
	}
	
	public function save($lp_settings)
	{
		//echo "<pre> settings " . print_r($lp_settings,true) . "</pre>";
		$lp_settings['lp_inc_post_ids'] = array_filter(array_unique(explode(',' , trim($lp_settings['lp_inc_post_ids']))));
		$lp_settings['lp_exc_post_ids'] = array_filter(array_unique(explode(',' , trim($lp_settings['lp_exc_post_ids']))));
		//echo "<pre> settings " . print_r($lp_settings,true) . "</pre>";

		return $lp_settings;
	}
	
	public function settings($lp_settings)
	{
		$lp_settings = shortcode_atts($this->defaults, $lp_settings);
		
		$lp_settings['lp_inc_post_ids'] = $lp_settings['lp_inc_post_ids'] ? $lp_settings['lp_inc_post_ids'] : array();
		$lp_settings['lp_exc_post_ids'] = $lp_settings['lp_exc_post_ids'] ? $lp_settings['lp_exc_post_ids'] : array();

		?>
<div id="lp_posts" class="tabcontent">
  <h3>Posts <img class="lp_title_icon" src="<?php echo plugins_url('images/post-icon.png', dirname(dirname(__FILE__))); ?>" /></h3>
  <div class="inside">
  		<p>
        <span id="lp_inc_post_ids" class="sub-control" data-checked="<?php echo $lp_settings['lp_inc_post_ids'] ? " checked " : "" ?>"></span>
        <strong>Display</strong> on Posts:
        </p>
        <div class="lp-inc-post-ids sub-setting sm-round" <?php echo $lp_settings['lp_inc_post_ids'] ? "" : " style='display:none' " ?>>
          Post id's <input type="text" id="lp_inc_post_ids" name="lp_settings[lp_inc_post_ids]" class="lp_post_ids" value="<?php echo implode(',', $lp_settings['lp_inc_post_ids']); ?><?php  echo $lp_settings['lp_inc_post_ids']?", " : "" ?>"/> (see notes)
          <br/>
          <?php if (!empty($lp_settings['lp_inc_post_ids']) ) : ?>
          Previously selected Posts
          <ul>
          <?php
			   $inc_posts = new WP_Query( array('post_type' => 'post', 'post__in' => $lp_settings['lp_inc_post_ids']) );
	  			while ( $inc_posts->have_posts() ) :
			      $inc_posts->the_post();
				  echo "<li>(" . get_the_ID() . ") " . substr(get_the_title(), 0, 30) . "</li>";
				endwhile;  
			?>
          </ul>
          <?php endif; ?>
        </div>
        
        <p>
        <span id="lp_exc_post_ids" class="sub-control" data-checked="<?php echo $lp_settings['lp_exc_post_ids'] ? " checked " : "" ?>"></span>
        <strong>Hide</strong> on Posts:
        </p>
        <div class="lp-exc-post-ids sub-setting sm-round" <?php echo $lp_settings['lp_exc_post_ids'] ? "" : " style='display:none' " ?>>
          Post id's <input type="text" id="lp_exc_post_ids" name="lp_settings[lp_exc_post_ids]" class="lp_post_ids" value="<?php echo implode(',',$lp_settings['lp_exc_post_ids']); ?><?php echo $lp_settings['lp_exc_post_ids']? ", " : ""?>"/> (see notes)
          <br/>
          <?php if (!empty($lp_settings['lp_exc_post_ids']) ) : ?>
          Previously selected Posts
          <ul>
          <?php
			   $inc_posts = new WP_Query( array('post_type' => 'post', 'post__in' => $lp_settings['lp_exc_post_ids']) );
	  			while ( $inc_posts->have_posts() ) :
			      $inc_posts->the_post();
				  echo "<li>(" . get_the_ID() . ") " . substr(get_the_title(), 0, 30) . "</li>";
				endwhile;  
				
			?>
          </ul>
          <?php endif; ?>
        </div>
        
<p>Notes: <br>
          </p>
          <ul class="notes">
            <li>The post ID fields include a custom <em>AUTOCOMPLETE</em> feature. Just start typing the post title and select the post from the list, the ID will be populated for you.</li>
            <li>Enter comma separated list post ID's for these fields. Leave the field blank to not use this setting.</li>
            <li>Hide overrides Display. Selecting Display for 'ALL' and Hide for a single page will show the content for all pages except the one selected in Hide.</li>
          </ul>
  </div>
</div>

	<?php
	}
	
	public function process_logic($meta, $debug, $use_content)
	{
		
		//  return an ARRAY
		$result = array('show' => false, 'skip' => false, 'debugMsg'=> '', 'content'=> '');
		// show  (true or false) 
		// skip  (true or false)
		// debugMsg  String of debug data,  can always be populated,  main process_logic will decide what to do with it.
		// content populate with string to display instead of actual content
		
		if (!$this->is_used($meta))
		{
			$result['skip'] = true;
			return $result;
		}
		
		// -----------  POSTS  ----------------------
		global $post;
		$postID = isset($_POST['post']) ? $_POST['post'] : $post->ID;
		
		if ((is_single() || defined('DOING_AJAX')) && !empty($meta['lp_exc_post_ids']) )
		{
			if (in_array($postID, $meta['lp_exc_post_ids']) )
			{
				$result['debugMsg'] = $meta['id'] ." - excluded post IDs" ;
				return $result;
			}
		}
		
		if ( (is_single() || defined('DOING_AJAX')) && !empty($meta['lp_inc_post_ids']) )
		{
			if (!in_array($postID, $meta['lp_inc_post_ids']) )
			{
				$result['debugMsg'] = $meta['id'] ." - Not in included post IDs" ;
				return $result;
			}
		}
		$result['show'] = true;
		return $result;
	}
	
	public function is_used($meta)
	{
		if (!empty($meta['lp_inc_post_ids'])  || !empty($meta['lp_exc_post_ids']))
		{
			return true;
		}
		return false;
	}	
		
}// end class
}// and if class

$lp_posts = new LP_posts;
?>