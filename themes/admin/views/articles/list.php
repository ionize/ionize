<?php
/**
 * Article List
 * Loaded through XHR
 *
 */
?>

<?php if ($articles_pages > 1 OR $current_page > 1) :?>
	<!-- Pages -->
	<ul class="pagination mt5" id="articles_pagination">
		<?php
		for($i=1; $i<=$articles_pages; $i++)
		{
			?>
			<li><a <?php if($i == $current_page) :?>class="current"<?php endif; ?> data-page-number="<?php echo $i ?>"><?php echo $i ?></a></li>
		<?php
		}
		?>
	</ul>
<?php endif; ?>


<table class="list" id="articlesTable">

	<thead>
	<tr>
		<th axis="string"><?php echo lang('ionize_label_pages'); ?></th>
		<th axis="string"><?php echo lang('ionize_label_article'); ?></th>
		<th axis="string" class="center"><?php echo lang('ionize_label_content_ok'); ?></th>
		<th axis="string" class="center"><?php echo lang('ionize_label_content_missing'); ?></th>
		<th class="right" style="width:70px;"></th>
	</tr>
	</thead>

	<tbody>

	<?php foreach ($articles as $id_article => $article) :?>

		<?php
		$data = $article['data'];
		$title = ($data['title'] != '') ? $data['title'] : $data['name'];

		$has_content_for_lang = array();
		?>

		<tr class="article">

			<!-- Pages -->
			<td class="pl10">
				<?php if(empty($data['pages'])) :?>
					<img class="help" src="<?php echo theme_url(); ?>images/icon_16_alert.png" title="<?php echo lang('ionize_help_orphan_article'); ?>" />
				<?php endif; ?>

				<?php foreach( $data['pages'] as $idx => $page) :?>
					<?php if ($idx > 0) :?> <span class="lite">&bull;</span><?php endif ?>
					<a class="page-breadcrumb" data-id="<?php echo $page['id_page'] ?>"><?php echo $page['breadcrumb'] ?></a>
				<?php endforeach ;?>
			</td>

			<!-- Title -->
			<td>
				<a class="title" data-id="<?php echo $data['id_page'].'.'.$data['id_article'] ?>"><?php echo $title; ?></a>
			</td>

			<!-- Content missing alert -->
			<td class="center">
				<?php foreach(Settings::get_languages() as $language) :?>

					<?php if ( isset($article[$language['lang']]) && $article[$language['lang']]['content'] != '') : ?>
						<img class="pr5" src="<?php echo admin_style_url(); ?>images/world_flags/flag_<?php echo $language['lang'] ?>.gif" />
					<?php endif; ?>
				<?php endforeach ;?>
			</td>

			<td class="center">
				<?php foreach(Settings::get_languages() as $language) :?>

					<?php if ( ! isset($article[$language['lang']]) OR $article[$language['lang']]['content'] == '') : ?>
						<img class="pr5" src="<?php echo admin_style_url(); ?>images/world_flags/flag_<?php echo $language['lang'] ?>.gif" />
					<?php endif; ?>

				<?php endforeach ;?>
			</td>

			<td class="pr10">
				<a class="icon right delete" data-id="<?php echo $id_article; ?>"></a>
			</td>

		</tr>
	<?php endforeach ;?>
	</tbody>

</table>

<script type="text/javascript">

	// Sortable
	new SortableTable('articlesTable',{sortOn: 0, sortBy: 'ASC'});

	// Make each article draggable
	$$('#articlesTable .article .title').each(function(item)
	{
		ION.addDragDrop(
			item,
			'.dropArticleInPage,.dropArticleAsLink,.folder',
			'ION.dropArticleInPage,ION.dropArticleAsLink,ION.dropArticleInPage'
		);

		var rel = (item.getProperty('data-id')).split('.');
		var id_page = rel[0];
		var id_article = rel[1];
		var title = item.get('text');

		item.addEvent('click', function(e)
		{
			e.stop();
			ION.splitPanel({
				'urlMain': ION.adminUrl + 'article/edit/' + id_page + '.' + id_article,
				'urlOptions': ION.adminUrl + 'article/get_options/' + id_page + '.' + id_article,
				'title': Lang.get('ionize_title_edit_page') + ' : ' + title
			});
		});

	});

	var confirmDeleteMessage = Lang.get('ionize_confirm_element_delete');
	var url = admin_url + 'article/delete/';

	$$('#articlesTable .delete').each(function(item)
	{
		ION.initRequestEvent(
			item,
			url + item.getProperty('data-id'),
			{},
			{
				'confirm':true,
				'message': confirmDeleteMessage,
				'onSuccess': function()
				{
					$('btnSubmitFilter').fireEvent('click');
				}
			}
		);
	});




	$$('#articlesTable .page-breadcrumb').each(function(item)
	{
		item.addEvent('click', function(e)
		{
			e.stop();
			var id = item.getProperty('data-id');
			var title = item.get('text');

			ION.splitPanel({
				'urlMain': ION.adminUrl + 'page/edit/' + id,
				'urlOptions': ION.adminUrl + 'page/get_options/' + id,
				'title': Lang.get('ionize_title_edit_page') + ' : ' + title
			});
		});
	});


	// Pagination
	$$('#articles_pagination li a').each(function(item)
	{
		item.addEvent('click', function(e)
		{
			e.stop();

			new Request.HTML({
				url: ION.adminUrl + 'article/get_articles_list/' + this.getProperty('data-page-number'),
				method: 'post',
				loadMethod: 'xhr',
				data: $('articleFilter'),
				update: $('articleList')
			}).send();
		});
	});


</script>