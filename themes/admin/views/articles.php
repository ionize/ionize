	<!-- 
	
		Filters form : 
		- Pages
		- Search field (title, content, etc.)	
	
	 -->


	

	<div id="maincolumn">
		

		<h2 class="main articles" id="main-title"><?= lang('ionize_title_articles') ?></h2>

		<?php
		
			$nbLang = count(Settings::get_languages());
			$width = (100 / $nbLang) - 10;
			$flag_width = (30 * $nbLang);

		?>


		<!-- Tabs -->
		<div class="tab">
			<ul id="tabs" class="tab-content">
				<li id="tab-articles" rel="articles" item="data"><a><span><?= lang('ionize_title_articles') ?></span></a></li>
				<li id="tab-categories" rel="categories" item="data"><a><span><?= lang('ionize_title_categories') ?></span></a></li>
				<li id="tab-types" rel="types" item="data"><a><span><?= lang('ionize_title_types') ?></span></a></li>
				<li id="tab-markers" rel="markers" item="data"><a title="<?= lang('ionize_help_flags') ?>"><span><?= lang('ionize_label_flags') ?></span></a></li>
			</ul>
		</div>


		<!-- Articles list -->
		<div id="block-articles" class="block data">

			<!-- Article list filtering -->
			<form id="filterArticles">
				<label class="left" title="<?= lang('ionize_help_article_filter') ?>"><?= lang('ionize_label_article_filter') ?></label>
				<input id="contains" type="text" class="inputtext w160 left"></input>
				<a id="cleanFilter" class="icon clearfield left ml5"></a>
			</form>

			
			<table class="list" id="articlesTable">
		
				<thead>
					<tr>
						<th><?= lang('ionize_label_title') ?></th>
						<th axis="string"><?= lang('ionize_label_pages') ?></th>
						<th axis="string" style="width:<?= $flag_width ?>px;"><?= lang('ionize_label_content_for_lang') ?></th>
						<th class="right" style="width:70px;"><?= lang('ionize_label_actions') ?></th>
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
	
					<tr class="article<?= $article['id_article'] ?>">
						

						<td style="overflow:hidden;" class="title">

							<div style="overflow:hidden;">
								<span class="toggler left" rel="content<?= $article['id_article'] ?>">
									<a class="left article" rel="0.<?= $article['id_article'] ?>"><span class="flag flag<?= $article['flag'] ?>"></span><?= $title ?></a>
								</span>
							</div>
							
							<div id="content<?= $article['id_article'] ?>" class="content">
	
								<div class="text">
									
									<?php foreach(Settings::get_languages() as $language) :?>
								
										<?php $lang = $language['lang']; ?>
										
										<div style="width:<?=$width?>%;" class="mr10 left langcontent<?php if($language['def'] == '1') :?> dl<?php endif ;?>">
											
											<img class="pr5" src="<?= theme_url() ?>images/world_flags/flag_<?= $lang ?>.gif" />
											
											<div>
												<?= strip_tags($article['langs'][$lang]['content'], ('<p>,<ul>,<ol>,<li>,<h1>,<h2>,<h3>')) ?>
											</div>
										
										</div>
									
									<?php endforeach ;?>
								
								</div>
							</div>
						</td>
						
						
						<td>
							<?php if(empty($pages_html)) :?>
								<img class="help" src="<?= theme_url() ?>images/icon_16_alert.png" title="<?= lang('ionize_help_orphan_article') ?>" />
							<?php endif; ?>
							
							<?= $pages_html ?>
						</td>
						
						<td>
							<?= $content_html ?>
							<?php if(count($content) < $nbLang) :?><img class="help" src="<?= theme_url() ?>images/icon_16_alert.png"  title="<?= lang('ionize_help_missing_translated_content') ?>" /><?php endif; ?>
						</td>
						
			<!--			<td><?= $online_html ?></td> -->
						
						<td>
							<a class="icon right delete" rel="<?= $article['id_article'] ?>"></a>
							<a class="icon right duplicate mr5" rel="<?= $article['id_article'] ?>|<?= $article['name'] ?>"></a>
							<a class="icon right edit mr5" rel="<?= $article['id_article'] ?>" title="<?= $title ?>"></a>
						</td>
						
	
					</tr>
		
				<?php endforeach ;?>
				
				</tbody>
		
			</table>
			
		</div>
		
		
		<!-- Categories -->
		<div id="block-categories" class="block data">
		
			<div class="tabsidecolumn">
			
				<h3><?= lang('ionize_title_category_new') ?></h3>
				
				<form name="newCategoryForm" id="newCategoryForm" action="<?= admin_url() ?>category/save">
				
				
					<!-- Name -->
					<dl class="small">
						<dt>
							<label for="name"><?=lang('ionize_label_name')?></label>
						</dt>
						<dd>
							<input id="name" name="name" class="inputtext required" type="text" value="" />
						</dd>
					</dl>
					
					<fieldset id="blocks">
				
						<!-- Tabs -->
						<div class="tab">
							<ul class="tab-content">
					
								<?php foreach(Settings::get_languages() as $l) :?>
									<li id="tabnewcategory-<?= $l['lang'] ?>"><a><span><?= ucfirst($l['name']) ?></span></a></li>
								<?php endforeach ;?>
				
							</ul>
						</div>
				
						<!-- Text block -->
						<?php foreach(Settings::get_languages() as $l) :?>
							
							<?php $lang = $l['lang']; ?>
				
							<div id="blocknewcategory-<?= $lang ?>" class="block newcategory">
				
								<!-- title -->
								<dl class="small">
									<dt>
										<label for="title"><?= lang('ionize_label_title') ?></label>
									</dt>
									<dd>
										<input id="title_<?= $lang ?>" name="title_<?= $lang ?>" class="inputtext" type="text" value=""/>
									</dd>
								</dl>
				
								<!-- description -->
								<dl class="small">
									<dt>
										<label for="description"><?= lang('ionize_label_description') ?></label>
									</dt>
									<dd>
										<input id="description_<?= $lang ?>" name="description_<?= $lang ?>" class="inputtext" type="text" value=""/>
									</dd>
								</dl>
				
							</div>
						<?php endforeach ;?>
						
						<!-- save button -->
						<dl class="small">
							<dt>&#160;</dt>
							<dd>
								<button id="bSaveNewCategory" type="button" class="button yes"><?= lang('ionize_button_save') ?></button>
							</dd>
						</dl>
						
					</fieldset>
				</form>
			
			</div>


			<div class="tabcolumn pt15">
			
				<!-- Existing categories -->
				<ul id="categoryList" class="mb20">
				
				<?php foreach($categories as $category) :?>
				
					<li class="sortme category<?= $category['id_category'] ?>" id="category_<?= $category['id_category'] ?>" rel="<?= $category['id_category'] ?>">
						<a class="icon delete right" rel="<?= $category['id_category'] ?>"></a>
						<img class="icon left drag pr5" src="<?= theme_url() ?>images/icon_16_ordering.png" />
						<a class="left pl5 title" rel="<?= $category['id_category'] ?>"><?= $category['name'] ?></a>
					</li>
				<?php endforeach ;?>
				</ul>
			</div>
			
		</div>


		<!-- Types -->
		<div id="block-types" class="block data">


			<!-- New type -->
			<div class="tabsidecolumn">
			
				<h3><?= lang('ionize_title_type_new') ?></h3>
			
				<form name="newTypeForm" id="newTypeForm" action="<?= admin_url() ?>article_type/save">
				
					<!-- Name -->
					<dl class="small">
						<dt>
							<label for="type"><?=lang('ionize_label_type')?></label>
						</dt>
						<dd>
							<input id="type" name="type" class="inputtext" type="text" value="" />
						</dd>
					</dl>
					
					<!-- save button -->
					<dl class="small">
						<dt>&#160;</dt>
						<dd>
							<button id="bSaveNewType" type="button" class="button yes"><?= lang('ionize_button_save') ?></button>
						</dd>
					</dl>
				</form>
			</div>
			

			<!-- Existing types -->
			<div class="tabcolumn pt15">
			
				<ul id="article_typeList">
				
				<?php foreach($types as $type) :?>
				
					<li class="sortme article_type<?= $type['id_type'] ?>" id="article_type_<?= $type['id_type'] ?>" rel="<?= $type['id_type'] ?>">
						<a class="icon delete right" rel="<?= $type['id_type'] ?>"></a>
						<img class="icon left drag pr5" src="<?= theme_url() ?>images/icon_16_ordering.png" />
						<a class="left pl5 title" rel="<?= $type['id_type'] ?>"><?= $type['type'] ?></a>
					</li>
				
				<?php endforeach ;?>
				
				</ul>
			</div>
		</div>
		
		
		<!-- Articles Markers -->
		<div id="block-markers" class="block data pl15 pt15">

			<form name="flagsForm" id="flagsForm">
			
				<label class="flag flag1" for="flag1"></label><input type="text" class="inputtext w180 mb2 ml10" id="flag1" name="flag1" value="<?= Settings::get('flag1') ?>" /><br/>
				<label class="flag flag2" for="flag2"></label><input type="text" class="inputtext w180 mb2 ml10" id="flag2" name="flag2" value="<?= Settings::get('flag2') ?>" /><br/>
				<label class="flag flag3" for="flag3"></label><input type="text" class="inputtext w180 mb2 ml10" id="flag3" name="flag3" value="<?= Settings::get('flag3') ?>" /><br/>
				<label class="flag flag4" for="flag4"></label><input type="text" class="inputtext w180 mb2 ml10" id="flag4" name="flag4" value="<?= Settings::get('flag4') ?>" /><br/>
				<label class="flag flag5" for="flag5"></label><input type="text" class="inputtext w180 ml10" id="flag5" name="flag5" value="<?= Settings::get('flag5') ?>" /><br/>
			
			
				<label></label><button  id="bSaveFlags" type="button" class="button yes ml20 mt10"><?= lang('ionize_button_save') ?></button>
			</form>
		</div>
	</div>


