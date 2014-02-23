<?php
/**
 * Report of unused media
 * Gives ability to remove unused physical files
 *
 * Receives :
 * 	$nb :	Number of unused media
 *  $size :	Disk space used by all non used media
 *
 */

$mimes = Settings::get_mimes_types();
$pictures = array_keys($mimes['picture']);

?>

<?php echo lang('ionize_text_unused_media_files') ?>
<hr/>
<?php echo lang('ionize_title_medias') ?> : <b><?php echo count($files) ?></b><br/>
<?php echo lang('ionize_label_media_size') ?> : <b><?php echo $size ?></b><br/>

<hr />
<p class="30">
	<a id="btnDeleteUnusedMedia" class="button red right">
		<?php echo lang('ionize_button_delete_selected_files') ?>
	</a>
	<a id="btnCheckUnusedMedia" class="button light">
		<?php echo lang('ionize_button_check_uncheck_all') ?>
	</a>
</p>

<form id="formUnusedMedia">
	<table>
		<?php foreach($files as $idx => $file) :?>
			<tr>
				<td>
					<input id="file<?php echo $idx; ?>" name="files[]" type="checkbox" value="<?php echo $file['path'] ?>" />
				</td>
				<td>
					<label for="file<?php echo $idx; ?>">
						<?php if (in_array(pathinfo($file['path'], PATHINFO_EXTENSION), $pictures)) :?>
							<img src="<?php echo base_url() ?><?php echo $file['path'] ?>" style="max-width:150px;background-color: #777"/>
						<?php endif ;?>
					</label>
				</td>
				<td>
					<label for="file<?php echo $idx; ?>" class="m0">
						<?php echo $file['path'] ?><br/>
						<?php echo $file['size'] ?>
					</label>
				</td>
			</tr>

		<?php endforeach; ?>
	</table>
</form>

<script type="text/javascript">

	var form = $('formUnusedMedia');

	$('btnCheckUnusedMedia').addEvent('click', function()
	{
		var status = $('btnCheckUnusedMedia').retrieve('status');
		var cbs = $('formUnusedMedia').getElements('input');

		if ( ! status)
		{
			form.getElements('input[type=checkbox]').setProperty('checked', 'checked');
			this.store('status', 'checked')
		}
		else
		{
			form.getElements('input[type=checkbox]').removeProperty('checked');
			this.eliminate('status');
		}
	});

	ION.initRequestEvent(
		$('btnDeleteUnusedMedia'),
		ION.adminUrl + 'system_check/unused_media_delete',
		$('formUnusedMedia'),
		{
			confirm: true,
			message: 'ionize_modal_confirmation_title',
			update:'unusedMediaContainer'
		},
		'HTML'
	);

</script>