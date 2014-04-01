<?php

/**
 * Displays out all the extends fields
 *
 */

?>

<div id="maincolumn">

	<h2 class="main extends"><?php echo lang('ionize_title_extend_fields') ?></h2>

	<div id="extend_fields" class="sortable-container mt20"></div>

	<script type="text/javascript">

		// Get Extends fields list
		ION.HTML(
			'extend_field/get_extend_fields',
			{},
			{'update': 'extend_fields'}
		);

	</script>

</div>

<script type="text/javascript">
	
	// Panel toolbox
	ION.initToolbox('extend_fields_toolbox');

</script>
