
<h2 class="main help mb20"><?= $title ?></h2>


<?php foreach($data as $d) :?>

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

	<h2 class="mb0"><?= $title ?></h2>

	<p><?= $d['description'] ?></p>

<?php endforeach ;?>

<div class="buttons">
	<button id="bClose<?= $UNIQ ?>" type="button" class="button yes right mr40"><?= lang('ionize_button_ok') ?></button>
</div>

<script type="text/javascript">

	// Event on btn No : Simply close the window
	$('bClose<?= $UNIQ ?>').addEvent('click', function() 
	{
		ION.closeWindow($('w<?= $table ?>Help'));
	}.bind(this));

</script>


