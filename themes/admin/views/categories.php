<?php

/**
 * Categories panel
 *
 */

?>
<div id="maincolumn">

	<!-- Title -->
	<h2 class="main categories"><?php echo lang('ionize_title_categories'); ?></h2>

	<!--
		Categories container
		Loaded trough XHR
	-->
	<div class="tabcolumn pt15" id="categoriesContainer"></div>

</div>


<script type="text/javascript">

	// Categories list
	ION.HTML(admin_url + 'category/get_list', '', {'update': 'categoriesContainer'});

	/**
	 * Panel toolbox
	 *
	 */

	ION.initToolbox('categories_toolbox');

</script>
