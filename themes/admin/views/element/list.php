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
	$title = ! empty($element['title']) ? $element['title'] : $element['name'];
	?>

	<li class="sortme element_definition" id="element_definition_<?php echo $id; ?>" data-id="<?php echo $id; ?>">

		<a class="icon plus left" data-id="<?php echo $id; ?>"></a>
		<a class="left pl10 plus" data-id="<?php echo $id; ?>"><?php echo $title; ?></a>
	
		<span class="toggler right" style="display:block;height:16px;" data-id="<?php echo $id; ?>">
			<a class="left" data-id="<?php echo $id; ?>"><?php echo lang('ionize_label_see_element_detail'); ?></a>
		</span>
	
		<div style="overflow:hidden;clear:both;" class="ml20 mr20">
			
			<div class="pt5" id="add_def_<?php echo $id; ?>">
				
				<ul class="fields" id="fields<?php echo $id; ?>" data-id="<?php echo $id; ?>">

					<?php foreach($element['fields'] as $field) :?>
						<li class="" data-id="<?php echo $field['id_extend_field']; ?>">
							<span class="lite right mr10" data-id="<?php echo $field['id_extend_field']; ?>">
								<?php echo $field['type_name']; ?>
								<?php if($field['translated'] == '1') :?>
									 / <?php echo lang('ionize_label_multilingual'); ?>
								<?php endif ;?>
							</span>
							<span class="left ml10" data-id="<?php echo $field['id_extend_field']; ?>"><?php echo $field['label']; ?></span>
						</li>
					<?php endforeach ;?>
				</ul>
			</div>
		</div>
	
	</li>

<?php endforeach ;?>


<script type="text/javascript">

// Window Title
if ($('titleAddContentElement'))
	$('titleAddContentElement').set('text', '<?php echo lang('ionize_title_add_content_element') ?>');

// Add toggler to each definition
$$('li.element_definition span.toggler').each(function(el)
{
	ION.initListToggler(el, $('add_def_' + el.getProperty('data-id')));
});

// Plus icon Event
$$('li.element_definition .plus').each(function(item)
{
	item.addEvent('click', function(e)
	{
		var id = item.getProperty('data-id');

		ION.HTML(
			'element_definition/get_element_detail',
			{
				'id_element_definition': id,
				'parent': '<?php echo $parent; ?>',
				'id_parent': '<?php echo $id_parent; ?>'
			},
			{'update': 'elementAddContainer' }
		);
	});
});


// ION.windowResize('contentElement', {height: 300, width: 400});


</script>
