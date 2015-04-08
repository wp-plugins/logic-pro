<?php
if (!class_exists('lp_CORE'))
{

class lp_CORE
{
	public $tracking = array();
	public $reg_modules;
	public function __construct()
	{
		$this->reg_modules = array();
		add_action('save_post', array($this,'save_meta'));
	}

	public function register($name)
	{
		$this->reg_modules = $this->reg_modules + $name;
		ksort($this->reg_modules);
	}

	/* Save the post metadata. */
	public function save_meta() {
	    global $post;
		
    	if ($_POST['lp_hidden'] == 'true') 
		{
			$lp_settings = $_POST['lp_settings'];
			foreach ($this->reg_modules as $lp_order => $lp_module)
			{
			   $classname = 'lp_' . strtoupper($lp_module);

			   if (class_exists($classname))
				{ 
				  $lp_class = new $classname;
				   
				   	  if (method_exists ($lp_class, 'save') )
					{  
					   lp_log("Save_meta : " . $classname . print_r($lp_settings,true));
						$lp_settings = $lp_class->save($lp_settings); 
					   lp_log("Save_meta : after :" . $classname . print_r($lp_settings,true));
					}
			   }
			}
			
			update_post_meta($post->ID, 'lp_settings', $lp_settings);
		}
	}

	public function get_meta($id)
	{
		return get_post_meta($id, 'lp_settings', true);
	}

	public function process_logic($id, $debug = false, $use_content = NULL)
	{
		//  function will return content to be displayed.  returns false if the content is not to be displayed
		
		$block = get_post($id);
		$meta = $this->get_meta($id);
		$meta['id'] = $id;
		$track_type = 'b';
		
		if ($meta['lp_enable'] == 'off')
		{
			if ($debug)
			{
				return $meta['id'] . " - General setting is Off.";
			}
			return false;
		}

		if ($meta['lp_enable'] == 'on')
		{
			$this->tracking[$id] = $track_type;
			return $use_content ? $use_content : $block->post_content;
		}
		
		foreach ($this->reg_modules as $lp_order => $lp_module)
		{
		   $classname = 'lp_' . strtoupper($lp_module);

		   if (class_exists($classname))
			{ 
				$result = array();
				$lp_class = new $classname;
			  	if (method_exists ($lp_class, 'process_logic') )
				{  
					$result = $lp_class->process_logic($meta, $debug, $use_content);
				}
				else
				{
					$result['skip'] = true;
				}
				
				if ($result['override'] == true)
				{
					$track_type = 'o';
				}
				if ($result['reset'] == true )
			 	{
					$track_type = 'r';
					$this->tracking['rb'] = $result['reset_blocks'];
				}
				if ($result['count_view'] == true)
				{
					$this->tracking[$id] = 'vm';
				}
				
				
			  	if ($result['skip'] == true || $result['show'] == true )
			 	{
					continue;
				}
				elseif (!empty($result['content']) )
				{
					echo "Here";
				  	return $result['content'];
				}
				else
				{
					echo "HER 2";
				  return $debug ? $result['debugMsg'] : FALSE;
				}
		   }
		}
		$this->tracking[$id] = $track_type;
		
		return $use_content ? $use_content : $block->post_content ;
		
	}
	
	public function track_posts()
	{
		global $post;
		$cur_post = $post->ID;
		
		$sw_args = array(
		    'post_type' => 'logicblock',
		    'meta_query' => array(
        		array(
            		'key' => 'lp_settings',
            		'value' => 'lp_view_p',
            		'compare' => 'LIKE'
        			)
        		)
			);
			
		$query = new WP_Query( $sw_args );
		$posts = array();

		while( $query->have_posts() )
		{
			$query->the_post();
			$meta = $this->get_meta($query->post->ID);
			if ( (is_array($meta['lp_view_pages']) && in_array($cur_post, $meta['lp_view_pages']) )
			   ||(is_array($meta['lp_view_posts']) && in_array($cur_post, $meta['lp_view_posts']) )
			   )
			{
				$this->tracking[$query->post->ID] = 'o';
				break;
			}
		}
		wp_reset_postdata();
	}
	
}
} //  end if class

global $lp_core;
$lp_core = new lp_CORE();

foreach (new DirectoryIterator(plugin_dir_path( __FILE__ ) . 'modules') as $fileInfo) {
    if($fileInfo->isDot()) continue;
     include_once ($fileInfo->getPathname() ) ;
}
?>