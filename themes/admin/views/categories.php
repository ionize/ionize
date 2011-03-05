
<!-- Existing categories -->
<h3 class="toggler2"><?= lang('ionize_title_category_exist') ?></h3>

<div class="element2">
	
	<ul id="categoryContainer" class="mb20">
	
	<?php foreach($categories as $category) :?>
	
		<li class="sortme category<?= $category['id_category'] ?>" id="category_<?= $category['id_category'] ?>" rel="<?= $category['id_category'] ?>">
			<a class="icon delete right" rel="<?= $category['id_category'] ?>"></a>
			<img class="icon left drag" src="<?= theme_url() ?>images/icon_16_ordering.png" />
			
			<a class="left pl5" href="javascript:void(0);" onclick="javascript:MUI.formWindow('Category', 'categoryForm', '<?= lang('ionize_title_category_edit') ?>', 'category/edit/<?= $category['id_category'] ?>/<?= $parent ?>/<?= $id_parent ?>', {width: 360, height: 230});" title="edit"><?= $category['name'] ?></a>
		</li>
	
	<?php endforeach ;?>
	
	</ul>
</div>


<!-- New category -->

<h3 class="toggler2"><?= lang('ionize_title_category_new') ?></h3>

<div class="element2">

	<form name="newCategoryForm" id="newCategoryForm" action="<?= admin_url() ?>category/save">
	
		<!-- Hidden fields -->
		<input id="id_category" name="id_category" type="hidden" value="<?= $id_category ?>" />
		<input id="parent" name="parent" type="hidden" value="<?= $parent ?>" />
		<input id="id_parent" name="id_parent" type="hidden" value="<?= $id_parent ?>" />
		<input id="ordering" name="ordering" type="hidden" value="<?= $ordering ?>" />
	
	
		<!-- Name -->
		<dl class="small">
			<dt>
				<label for="name"><?=lang('ionize_label_name')?></label>
			</dt>
			<dd>
				<input id="name" name="name" class="inputtext required" type="text" value="<?= $name ?>" />
			</dd>
		</dl>
		
		<fieldset id="blocks">
	
			<!-- Tabs -->
			<div class="tab">
				<ul class="tab-content">
		
					<?php foreach(Settings::get_languages() as $l) :?>
						<li id="tabcategories-<?= $l['lang'] ?>"><a><span><?= ucfirst($l['name']) ?></span></a></li>
					<?php endforeach ;?>
	
				</ul>
			</div>
	
			<!-- Text block -->
			<?php foreach(Settings::get_languages() as $l) :?>
				
				<?php $lang = $l['lang']; ?>
	
				<div id="blockcategories-<?= $lang ?>" class="block category">
	
					<!-- title -->
					<dl class="small">
						<dt>
							<label for="title"><?= lang('ionize_label_title') ?></label>
						</dt>
						<dd>
							<input id="title_<?= $lang ?>" name="title_<?= $lang ?>" class="inputtext w180" type="text" value="<?= ${$lang}['title'] ?>"/>
						</dd>
					</dl>
	
					<!-- description -->
					<dl class="small">
						<dt>
							<label for="description"><?= lang('ionize_label_description') ?></label>
						</dt>
						<dd>
							<input id="description_<?= $lang ?>" name="description_<?= $lang ?>" class="inputtext w180" type="text" value="<?= ${$lang}['description'] ?>"/>
						</dd>
					</dl>
	
					<!-- save button -->
					<dl class="small">
						<dt>&#160;</dt>
						<dd>
							<button id="bSaveNewCategory" type="button" class="button yes"><?= lang('ionize_button_save') ?></button>
						</dd>
					</dl>
				
				</div>
			
			<?php endforeach ;?>
			
		</fieldset>
	</form>
</div>


<script type="text/javascript">

	/**
	 * Categories list itemManager
	 *
	 */
	categoriesManager = new ION.ItemManager(
	{
		parent: 	'<?= $parent ?>',
		idParent: 	'<?= $id_parent ?>',
		element: 	'category',
		container: 	'categoryContainer'	
	});
	
	categoriesManager.makeSortable();
	
	
	/**
	 * Options Accordion
	 *
	 */
	MUI.initAccordion('.toggler2', 'div.element2');
	
	
	/** 
	 * Show current tabs
	 */
	ION.displayBlock('.category', '<?= Settings::get_lang('first') ?>', 'categories');
	
	
	/** 
	 * Add events to tabs
	 * - Lang Tab Events 
	 * - Options Tab Events
	 * - Wysiwyg buttons
	 */
	<?php foreach(Settings::get_languages() as $l) :?>

		$('tabcategories-<?= $l["lang"] ?>').addEvent('click', function()
		{ 
			ION.displayBlock('.category', '<?= $l["lang"] ?>', 'categories'); 
		});

	<?php endforeach ;?>
	
	

	/**
	 * Form submit
	 *
	 */
	$('bSaveNewCategory').addEvent('click', function(e) {
		e.stop();
		MUI.sendData(admin_url + 'category/save', $('newCategoryForm'));
	});

</script>
