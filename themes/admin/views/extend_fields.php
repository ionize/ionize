<?php

/**
 * Displays out all the extends fields
 *
 */
    log_message('error', 'View File Loaded : extend_fields.php');
?>


<div id="maincolumn">

	<!-- Page extend fields -->
	<?php if ($this->connect->is('admins') ) :?>

		
		<!-- Tables of existing extended fields.
			 Must be named like this : extend_fields_<table_type>
		-->
		<div id="extend_fields" class="sortable-container"></div>


		<script type="text/javascript">
			
			/**
			 * Get Extends fields table
			 *
			 */
			ION.updateElement({element:'extend_fields', url:'extend_field/get_extend_fields'});
			
		</script>


	<?php endif ;?>
	
</div>

<script type="text/javascript">
	
	/**
	 * Panel toolbox
	 *
	 */
	ION.initToolbox('extend_fields_toolbox');

</script>
