

<ul id="articleList<?php echo $id_page; ?>" class="sortable-container">

<?php
	$nbLang = count(Settings::get_languages());
	$flag_width = (25 * $nbLang);

	$select_views_class = is_null($all_article_types) ? 'w180' : 'w110';
	$select_types_class = is_null($all_article_views) ? 'w180' : 'w80';

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
	
	foreach($article['languages'] as $lang_content)
	{
		if ( ! empty($lang_content['content'])) $content[] = '<img class="left pl5 pt3" src="'. admin_style_url() . 'images/world_flags/flag_' . $lang_content['lang'] . '.gif" />';
	}
	
	// HTML
	$content_html = implode('', $content);
	
	?>

	<li id="articleinpage<?php echo $article['id_article']; ?>" class="sortme article<?php echo $article['id_article']; ?> article<?php echo $flat_rel; ?> <?php echo $status ;?>" data-id="<?php echo $rel; ?>">
		
		<!-- Drag icon -->
		<div class="left" style="width: 30%;overflow: hidden;height: 18px;">
			<span class="icon left drag mr5"></span>

			<input type="checkbox" name="ids[]" value="<?php echo $article['id_article']; ?>" class="mr5" />

			<!-- Title (draggable) -->
			<a class="article article<?php echo $flat_rel; ?> <?php echo $status ;?>" title="<?php echo lang('ionize_label_edit'); ?> / <?php echo lang('ionize_label_drag_to_page'); ?>" data-id="<?php echo $rel; ?>"><span><span class="flag flag<?php echo $article['type_flag']; ?>"></span><?php echo $title; ?></span></a>
		</div>

		<div class="right mb2">
			<!-- Status icon -->
			<a class="icon right status article<?php echo $article['id_article']; ?> article<?php echo $flat_rel; ?> <?php echo $status ;?>" data-id="<?php echo $rel; ?>"></a>

			<!-- Unlink icon -->
			<a class="icon right mr5 unlink" data-id="<?php echo $rel; ?>" title="<?php echo lang('ionize_label_unlink'); ?>"></a>

			<!-- Flags : Available content for language -->
			<span style="width:<?php echo$flag_width?>px;display:block;height:16px;" class="right mr10 ml10"><?php echo $content_html; ?></span>
		</div>

		<div class="right" style="width: 40%;overflow: hidden;">

			<!-- Main parent page -->
			<?php if (count($article['pages']) > 1) :?>
				<span class="left " data-id="<?php echo $rel; ?>" style="width: 32%;margin-right:1%;">

					<select id="amp<?php echo $flat_rel; ?>" class="p1 select parent left w100p" data-id="<?php echo $rel; ?>">
						<?php foreach($article['pages'] as $page) :?>
							<option <?php if ($page['main_parent'] == '1') :?>selected="selected"<?php endif; ?> value="<?php echo $page['id_page']; ?>"><?php echo $page['title']; ?></option>
						<?php endforeach ;?>
					</select>

				</span>
			<?php endif ;?>

			<!-- Type -->
			<?php if ( ! is_null($all_article_types)) :?>
				<span class="left " data-id="<?php echo $rel; ?>" style="width: 32%;margin-right:1%;">

					<select id="type<?php echo $flat_rel; ?>" class="p1 select type left w100p" data-id="<?php echo $rel; ?>">
						<?php foreach($all_article_types as $idx => $type) :?>
							<option <?php if ($article['id_type'] == $idx) :?>selected="selected"<?php endif; ?>  value="<?php echo $idx; ?>"><?php echo $type; ?></option>
						<?php endforeach ;?>
					</select>

				</span>
			<?php endif ;?>

			<!-- Views -->
			<?php if ( ! is_null($all_article_views)) :?>
				<span class="left" style="width: 32%;margin-right:1%;">

					<select id="view<?php echo $flat_rel; ?>" class="p0 select view w100p" data-id="<?php echo $rel; ?>" >
						<?php foreach($all_article_views as $idx => $view) :?>
							<option <?php if ($article['view'] == $idx) :?>selected="selected"<?php endif; ?> value="<?php echo $idx; ?>"><?php echo $view; ?></option>
						<?php endforeach ;?>
					</select>

				</span>
			<?php endif ;?>

		</div>
	</li>

<?php endforeach ;?>

</ul>

<div>
	<select id="selectAction" class="select ml40">
		<option value=""><?php echo lang('ionize_select_on_checked') ?></option>
		<option value="unlink"><?php echo lang('ionize_label_unlink') ?></option>
		<option value="offline"><?php echo lang('ionize_label_set_offline') ?></option>
		<option value="online"><?php echo lang('ionize_label_set_online') ?></option>
		<option value="delete"><?php echo lang('ionize_label_delete') ?></option>
	</select>
</div>

<script type="text/javascript">

	var id_page = '<?php echo $id_page; ?>';

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


	/**
	 * Processes multiple action on articles
	 *
	 * @param action
	 * @param selectObj
	 */
	var processMultipleAction = function(action, selectObj)
	{
		var cbs = $('articleList'+id_page).getElements('input[name="ids[]"]:checked'),
			ids = []
		;

		Object.each(cbs, function(cb)
		{
			if (typeOf(cb.value) != 'null')
				ids.push(cb.value);
		});

		if (Object.getLength(ids) > 0)
		{
			ION.JSON(
				admin_url + 'article/multiple_action',
				{
					id_page: id_page,
					ids: ids,
					action: action
				},
				{
					// Receive action & items ID
					onSuccess: function(json)
					{
						switch(json.action)
						{
							case 'unlink':
								(json.ids).each(function(id){
									ION.unlinkArticleFromPageDOM({id_page:json.id_page, id_article:id});
								});
								break;

							case 'online':
							case 'offline':
								var status = json.action == 'online' ? 1 : 0;
								(json.ids).each(function(id){
									ION.switchOnlineStatus({selector:'.article'+json.id_page+'x'+id, status:status});
								});
								break;

							case 'delete':
								(json.ids).each(function(id){
									ION.deleteDomElements('.article' + id);
								});
								break
						}
						// Restore Select position
						selectObj.set('value', '');
					}
				}
			);

		}
	};


	// Select : Multi-actions
	$('selectAction').addEvent('change', function()
	{
		var action = this.value;

		if (action != '')
		{
			if (action == 'delete')
			{
				var self = this;

				ION.confirmation(
					'confDeleteArticles',
					function(){
						processMultipleAction(action, self);
					},
					Lang.get('ionize_confirm_articles_delete')
				);
			}
			else
			{
				processMultipleAction(action, this);
			}
		}
	});

	<?php if (count($articles) == 0):?>
		$('selectAction').hide();
	<?php endif; ?>


</script>
