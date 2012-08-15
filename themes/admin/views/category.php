
<!-- Category edit view - Modal window -->

<form name="categoryForm<?= $id_category ?>" id="categoryForm<?= $id_category ?>" action="<?= admin_url() ?>category/save">

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
		<div id="categoryTab<?= $id_category ?>" class="mainTabs">
			<ul class="tab-menu">
				<?php foreach(Settings::get_languages() as $l) :?>
					<li class="tab_edit_category<?= $id_category ?>" rel="<?= $l['lang'] ?>"><a><?= ucfirst($l['name']) ?></a></li>
				<?php endforeach ;?>
			</ul>
			<div class="clear"></div>
		</div>

		<div id="categoryTabContent<?= $id_category ?>">

			<!-- Text block -->
			<?php foreach(Settings::get_languages() as $l) :?>
				
				<?php $lang = $l['lang']; ?>
	
				<div class="tabcontent<?= $id_category ?>">
	
					<!-- title -->
					<dl class="small">
						<dt>
							<label for="title"><?= lang('ionize_label_title') ?></label>
						</dt>
						<dd>
							<input id="title_<?= $lang ?>" name="title_<?= $lang ?>" class="inputtext w180" type="text" value="<?= ${$lang}['title'] ?>"/>
						</dd>
					</dl>
	
					<!-- subtitle -->
					<dl class="small">
						<dt>
							<label for="subtitle<?= $lang ?><?= $id_category ?>"><?= lang('ionize_label_subtitle') ?></label>
						</dt>
						<dd>
							<input id="subtitle_<?= $lang ?><?= $id_category ?>" name="subtitle_<?= $lang ?>" class="inputtext" type="text" value="<?= ${$lang}['subtitle'] ?>"/>
						</dd>
					</dl>
						
					<!-- description -->
					<dl class="small">
						<dt>
							<label title="<?= lang('ionize_label_help_description') ?>" for="description_<?= $lang ?><?= $id_category ?>"><?= lang('ionize_label_description') ?></label>
						</dt>
						<dd>
							<textarea id="description_<?= $lang ?><?= $id_category ?>" name="description_<?= $lang ?>" class="tinyCategory w220 h120" rel="<?= $lang ?>"><?= ${$lang}['description'] ?></textarea>
						</dd>
					</dl>
				
				</div>
			
			<?php endforeach ;?>

		</div>
	</fieldset>

</form>


<!-- Save / Cancel buttons
	 Must be named bSave[windows_id] where 'window_id' is the used ID for the window opening through ION.formWindow()
--> 
<div class="buttons">
	<button id="bSavecategory<?= $id_category ?>" type="button" class="button yes right mr40"><?= lang('ionize_button_save_close') ?></button>
	<button id="bCancelcategory<?= $id_category ?>"  type="button" class="button no right"><?= lang('ionize_button_cancel') ?></button>
</div>

<script type="text/javascript">

	/**
	 * Window resize
	 *
	 */
	ION.windowResize('category<?= $id_category ?>', {width:550, height:400});

	/** 
	 * Tabs init
	 *
	 */
	new TabSwapper({tabsContainer: 'categoryTab<?= $id_category ?>', sectionsContainer: 'categoryTabContent<?= $id_category ?>', selectedClass: 'selected', deselectedClass: '', tabs: 'li', clickers: 'li a', sections: 'div.tabcontent<?= $id_category ?>' });

	ION.initLabelHelpLinks('#categoryForm<?= $id_category ?>');

	/**
	 * TinyEditors
	 * Must be called after tabs init.
	 *
	 */
	ION.initTinyEditors('.tab_edit_category<?= $id_category ?>', '#categoryTabContent<?= $id_category ?> .tinyCategory', 'small', {'height':120});



</script>
