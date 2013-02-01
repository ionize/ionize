<?php 

/**
 * Ionize Content Management system
 * Javascript Settings Class
 *
 * Put in the Settings object all Ionize Settings
 * so they will be available to javascript
 *
 * @package		Ionize
 * @author		Partikule
 * @copyright	Copyright (c) 2013, Partikule
 * @category	Javascript
 * @since		Version 1.0
 * @link		http://www.partikule.net
 *
 */

?>
<?php
	$settings = json_encode(Settings::get_settings());
?>
var Settings = {
	'setting' : JSON.decode(<?php echo json_encode($settings); ?>, true)
}
Object.append(Settings, {
	get: function(key)
	{
		return this.setting[key];
	}
});
