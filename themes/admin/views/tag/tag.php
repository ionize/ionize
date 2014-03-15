
<!-- Category edit view - Modal window -->

<form name="categoryForm<?php echo $id_category; ?>" id="categoryForm<?php echo $id_category; ?>" action="<?php echo admin_url(); ?>category/save">

	<!-- Hidden fields -->
	<input id="id_category" name="id_category" type="hidden" value="<?php echo $id_category; ?>" />
	<input id="parent" name="parent" type="hidden" value="<?php echo $parent; ?>" />
	<input id="id_parent" name="id_parent" type="hidden" value="<?php echo $id_parent; ?>" />
	<input id="ordering" name="ordering" type="hidden" value="<?php echo $ordering; ?>" />


	<!-- Name -->
	<dl class="small">
		<dt>
			<label for="name"><?php echo lang('ionize_label_name'); ?></label>
		</dt>
		<dd>
			<input id="name" name="name" class="inputtext required" type="text" value="<?php echo $name; ?>" />
		</dd>
	</dl>
	
	<fieldset id="blocks">

		<!-- Tabs -->
		<div id="categoryTab<?php echo $id_category; ?>" class="mainTabs">
			<ul class="tab-menu">
				<?php foreach(Settings::get_languages() as $l) :?>
					<li class="tab_edit_category<?php echo $id_category; ?>" rel="<?php echo $l['lang']; ?>"><a><?php echo ucfirst($l['name']); ?></a></li>
				<?php endforeach ;?>
			</ul>
			<div class="clear"></div>
		</div>

		<div id="categoryTabContent<?php echo $id_category; ?>">

			<!-- Text block -->
			<?php foreach(Settings::get_languages() as $l) :?>
				
				<?php $lang = $l['lang']; ?>
	
				<div class="tabcontent<?php echo $id_category; ?>">
	
					<!-- title -->
					<dl class="small">
						<dt>
							<label for="title"><?php echo lang('ionize_label_title'); ?></label>
						</dt>
						<dd>
							<input id="title_<?php echo $lang; ?>" name="title_<?php echo $lang; ?>" class="inputtext w180" type="text" value="<?php echo $languages[$lang]['title']; ?>"/>
						</dd>
					</dl>
	
					<!-- subtitle -->
					<dl class="small">
						<dt>
							<label for="subtitle<?php echo $lang; ?><?php echo $id_category; ?>"><?php echo lang('ionize_label_subtitle'); ?></label>
						</dt>
						<dd>
							<input id="subtitle_<?php echo $lang; ?><?php echo $id_category; ?>" name="subtitle_<?php echo $lang; ?>" class="inputtext" type="text" value="<?php echo $languages[$lang]['subtitle']; ?>"/>
						</dd>
					</dl>
						
					<!-- description -->
					<dl class="small">
						<dt>
							<label for="description_<?php echo $lang; ?><?php echo $id_category; ?>"><?php echo lang('ionize_label_description'); ?></label>
						</dt>
						<dd>
							<textarea id="description_<?php echo $lang; ?><?php echo $id_category; ?>" name="description_<?php echo $lang; ?>" class="tinyCategory w220 h120" rel="<?php echo $lang; ?>"><?php echo $languages[$lang]['description']; ?></textarea>
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
	<button id="bSavecategory<?php echo $id_category; ?>" type="button" class="button yes right"><?php echo lang('ionize_button_save_close'); ?></button>
	<button id="bCancelcategory<?php echo $id_category; ?>"  type="button" class="button no right"><?php echo lang('ionize_button_cancel'); ?></button>
</div>

<script type="text/javascript">

	/**
	 * Window resize
	 *
	 */
	ION.windowResize('category<?php echo $id_category; ?>', {width:550, height:400});

	/** 
	 * Tabs init
	 *
	 */
	new TabSwapper({tabsContainer: 'categoryTab<?php echo $id_category; ?>', sectionsContainer: 'categoryTabContent<?php echo $id_category; ?>', selectedClass: 'selected', deselectedClass: '', tabs: 'li', clickers: 'li a', sections: 'div.tabcontent<?php echo $id_category; ?>' });

	/**
	 * TinyEditors
	 * Must be called after tabs init.
	 *
	 */
	ION.initTinyEditors('.tab_edit_category<?php echo $id_category; ?>', '#categoryTabContent<?php echo $id_category; ?> .tinyCategory', 'small', {'height':120});



</script>
