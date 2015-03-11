<?php
if (!session_id()){	session_start(); }

if (isset($_SESSION['block_tracker']) && $_SESSION['ltime'] == round(time() /3)  )
{
	$block_tracker = $_SESSION['block_tracker'];
}
elseif(isset($_COOKIE['lp']))
{
	$block_tracker = json_decode(stripslashes($_COOKIE['lp']),true);
}
else
{
	$block_tracker = array();
}
// offsite override pixel
if (is_numeric($_GET['o']) && $block_id = $_GET['o'] )
{
	$setTime = !empty($_GET['test']) ? time() + ($_GET['test'] * 86400) : time();
	$block_tracker[$block_id]['o'] = $setTime;
}
elseif (is_numeric($_GET['r']) && $block_id = $_GET['r'] )
{
	unset($block_tracker[$block_id]);
}
elseif (isset($_GET['s']) )
{
	// s now hold a json array of all the blocks to track.

    $in = json_decode(urldecode($_GET['s']),true);
	if (is_array($in))
	{
		$setTime = !empty($_GET['test']) ? time() + ($_GET['test'] * 86400) : time();

		foreach($in as $block_id => $track_type)
		{
			if ($track_type == 'b')
			{
				$block_tracker[$block_id]['c'] = $block_tracker[$block_id]['c'] + 1;
							if (empty($block_tracker[$block_id]['f']) )
				{
					$block_tracker[$block_id]['f'] = $setTime ;
				}
				$block_tracker[$block_id]['l'] = time();
			}
			elseif ($track_type == 'x')
			{
				$block_tracker = array('s'=>1);
			}
			elseif ($track_type == 'r')
			{
				unset($block_tracker[$block_id]);
				if (isset($in['rb']) )
				{
					foreach ($in['rb'] as $r_block)
					{
						unset($block_tracker[$r_block]);
					}
				}
						
			}
			elseif ($track_type == 'o') // internal override , similiar to offsite override
			{
				$setTime = !empty($_GET['test']) ? time() + ($_GET['test'] * 86400) : time();
				$block_tracker[$block_id]['o'] = $setTime;
			}
			elseif ($track_type = 'vm')
			{
				$block_tracker[$block_id]['vm'] = $block_tracker[$block_id]['vm'] +1; 
			}
		}
	}
}

$block_tracker['s'] = $block_tracker['s'] + 1;
$block_tracker['f'] = !empty($block_tracker['f']) ? $block_tracker['f'] : time();

// dump the picture and stop the script
if (!empty($_GET['test']))
{
	echo "<pre>" . print_r($block_tracker,true) . "</pre>";
	echo round(time() / 3);
	echo "<pre> decoded : " . print_r($in, true) . "</pre>";
}
else
{
	// send the right headers
	header("Content-Type: image/gif");
	header("Cache-Control: private, no-cache, no-cache=Set-Cookie, proxy-revalidate");
	header("Expires: Wed, 11 Jan 2000 12:59:00 GMT");
	header("Last-Modified: Wed, 11 Jan 2006 12:59:00 GMT");
	header("Pragma: no-cache");
	$_SESSION['block_tracker'] = $block_tracker;
	$_SESSION['ltime'] = round(time() / 3);
	setcookie('lp', json_encode($block_tracker), time() + (86400 * 365) ,"/" );	
	echo base64_decode("R0lGODlhAQABAIAAAP///wAAACwAAAAAAQABAAACAkQBADs=");
	die(0);
	
}
exit;