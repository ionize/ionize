
<h2 class="main help mb20"><?php echo html_entity_decode($title); ?></h2>


<?php foreach($data as $d): ?>

	<?php
		$title = '';
		$fields = array('name', 'title', 'type');

		foreach($fields as $field)
		{
			if (isset($d[$field]))
			{
				$title = $d[$field];
				break;
			}
		}
	?>

	<h2 class="mb0"><?php echo $title; ?></h2>

	<p><?php echo $d['description']; ?></p>

<?php endforeach ;?>

<div class="buttons">
	<button id="bClose<?php echo $UNIQ; ?>" type="button" class="button yes right"><?php echo lang('ionize_button_ok'); ?></button>
</div>

<script type="text/javascript">

	// Event on btn No : Simply close the window
	$('bClose<?php echo $UNIQ; ?>').addEvent('click', function()
	{
		ION.closeWindow($('w<?php echo $table; ?>Help'));
	}.bind(this));

</script>


