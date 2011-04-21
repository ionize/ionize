<?php

/**
 * Displays out all the extends fields
 *
 */
?>


<div id="maincolumn">

	<!-- Page extend fields -->
	<?php if ($this->connect->is('admins') ) :?>

		

		<!-- Tables of existing extended fields.
			 Must be named like this : extend_fields_<table_type>
		-->
		<div id="extend_fields" class="sortable-container"></div>

		<div id="extend_fields_page" class="sortable-container"></div>
		
		<div id="extend_fields_article" class="sortable-container"></div>
		
		<div id="extend_fields_media" class="sortable-container"></div>

		<div id="extend_fields_users" class="sortable-container"></div>
		

		<script type="text/javascript">
			
			/**
			 * Get Extends fields table
			 *
			 */
			ION.updateElement({element:'extend_fields', url:'extend_field/get_extend_fields'});
			
			ION.updateElement({element:'extend_fields_page', url:'extend_field/get_extend_fields/page'});
			
			ION.updateElement({element:'extend_fields_article', url:'extend_field/get_extend_fields/article'});
			
			ION.updateElement({element:'extend_fields_media', url:'extend_field/get_extend_fields/media'});

//			ION.updateElement({element:'extend_fields_users', url:'extend_field/get_element_extend_fields_table/users'});
			
		//	ION.updateElement({element:'extend_fields_setting', url:'extend_field/get_element_extend_fields_table/settings'});
		
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
