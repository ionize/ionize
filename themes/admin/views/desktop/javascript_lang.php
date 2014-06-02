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
	get: function()
	{
		var args = Array.prototype.slice.call(arguments);

		var str = this.language[args[0]];

		if (args.length > 1)
		{
			if (typeOf(args[1]) == 'array')
				str = vsprintf(str, args[1]);
			else
				str = sprintf(str, args[1]);
		}
		return str;
	},
	'current': '<?php echo $this->config->item('detected_lang_code'); ?>',
	'first': '<?php echo Settings::get_lang('first'); ?>',
	'default': '<?php echo Settings::get_lang('default'); ?>',

	'languages': new Array('<?php echo implode("','", array_keys($this->config->item('available_languages'))); ?>')
});
