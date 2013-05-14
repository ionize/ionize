<?php
/**
 * Displays the tags list
 */

?>

<ul id="tagsList" class="tagsList list"></ul>

<script type="text/javascript">

	ION.JSON(
		ION.adminUrl + 'tag/get_json_list',{},
		{
			onSuccess: function(r)
			{
				r.each(function(tag)
				{
					var li = new Element('li', {'data-id':tag[0],'class':'left mr5'});
					var a = new Element('a', {'class':'title left', 'data-id':tag[0], text:tag[2]}).inject(li);
					var ad = new Element('a', {'class':'icon delete right ml5', 'data-id':tag[0]}).inject(li);

					var input = new Element('input', {'type': 'text', 'class':'inputtext left no-border', 'name':'name', 'value': a.get('text')});

					input.addEvent('blur', function(e)
					{
						if (input.value != '')
						{
							ION.sendData('tag/update', {
								'id_tag':tag[0],
								'tag_name':input.value,
								selector:'.tagsList a.title[data-id='+tag[0]+']' });
						}
						input.hide();
						a.show();
					});
					input.inject(a, 'before').hide();

					a.addEvent('click', function(e)
					{
						input.show().focus();
						a.hide();
					});

					ION.initRequestEvent(
						ad,
						'tag/delete',
						{'id':tag[0]},
						{
							'confirm': true,
							'message': Lang.get('ionize_confirm_element_delete')
						}
					);

					ad.addEvent('click', function(){

					})

					li.inject($('tagsList'));
				})
			}
		}
	);

</script>
