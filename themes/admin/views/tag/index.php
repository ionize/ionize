<?php
/**
 * Tags panel
 *
 */
?>
<style type="text/css">
	li.sortme input:focus, ul.list li input:focus {
		border: none;
		background-color: #fff;
		padding:2px 4px;
	}
</style>

<div id="maincolumn">

	<!-- Title -->
	<h2 class="main tags"><?php echo lang('ionize_title_tags'); ?></h2>

	<!--
		Tags container
		Loaded trough XHR
	-->
	<div class="mb10 h30">
		<form name="tagForm" id="tagForm" method="post">
			<input  id="inputAddTag" type="text" class="inputtext w180 left" name="tag" value="" id="tag" />
			<button id="btnAddTag" class="button green left ml5"><?php echo lang('ionize_button_add_tag'); ?></button>
		</form>
	</div>

	<div id="tagsContainer"></div>

</div>


<script type="text/javascript">

	// Tool box
	ION.initToolbox('empty_toolbox');

	// Tags list
	ION.HTML(admin_url + 'tag/get_list', '', {'update': 'tagsContainer'});

	// New tag
	$('btnAddTag').addEvent('click', function(e)
	{
		e.stop();
		if ($('inputAddTag').value != '')
		{
			ION.sendData('tag/add', {
				'tag_name':$('inputAddTag').value
			});
		}
	});


</script>
