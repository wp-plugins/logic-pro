<?php
if (!class_exists('lp_getresponse') )
{
	
class lp_getresponse
{
	public $title = 'GetResponse';
	public $defaults = array();
	public function __construct()
	{
		global $lp_core;
		if (empty($lp_core) )
		{
			return;
		}
		
		$lp_core->register(array(11=>'getresponse'));
	}
		
	public function settings($lp_settings)
	{
		$lp_settings = shortcode_atts($this->defaults, $lp_settings);
		
		?>
<div id="lp_getresponse" class="tabcontent">
    <h3 ><img class="getresponseImg" src="<?php echo plugins_url('images/get-response.png', dirname(dirname(__FILE__))); ?>" /></h3>
    <div class="inside">
         <h2>Upgrade To Pro For This Feature!</h2>
            <p>We hope you have found the Visitor Logic Lite features helpful. If you would like to unlock the advanced features and integrations of <strong>Visitor Logic Pro</strong>, please visit <a href="http://VisitorLogicPro.com" target="_blank">VisitorLogicPro.com</a> to obtain a license code and activate the advanced features.</p>
            <h4>How VLP Works With GetResponse</h4>
            <ul class="notes">
                <li>VLP will check to see if it knows the visitor (via cookie).</li>
                <li>If the visitor can be identified, GetResponse is checked to see if the visitor is in the campaign you have selected.</li>
                <li>If the visitor is int he selected campaign, The content is displayed.</li>
                <li>Visitors that are not in the campaign, or cannot be identified ..
                    <ul>
                        <li>If you have checked the "Show Opt-In form" option above the opt in form code is displayed. either by pulling the webform from GetResponse,  or using the custom webform code.</li>
                        <li>When the "Show Opt-In form" unchecked,  nothing is displayed</li>
                    </ul>
                </li>
            </ul>
            <ul class="notes">
                <li>VLP will not be able to identify visitors that have not used the webform in this logic block.</li>
                <li>Should a visitor clear their cookies or use a different browser, VLP will not be able to detect if they are tagged or not. Take this into consideration when designing your content for this block.</li>
            </ul>
    </div>
</div>
<?php
	}

	public function is_used($meta)
	{
		return false;
	}
    
}// end class
}// and if class

$lp_getresponse = new LP_getresponse;
?>