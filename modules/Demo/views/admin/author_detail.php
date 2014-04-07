<?php
	$id = $id_author;
?>

<form name="authorForm<?php echo $id ?>" id="authorForm<?php echo $id ?>" action="<?php echo admin_url() ?>module/demo/author/save">

	<!-- Hidden fields -->
	<input id="id_author<?php echo $id ?>" name="id_author" type="hidden" value="<?php echo $id ?>" />

	<!-- Name -->
	<dl class="small">
		<dt>
			<label for="name<?php echo $id ?>"><?php echo lang('ionize_label_name')?></label>
		</dt>
		<dd>
			<!--
			The validation of this mandatory field is first done by JS
			by adding the attribute data-validators="required"
			see : http://mootools.net/docs/more/Forms/Form.Validator#Validators
			-->
			<input id="name<?php echo $id ?>" name="name" class="inputtext required" type="text" value="<?php echo $name ?>" data-validators="required" />
		</dd>
	</dl>

	<!-- Gender -->
	<dl class="small">
		<dt>
			<label><?php echo lang('module_demo_label_gender')?></label>
		</dt>
		<dd>
			<input id="genderMale<?php echo $id ?>" name="gender" <?php if ($gender == 1):?>checked="checked" <?php endif; ?>class="radio" type="radio" value="1" />
			<label for="genderMale<?php echo $id ?>">
				<?php echo lang('module_demo_label_male')?>
			</label>
			<br/>
			<input id="genderFemale<?php echo $id ?>" name="gender" <?php if ($gender == 2):?>checked="checked" <?php endif; ?> class="radio" type="radio" value="2" />
			<label for="genderFemale<?php echo $id ?>">
				<?php echo lang('module_demo_label_female')?>
			</label>
		</dd>
	</dl>

	<fieldset>

		<!-- Tabs -->
		<div id="authorTab<?php echo $id ?>" class="mainTabs">
			<ul class="tab-menu">
				<?php foreach(Settings::get_languages() as $l) :?>
					<li class="tab_edit_author<?php echo $id ?><?php if($l['def'] == '1') :?> dl<?php endif ;?>"><a><span><?php echo ucfirst($l['name']) ?></span></a></li>
				<?php endforeach ;?>
			</ul>
			<div class="clear"></div>
		</div>
	
		<div id="authorTabContent<?php echo $id ?>">
			
			<!-- Text block -->
			<?php foreach(Settings::get_languages() as $language) :?>
		
				<?php $lang = $language['lang']; ?>
				
				<div class="tabcontent<?php echo $id ?>">
		
					<!-- description -->
					<textarea id="description_<?php echo $lang ?><?php echo $id ?>" name="description_<?php echo $lang ?>" class="textarea autogrow" rel="<?php echo $lang ?>"><?php echo $languages[$lang]['description'] ?></textarea>
		
				</div>
				
			<?php endforeach ;?>
		
		</div>
	
	</fieldset>

</form>

<!-- Save / Cancel buttons
	 Must be named bSave[windows_id] where 'window_id' is the used ID
	 or the window opening through ION.formWindow()
-->
<div class="buttons">
	<button id="bSaveauthor<?php echo $id ?>" type="button" class="button yes right"><?php echo lang('ionize_button_save_close') ?></button>
	<button id="bCancelauthor<?php echo $id ?>"  type="button" class="button no right"><?php echo lang('ionize_button_cancel') ?></button>
</div>



<script type="text/javascript">

	// Tabs init
	new TabSwapper({
		tabsContainer: 'authorTab<?php echo $id ?>',
		sectionsContainer: 'authorTabContent<?php echo $id ?>',
		selectedClass: 'selected',
		tabs: 'li',
		clickers: 'li a',
		sections: 'div.tabcontent<?php echo $id ?>'
	});

	// Autogrow textareas of the given form ID
	ION.initFormAutoGrow('authorForm<?php echo $id ?>');

</script>