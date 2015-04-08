<?php
if (!class_exists('lp_general') )
{
	
class lp_general
{
	public $title = 'General';
	public $defaults = array(
						'lp_enable'=>'',
						'lp_login_status'=>'',
						'lp_start_date'=> '',
						'lp_end_date'=>'',
						'lp_url_keywords'=>'');
	
	public function __construct()
	{
		global $lp_core;
		if (empty($lp_core) )
		{
			return;
		}
		$lp_core->register(array(2=>'general'));
	}
	
	public function admin_settings()
	{
		?>
    
	<div id="lp_general_admin" class="tabcontent active" style="margin-top:30px">
	  <h3>General Settings <img class="lp_title_icon" class="generalImg" src="<?php echo plugins_url('images/general-icon.png', dirname(dirname(__FILE__))); ?>" /></h3>
      <table class="form-table">
    	<tr valign="top">
        	<th scope="row"><?php _e("Clear Cached Data", LP_DOMAIN); ?></th>
           	<td><div class="button-primary" onClick="lp_clear_cache();">Clear cached data. This page will refresh.</div></td>
		</tr>
    	<tr valign="top">
        	<th scope="row"><?php _e("Bypass Site File Caching", LP_DOMAIN); ?></th>
           	<td><form method="post" action="options.php">
            <?php settings_fields('logic_lite_group'); ?>
            <input type="checkbox" name="lp_bypass_cache" value="1" <?php  if(get_option('lp_bypass_cache') == 1) echo 'checked="checked"'; ?>/> <span class="description"><?php _e("File Caching Plugins like W3 Total Cache or hosting services like WPEngine that use Caching services to speed up WordPress Sites will cause issues with the features that VLP provides. Caching and dynamic content do not play well together. Some of the features of VLP will not work properly with aggressive caching turned on. If you check this bypass Cache checkbox, VLP will try to use a different method of loading the content of logic blocks that may allow the caching and the dynamic content to cooperate. File Caching will not work with shortcodes that are using the \"use_content\" parameter.", LP_DOMAIN); ?></span> 
            </td>
		</tr>
       <tr>
			<th scope="row"><?php _e("Enable Debug for all Logic Blocks", LP_DOMAIN); ?></th>
				<td><input type="checkbox" name="lp_global_debug" value="1" <?php  if(get_option('lp_global_debug') == 1) echo 'checked="checked"'; ?>/>
					<span class="description"><?php _e("This will enable each block to either display the content it contains, or to display a reason why the content is not being displayed. This should only be used for testing and should be disabled for live sites. Debugging can be enabled on a block by block basis by adding debug=1 to the shortcode, or by enabling the debug option on the Logic Lite Widget.", LP_DOMAIN); ?></span> <br /><br /> <p class="submit">
		<input type="submit" class="button-primary"	value="<?php _e('Save Settings', LP_DOMAIN) ?>" />
	</p>

	</form>
				</td>
		</tr>
	</table>
	
	
	</div>
	<div class="clearfix"></div>
    <script type="text/javascript">
	   function lp_clear_cache() {
		 jQuery.get('<?php echo admin_url('admin-ajax.php'); ?>?action=lp_clearCache');
		 location.reload();  
	   }
	</script>
    <?php	
	}
	
	public function settings($lp_settings)
	{
		$lp_settings = shortcode_atts($this->defaults, $lp_settings);
		?>
        
<style>
.lp-3way {
    background: transparent url("<?php echo plugins_url('logic-pro/images/3-way.png') ?>") left top no-repeat;
	width: 70px;
	height: 70px;
}
.lp-3way-on {
	background-position: 0px 0px;
}

.lp-3way-off {
	background-position: -151.5px 0px;
}

.lp-3way-logic {
	background-position: -75.5px 0px;
}
</style>

<div id="lp_general" class="tabcontent">
<h3>General Settings <img class="lp_title_icon" class="generalImg" src="<?php echo plugins_url('images/general-icon.png', dirname(dirname(__FILE__))); ?>" /></h3>
  <div class="inside">
    <div class="lp_3way_switch lp-3way-logic">
      <div id="lp_3way_label">Show this Content Block: </div>
      <div class="clear"></div>
      <div id="lp_enable_on">ON
        <input type="radio" name="lp_settings[lp_enable]" value="on" <?php if ($lp_settings['lp_enable'] == 'on') echo 'checked'; ?> onclick="jQuery('#lp_3switch').removeClass('lp-3way-logic lp-3way-off').addClass('lp-3way-on');">
        <br>
        (Always Show)</div>
      <div id="lp_3switch" class="lp-3way lp-3way-<?php echo $lp_settings['lp_enable'] ? $lp_settings['lp_enable'] : "logic" ?>"></div>
      <div id="lp_enable_off">
        <input type="radio" name="lp_settings[lp_enable]" value="off" <?php if ($lp_settings['lp_enable'] == 'off') echo 'checked'; ?>  onclick="jQuery('#lp_3switch').removeClass('lp-3way-logic lp-3way-on').addClass('lp-3way-off');">
        OFF<br>
        (Always Hide)</div>
      <div class="clear"></div>
      <div id="lp_enable_logic">
        <input type="radio" name="lp_settings[lp_enable]" value="logic" <?php if ($lp_settings['lp_enable'] == 'logic' || $lp_settings['lp_enable'] == '') echo 'checked'; ?>  onclick="jQuery('#lp_3switch').removeClass('lp-3way-off lp-3way-on').addClass('lp-3way-logic');">
        <br>
        Use<br>
        VLP Logic</div>
        <div class="clear">&nbsp;</div>
    </div>
    
    <p>
    <span id="lp_login_status" class="sub-control" data-checked="<?php echo $lp_settings['lp_login_status'] ? " checked " : "" ?>"></span>
    <strong>Base This Logic Block On Login Status</strong>
    </p>
    <div class="lp-login-status sub-setting sm-round" <?php echo $lp_settings['lp_login_status'] ? "" : " style='display:none' " ?>>
    Login Status:
      <select name="lp_settings[lp_login_status]">
      <option value="everyone" <?php if ($lp_settings['lp_login_status'] == "everyone") { echo "selected"; } ?>>Everyone</option>
      <option value="logged_in" <?php if ($lp_settings['lp_login_status'] == "logged_in") { echo "selected"; } ?>>Logged In</option>
      <option value="logged_out" <?php if ($lp_settings['lp_login_status'] == "logged_out") { echo "selected"; } ?>>Not Logged In</option>
      </select>
	</div>

    <p>
    <span class="sub-control" data-checked="<?php echo $lp_settings['lp_url_keywords'] ? " checked " : "" ?>"></span>
    <strong>Available In Pro!</strong>
    </p>
    <div class="sub-setting sm-round" style="display:none">
     <p>We hope you have found the Visitor Logic Lite features helpful. If you would like to unlock the advanced features and integrations of <strong>Visitor Logic Pro</strong>, please visit <a href="http://VisitorLogicPro.com" target="_blank">VisitorLogicPro.com</a> to obtain a license code and activate the advanced features.</p><br/>
    <h4>Advanced General Features</h4><br/>
    <p><strong>Set Display Start Date</strong></p>
    <ul class="notes">
        <li>Content will only be displayed after this date and time. Based on server time.</li>
        </ul>
    <p><strong>Set Display End Date</strong></p>
    <ul class="notes">
        <li>Content will not be displayed after this date and time. Based on server time.</li>
        </ul>
    <p><strong>Require KeyStrings in URL:</strong></p>
    <ul class="notes">
        <li>Enter a comma separated list of keystrings or character sequences that must exist in the requested URL. If none of the keystrings exist in the URL, this content block will not be displayed. Keystrings are not case sensitive.</li>
        </ul>
      </div>
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
		/*	moved to lp_core logic for main switch in lp_core */
		
		if ($meta['lp_login_status'] != 'everyone')
		{
			if ($meta['lp_login_status'] == 'logged_out' && is_user_logged_in() )
			{
				$result['debugMsg'] = $meta['id'] ." - logged in";
				return $result;
			}
			elseif ($meta['lp_login_status'] == 'logged_in' && !is_user_logged_in())
			{
				$result['debugMsg'] = $meta['id'] ." - logged out";
				return $result;
			}
		}
		
		if (strtotime($meta['lp_start_date']) > time() 
		    || (strtotime($meta['lp_end_date']) > '1990' 
				 && strtotime($meta['lp_end_date']) < time()
			    ) )
		{
			$result['debugMsg'] = $meta['id'] ." - Date range";
			return $result;
		}
		
		if (!empty($meta['lp_url_keywords']) )
		{
			$metaArr = explode(',' , $meta['lp_url_keywords']);
			$found = false;
			foreach ($metaArr as $keyword)
			{
				if (strpos("/". $_SERVER['REQUEST_URI'],$keyword) )
				{
					$found = true;
					break;
				}
			}
			if (!$found)
			{
				$result['debugMsg'] = $meta['id'] ." - Keyword";
				return $result;
			}
		}
		$result['show'] = true;
		return $result;
	}
	
	public function is_used($meta)
	{
		if ((isset($meta['lp_login_status']) && $meta['lp_login_status'] != 'everyone')
			|| !empty($meta['lp_url_keywords']) 
			|| !empty($meta['lp_start_date']) 
			|| !empty ($meta['lp_end_date'])
		   )
		{
			return true;
		}
		return false;
	}
}// end class
}// and if class

$lp_general = new LP_GENERAL;
?>