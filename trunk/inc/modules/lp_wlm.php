<?php
if (!class_exists('lp_wlm') )
{
	
class lp_wlm
{
	public $title = "Wishlist";
						
	public function __construct()
	{
		global $lp_core;
		if (empty($lp_core) )
		{
			return;
		}
		$lp_core->register(array(8 => 'wlm'));
	}
	
	public function settings($lp_settings)
	{
		?>
        <div id="lp_wlm" class="tabcontent">
  <h3><img class="aWeberImg" src="<?php echo plugins_url('images/wishlist.png', dirname(dirname(__FILE__))); ?>" /></h3>
  <div class="inside">
            <h2>Upgrade To Pro For This Feature!</h2>
            <p>We hope you have found the Visitor Logic Lite features helpful. If you would like to unlock the advanced features and integrations of <strong>Visitor Logic Pro</strong>, please visit <a href="http://VisitorLogicPro.com" target="_blank">VisitorLogicPro.com</a> to obtain a license code and activate the advanced features.</p>
            <h4>How VLP Works With WishList</h4>
           	<ul class="notes">
                <li>VLP will use your Wishlist Member levels to determine if this content block is displayed</li>
                <li>Enabling this option will require that the visitor is logged in to show this content block.</li>
                <li>If the visitor is a member of the selected level, The content is displayed.</li>
                <li>If the visitor is not on the list, or are not logged in, the logic block is not dsplayed.</li>
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

$lp_wlm = new LP_WLM();
?>