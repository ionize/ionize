<?php

    $id = $id_element_definition;

?>
<h3 class="toggler toggler-<?php echo $id; ?>"><?php echo $name; ?></h3>
    <div class="element element-<?php echo $id; ?>">
        <li class="sortme nohover element_definition" id="element_definition_--><?//= $id; ?><!--" rel="--><?//= $id; ?><!--">
	
	
	<div class="h20">

		<a class="icon delete right" rel="<?php echo $id; ?>"></a>

		<a class="icon drag left mr10"></a>

<!--	<span class="toggler left" style="display:block;height:16px;" rel="<?php echo $id; ?>"></span>-->


		<?php if ($name == '') :?>
			<input id="elementName<?php echo $id; ?>" type="text" class="inputtext w120 left" />
			
				<a id="elementDefinitionSave<?php echo $id; ?>" class="light button left ml10">
					<i class="icon-save"></i><?php echo lang('ionize_label_element_set_name'); ?>
				</a>
			
			<script type="text/javascript">
			
				$('elementDefinitionSave<?php echo $id; ?>').addEvent('click', function()
				{
					ION.sendData('element_definition/save_field', {'id':'<?php echo $id; ?>', 'field': 'name', 'value': $('elementName<?php echo $id; ?>').value, selector:'.element_definition a.name[rel=<?php echo $id; ?>]'});
				});
				
				$('elementName<?php echo $id; ?>').focus();
				$('element_definition_<?php echo $id; ?>').addClass('stripped');
			
			</script>
			
		<?php else :?>
			<a class="edit name left" rel="<?php echo $id; ?>"><?php echo $name; ?></a>
		<?php endif ;?>
	</div>

	<?php if ($name != '') :?>

	<div class="clear mt10 mr20 ml40 ">
	
		<!-- Tabs -->
		<div id="elementDefinitonTab<?php echo $id; ?>" class="mainTabs transparent">
			
			<ul class="tab-menu">
				<?php foreach(Settings::get_languages() as $language) :?>
					<li <?php if($language['def'] == '1') :?> class="dl"<?php endif ;?>><a><?php echo ucfirst($language['name']); ?></a></li>
				<?php endforeach ;?>
			</ul>
			<div class="clear"></div>
		
		</div>
	
		<div id="elementDefinitonTabContent<?php echo $id; ?>" class="ml20 mr20 mb10">
	
			<!-- Text block -->
			<?php foreach(Settings::get_languages() as $language) :?>
		
				<?php 
				
					$lang = $language['lang']; 
					$aTitle = lang('ionize_label_change').' '.lang('ionize_label_title');
				
				?>
				
				<div class="tabcontent">
					<a class="edit title left" rel="<?php echo $id; ?>.<?php echo $lang; ?>" title="<?php echo $aTitle; ?>"><?php echo ${$lang}['title']; ?></a>
				</div>
	
			<?php endforeach ;?>
		
		
		</div>
		
		<hr />
	
		<!-- Fields -->
		<div style="overflow:hidden;clear:both;">
			
			<div class="pt5" id="def_<?php echo $id; ?>">
				
				<!-- Add Field button -->
				<?php if ($id != 0) :?>
					<a class="light button mb5 ml5 add_field" rel="<?php echo $id; ?>">
						<i class="icon-plus"></i><?php echo lang('ionize_label_element_add_field'); ?>
					</a>
				<?php endif ;?>
				
				<ul class="fields" id="fields<?php echo $id; ?>" rel="<?php echo $id; ?>">
	
					<?php foreach($fields as $field) :?>
						<li class="sortme" rel="<?php echo $field['id_extend_field']; ?>">
							<a class="icon delete right" rel="<?php echo $field['id_extend_field']; ?>"></a>
							
							<span class="lite right mr10" rel="<?php echo $field['id_extend_field']; ?>">
								<?php echo $field['type_name']; ?>
								<?php if($field['translated'] == '1') :?>
									 / <?php echo lang('ionize_label_multilingual'); ?>
								<?php endif ;?>

							</span>
							
							<a class="icon drag left"></a>
							<a class="left ml10 edit_field" rel="<?php echo $field['id_extend_field']; ?>"><?php echo $field['name']; ?></a>
						</li>
					<?php endforeach ;?>
				</ul>
			</div>
		</div>
	
	
	</div>
	
	<?php endif ;?>
    </li>
</div>
<script type="text/javascript">
	new TabSwapper({tabsContainer: 'elementDefinitonTab<?php echo $id; ?>', sectionsContainer: 'elementDefinitonTabContent<?php echo $id; ?>', selectedClass: 'selected', deselectedClass: '', tabs: 'li', clickers: 'li a', sections: 'div.tabcontent'});
    ION.initAccordion('.toggler-<?php echo $id; ?>', 'div.element-<?php echo $id; ?>', true, 'elementDefinationAccordion-<?php echo $id; ?>');
</script>
