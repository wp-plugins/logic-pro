<?php
if (!class_exists('lp_authors') )
{
	
class lp_authors
{
	public $title = 'Authors';
	public $defaults = array();
	public function __construct()
	{
		global $lp_core;
		if (empty($lp_core) )
		{
			return;
		}
		$lp_core->register(array(6=>'authors'));
	}
	
	public function settings($lp_settings)
	{
		$lp_settings = shortcode_atts($this->defaults, $lp_settings);
		?>
 <div id="lp_authors" class="tabcontent">
  <h3>Authors <img class="lp_title_icon" src="<?php echo plugins_url('images/author-icon.png', dirname(dirname(__FILE__))); ?>" /></h3>
  		<div class="inside">
           <h2>Upgrade To Pro For This Feature!</h2>
            <p>We hope you have found the Visitor Logic Lite features helpful. If you would like to unlock the advanced features and integrations of <strong>Visitor Logic Pro</strong>, please visit <a href="http://VisitorLogicPro.com" target="_blank">VisitorLogicPro.com</a> to obtain a license code and activate the advanced features.</p>
            <h4>Display Logic Blocks By Author</h4>
           	<ul class="notes">
                <li>You can choose to display or hide logic blocks depending on the author of a single post or an author page a visitor is on.</li>
             </ul>
  </div>
</div>

	<?php
	}
	
	public function is_used($meta)
	{
		if (!empty($meta['lp_exc_authors']) || !empty($meta['lp_inc_authors']) )
		{
			return true;
		}
		return false;
	}		
}// end class
}// and if class

$lp_authors = new LP_AUTHORS;
?>