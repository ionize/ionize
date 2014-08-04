
<?php
	
/**
 * Used to add an element to a container (page , article) on the Editor side
 * Called by element_definition/get_element_detail() when the user click on one element definition
 * When saving, creates an element instance through : element/save
 * @receives:
 *          $lang_fields
 *          $fields
 */


// These values are empty when adding a new element
$id_element = ( !empty($id_element)) ? $id_element : '';
$parent = ( !empty($parent)) ? $parent : '';
$id_parent = ( !empty($id_parent)) ? $id_parent : '';
$title = ! empty($element_definition['title']) ? $element_definition['title'] : $element_definition['name'];
$id_element_definition = $element_definition['id_element_definition'];

?>

<?php if ($id_element == '') :?>
	<a id="elementAddBackButton" class="light button back">
		<i class="icon-back"></i><?php echo lang('ionize_label_back_to_element_list'); ?>
	</a>
<?php endif ;?>

<div class="mt10" id="elementDiv<?php echo $id_element; ?>">

	<form name="elementForm" id="elementForm<?php echo $id_element; ?>" method="post">

		<input type="hidden" id="elementParent<?php echo $id_element; ?>" name="parent" value="<?php echo $parent; ?>" />
		<input type="hidden" id="elementIdParent<?php echo $id_element; ?>" name="id_parent" value="<?php echo $id_parent; ?>" />
		<input type="hidden" id="id_element<?php echo $id_element; ?>" name="id_element" value="<?php echo $id_element; ?>" />
		<input type="hidden" id="id_element_definition<?php echo $id_element; ?>" name="id_element_definition" value="<?php echo $element_definition['id_element_definition']; ?>" />

		<!-- Ordering : First or last (or Element one if exists ) -->
		<?php if( empty($id_element)) :?>
		<dl class=" mb10">
			<dt >
				<label for="ordering"><?php echo lang('ionize_label_ordering'); ?></label>
			</dt>
			<dd>
				<select name="ordering" id="ordering<?php echo $id_element; ?>" class="select">
					<?php if( ! empty($id_element)) :?>
						<option value="<?php echo $ordering; ?>"><?php echo $ordering; ?></option>
					<?php endif ;?>
					<option value="first"><?php echo lang('ionize_label_ordering_first'); ?></option>
					<option value="last"><?php echo lang('ionize_label_ordering_last'); ?></option>
				</select>
			</dd>
		</dl>
		<?php endif ;?>

		<div id="elementExtendContainer<?php echo $id_element_definition; ?>-<?php echo $id_element; ?>"></div>


	</form>
</div>

<div class="buttons">
	<button id="saveElementFormSubmit<?php echo $id_element; ?>" type="button" class="button yes right"><?php echo lang('ionize_button_save_element'); ?></button>
	<button id="saveAndReopenElement<?php echo $id_element; ?>" type="button" class="button blue right ml10" ><?php echo lang('ionize_button_save'); ?></button>
</div>


<script type="text/javascript">

	var id = '<?php echo $id_element; ?>';
	var id_definition = '<?php echo $element_definition['id_element_definition']; ?>';

	// Window Title : Add Element
	if ($('titleAddContentElement'))
		$('titleAddContentElement').set('text', '<?php echo $title ?>');
	// Edit mode
	else
	{
		var title = new Element('h2', {
			'class':'main elements',
			'text': '<?php echo $title ?>'
		});
		title.inject($('elementDiv' + id), 'before');
	}

	// Extend fields
	var options = {
		parent: 'element',
		id_field_parent: id_definition,
		destination: 'elementExtendContainer' + id_definition + '-' + id
	};

	if (id != '') options['id_parent'] = id;

	// Init the ExtendManager
	extendManager.init(options);

	// Get Item Definition Extend Fields
	extendManager.getParentInstances();


	// Back button
	if ($('elementAddBackButton'))
	{
		$('elementAddBackButton').addEvent('click', function(el)
		{
			ION.HTML('element_definition/get_element_list', {'parent': '<?php echo $parent?>', 'id_parent': '<?php echo $id_parent?>'}, {'update': 'elementAddContainer' });
		});
	}

	var saveElement<?php echo $id_element; ?> = function(options)
	{
		// New Element : Add current opened parent / parent_id to the form
		if ($('element') && $('id_element' + id).value == '')
		{
			var parent = $('element').value;
			var id_parent = $('id_' + parent).value;

			if (parent && id_parent)
			{
				$('elementParent' + id).value = parent;
				$('elementIdParent' + id).value = id_parent;
			}
		}

		if ($('elementParent'+ id).value !='' && $('elementIdParent'+ id).value != '')
		{
			// tinyMCE and CKEditor trigerSave
			// mandatory for text save. See how to externalize without make it too complex.
			if (typeof tinyMCE != "undefined")
				tinyMCE.triggerSave();

			// Get the form
			var rOptions = ION.getJSONRequestOptions(
				'element/save',
				$('elementForm'+ id),
				{
					'onSuccess': function(json)
					{
						// Reopen the element
						if(options.reload)
						{
							ION.dataWindow(
								'contentElement' + json.id_element,
								'ionize_title_edit_content_element',
								'element/edit',
								{width:500, height:350},
								{'id_element': json.id_element}
							);
						};
						ION.closeWindow($('wcontentElement'+ id))
					}
				}
			);

			var r = new Request.JSON(rOptions);

			r.send();
		}
		else
		{
			ION.notification('error', Lang.get('ionize_message_element_cannot_be_added_to_parent'));
		}


	};

	// Save button
	$('saveElementFormSubmit'+ id).addEvent('click', function()
	{
		saveElement<?php echo $id_element; ?>({});
	});

	$('saveAndReopenElement' + id).addEvent('click', function()
	{
		saveElement<?php echo $id_element; ?>({reload:true});
	});




	// Tabs
	new TabSwapper({
		tabsContainer: 'elementTab<?php echo $UNIQ; ?>',
		sectionsContainer: 'elementTabContent<?php echo $UNIQ; ?>',
		selectedClass: 'selected',
		deselectedClass: '',
		tabs: 'li',
		clickers: 'li a',
		sections: 'div.tabcontent<?php echo $UNIQ; ?>'
	});

</script>
