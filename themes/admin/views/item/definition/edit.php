<?php
/**
 * Collection Edit view
 * @receives :
 * 		$definition : 	Array of the current edited definition
 *           			empty if new definition
 */

$id = $definition['id_item_definition'];

?>

<form name="definitionForm<?php echo $id ?>" id="definitionForm<?php echo $id ?>" action="<?php echo base_url() ?>item_definition/save">

	<!-- Hidden fields -->
	<input name="id_item_definition" type="hidden" value="<?php echo $id ?>" />

	<div id="definitionMain<?php echo $id ?>">

		<!-- Name -->
		<dl class="small required">
			<dt>
				<label for="name<?php echo $UNIQ ?>" title="<?php echo lang('ionize_help_definition_name') ?>"><?php echo lang('ionize_label_name'); ?></label>
			</dt>
			<dd>
				<input id="name<?php echo $UNIQ ?>" name="name" class="inputtext required title w200 definitionNameUnique" type="text" value="<?php echo $definition['name'] ?>" />
			</dd>
		</dl>

		<!-- Description -->
		<dl class="small">
			<dt>
				<label for="description<?php echo $UNIQ ?>"><?php echo lang('ionize_label_description'); ?></label>
			</dt>
			<dd>
				<textarea id="description<?php echo $UNIQ ?>" name="description" class="textarea autogrow"><?php echo $definition['description'] ?></textarea>
			</dd>
		</dl>

	</div>


	<fieldset>

		<!-- Tabs -->
		<div id="definitionTab<?php echo $UNIQ ?>" class="mainTabs">
			<ul class="tab-menu">
				<?php foreach(Settings::get_languages() as $language) :?>
					<li class="<?php if($language['def'] == '1') :?> dl<?php endif ;?>" rel="<?php echo $language['lang'] ?>"><a><?php echo ucfirst($language['name']) ?></a></li>
				<?php endforeach ;?>
			</ul>
		</div>

		<div id="definitionTabContent<?php echo $UNIQ ?>">

			<?php foreach(Settings::get_languages() as $language) :?>

				<?php $lang = $language['lang']; ?>

				<div class="tabcontent <?php echo $lang; ?>">

					<!-- Collection Title -->
					<dl class="small">
						<dt>
							<label for="tc<?php echo $UNIQ ?>" title="<?php echo lang('ionize_help_item_title_definition') ?>"><?php echo lang('ionize_label_item_title_definition') ?></label>
						</dt>
						<dd>
							<input id="tc<?php echo $UNIQ ?>" name="title_definition_<?php echo $lang ?>" class="inputtext" type="text" value="<?php echo $definition['languages'][$lang]['title_definition'] ?>"/>
						</dd>
					</dl>

					<!-- Item Title -->
					<dl class="small">
						<dt>
							<label for="ti<?php echo $UNIQ ?>" title="<?php echo lang('ionize_help_item_title_definition_item') ?>"><?php echo lang('ionize_label_item_title_definition_item') ?></label>
						</dt>
						<dd>
							<input id="ti<?php echo $UNIQ ?>" name="title_item_<?php echo $lang ?>" class="inputtext" type="text" value="<?php echo $definition['languages'][$lang]['title_item'] ?>"/>
						</dd>
					</dl>
				</div>

			<?php endforeach ;?>
		</div>
	</fieldset>

</form>

<div class="buttons">
	<button class="button yes right mr40" type="button" id="bSavedefinition<?php echo $id ?>"><?php echo lang('ionize_button_save_close') ?></button>
	<button class="button no right" type="button" id="bCanceldefinition<?php echo $id ?>"><?php echo lang('ionize_button_cancel') ?></button>
</div>


<script type="text/javascript">

	var uniq = '<?php echo $UNIQ ?>';

	ION.initFormAutoGrow();

	// Tabs
	var definitionTab = new TabSwapper({
		tabsContainer: 'definitionTab' + uniq,
		sectionsContainer: 'definitionTabContent' + uniq,
		selectedClass: 'selected',
		deselectedClass: '',
		tabs: 'li',
		clickers: 'li a',
		sections: 'div.tabcontent',
		cookieName: 'definitionTab'
	});

	Form.Validator.add(
		'definitionNameUnique',
		{
			errorMsg: '<?php echo lang('ionize_message_item_definition_already_exists') ?>',
			test: function(element, props)
			{
				if (element.value.length > 0) {
					var req = new Request({
						url: ION.adminUrl + 'item_definition/check_exists',
						async: false,
						data: {
							name: $('name' + uniq).value,
							id_item_definition: '<?php echo $definition['id_item_definition'] ?>'
						}
					}).send();
					return (req.response.text != '1');
				}
				return true;
			}
		}
	);

</script>
