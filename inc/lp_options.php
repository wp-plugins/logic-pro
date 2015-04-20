<div class="wrap lp-options">
	<div id="icon-options-general" class="icon32">
		<br>
	</div>
	<img class="lp-logo" src="<?php echo plugins_url('images/Visitor-Logic-Pro-300.png', dirname(__FILE__));?>" alt="<?php _e("Visitor Logic Lite Options", LP_DOMAIN); ?>" />

	<div class="visit-site">
		<a href="http://VisitorLogicPro.com"><?php _e('Visit plugin site'); ?>
		</a>  
	</div>
    <div class="clearfix"></div>
<?php
 global $lp_core;
 $lp_admin_settings = get_option('lp_admin_settings');
 
 foreach ($lp_core->reg_modules as $lp_module)
 {
	 $classname = 'lp_' . $lp_module;
	 if (class_exists($classname))
	  { 
  	 	$lp_class = new $classname;
		if (method_exists($lp_class, 'admin_settings'))
		{
			$lp_class->admin_settings($lp_admin_settings);
		}
	 }
 }

?>

</div>
