<?php

$id = $id_element_definition;

?>

<li class="sortme nohover element_definition" id="element_definition_<?php echo $id ;?>" data-id="<?php echo $id ;?>">

	<div class="h20">

		<?php if ( Authority::can('delete', 'admin/element')) :?>
			<a class="icon delete right" data-id="<?php echo $id ;?>"></a>
		<?php endif;?>

		<span class="icon left drag mr10"></span>
		<span class="toggler element-list" style="float:left">&nbsp;</span>

		<?php if ($name == '') :?>
		<input id="elementName<?php echo $id ;?>" type="text" class="inputtext w120 left" />
			<button id="elementDefinitionSave<?php echo $id ;?>" type="button" class="light-button left ml10" value="Save">
				<?php echo lang('ionize_label_element_set_name') ;?>
			</button>

			<script type="text/javascript">
				var func_save = function()
				{
					ION.sendData('element_definition/save_field', {
						'id':'<?php echo $id ;?>',
						'field': 'name',
						'value': $('elementName<?php echo $id ;?>').value,
						selector:'.element_definition a.name[data-id=<?php echo $id ;?>]'
					});
				};

				var el_name = $('elementName<?php echo $id ;?>');
				$('elementDefinitionSave<?php echo $id ;?>').addEvent('click', func_save);

				el_name.addEvent('keyup', function(e)
				{
					if(e && e.code == 13 && $('elementName<?php echo $id ;?>').value.trim() != '' ) {
						func_save();
					}
				});

				el_name.focus();
				$('element_definition_<?php echo $id ;?>').addClass('stripped');
			</script>

		<?php else :?>
			<a class="edit name left" data-id="<?php echo $id ;?>"><?php echo $name ;?></a>
		<?php endif ;?>

	</div>
	<div class="element element-list">

		<?php if ($name != '') :?>

			<div class="clear mt10 mr20 ml40 ">

				<!-- Tabs -->
				<div id="elementDefinitonTab<?php echo $id ;?>" class="mainTabs">

					<ul class="tab-menu">
						<?php foreach(Settings::get_languages() as $language) :?>
							<li <?php if($language['def'] == '1') :?> class="dl"<?php endif ;?>><a><?php echo ucfirst($language['name']) ;?></a></li>
						<?php endforeach ;?>

						<li class="right" id="usageTab"><a><?php echo count($usages) . ' ' . lang('ionize_button_usage'); ?></a></li>
					</ul>
					<div class="clear"></div>

				</div>

				<div id="elementDefinitionTabContent<?php echo $id ;?>" class="ml20 mr20 mb10">

					<!-- Text block -->
					<?php foreach(Settings::get_languages() as $language) :
						$lang = $language['lang'];
						$aTitle = lang('ionize_label_change').' '.lang('ionize_label_title');
					?>

						<div class="tabcontent">
							<a class="edit title left" data-id="<?php echo $id ;?>.<?php echo $lang ;?>" title="<?php echo $aTitle ;?>"><?php echo $languages[$lang]['title'] ;?></a>
						</div>

					<?php endforeach ;?>

					<div class="tabcontent">
						<table class="list">
							<thead>
							<tr>
								<th><?php echo lang('ionize_title_pages'); ?></th>
								<th><?php echo lang('ionize_title_articles'); ?></th>
								<th style="width:70px;" class="right"></th>
							</tr>
							</thead>

							<tbody>
							<?php foreach($usages as $elementUsage) { ?>
								<tr>
									<td class="pl10">
										<a title="<?php echo $elementUsage['page']['id_page'] ?>: <?php echo $elementUsage['page']['name'] ?>" onclick="$$('a.page<?php echo $elementUsage['page']['id_page'] ?>').getLast().fireEvent('click')" class="page-breadcrumb">
											<?php echo $elementUsage['page']['name'] ?>
										</a>
									</td>
									<td class="pl10">
										<?php if( $elementUsage['article'] === null ) { ?>
											-
										<?php } else { ?>
											<a title="<?php echo $elementUsage['page']['id_page'] ?>.<?php echo $elementUsage['article']['id_article'] ?>: <?php echo $elementUsage['article']['name'] ?>" onclick="$$('a.article<?php echo $elementUsage['page']['id_page'] ?>x<?php echo $elementUsage['article']['id_article'] ?>').getLast().fireEvent('click')" class="page-breadcrumb">
												<?php echo $elementUsage['article']['name'] ?>
											</a>
										<?php } ?>
									</td>
									<td class="pr10">
										<a data-id="<?php echo $elementUsage['id_element'] ?>" class="delete-usage icon right"></a>
									</td>
								</tr>
							<?php } ?>
							</tbody>
						</table>
					</div>
					
				</div>

				<hr />

				<!-- Fields -->
				<div style="overflow:hidden;clear:both;">

					<div class="pt5" id="def_<?php echo $id ;?>">

						<!-- Add Field button -->
						<?php if ($id != 0 && Authority::can('edit', 'admin/element')) :?>
							<a class="light button plus mb5 ml5 add_field" data-id="<?php echo $id ;?>">
								<i class="icon-plus"></i>
								Add field
							</a>
						<?php endif ;?>

						<ul class="fields" id="fields<?php echo $id ;?>" data-id="<?php echo $id ;?>">

							<?php foreach($fields as $field) :?>
								<li class="sortme element_field" data-id="<?php echo $field['id_extend_field'] ;?>" id="element_field<?php echo $field['id_extend_field'] ;?>">
									<span class="icon left drag"></span>

									<?php if ( Authority::can('edit', 'admin/element')) :?>
										<a class="icon delete right" data-id="<?php echo $field['id_extend_field'] ;?>"></a>
									<?php endif ;?>

									<span class="lite right mr10" data-id="<?php echo $field['id_extend_field'] ;?>">
										<?php echo $field['type_name'] ;?>
										<?php if($field['translated'] == '1') :?>
											/ <?php echo lang('ionize_label_multilingual') ;?>
										<?php endif ;?>
									</span>

									<a class="left ml10 edit_field" data-id="<?php echo $field['id_extend_field'] ;?>"><?php echo $field['name'] ;?></a>
								</li>
							<?php endforeach ;;?>
						</ul>
					</div>
				</div>


				<!-- Order items by... button -->
				<?php

				/*
				 * @TODO : 	Define the ordering when adding one element
				 * 			Planned for 1.0
				 *
				<hr/>

				<label for=""><? echo lang('ionize_next_element_will_be') ?></label>
				<select class="inputtext" name="order_by">
					<option value="-1" label="Descroissant"><?php echo lang('ionize_label_first') ;?></option>
					<option value="1" label="Croissant"><?php echo lang('ionize_label_last') ;?></option>
				</select>
				*/
				?>

			</div>

		<?php endif ;?>
	</div>

</li>
<script type="text/javascript">
    	new TabSwapper({
		tabsContainer: 'elementDefinitonTab<?php echo $id ;?>',
		sectionsContainer: 'elementDefinitionTabContent<?php echo $id ;?>',
		selectedClass: 'selected',
		deselectedClass: '',
		tabs: 'li',
		clickers: 'li a',
		sections: 'div.tabcontent'
	});
	
	$$('.delete-usage').each(function(el){
		el.addEvent('click', function() {
			ION.initRequestEvent(
				el,
				admin_url + 'element/delete/' + el.get('data-id'),
				{},
				{
					'confirm': true,
					'message': Lang.get('ionize_confirm_element_delete_usage'),
					'onSuccess': function()
					{
						$$('a[href="element_definition/index"]')[0].click();
					}
				}
			);
		});
	});	
</script>
