<?php
/**
 * Used to create on item instance based on its definition
 * When saved, creates an item instance through : item/save
 *
 * @receives:
 * 		$item_definition
 *
 */

// These values are empty when adding a new item
$id_item = ( !empty($id_item)) ? $id_item : '';
$id_item_definition = $item_definition['id_item_definition'];

?>

<h2 class="main items"><?php echo($item_definition['title_item']) ?></h2>
<?php if ( User()->is('super-admin') && ! empty($id_item)) :?>
	<div class="main subtitle">
		<p>
			<span class="lite"><?php echo lang('ionize_label_id') ?> : </span> <?php echo $id_item; ?> |
			<span class="lite"><?php echo lang('ionize_label_key') ?> : </span> <?php echo $item_definition['name'] ?>
		</p>
	</div>
<?php endif ;?>

<div>
	<form name="itemForm" id="itemForm<?php echo $id_item; ?>" method="post" action="<?php echo admin_url() ?>item/save">

		<input type="hidden" name="id_item" value="<?php echo $id_item; ?>" />
		<input type="hidden" name="id_item_definition" value="<?php echo $item_definition['id_item_definition']; ?>" />
		<input type="hidden" name="reload" id="reloadItem<?php echo $id_item; ?>" value="0" />

		<!-- Ordering : First or last (or Element one if exists ) -->
		<?php if( empty($id_item)) :?>
			<dl class="small mb10">
				<dt>
					<label for="ordering"><?php echo lang('ionize_label_ordering'); ?></label>
				</dt>
				<dd>
					<select name="ordering" id="ordering<?php echo $id_item; ?>" class="select">
						<?php if( ! empty($id_item)) :?>
							<option value="<?php echo $ordering; ?>"><?php echo $ordering; ?></option>
						<?php endif ;?>
						<option value="first"><?php echo lang('ionize_label_ordering_first'); ?></option>
						<option value="last"><?php echo lang('ionize_label_ordering_last'); ?></option>
					</select>
				</dd>
			</dl>
		<?php endif ;?>

		<div id="itemFieldsContainer<?php echo $id_item; ?>"></div>

	</form>
</div>

<div class="buttons">
	<button class="button yes right" id="bSaveitem<?php echo $id_item; ?>" type="button" ><?php echo lang('ionize_button_save_close'); ?></button>
	<button class="button blue right ml10" id="bSaveAndStay<?php echo $id_item; ?>" type="button" ><?php echo lang('ionize_button_save'); ?></button>
	<button class="button no right" type="button" id="bCancelitem<?php echo $id_item ?>"><?php echo lang('ionize_button_cancel') ?></button>
</div>

<script type="text/javascript">

	var id_item = '<?php echo $id_item; ?>';

	// Saves and re-opens if the item is new
	$('bSaveAndStay' + id_item).addEvent('click', function()
	{
		var reload = $('reloadItem' + id_item);
		reload.set('value', 1);

		ION.JSON(
			$('itemForm' + id_item).getAttribute('action'),
			$('itemForm' + id_item),
			{
				onSuccess:function()
				{
					reload.set('value', 0);
				}
			}
		);

		if (id_item == '')
		{
			var parent = $('itemForm' + id_item).getParent('.mocha');
			parent.close();
		}
	});





</script>