<?php
/**
 * Media List
 *
 */
?>

<div id="medialistContainer"></div>

<script type="text/javascript">

	ION.initToolbox('medialist_toolbox');

	ION.HTML(
		ION.adminUrl + 'medialist/get_list',
		{},
		{
			update:'medialistContainer'
		}
	);

</script>

