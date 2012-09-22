
<h3 class="toggler toggler-options"><?php echo lang('module_demo_title_authors'); ?></h3>

<div class="element element-options">

	<div class="element-content">

		<!-- Droppable area -->
		<div class="droppable dropDemoAuthor" data-parent="article" data-parent-id="<?php echo $article['id_article'];?>">
			<?php echo lang('module_demo_label_drop_author'); ?>
		</div>

		<!-- Linked Authors container -->
		<?php if ($article['id_article'] != '') :?>

			<div id="demoAuthorsContainer"></div>

		<?php endif ;?>

		<!--
			Button : Link one author
		-->
		<a id="btnDemoLinkAuthor" class="button light plus">
			<i class="icon-plus"></i>
			<?php echo lang('module_demo_button_link_authors') ?>
		</a>

	</div>
</div>

<script type="text/javascript">

	// Opens the authors window
	$('btnDemoLinkAuthor').addEvent('click', function()
	{
		// See : /themes/admin/javascript/ionize/ionize_window.js
		ION.dataWindow(
			'demoAuthors',						// ID of the window
			'module_demo_title_link_authors', 		// Lang term used for window title
			ION.adminUrl + 'module/demo/author/get_list', 	// URL to the content of the window
			// Window options
			{
				'width':400,
				'height':250
			},
			// Data to send by POST to the called URL
			{
				'id_article': '<?php echo $article['id_article'] ?>'
			}
		);
	});

	// Linked creators : Called when this view is loaded
	if ($('demoAuthorsContainer'))
	{
		ION.HTML(
			admin_url + 'module/demo/author/get_linked_authors',
			{
				'parent': 'article',
				'id_parent': '<?= $article['id_article'] ?>'
			},
			{'update': 'demoAuthorsContainer'}
		);
	}

</script>