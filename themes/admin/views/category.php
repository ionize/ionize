
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
		<div class="tab">
			<ul class="tab-content">
	
				<?php foreach(Settings::get_languages() as $l) :?>
					<li id="tabwindowCategory<?= $id_category ?>-<?= $l['lang'] ?>"><a><span><?= ucfirst($l['name']) ?></span></a></li>
				<?php endforeach ;?>

			</ul>
		</div>

		<!-- Text block -->
		<?php foreach(Settings::get_languages() as $l) :?>
			
			<?php $lang = $l['lang']; ?>

			<div id="blockwindowCategory<?= $id_category ?>-<?= $lang ?>" class="block windowCategory<?= $id_category ?>">

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
			
			</div>
		
		<?php endforeach ;?>
		
	</fieldset>

</form>


<!-- Save / Cancel buttons
	 Must be named bSave[windows_id] where 'window_id' is the used ID for the window opening through MUI.formWindow()
--> 
<div class="buttons">
	<button id="bSavecategory<?= $id_category ?>" type="button" class="button yes right mr40"><?= lang('ionize_button_save_close') ?></button>
	<button id="bCancelcategory<?= $id_category ?>"  type="button" class="button no "><?= lang('ionize_button_cancel') ?></button>
</div>

<script type="text/javascript">

	/** 
	 * Show current tabs
	 */
	ION.displayBlock('.windowCategory<?= $id_category ?>', '<?= Settings::get_lang('first') ?>', 'windowCategory<?= $id_category ?>');
	
	
	/** 
	 * Add events to tabs
	 * - Lang Tab Events 
	 * - Options Tab Events
	 * - Wysiwyg buttons
	 */
	<?php foreach(Settings::get_languages() as $l) :?>

		$('tabwindowCategory<?= $id_category ?>-<?= $l["lang"] ?>').addEvent('click', function()
		{ 
			ION.displayBlock('.windowCategory<?= $id_category ?>', '<?= $l["lang"] ?>', 'windowCategory<?= $id_category ?>'); 
		});

	<?php endforeach ;?>

</script>

