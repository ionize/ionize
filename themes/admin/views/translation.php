
<div id="maincolumn">

	<h2 class="main languages" id="main-title"><?= lang('ionize_title_translation') ?></h2>

	<form name="translationForm" id="translationForm" method="post" action="<?= admin_url() ?>translation/save">


		<fieldset id="blocks">

			<?php
				$nbLang = count(Settings::get_languages());
				$width = (100 / $nbLang) - 2;
			?>
			
			<div id="block" class="block data">

				<?php
					$el_id = 0;
				?>

				<?php foreach($terms as $term) :?>
				
					<?php
						$el_id ++;
					?>
				
					<ul class="term">
					
						<li>
							<span class="toggler left" style="display:block;height:16px;" rel="<?= $el_id ?>"></span><input type="text" class="left inputtext w300" id="key_<?= $el_id ?>" name="key_<?= $el_id ?>" value="<?= $term ?>"></input>
							<a class="left icon delete ml5" rel="<?= $el_id ?>"></a>
						</li>
						
						<div class="translation pl5" id="el_<?= $el_id ?>">
						
							<?php foreach(Settings::get_languages() as $language) :?>
						
								<?php $lang = $language['lang']; ?>
								
								<div style="float:left;width:<?=$width?>%;">
									<label for="<?=$lang?>_<?= $el_id ?>"><?=$language['name']?></label>
									<textarea name="value_<?=$lang?>_<?= $el_id ?>" id="<?=$lang?>_<?= $el_id ?>" class="h60 ml5" style="width:100%;"><?= $translated_items[$lang][$term] ?></textarea>
								</div>
								
							<?php endforeach ;?>

							<p class="pl5 lite small">
								<?php if( ! empty($views_terms['views'][$term]) ) :?>
								
									<?= $views_terms['views'][$term] ?>
								
								<?php endif ;?>
							
							</p>
												
						</div>
					</ul>

				<?php endforeach ;?>
		
			</div>
		</fieldset>
	</form>
	
	<!-- Term block model -->
	<ul id="termModel" class="term" style="display:none;">
		<li><span class="toggler"></span><input type="text" class="inputtext w300"></input></li>
		<div class="translation ml15">
			<?php foreach(Settings::get_languages() as $language) :?>
				<?php $lang = $language['lang']; ?>
				<div style="float:left;width:<?=$width?>%;" class="ml5">
					<label for="<?=$lang?>_"><?=$language['name']?></label>
					<textarea name="value_<?=$lang?>_" class="h60 ml5" style="width:100%;"></textarea>
				</div>
			<?php endforeach ;?>
			<p class="clear"></p>
		</div>
	</ul>
	
</div>

<script type="text/javascript">
	
	/**
	 * Panel toolbox
	 * Init the panel toolbox is mandatory !!! 
	 *
	 */
	MUI.initToolbox('translation_toolbox');

	$$('#block .toggler').each(function(el)
	{
		ION.initListToggler(el, $('el_' + el.getProperty('rel')));
	});
	
	
	/**
	 * Term delete
	 *
	 */
	$$('#block .delete').each(function(item)
	{
		var rel = item.getProperty('rel');
		
		item.addEvent('click', function(e)
		{
			MUI.confirmation(
				'deleteTranslationTerm' + rel,
				function()
				{
					$('key_' + rel).value = '';
					MUI.sendData(base_url + 'translation/save', $('translationForm'))
				},
				Lang.get('ionize_message_delete_translation')
			);
		});

	});
	
</script>