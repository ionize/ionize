<?php

$id = $id_element_definition;

?>

<li class="sortme nohover element_definition" id="element_definition_<?= $id ?>" rel="<?= $id ?>">
	
	
	<div class="h20">

		<a class="icon delete right" rel="<?= $id ?>"></a>

		<a class="icon drag left mr10"></a>

<!--	<span class="toggler left" style="display:block;height:16px;" rel="<?= $id ?>"></span>-->


		<?php if ($name == '') :?>
			<input id="elementName<?= $id ?>" type="text" class="inputtext w120 left" />
			
				<a id="elementDefinitionSave<?= $id ?>" class="light button left ml10">
					<i class="icon-save"></i><?= lang('ionize_label_element_set_name') ?>
				</a>
			
			<script type="text/javascript">
			
				$('elementDefinitionSave<?= $id ?>').addEvent('click', function()
				{
					ION.sendData('element_definition/save_field', {'id':'<?= $id ?>', 'field': 'name', 'value': $('elementName<?= $id ?>').value, selector:'.element_definition a.name[rel=<?= $id ?>]'});
				});
				
				$('elementName<?= $id ?>').focus();
				$('element_definition_<?= $id ?>').addClass('stripped');
			
			</script>
			
		<?php else :?>
			<a class="edit name left" rel="<?= $id ?>"><?= $name ?></a>
		<?php endif ;?>
	</div>

	<?php if ($name != '') :?>

	<div class="clear mt10 mr20 ml40 ">
	
		<!-- Tabs -->
		<div id="elementDefinitonTab<?= $id ?>" class="mainTabs transparent">
			
			<ul class="tab-menu">
				<?php foreach(Settings::get_languages() as $language) :?>
					<li <?php if($language['def'] == '1') :?> class="dl"<?php endif ;?>><a><?= ucfirst($language['name']) ?></a></li>
				<?php endforeach ;?>
			</ul>
			<div class="clear"></div>
		
		</div>
	
		<div id="elementDefinitonTabContent<?= $id ?>" class="ml20 mr20 mb10">
	
			<!-- Text block -->
			<?php foreach(Settings::get_languages() as $language) :?>
		
				<?php 
				
					$lang = $language['lang']; 
					$aTitle = lang('ionize_label_change').' '.lang('ionize_label_title');
				
				?>
				
				<div class="tabcontent">
					<a class="edit title left" rel="<?= $id ?>.<?= $lang ?>" title="<?= $aTitle ?>"><?= ${$lang}['title'] ?></a>
				</div>
	
			<?php endforeach ;?>
		
		
		</div>
		
		<hr />
	
		<!-- Fields -->
		<div style="overflow:hidden;clear:both;">
			
			<div class="pt5" id="def_<?= $id ?>">
				
				<!-- Add Field button -->
				<?php if ($id != 0) :?>
					<a class="light button mb5 ml5 add_field" rel="<?= $id ?>">
						<i class="icon-plus"></i><?php echo lang('ionize_label_element_add_field'); ?>
					</a>
				<?php endif ;?>
				
				<ul class="fields" id="fields<?= $id ?>" rel="<?= $id ?>">
	
					<?php foreach($fields as $field) :?>
						<li class="sortme" rel="<?= $field['id_extend_field'] ?>">
							<a class="icon delete right" rel="<?= $field['id_extend_field'] ?>"></a>
							
							<span class="lite right mr10" rel="<?= $field['id_extend_field'] ?>">
								<?= $field['type_name'] ?>
								<?php if($field['translated'] == '1') :?>
									 / <?= lang('ionize_label_multilingual') ?>
								<?php endif ;?>

							</span>
							
							<a class="icon drag left"></a>
							<a class="left ml10 edit_field" rel="<?= $field['id_extend_field'] ?>"><?= $field['name'] ?></a>
						</li>
					<?php endforeach ;?>
				</ul>
			</div>
		</div>
	
	
	</div>
	
	<?php endif ;?>
	

</li>
<script type="text/javascript">
	new TabSwapper({tabsContainer: 'elementDefinitonTab<?= $id ?>', sectionsContainer: 'elementDefinitonTabContent<?= $id ?>', selectedClass: 'selected', deselectedClass: '', tabs: 'li', clickers: 'li a', sections: 'div.tabcontent'});
</script>
