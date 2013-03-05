	<!--
	
		Filters form : 
		- Pages
		- Search field (title, content, etc.)	
	
	 -->

	<div id="maincolumn">
		

		<h2 class="main articles" id="main-title"><?php echo lang('ionize_title_articles'); ?></h2>

		<?php
		
			$nbLang = count(Settings::get_languages());
			$width = (100 / $nbLang) - 10;
			$flag_width = (30 * $nbLang);

		?>



		<!-- Articles list -->

				<!-- Article list filtering -->
				<div class="relative h20 w270">
				<form id="filterArticles">
					<label class="left" title="<?php echo lang('ionize_help_article_filter'); ?>"><?php echo lang('ionize_label_article_filter'); ?></label>
					<input id="contains" type="text" class="inputtext w180 left" />
					<a id="cleanFilter" class="icon clearfield left ml5"></a>
				</form>
				</div>
	
				
				<table class="list" id="articlesTable">
			
					<thead>
						<tr>
							<th><?php echo lang('ionize_label_title'); ?></th>
							<th axis="string"><?php echo lang('ionize_label_pages'); ?></th>
							<th axis="string" style="width:<?php echo $flag_width; ?>px;"><?php echo lang('ionize_label_content_for_lang'); ?></th>
							<th class="right" style="width:70px;"><?php echo lang('ionize_label_actions'); ?></th>
						</tr>
					</thead>
		
					<tbody>
					
					<?php foreach ($articles as $article) :?>
						
						<?php
							$title = ($article['title'] != '') ? $article['title'] : $article['name'];
							
							// HTML strings
							$online_html = $content_html = $pages_html ='';
							
							// Array of status
							$pages = $content = $online = array();
							
							foreach($article['langs'] as $lang)
							{
								if($lang['online'] == '1') $online[] = '<img class="pr5" src="'. theme_url() . 'images/world_flags/flag_' . $lang['lang'] . '.gif" />';
								if ($lang['content'] != '') $content[] = '<img class="pr5" src="'. theme_url() . 'images/world_flags/flag_' . $lang['lang'] . '.gif" />';
							}
							
							// Article parent pages links
							foreach($article['pages'] as $page)
							{
								if (!empty($page['page']))
								{
									$page_title = (! empty($page['page']['title'])) ? $page['page']['title'] : $page['page']['name'];
									$pages[] = '<span rel="" >' . $page_title . '</span>';
								}
							}					
							
							// HTML
							$pages_html = implode(', ', $pages);
							$content_html = implode('', $content);
							$online_html = implode('', $online);
						?>
		
						<tr class="article<?php echo $article['id_article']; ?>">
							
	
							<td style="overflow:hidden;" class="title">
	
								<div style="overflow:hidden;">
									<span class="toggler left" rel="content<?php echo $article['id_article']; ?>">
										<a class="left article" rel="0.<?php echo $article['id_article']; ?>"><span class="flag flag<?php echo $article['flag']; ?>"></span><?php echo $title; ?></a>
									</span>
								</div>
								
								<div id="content<?php echo $article['id_article']; ?>" class="content">
		
									<div class="text">
										
										<?php foreach(Settings::get_languages() as $language) :?>
									
											<?php $lang = $language['lang']; ?>
											
											<div style="width:<?php echo $width; ?>%;" class="mr10 left langcontent<?php if($language['def'] == '1') :?> dl<?php endif ;?>">
												
												<img class="pr5" src="<?php echo theme_url(); ?>images/world_flags/flag_<?php echo $lang; ?>.gif" />
												
												<div>
													<?php echo strip_tags($article['langs'][$lang]['content'], ('<p>,<ul>,<ol>,<li>,<h1>,<h2>,<h3>')); ?>
												</div>
											
											</div>
										
										<?php endforeach ;?>
									
									</div>
								</div>
							</td>
							
							
							<td>
								<?php if(empty($pages_html)) :?>
									<img class="help" src="<?php echo theme_url(); ?>images/icon_16_alert.png" title="<?php echo lang('ionize_help_orphan_article'); ?>" />
								<?php endif; ?>
								
								<?php echo $pages_html; ?>
							</td>
							
							<td>
								<?php echo $content_html; ?>
								<?php if(count($content) < $nbLang) :?><img class="help" src="<?php echo theme_url(); ?>images/icon_16_alert.png"  title="<?php echo lang('ionize_help_missing_translated_content'); ?>" /><?php endif; ?>
							</td>
							
				<!--			<td><?php echo $online_html; ?></td> -->
							
							<td>
								<a class="icon right delete" rel="<?php echo $article['id_article']; ?>"></a>
								<a class="icon right duplicate mr5" rel="<?php echo $article['id_article']; ?>|<?php echo $article['name']; ?>"></a>
								<a class="icon right edit mr5" rel="<?php echo $article['id_article']; ?>" title="<?php echo $title; ?>"></a>
							</td>
							
		
						</tr>
			
					<?php endforeach ;?>
					
					</tbody>
			
				</table>
				
	</div>


