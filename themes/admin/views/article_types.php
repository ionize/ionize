<?php

/**
 * Article types panel
 *
 */

?>

<div id="maincolumn">

	<!-- Title -->
	<h2 class="main article_types"><?php echo lang('ionize_title_types'); ?></h2>

	<!--
		Types container
		Loaded trough XHR
	-->
	<div class="tabcolumn pt15" id="articleTypesContainer"></div>

</div>

<script type="text/javascript">

	// Categories list
	ION.HTML(admin_url + 'article_type/get_list', '', {'update': 'articleTypesContainer'});

	/**
	 * Panel toolbox
	 *
	 */

	ION.initToolbox('article_types_toolbox');

</script>
