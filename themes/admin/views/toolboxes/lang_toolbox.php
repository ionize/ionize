
<div class="divider">
	<a class="button light" id="newLangToolbarButton">
		<i class="icon-plus"></i><?php echo lang('ionize_label_new_lang'); ?>
	</a>
</div>

<script type="text/javascript">

	// Button New lang
	$('newLangToolbarButton').addEvent('click', function(e)
	{
		ION.formWindow(
			'lang',
			'langForm',
			Lang.get('ionize_label_new_lang'),
			'lang/get_form',
			{
				width:350,
				height:130
			}
		);
	});

</script>
