
<h2 class="main video"><?php echo lang('ionize_label_add_video') ?></h2>
<div class="main subtitle ">
	<p><span class="lite"><?php echo lang('ionize_message_paste_video_url') ?></span></p>
</div>

<form name="addVideoForm" id="addVideoForm">

	<textarea id="externalVideoUrl" name="addVideo" style="width:80%" class="inputtext autogrow left ml40" type="text"></textarea>

</form>

<div class="buttons">
	<button class="button yes right" id="bAddUrl" type="button" ><?php echo lang('ionize_button_save_close'); ?></button>
	<button class="button no right" type="button" id="bCancelUrl"><?php echo lang('ionize_button_cancel') ?></button>
</div>

<script type="text/javascript">

	ION.initFormAutoGrow('addVideoForm');

	// Add Video button
	$('bAddUrl').addEvent('click', function()
	{
		if ($('externalVideoUrl').value !='')
		{
			ION.JSON(
				'media/add_external_media',
				{
					'type': 'video',
					'parent': '<?php echo $parent ?>',
					'id_parent': '<?php echo $id_parent ?>',
					'path': $('externalVideoUrl').value
				},
				{
					onSuccess: function()
					{
						MUI.get('waddExternalMedia').close();
					}
				}
			);
		}
		return false;
	});

	$('bCancelUrl').addEvent('click', function(e)
	{
		MUI.get('waddExternalMedia').close();
	});

</script>