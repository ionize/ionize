
<!-- Existing types -->
<h3 class="toggler3"><?= lang('ionize_title_types_exist') ?></h3>

<div class="element3">

	<ul id="typeContainer">
	
	<?php foreach($types as $type) :?>
	
		<li class="sortme article_type<?= $type['id_type'] ?>" id="article_type_<?= $type['id_type'] ?>" rel="<?= $type['id_type'] ?>">
			<a class="icon delete right" rel="<?= $type['id_type'] ?>"></a>
			<img class="icon left drag" src="<?= theme_url() ?>images/icon_16_ordering.png" />
			<a class="left pl5" href="javascript:void(0);" onclick="javascript:MUI.formWindow('Type', 'typeForm', '<?= lang('ionize_title_type_edit')?>', 'article_type/edit/<?= $type['id_type'] ?>', {width: 360, height: 75});" title="edit"><?= $type['type'] ?></a>
		</li>
	
	<?php endforeach ;?>
	
	</ul>
</div>


<!-- New type -->
<h3 class="toggler3"><?= lang('ionize_title_type_new') ?></h3>

<div class="element3">

	<form name="newTypeForm" id="newTypeForm" action="<?= admin_url() ?>article_type/save">
	
		<!-- Hidden fields -->
		<input id="id_type" name="id_type" type="hidden" value="<?= $id_type ?>" />
		<input id="parent" name="parent" type="hidden" value="<?= $parent ?>" />
		<input id="id_parent" name="id_parent" type="hidden" value="<?= $id_parent ?>" />
	
		
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



<script type="text/javascript">

	/**
	 * Categories list itemManager
	 *
	 */
	typesManager = new ION.ItemManager(
	{
		parent: 	'<?= $parent ?>',
		idParent: 	'<?= $id_parent ?>',
		element: 	'article_type',
		container: 	'typeContainer'	
	});
	
	typesManager.makeSortable();
	
	
	/**
	 * Options Accordion
	 *
	 */
	MUI.initAccordion('.toggler3', 'div.element3');
	
	
	/**
	 * Form submit
	 *
	 */
	var url = admin_url + 'article_type/save';
	
	$('bSaveNewType').addEvent('click', function(e) {
		e.stop();
		MUI.sendData(url, $('newTypeForm'));
	});

</script>
