<?php
/**
 * Tags panel
 *
 */
?>
<style type="text/css">
	.textboxlist-bit-editable:after{
		content: "<?php echo lang('ionize_help_tag_textbox') ?>";
	}
</style>

<div id="maincolumn">

	<!-- Title -->
	<h2 class="main tags"><?php echo lang('ionize_title_tags'); ?></h2>

	<!--
		Tags container
		Loaded trough XHR
	-->
	<div class="tabcolumn pt15" id="tagsContainer">

		<form name="tagsForm" id="tagsForm" method="post">

			<input type="text" name="tags" value="" id="tags" />

		</form>

	</div>

</div>


<script type="text/javascript">

	// Tags
	var tags = new TextboxList(
		'tags',
		{
			unique: true,
			plugins: {autocomplete: {placeholder:null}}
		}
	);

	tags.container.addClass('textboxlist-loading');

	ION.JSON(
		ION.adminUrl + 'tag/get_json_list',{},
		{
			onSuccess: function(r)
			{
				tags.plugins['autocomplete'].setValues(r);

				ION.JSON(
					ION.adminUrl + 'tag/get_json_list',
					{},
					{
						onSuccess: function(r)
						{
							tags.container.removeClass('textboxlist-loading');
							tags.plugins['autocomplete'].setSelected(r);
						}
					}
				);
			}
		}
	);

	// Tool box
	ION.initToolbox('tags_toolbox');

</script>
