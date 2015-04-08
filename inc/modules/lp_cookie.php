<?php
if (!class_exists('lp_cookie') )
{
	
class lp_cookie
{
	public $title = 'Cookies';
	public $defaults = array(
						'lp_site_limit'=>'',
						'lp_site_min'=>'',
						'lp_site_limit'=>'',
						);
				
	public function __construct()
	{
		global $lp_core;
		if (empty($lp_core) )
		{
			return;
		}
		$lp_core->register(array(7=>'cookie'));
	}

	public function save($lp_settings)
	{
		$lp_settings['lp_view_posts'] = array_filter(array_unique(explode(',' , trim($lp_settings['lp_view_posts']))));

		return $lp_settings;
	}
	
	
	public function settings($lp_settings)
	{
		$lp_settings = shortcode_atts($this->defaults, $lp_settings);
		$lp_settings['lp_view_posts'] = $lp_settings['lp_view_posts'] ? $lp_settings['lp_view_posts'] : array();
		
		?>

<div id="lp_cookie" class="tabcontent">
    <h3>Cookies <img class="lp_title_icon" src="<?php echo plugins_url('images/cookie-icon.png', dirname(dirname(__FILE__))); ?>" /></h3>
    <div class="inside">
    
        <?php $visits_used = (!empty($lp_settings['lp_site_min']) || !empty($lp_settings['lp_site_limit'])) ? true : false; ?>
        
        <p>I want users to see this block based on: <br/><small> (Click the plus icon next to each option to display option settings)</small></p>
        <p>
            <span id="lp_cookie_visits" class="sub-control" data-checked="<?php echo $visits_used ? " checked " : "" ?>"></span>
            <strong>Visits</strong> to this site. (Page views)</p>
        <div class="lp-site-visits sub-setting sm-round" <?php echo $visits_used ? "" : " style='display:none' " ?>>
            <p><strong>Site Visits:</strong> Visitor will see this content block ...</p>
            <p>After they have visited
                <input class="lp_hover_help" type="text" name="lp_settings[lp_site_min]" value="<?php echo $lp_settings['lp_site_min'] ?>" size="2" title="Blank = first page view, 1 or more = AFTER X page views."/>
                pages on this site but will stop seeing the content after they have visited
                <input  class="lp_hover_help" type="text" name="lp_settings[lp_site_limit]" value="<?php echo $lp_settings['lp_site_limit'] ?>" size="2" title="Blank = unlimited page views"/>
                pages on this site.</p>
            <p><small>Site visits are tracked accross all pages and are not limited to pages with logic blocks.</small> </p>
        </div>
        <p>
    <span class="sub-control" data-checked=""></span>
        <strong>Available In Pro!</strong>
        </p>
        <div class="sub-setting sm-round upsell" style="display:none">
            <p>We hope you have found the Visitor Logic Lite features helpful. If you would like to unlock the advanced features and integrations of <strong>Visitor Logic Pro</strong>, please visit <a href="http://VisitorLogicPro.com" target="_blank">VisitorLogicPro.com</a> to obtain a license code and activate the advanced features.</p><br/>
        <h4>Advanced Cookie Features</h4><br/>
        <p><strong>Day Range: Days since their first visit to the site.</strong></p>
        <ul class="notes">
        <li>Visitor will see this content block after a set day of first visiting the site but not after a set day of first visiting the site.<br/>
        Day Range is based on the visitors first visit to the site.</li>
        </ul>
        
        <p><strong>Block View Limit</strong></p>
        <ul class="notes">
        <li>Start showing this block after a set amount of visits to a page(s) that this block should be displayed on.</li>
        <li>Stop showing this block after the visitor has seen this block a set amount of times.</li>
        <li>Block views are only tracked when this block is displayed.</li>
        </ul>
        
        <p><strong>Global Tracking Pixel</strong></p>
        <ul class="notes">
        <li>Visitor will see this content block ONLY if they have this Tracking Cookie set.</li>
        <li>Place this tracking pixel on any page in any domain to SET the cookie used by this content block.</li>
        </ul>
        
        <p><strong>Remove Cookie: </strong></p>
        <ul class="notes">
        <li>Place this tracking pixel on any page in any domain to REMOVE the cookie used by this content block.</li>
        </ul>
        
        <p><strong>Pages and Posts Tracking</strong></p>
        <ul class="notes">
        <li>Visitor will see this content if they have visited one of the set pages or posts on this site.</li>
        </ul>
        
        <p><strong>Reset Rule</strong></p>
        <ul class="notes">
        <li>Reset tracking for blocks with a specific tag set.</li>
        </ul>
        
      </div>
    </div>
</div>
<?php

	}
	
	public function process_logic($meta, $debug, $use_content)
	{
		//  return an ARRAY
		$result = array('show' => false, 'skip' => false, 'debugMsg'=> '', 'content'=> '', 'reset' =>false);
		// show  (true or false) 
		// skip  (true or false)
		// debugMsg  String of debug data,  can always be populated,  main process_logic will decide what to do with it.
		// content populate with string to display instead of actual content
		
		if (!$this->is_used($meta))
		{
			$result['skip'] = true;
			return $result;
		}

		$lpCookie = json_decode(stripslashes($_COOKIE['lp']),true);
		$blockView = $lpCookie[$meta['id']];
		$siteVisits = $lpCookie['s'];
		$siteFirst = isset($lpCookie['f']) ? $lpCookie['f'] : time();

		if (!empty($meta['lp_site_limit']) && !empty($siteVisits) )
		{
			if ($siteVisits > $meta['lp_site_limit'] ) 
			{
				$result['debugMsg'] = $meta['id'] ." - $siteVisits - Site Visit Limit Reached. " ;
				return $result;
			}
		}

		if (!empty($meta['lp_site_min']) )
		{
			if (empty($siteVisits) || $siteVisits < $meta['lp_site_min']  ) 
			{
				$result['debugMsg'] = $meta['id'] ." - Site Min not Reached. " ;
				return $result;
			}
		}

		$result['show'] = true;
		return $result;
	}
	
	public function is_used($meta)
	{
		if (   !empty($meta['lp_site_min']) 
		    || !empty($meta['lp_site_limit']) 
			)
		{
			return true;
		}
		return false;
	}

}// end class
}// and if class

$lp_cookie = new LP_COOKIE;
?>