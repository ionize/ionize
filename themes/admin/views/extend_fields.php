<?php

/**
 * Displays out all the extends fields
 *
 */
?>


<div id="maincolumn">

	<!-- Page extend fields -->
	<?php if ($this->connect->is('admins') && Settings::get('use_extend_fields') == '1') :?>

		

		<!-- Tables of existing extended fields.
			 Must be named like this : extend_fields_<table_type>
		-->
		<div id="extend_fields_page"></div>
		
		<div id="extend_fields_article"></div>
		
		<div id="extend_fields_media"></div>

		<div id="extend_fields_users"></div>
		

		<script type="text/javascript">
			
			/**
			 * Get Extends fields table
			 *
			 */
			MUI.updateElement({element:'extend_fields_page', url:'extend_field/get_element_extend_fields_table/page'});
			
			MUI.updateElement({element:'extend_fields_article', url:'extend_field/get_element_extend_fields_table/article'});
			
			MUI.updateElement({element:'extend_fields_media', url:'extend_field/get_element_extend_fields_table/media'});

//			MUI.updateElement({element:'extend_fields_users', url:'extend_field/get_element_extend_fields_table/users'});
			
		//	MUI.updateElement({element:'extend_fields_setting', url:'extend_field/get_element_extend_fields_table/settings'});
		
		</script>

	<?php endif ;?>
	
</div>

<script type="text/javascript">
	
	/**
	 * Panel toolbox
	 *
	 */
	MUI.initToolbox('extend_fields_toolbox');

</script>
