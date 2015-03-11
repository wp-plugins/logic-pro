<?php
if (!class_exists('lp_categories') )
{
	
class lp_categories
{
	public $title = 'Categories';
	public $defaults = array(
						'lp_include_cat'=>array(),
						'lp_exclude_cat'=>array()
						);
	public function __construct()
	{
		global $lp_core;
		if (empty($lp_core) )
		{
			return;
		}
		$lp_core->register(array(5=>'categories'));
	}
	
	public function settings($lp_settings)
	{
		$lp_settings = shortcode_atts($this->defaults, $lp_settings);
		?>
<div id="lp_categories" class="tabcontent">
  <h3>Categories <img class="lp_title_icon" src="<?php echo plugins_url('images/cat-icon.png', dirname(dirname(__FILE__))); ?>" /></h3>
  <div class="inside">
  		<p>
        <span id="lp_inc_cat" class="sub-control" data-checked="<?php echo $lp_settings['lp_include_cat'] ? " checked " : "" ?>"></span>
        <strong>Display</strong> in Categories:
        </p>
        <div class="lp-inc-cat sub-setting sm-round" <?php echo $lp_settings['lp_include_cat'] ? "" : " style='display:none' " ?>>
          <?php $args = array(
					'show_option_all' => 'ALL (default)',
					'echo' => 0,
					'walker' => new Walker_CategoryDropdown_Custom(),
					'selected' => $lp_settings['lp_include_cat'], // The selected option can now be an array as well as a string
					);
				$catDropdown = wp_dropdown_categories( $args );
				$Catoptions = preg_replace( '#^\s*<select[^>]*>#', '', $catDropdown );
				$Catoptions = preg_replace( '#</select>\s*$#', '', $Catoptions );
				?>
          <select name="lp_settings[lp_include_cat][]" multiple="multiple">
            <?php echo $Catoptions; ?>
          </select>
        </div>
        
        <p>
        <span id="lp_exclude_cat" class="sub-control" data-checked="<?php echo $lp_settings['lp_exclude_cat'] ? " checked " : "" ?>"></span>
        <strong>Hide</strong> from Categories:
        </p>
        <div class="lp-exclude-cat sub-setting sm-round" <?php echo $lp_settings['lp_exclude_cat'] ? "" : " style='display:none' " ?>>
          <?php $args = array(
					'show_option_none' => 'NONE (default)',
					'echo' => 0,
					'walker' => new Walker_CategoryDropdown_Custom(),
					'selected' => $lp_settings['lp_exclude_cat'], // The selected option can now be an array as well as a string
					);
				$catDropdown = wp_dropdown_categories( $args );
				$Catoptions = preg_replace( '#^\s*<select[^>]*>#', '', $catDropdown );
				$Catoptions = preg_replace( '#</select>\s*$#', '', $Catoptions );
				?>
          <select name="lp_settings[lp_exclude_cat][]" multiple="multiple">
            <?php echo $Catoptions; ?>
          </select>
        </div>
      
      <p>Notes: <br>
          </p>
          <ul class="notes">
            <li>Use CTRL+click to select multiple categories. CTRL+click will also deselect categories. </li>
            <li>Hide overrides Display. Selecting Display for 'ALL' and Hide for a single category will show the content for all categories except the one selected in Hide.</li>
            <li>Category selections only apply if either:
              <ul>
                <li>A single post is being displayed.</li>
                <li>The page is a Category page.</li>
              </ul>
            </li>
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
		
		// -------------  Categories ---------------------
		$ajaxCats =  !empty($_POST['cats']) ? $_POST['cats'] : array();
		
		if (!empty($meta['lp_exclude_cat']) )
		{
			foreach($meta['lp_exclude_cat'] as $cat)
			{
				if($cat && is_category($cat) || in_array($cat, $ajaxCats) )
				{
					$result['debugMsg'] = $meta['id'] ." - in exc Cat " . $cat ;
					return $result;
				}
			}
			
			if (is_single() )
			{
				global $post;
				$postCats = wp_get_post_categories($post->ID);
			
				$commonCats = array_intersect($meta['lp_exclude_cat'], $postCats);
				if (!empty($commonCats) )
				{
					$result['debugMsg'] = $meta['id'] ." - in post + exc Cat" ;
					return $result;
				}
			}
		}
		
		if (!empty($meta['lp_include_cat']) )
		{
			$found = false;
			foreach($meta['lp_include_cat'] as $cat)
			{
				if(($cat && is_category($cat) ) || in_array($cat, $ajaxCats) )
				{
					$found = true;
					break;
				}
			}
			
			if (is_single() )
			{
				global $post;
				$postCats = wp_get_post_categories($post->ID);
			
				$commonCats = array_intersect($meta['lp_include_cat'], $postCats);
				if (!empty($commonCats) )
				{
					$found = true;
				}
			}
			
			if (!$found)
			{
				$result['debugMsg'] = $meta['id'] ." - Not in Include Cats";
				return $result;
			}
	
		}
		
		$result['show'] = true;		
		return $result;
	}
	
	public function is_used($meta)
	{
		if (!empty($meta['lp_exclude_cat']) || !empty($meta['lp_include_cat']) )
		{
			return true;
		}
		return false;
	}	
}// end class
}// and if class

$lp_categories = new LP_CATEGORIES;
?>