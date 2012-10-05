<?php

//    log_message('error', print_r($elements, TRUE));
    log_message('error', 'View File Loaded : element_definition_list.php');

?>


<?php foreach($elements as $element) :?>

	<?php $this->load->view('element_definition', $element); ?>

<?php endforeach ;?>


<script type="text/javascript">

/**
 * Content Element itemManager
 *
 */
var elementManager = new ION.ItemManager({container: 'elementContainer', 'element':'element_definition'});
elementManager.makeSortable();

/**
 * Content Elements fields manager
 *
 */
$$('#elementContainer .fields').each(function(item, idx)
{
	 item['im' + idx] = new ION.ItemManager({container: item.id, 'element':'element_field'});
	 item['im' + idx].makeSortable();
});


/**
 * Name Edit
 *
 */
$$('#elementContainer .edit.name').each(function(item, idx)
{
	var id = item.getProperty('rel');
	var input = new Element('input', {'type': 'text', 'class':'inputtext left w180', 'name':'name', 'value': item.get('text')});

	input.addEvent('blur', function(e)
	{
		if (input.value != '')
		{
			ION.sendData('element_definition/save_field', {'id':id, 'field': 'name', 'value':input.value, selector:'.element_definition a.name[rel='+id+']' });
		}
		input.hide();
		item.show();
	});

	input.inject(item, 'before').hide();

	item.addEvent('click', function(e)
	{
		input.show().focus();
		item.hide();
	});
});

$$('#elementContainer .edit.title').each(function(item, idx)
{
	var rel = (item.getProperty('rel')).split(".");
	var id = rel[0];
	var lang = rel[1];
	var title = item.getProperty('title');
	
	if (item.get('text') == false) { item.set('text', title).addClass('lite').addClass('italic'); }
	
	var input = new Element('input', {'type': 'text', 'class':'inputtext left w180', 'name':'title' });
	if (item.get('text') != title) { input.value = item.get('text'); }
	
	input.inject(item, 'before').hide();

	input.addEvent('blur', function(e)
	{
		var value = input.value;
		
		if (input.value != '' && input.value != title)
		{
			ION.sendData('element_definition/save_lang_field', {'id':id, 'field': 'title', 'lang':lang, 'value': value, selector:'a.title[rel='+item.getProperty('rel')+']' });
			item.removeClass('lite').removeClass('italic');
		}

		input.hide();
		item.show();
	});

	item.addEvent('click', function(e)
	{
		input.show().focus();
		item.hide();
	});
});



/**
 * Extend Field edit
 *
 */
$$('#elementContainer .edit_field').each(function(item, idx)
{
	item.addEvent('click', function(e)
	{
		e.stop();
		var id = item.getProperty('rel');
		ION.formWindow('elementfield'+id, 'elementfieldForm'+id, 'ionize_title_element_field_edit', 'element_field/edit', {width:400, height:330}, {'id_extend_field': id});
	});		
});



/**
 * Add field button event
 *
 */
$$('#elementContainer li .add_field').each(function(item)
{
	item.addEvent('click', function(e)
	{
		e.stop();
		var id = this.getProperty('rel');
		ION.formWindow('elementfield', 'elementfieldForm', 'ionize_title_element_field_new', 'element_field/create', {width:400, height:330}, {'id_element_definition': id});
	});		
});


</script>
