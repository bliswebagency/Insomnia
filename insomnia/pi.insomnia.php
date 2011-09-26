<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2003 - 2011, EllisLab, Inc.
 * @license		http://expressionengine.com/user_guide/license.html
 * @link		http://expressionengine.com
 * @since		Version 2.0
 * @filesource
 */
 
// ------------------------------------------------------------------------

/**
 * Insomnia Plugin
 *
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Plugin
 * @author		Blis Web Agency
 * @link		http://blis.net.au
 */

$plugin_info = array(
	'pi_name'		=> 'Insomnia',
	'pi_version'	=> '1.0',
	'pi_author'		=> 'Blis Web Agency',
	'pi_author_url'	=> 'http://blis.net.au',
	'pi_description'=> 'Finds relationships between entries and then combines the data into a single channel',
	'pi_usage'		=> Insomnia::usage()
);


class Insomnia {

	public $return_data;
    
	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->EE =& get_instance();
	}
	
	// ----------------------------------------------------------------
	
	/**
	 * Plugin Usage
	 */
	public static function usage()
	{
		ob_start();
?>

Ok, here's the idea; you have setup EE with 2 channels with the idea it would be a one to many relationship but you later decided it's a one to one relationship.

Given this, now it's a pain to have the data separated, so instead we decide to merge them together into one SUPER CHANNEL!

To do this you need to define your Source channel with all the data you want to suck up and the Target channel which will accommodate your data.

Next you need to make sure your target channel has the fields required to match up with the source. These should be exactly the same.

Now you need to define a relationship that can be used to identify where to put the data from each entry. We're running with Author in this case for now but I'm sure this could be expanded in future versions.

Finally, you'll need to map each of your field ids as CSVs

To make sure you don't stuff anything, we've also got a "preview" mode which is "on" by default. Setting this to "off" will execute the transfer of data.

The end result should look like this:

{exp:insomnia:run source="cars" target="drivers" source_fields="22,6,23,24,40" target_fields="41,42,43,44,45" join="author" preview="on"}
<?php
		$buffer = ob_get_contents();
		ob_end_clean();
		return $buffer;
	}
}


/* End of file pi.insomnia.php */
/* Location: /system/expressionengine/third_party/insomnia/pi.insomnia.php */