<?php
if (!class_exists('lp_pages') )
{
	
class lp_pages
{
	public $title = 'Pages';
	public $defaults = array(
						'lp_inc_page_ids' =>'',
						'lp_exc_page_ids'=>''
						);
						
	public function __construct()
	{
		global $lp_core;
		if (empty($lp_core) )
		{
			return;
		}
		$lp_core->register(array(3=>'pages'));
	}
	
	public function settings($lp_settings)
	{
		$lp_settings = shortcode_atts($this->defaults, $lp_settings);
		?>
<div id="lp_pages" class="tabcontent">
  <h3>Pages <img class="lp_title_icon" src="<?php echo plugins_url('images/pages-icon.png', dirname(dirname(__FILE__))); ?>" /></h3>
  <div class="inside">
  		<p>
        <span id="lp_inc_page_ids" class="sub-control" data-checked="<?php echo $lp_settings['lp_inc_page_ids'] ? " checked " : "" ?>"></span>
        <strong>Display</strong> only on Pages:
        </p>
        <div class="lp-inc-page-ids sub-setting sm-round" <?php echo $lp_settings['lp_inc_page_ids'] ? "" : " style='display:none' " ?>>
          <?php $args = array(
					'show_option_none' => 'ALL (default)',
					'option_none_value' => '',
					'echo' => 0,
					'walker' => new Walker_PageDropdown_Multiple(),
					'selected' => $lp_settings['lp_inc_page_ids'] // The selected option can now be an array as well as a string
					);
				$pagesDropdown = wp_dropdown_pages( $args );
				$Pageoptions = preg_replace( '#^\s*<select[^>]*>#', '', $pagesDropdown );
				$Pageoptions = preg_replace( '#</select>\s*$#', '', $Pageoptions );
				?>
          <select name="lp_settings[lp_inc_page_ids][]" multiple="multiple">
            <?php echo $Pageoptions; ?>
          </select>
        </div>
        
        <p>
        <span id="lp_exc_page_ids" class="sub-control" data-checked="<?php echo $lp_settings['lp_exc_page_ids'] ? " checked " : "" ?>"></span>
        <strong>Hide</strong> on Pages:
        </p>
        <div class="lp-exc-page-ids sub-setting sm-round" <?php echo $lp_settings['lp_exc_page_ids'] ? "" : " style='display:none' " ?>>
          <?php
				$args = array(
					'show_option_none' => 'NONE (default)',
					'option_none_value' => '',
					'echo' => 0,
					'walker' => new Walker_PageDropdown_Multiple(),
					'selected' => $lp_settings['lp_exc_page_ids'] // The selected option can now be an array as well as a string
					);
				$pagesDropdown = wp_dropdown_pages( $args );
				$Pageoptions = preg_replace( '#^\s*<select[^>]*>#', '', $pagesDropdown );
				$Pageoptions = preg_replace( '#</select>\s*$#', '', $Pageoptions );
				?>
          <select name="lp_settings[lp_exc_page_ids][]" multiple="multiple">
            <?php echo $Pageoptions; ?>
          </select>
        </div>
        
<p>Notes:<br>
          </p>
          <ul class="notes">
            <li>Use CTRL+click to select multiple pages. CTRL+click will also deselect pages. </li>
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
		
		
		global $post;
		
		$page_id = isset($_POST['page']) ? $_POST['page'] : (is_page() && $post->ID ? $post->ID : false);

		// ----------------- PAGES ---------------
		if (!empty($meta['lp_inc_page_ids'])  || !empty($meta['lp_exc_page_ids']) )
		{
			// get the current page
			
			if (!$page_id)
			{
				$result['debugMsg'] = $meta['id'] ." - Not a page";
				return $result;
			}
			
			if (!empty($meta['lp_exc_page_ids']) && in_array($page_id, $meta['lp_exc_page_ids']) )
			{
				$result['debugMsg'] = $meta['id'] ." - In exc page id";
				return $result;
			}
			if (!empty($meta['lp_inc_page_ids']) && !in_array($page_id, $meta['lp_inc_page_ids']) )
			{
				$result['debugMsg'] = $meta['id'] ." - not in inc page ids";
				return $result;
			}
		}
		$result['show'] = true;
		return $result;
	}

	public function is_used($meta)
	{
		if (!empty($meta['lp_inc_page_ids'])  || !empty($meta['lp_exc_page_ids']))
		{
			return true;
		}
		return false;
	}	

}// end class
}// and if class

$lp_pages = new LP_PAGES;
?>