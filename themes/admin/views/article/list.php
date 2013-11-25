

<ul id="articleList<?php echo $id_page; ?>" class="sortable-container">

<?php
	$nbLang = count(Settings::get_languages());
	$flag_width = (25 * $nbLang);
?>

<?php foreach ($articles as $article) :?>

	<?php
	
	$title = ($article['title'] != '') ? $article['title'] : $article['name'];
	
	$rel = $article['id_page'] . '.' . $article['id_article'];
	
	$flat_rel = $article['id_page'] . 'x' .  $article['id_article'];
	
	$status = (!$article['online_in_page']) ? 'offline' : 'online' ;

	// Content for each existing language
	$content_html = '';
	
	// Array of status
	$content = array();
	
	foreach($article['langs'] as $lang)
	{
		if ($lang['content'] != '') $content[] = '<img class="left pl5 pt3" src="'. theme_url() . 'images/world_flags/flag_' . $lang['lang'] . '.gif" />';
	}
	
	// HTML
	$content_html = implode('', $content);
	
	?>

	<li id="articleinpage<?php echo $article['id_article']; ?>" class="sortme article<?php echo $article['id_article']; ?> article<?php echo $flat_rel; ?> <?php echo $status ;?>" data-id="<?php echo $rel; ?>">
		
		<!-- Drag icon -->
		<span class="icon left drag mr5"></span>

		<!-- Status icon -->
		<a class="icon right mr5 status article<?php echo $article['id_article']; ?> article<?php echo $flat_rel; ?> <?php echo $status ;?>" data-id="<?php echo $rel; ?>"></a>

		<!-- Unlink icon -->
		<a class="icon right mr5 unlink" data-id="<?php echo $rel; ?>" title="<?php echo lang('ionize_label_unlink'); ?>"></a>
		
		<!-- Flags : Available content for language -->
		<span style="width:<?php echo$flag_width?>px;display:block;height:16px;" class="right mr10 ml10"><?php echo $content_html; ?></span>

		<!-- Type -->
		<span class="right ml10 type-block" data-id="<?php echo $rel; ?>">
			
			<select id="type<?php echo $flat_rel; ?>" class="select w80 type left" style="padding:0;" data-id="<?php echo $rel; ?>">
				<?php foreach($all_article_types as $idx => $type) :?>
					<option <?php if ($article['id_type'] == $idx) :?>selected="selected"<?php endif; ?>  value="<?php echo $idx; ?>"><?php echo $type; ?></option>
				<?php endforeach ;?>
			</select>

		</span>

		<!-- Used view -->
		<span class="right ml10">
		
			<select id="view<?php echo $flat_rel; ?>" class="select w110 view" style="padding:0;" data-id="<?php echo $rel; ?>">
				<?php foreach($all_article_views as $idx => $view) :?>
					<option <?php if ($article['view'] == $idx) :?>selected="selected"<?php endif; ?> value="<?php echo $idx; ?>"><?php echo $view; ?></option>
				<?php endforeach ;?>
			</select>
		
		</span>
		
		<!-- Main parent page -->
		<?php if (count($article['pages']) > 1) :?>
			<span class="right type-block" data-id="<?php echo $rel; ?>">
				
				<select id="amp<?php echo $flat_rel; ?>" class="select w100 parent left" style="padding:0;" data-id="<?php echo $rel; ?>">
					<?php foreach($article['pages'] as $page) :?>
						<option <?php if ($page['main_parent'] == '1') :?>selected="selected"<?php endif; ?> value="<?php echo $page['id_page']; ?>"><?php echo $page['title']; ?></option>
					<?php endforeach ;?>
				</select>
	
			</span>
		<?php endif ;?>

		<!-- Title (draggable) -->
		<a style="overflow:hidden;height:16px;display:block;" class=" pl5 pr10 article article<?php echo $flat_rel; ?> <?php echo $status ;?>" title="<?php echo lang('ionize_label_edit'); ?> / <?php echo lang('ionize_label_drag_to_page'); ?>" data-id="<?php echo $rel; ?>"><span class="flag flag<?php echo $article['flag']; ?>"></span><?php echo $title; ?></a>
	</li>

<?php endforeach ;?>

</ul>

<script type="text/javascript">

	// Articles view / type select for articles list
	$$('#articleList<?php echo $id_page; ?> .type').each(function(item)
	{
		var rel = item.getAttribute('data-id').split(".");

		item.addEvents({
		
			'change': function(e)
			{
				this.removeClass('a');
				
				if (this.value != '0' && this.value != '') { this.addClass('a'); }

				ION.JSON(
					admin_url + 'article/save_context', 
					{
						'id_page': rel[0],
						'id_article': rel[1],
						'id_type' : this.value
					}
				);
			}
		});
	});

	$$('#articleList<?php echo $id_page; ?> .view').each(function(item)
	{
		var rel = item.getAttribute('data-id').split(".");

		item.addEvents({
		
			'change': function(e)
			{
				this.removeClass('a');
				
				if (this.value != '0' && this.value != '') { this.addClass('a'); }

				ION.JSON(
					admin_url + 'article/save_context', 
					{
						'id_page': rel[0],
						'id_article': rel[1],
						'view' : this.value
					}
				);
			}
		});
	});
	
	$$('#articleList<?php echo $id_page; ?> .parent').each(function(item)
	{
		var rel = item.getAttribute('data-id').split(".");

		item.addEvents({
		
			'change': function(e)
			{
				this.removeClass('a');
				
				if (this.value != '0' && this.value != '') { this.addClass('a'); }

				ION.JSON(
					admin_url + 'article/save_main_parent', 
					{
						'id_page': this.value,
						'id_article': rel[1]
					}
				);
			}
		});
	});
	
	// Makes article title draggable
	$$('#articleList<?php echo $id_page; ?> .article').each(function(item)
	{
		var id_article = item.getProperty('data-id');
		var title = item.get('text');
		
		// Drag / Drop
		ION.addDragDrop(item, '.folder', 'ION.dropArticleInPage');
		
		// Edit link
		item.addEvent('click', function(e) {
			e.stop();
            ION.splitPanel({
                'urlMain': admin_url + 'article/edit/' + id_article,
                'urlOptions': admin_url + 'article/get_options/' + id_article,
                'title': Lang.get('ionize_title_edit_article') + ' : ' + title
            });
		});
	});

	// Article list itemManager
	articleManager = new ION.ArticleManager({container: 'articleList<?php echo $id_page; ?>', 'id_parent':'<?php echo $id_page; ?>'});

</script>