<script type="text/javascript">


	/**
	 * Make each article draggable
	 *
	 */
	$$('#articlesTable .article').each(function(item, idx)
	{
		ION.addDragDrop(item, '.dropArticleInPage,.dropArticleAsLink,.folder', 'ION.dropArticleInPage,ION.dropArticleAsLink,ION.dropArticleInPage');
	});	
	
	
	/**
	 * Adds Sortable function to the user list table
	 *
	 */
	new SortableTable('articlesTable',{sortOn: 0, sortBy: 'ASC'});

	/**
	 * Table action icons
	 *
	$$('#articlesTable .delete').each(function(item)
	{
		ION.initItemDeleteEvent(item, 'article');
	});
	 */
	var confirmDeleteMessage = Lang.get('ionize_confirm_element_delete');
 	var url = admin_url + 'article/delete/';

	$$('#articlesTable .delete').each(function(item)
	{
		ION.initRequestEvent(
			item,
			url + item.getProperty('rel'),
			{},
			{
				'confirm':true,
				'message': confirmDeleteMessage
			}
		);
	});



	$$('#articlesTable .duplicate').each(function(item)
	{
		var rel = item.getProperty('rel').split("|");
		var id = rel[0];
		var url = rel[1];
		item.addEvent('click', function(e)
		{
			e.stop();
			ION.formWindow('DuplicateArticle', 'newArticleForm', 'ionize_title_duplicate_article', 'article/duplicate/' + id + '/' + url, {width:520, height:280});
		});
	});

	$$('#articlesTable .edit').each(function(item)
	{
		var id_article = item.getProperty('rel');
		var title = item.getProperty('title');
		
		item.addEvent('click', function(e)
		{
			e.stop();
            ION.contentUpdate({
				'element': $('mainPanel'),
				'loadMethod': 'xhr',
				'url':	admin_url + 'article/edit/0.' + id_article,
				'title': Lang.get('ionize_title_edit_article') + ' : ' + title
			});
		});
	});



	/**
	 * Content togglers
	 *
	 */
	calculateTableLineSizes = function()
	{
		$$('#articlesTable tbody tr td.title').each(function(el)
		{
			var c = el.getFirst('.content');
			var toggler = el.getElement('.toggler');

			var text = c.getFirst();
			var s = text.getDimensions();
			
			if (s.height > 0)
			{
				toggler.store('max', s.height +10);
				
				if (toggler.hasClass('expand'))
				{
					el.setStyles({'height': 20 + s.height + 'px' });
					c.setStyles({'height': s.height + 'px' });
				}
			}
			else
			{
				toggler.store('max', s.height);
			}
		});
	}


	window.removeEvent('resize', calculateTableLineSizes);
	window.addEvent('resize', function()
	{
		calculateTableLineSizes();
	});
	
	window.fireEvent('resize');


	$$('#articlesTable tbody tr td .toggler').each(function(el)
	{
		el.fx = new Fx.Morph($(el.getProperty('rel')), {duration: 200, transition: Fx.Transitions.Sine.easeOut});
		el.fx2 = new Fx.Morph($(el.getParent('td')), {duration: 200, transition: Fx.Transitions.Sine.easeOut});
		
		$(el.getProperty('rel')).setStyles({'height':'0px'});
	});

	var toggleArticle = function(e)
	{
		e.stop();
		
		// this.fx.toggle();
		this.toggleClass('expand');
		
		var max = this.retrieve('max');
		var from = 0;
		var to = max;

		if (this.hasClass('expand') == 0)
		{
			from = max;
			to = 0;
			this.getParent('tr').removeClass('highlight');
		}
		else
		{
			this.getParent('tr').addClass('highlight');
		}
		
		this.fx.start({'height': [from, to]});
		this.fx2.start({'height': [from+20, to+20]});
	
	};

	$$('#articlesTable tbody tr td .toggler').addEvent('click', toggleArticle);
	$$('#articlesTable tbody tr td.title').addEvent('click', function(e){this.getElement('.toggler').fireEvent('click', e)});
	$$('#articlesTable tbody tr td .content').addEvent('click', function(e){this.getParent('td').getElement('.toggler').fireEvent('click', e)});


	/**
	 * Filtering
	 *
	 */
	var filterArticles = function(search)
	{
		var reg = new RegExp('<span class="highlight"[^><]*>|<.span[^><]*>','g')
		
		var search = RegExp(search,"gi");
		
		$$('#articlesTable .text').each(function(el)
		{
			var c = el.get('html');
			var tr = el.getParent('tr');
			var m = c.match(search);

			tr.setStyle('display', 'none');

			if ( (m))
			{
				h = c;
				h = h.replace(reg, '');

				m.each(function(item){
					h = h.replace(item, '<span class="highlight">' + item + '</span>');
				});
				el.set('html', h);
                tr.removeProperty('style');
                tr.setStyle('visibility', 'visible');
			}
			else
			{
				h = c.replace(reg, '');
				el.set('html', h);
			}
		});
	};


	$('contains').addEvent('keyup', function(e)
	{
		e.stop();
		
		var search = this.value;
		
		if (search.length > 2)
		{
			if (this.timeoutID)
			{
                clearInterval(this.timeoutID);
			}
			this.timeoutID = filterArticles.delay(500, this, search);
		}
	});
	
	$('cleanFilter').addEvent('click', function(e)
	{
		var reg = new RegExp('<span class="highlight"[^><]*>|<.span[^><]*>','g');

		$('contains').setProperty('value','').set('text', '');

		$$('#articlesTable tr').each(function(el)
		{
			el.removeProperty('style');
		});
		
		$$('#articlesTable .text').each(function(el){
			
			var c = el.get('html');
			c = c.replace(reg, '');
			el.set('html', c);
		});
	});


	/**
	 * Panel toolbox
	 *
	 */
	
	ION.initToolbox('articles_toolbox');
	

</script>