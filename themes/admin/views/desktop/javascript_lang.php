<?php 

/**
 * Ionize Content Management system
 * Javascript Lang Class
 *
 * Put in the Lang object all Ionize lang items
 * so they will be available to javascript
 *
 * @package		Ionize
 * @author		Partikule
 * @copyright	Copyright (c) 2009, Partikule
 * @category	Javascript
 * @since		Version 0.9.6
 * @link		http://www.partikule.net
 *
 */

?>
<?php
	$items = addslashes(str_replace(array("\r\n", "\r", "\n", "\t"), ' ', json_encode($this->lang)));
?>
var Lang = JSON.decode('<?php echo $items; ?>', true);
Object.append(Lang, {
	get: function(key)
	{
		return this.language[key];
	},
	'current': '<?php echo $this->config->item('detected_lang_code'); ?>',
	'first': '<?php echo Settings::get_lang('first'); ?>',
	'default': '<?php echo Settings::get_lang('default'); ?>',

	'languages': new Array('<?php echo implode("','", array_keys($this->config->item('available_languages'))); ?>')
});
