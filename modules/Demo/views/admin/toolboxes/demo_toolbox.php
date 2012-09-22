
<div class="divider">
	<a class="button light" id="newAuthorToolbarButton">
		<i class="icon-plus"></i><?= lang('module_demo_button_create_author') ?>
	</a>
</div>

<script type="text/javascript">

	$('newAuthorToolbarButton').addEvent('click', function(e)
	{
		ION.formWindow(
			'author',
			'authorForm',
			Lang.get('module_demo_label_new_author'),
			admin_url + 'module/demo/author/create',
			{
				'width':350,
				'height':200
			}
		);
	});

</script>