<script type="text/javascript">

	
	
	/**
	 * Make each article draggable
	 *
	 */
	$$('#articlesTable .article').each(function(item, idx)
	{
		ION.makeLinkDraggable(item, 'article');
	});	
	
	
	/**
	 * Adds Sortable function to the user list table
	 *
	 */
	new SortableTable('articlesTable',{sortOn: 0, sortBy: 'ASC'});


	MUI.initLabelHelpLinks('#articlesTable');
	MUI.initLabelHelpLinks('#filterArticles');



	/**
	 * Categories list itemManager
	 *
	 */
	categoriesManager = new ION.ItemManager({ element: 'category', container: 'categoryList' });
	
	categoriesManager.makeSortable();

	// Make all categories editable
	$$('#categoryList .title').each(function(item, idx)
	{
		var rel = item.getProperty('rel');
		
		item.addEvent('click', function(e){
			MUI.formWindow('category' + rel, 'categoryForm' + rel, Lang.get('ionize_title_category_edit'), 'category/edit/' + rel, {width:350, height:170});	
		});
	});

	// New category Form submit
	$('bSaveNewCategory').addEvent('click', function(e) {
		e.stop();
		MUI.sendData(admin_url + 'category/save', $('newCategoryForm'));
	});
	
	
	/** 
	 * New category tabs
	 */
	ION.displayBlock('.newcategory', '<?= Settings::get_lang('first') ?>', 'newcategory');
	
	// Add events to tabs
	<?php foreach(Settings::get_languages() as $l) :?>

		$('tabnewcategory-<?= $l["lang"] ?>').addEvent('click', function()
		{ 
			ION.displayBlock('.newcategory', '<?= $l["lang"] ?>', 'newcategory'); 
		});

	<?php endforeach ;?>




	/**
	 * Types list itemManager
	 *
	 */
	typesManager = new ION.ItemManager({ element: 'article_type', container: 'article_typeList' });
	
	typesManager.makeSortable();

	// Make all types editable
	$$('#article_typeList .title').each(function(item, idx)
	{
		var rel = item.getProperty('rel');
		
		item.addEvent('click', function(e){
			MUI.formWindow('article_type' + rel, 'article_typeForm' + rel, Lang.get('ionize_title_type_edit'), 'article_type/edit/' + rel, {width:350, height:100});	
		});
	});

	// New Type Form submit
	$('bSaveNewType').addEvent('click', function(e) {
		e.stop();
		MUI.sendData(admin_url + 'article_type/save', $('newTypeForm'));
	});


	/**
	 * Flags save button
	 *
	 */
	$('bSaveFlags').addEvent('click', function(e) {
		e.stop();
		MUI.sendData(admin_url + 'setting/save_flags', $('flagsForm'));
	});
	



	/**
	 * Articles Tabs
	 *
	 */
 	ION.displayBlock('.data', 'articles');	

	$$('#tabs li').each(function(item, idx)
	{
		item.addEvent('click', function(e)
		{
			e.stop();
			ION.displayBlock('.' + this.getProperty('item'), this.getProperty('rel')); 
		})
	});



	/**
	 * Table action icons
	 *
	 */
	$$('#articlesTable .delete').each(function(item)
	{
		ION.initItemDeleteEvent(item, 'article');
	});

	$$('#articlesTable .duplicate').each(function(item)
	{
		var rel = item.getProperty('rel').split("|");
		var id = rel[0];
		var url = rel[1];
		item.addEvent('click', function(e)
		{
			e.stop();
			MUI.formWindow('DuplicateArticle', 'newArticleForm', 'ionize_title_duplicate_article', 'article/duplicate/' + id + '/' + url, {width:520, height:280});
		});
	});

	$$('#articlesTable .edit').each(function(item)
	{
		var id_article = item.getProperty('rel');
		var title = item.getProperty('title');
		
		item.addEvent('click', function(e)
		{
			e.stop();
			MUI.updateContent({'element': $('mainPanel'),'loadMethod': 'xhr','url':	admin_url + 'article/edit/0.' + id_article, 'title': Lang.get('ionize_title_edit_article') + ' : ' + title	});
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
			
			if ( (m))
			{
				tr.setStyles({'background-color':'#FDFCED'});
				h = c;
				h = h.replace(reg, '');

				m.each(function(item){
					h = h.replace(item, '<span class="highlight">' + item + '</span>');
				})
				el.set('html', h);
				tr.setStyle('visibility', 'visible');
			}
			else
			{
				tr.removeProperty('style');
				h = c.replace(reg, '');
				el.set('html', h);
			}
		});
	}


	$('contains').addEvent('keyup', function(e)
	{
		e.stop();
		
		var search = this.value;
		
		if (search.length > 2)
		{
			if (this.timeoutID)
			{
				$clear(this.timeoutID);
			}
			this.timeoutID = filterArticles.delay(500, this, search);
		}
	});
	
	$('cleanFilter').addEvent('click', function(e)
	{
		var reg = new RegExp('<span class="highlight"[^><]*>|<.span[^><]*>','g')

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
	
	MUI.initToolbox('articles_toolbox');
	

</script>