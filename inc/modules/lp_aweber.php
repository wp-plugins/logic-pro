<?php
if (!class_exists('lp_aweber') )
{
	
class lp_aweber
{
	public $title = 'aWeber';
	public $defaults = array();
	public function __construct()
	{
		global $lp_core;
		if (empty($lp_core) )
		{
			return;
		}
		
		$lp_core->register(array(9=>'aweber'));
	}
	
	public function settings($lp_settings)
	{
		$lp_settings = shortcode_atts($this->defaults, $lp_settings);
		?>
		<div id="lp_aweber" class="tabcontent">
  		<h3 ><img class="aWeberImg" src="<?php echo plugins_url('images/aweber.png', dirname(dirname(__FILE__))); ?>" /></h3>
  		<div class="inside">
			<h2>Upgrade To Pro For This Feature!</h2>
            <p>We hope you have found the Visitor Logic Lite features helpful. If you would like to unlock the advanced features and integrations of <strong>Visitor Logic Pro</strong>, please visit <a href="http://VisitorLogicPro.com" target="_blank">VisitorLogicPro.com</a> to obtain a license code and activate the advanced features.</p>
            <h4>How VLP Works With Aweber</h4>
           	<ul class="notes">
                <li>VLP will check to see if it knows the visitor (via cookie).</li>
                <li>If the visitor can be identified, Aweber is checked to see if the visitor is on the list you have selected.</li>
                <li>If the visitor is on the selected list, The content is displayed.</li>
                <li>Visitors that are not on the list, or cannot be identified ..
                    <ul>
		            <li>If you have checked the "Show Opt-In form" option above the opt in form code is displayed using the Opt In form code.</li>
                    <li>When the "Show Opt-In form" unchecked,  nothing is displayed</li>
                    </ul>
                 </li>
             </ul>
           	<ul class="notes">
                <li>VLP will not be able to identify visitors that have not used the opt in form in this logic block.</li>
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

$lp_aweber = new LP_AWEBER;
?>