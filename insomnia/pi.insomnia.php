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
		$this->CI =& get_instance();		
	}
	
	public function run()
	{
		error_reporting(0);
		
		$source = $this->EE->TMPL->fetch_param('source');
		$target = $this->EE->TMPL->fetch_param('target');		
		$source_fields = $this->EE->TMPL->fetch_param('source_fields');
		$target_fields = $this->EE->TMPL->fetch_param('target_fields');	
		$join = $this->EE->TMPL->fetch_param('join');
		$preview = $this->EE->TMPL->fetch_param('preview');
		
		$source_field_array=explode(",", $source_fields);
		$target_field_array=explode(",", $target_fields);
		
		//DO THE JOIN (author)
		if ($join == "author"){
			
			//CREATE AN ARRAY OF AUTHORS
			$results = $this->EE->db->query("SELECT member_id FROM exp_members");			
			
			//FOR EACH AUTHOR:
			foreach($results->result_array() as $row){

				//FIND THE ENTRY IN THE SOURCE SOURCE
				$author_id = $row['member_id'];
				
				$query = "SELECT entry_id FROM exp_channel_titles WHERE channel_id = \"$source\" AND author_id = \"$author_id\" LIMIT 0,1";
				$title_results_source = $this->EE->db->query($query);				
				$source_entries[$author_id] = $title_results_source->row('entry_id');


				//FIND THE ENTRY IN THE TARGET CHANNEL				
				$query = "SELECT entry_id FROM exp_channel_titles WHERE channel_id = \"$target\" AND author_id = \"$author_id\" LIMIT 0,1";
				$title_results_target = $this->EE->db->query($query);				
				$target_entries[$author_id] = $title_results_target->row('entry_id');				
			
				//LOOP THROUGH EACH SOURCE FIELD
				for ($i=0;$source_field_array[$i] != NULL;$i++){
					
					//GET SOURCE VALUE
					$field_val = "field_id_".$source_field_array[$i];
					$query = "SELECT $field_val FROM exp_channel_data WHERE entry_id = \"".$source_entries[$author_id]."\" LIMIT 0,1";
					$results_source = $this->EE->db->query($query);				
				    $source_val = $results_source->row($field_val);

					if ($source_val == NULL) continue;
					
					$out .= $source_val . "|";
					
					if ($preview == "off"){
						$target_field_val = "field_id_".$target_field_array[$i];
						$data = array($target_field_val => $source_val);
						$sql = $this->EE->db->update_string('exp_channel_data', $data, "entry_id = '".$target_entries[$author_id]."'");
						$this->EE->db->query($sql);
					}
					$out .= "Wrote Entry ".$source_entries[$author_id]."($field_val): $source_val to Entry " . $target_entries[$author_id] . "($target_field_val)";
					
					

				}
				$out .= "<br />";

			}
			
			
				
				
			
		}
				
		
		//LOOP THROUGH SOURCE FIELDS
		/*
		for ($i=0;$source_field_array[$i] != NULL;$i++){
			$out .= $target_field_array[$i];
		}
		*/		
		
		return $out;							
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