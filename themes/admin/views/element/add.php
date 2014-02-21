<?php

/**
 * Modal window for elements list
 * Used by element/add_element/ when the user clicks on the "Add Element" button
 *
 */

?>

<h2 class="main elements" id="titleAddContentElement"><?php echo lang('ionize_title_add_content_element'); ?></h2>

<ul id="elementAddContainer"></ul>
	
<script type="text/javascript">

	ION.HTML(
		'element_definition/get_element_list',
		{
			'parent':'<?php echo $parent; ?>',
			'id_parent': '<?php echo $id_parent; ?>'
		},
		{
			'update': 'elementAddContainer'
		}
	);
	
</script>
