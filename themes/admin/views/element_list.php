<?php

/**
 * Modal window for elements list
 * Called by view : element_add (when the user clicks on the "Add Element" button)
 *
 */
?>

<?php foreach($elements as $element) :?>

	<?php
	
	$id = $element['id_element_definition'];
	$title = ($element['title'] != '' ) ? $element['title'] : $element['name'];
	
	?>

	<li class="sortme element_definition" id="element_definition_<?= $id ?>" rel="<?= $id ?>">

		<a class="icon plus left" rel="<?=$id?>"></a>
		<a class="left pl10 plus" rel="<?= $id ?>"><?= $title ?></a>
	
		<span class="toggler right" style="display:block;height:16px;" rel="<?= $id ?>">
			<a class="left" rel="<?= $id ?>"><?= lang('ionize_label_see_element_detail') ?></a>
		</span>
	
		<div style="overflow:hidden;clear:both;" class="ml20 mr20">
			
			<div class="pt5" id="add_def_<?= $id ?>">
				
				<ul class="fields" id="fields<?= $id ?>" rel="<?= $id ?>">

					<?php foreach($element['fields'] as $field) :?>
						<li class="" rel="<?= $field['id_extend_field'] ?>">
							<span class="lite right mr10" rel="<?= $field['id_extend_field'] ?>">
								<?= $field['type_name'] ?>
								<?php if($field['translated'] == '1') :?>
									 / <?= lang('ionize_label_multilingual') ?>
								<?php endif ;?>
							</span>
							<span class="left ml10" rel="<?= $field['id_extend_field'] ?>"><?= $field['label'] ?></span>
						</li>
					<?php endforeach ;?>
				</ul>
			</div>
		</div>
	
	</li>

<?php endforeach ;?>


<script type="text/javascript">

// Add toggler to each definition
$$('#element_definition_<?= $id ?> .toggler').each(function(el)
{
	ION.initListToggler(el, $('add_def_' + el.getProperty('rel')));
});

// Plus icon Event
$$('#element_definition_<?= $id ?> .plus').each(function(item)
{
	item.addEvent('click', function(e)
	{
		var id = item.getProperty('rel');

		ION.HTML('element_definition/get_element_detail', {'id_element_definition': id, 'parent': '<?= $parent?>', 'id_parent': '<?= $id_parent?>'}, {'update': 'elementAddContainer' });		
	});
});

var windowEl = $('waddContentElement');
var contentEl = $('waddContentElement_content');
windowEl.retrieve('instance').resize({height: 300, width: 400});



</script>
