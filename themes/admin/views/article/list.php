

<ul id="articleList<?= $id_page ?>" class="sortable-container">

<?php
	$nbLang = count(Settings::get_languages());
	$flag_width = (25 * $nbLang);
?>

<?php foreach ($articles as $article) :?>

	<?php
	
	$title = ($article['title'] != '') ? $article['title'] : $article['name'];
	
	$rel = $article['id_page'] . '.' . $article['id_article'];
	
	$flat_rel = $article['id_page'] . 'x' .  $article['id_article'];
	
	$status = (!$article['online']) ? 'offline' : 'online' ;

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

	<li id="articleinpage<?= $article['id_article'] ?>" class="sortme article<?= $article['id_article'] ?> article<?= $flat_rel ?> <?= $status ;?>" rel="<?= $rel ?>">
		
		<!-- Drag icon -->
		<span class="icon left drag mr5"></span>

		<!-- Status icon -->
		<a class="icon right mr5 status article<?= $article['id_article'] ?> article<?= $flat_rel ?> <?= $status ;?>" rel="<?= $rel ?>"></a>

		<!-- Unlink icon -->
		<a class="icon right mr5 unlink" rel="<?= $rel ?>" title="<?= lang('ionize_label_unlink') ?>"></a>
		
		
		<!-- Flags : Available content for language -->
		<span style="width:<?=$flag_width?>px;display:block;height:16px;" class="right mr10 ml10"><?= $content_html ?></span>

	

		<!-- Type -->
		<span class="right ml10 type-block" rel="<?= $rel ?>">
			
			<select id="type<?= $flat_rel ?>" class="select w80 type left" style="padding:0;" rel="<?= $rel ?>">
				<?php foreach($all_article_types as $idx => $type) :?>
					<option <?php if ($article['id_type'] == $idx) :?>selected="selected"<?php endif; ?>  value="<?= $idx ?>"><?= $type ?></option>
				<?php endforeach ;?>
			</select>

		</span>

		<!-- Used view -->
		<span class="right ml10">
		
			<select id="view<?= $flat_rel ?>" class="select w110 view" style="padding:0;" rel="<?= $rel ?>">
				<?php foreach($all_article_views as $idx => $view) :?>
					<option <?php if ($article['view'] == $idx) :?>selected="selected"<?php endif; ?> value="<?= $idx ?>"><?= $view ?></option>
				<?php endforeach ;?>
			</select>
		
		</span>
		
		<!-- Main parent page -->
		<?php if (count($article['pages']) > 1) :?>
			<span class="right type-block" rel="<?= $rel ?>">
				
				<select id="amp<?= $flat_rel ?>" class="select w100 parent left" style="padding:0;" rel="<?= $rel ?>">
					<?php foreach($article['pages'] as $page) :?>
						<option <?php if ($page['main_parent'] == '1') :?>selected="selected"<?php endif; ?> value="<?= $page['id_page'] ?>"><?= $page['title'] ?></option>
					<?php endforeach ;?>
				</select>
	
			</span>
		<?php endif ;?>

		<!-- Title (draggable) -->
		<a style="overflow:hidden;height:16px;display:block;" class=" pl5 pr10 article article<?= $flat_rel ?> <?= $status ;?>" title="<?= lang('ionize_label_edit') ?> / <?= lang('ionize_label_drag_to_page') ?>" rel="<?= $rel ?>"><span class="flag flag<?= $article['flag'] ?>"></span><?= $title ?></a>
	</li>

<?php endforeach ;?>

</ul>

<script type="text/javascript">

	/**
	 * Articles view / type select for articles list
	 *
	 */
	$$('#articleList<?= $id_page ?> .type').each(function(item, idx)
	{
		var rel = item.getAttribute('rel').split(".");

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

//		ION.initArticleTypeEvent(item);
	});

	$$('#articleList<?= $id_page ?> .view').each(function(item, idx)
	{
		var rel = item.getAttribute('rel').split(".");

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
	
//		ION.initArticleViewEvent(item);
	});
	
	$$('#articleList<?= $id_page ?> .parent').each(function(item, idx)
	{
		var rel = item.getAttribute('rel').split(".");

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
//		ION.initArticleMainParentEvent(item);
	});
	
	/**
	 * Makes article title draggable
	 *
	 */
	$$('#articleList<?= $id_page ?> .article').each(function(item, idx)
	{
		var id_article = item.getProperty('rel');
		var title = item.get('text');
		
		// Drag / Drop
		ION.addDragDrop(item, '.folder', 'ION.dropArticleInPage');
		
		// Edit link
		item.addEvent('click', function(e) {
			e.stop();
			MUI.Content.update({'element': $('mainPanel'),'url': admin_url + 'article/edit/' + id_article,'title': Lang.get('ionize_title_edit_article') + ' : ' + title});
		});
	});

	/**
	 * Article list itemManager
	 *
	 */
	articleManager = new ION.ArticleManager({container: 'articleList<?= $id_page ?>', 'id_parent':'<?= $id_page ?>'});

	
</script>
