
<form name="settingsForm" id="settingsForm" method="post" action="<?= admin_url() ?>setting/save">




<!-- Main Column -->
<div id="maincolumn">


	<h2 class="main website" id="main-title"><?= lang('ionize_title_site_settings') ?></h2>
	
	<!-- Title & Meta keywords & Meta description -->
	<fieldset id="blocks">

		<!-- Tabs -->
		<div id="langTab" class="mainTabs">
			<ul class="tab-menu">
				<?php foreach(Settings::get_languages() as $l) :?>
					<li<?php if($l['def'] == '1') :?> class="dl"<?php endif ;?>><a><span><?= ucfirst($l['name']) ?></span></a></li>
				<?php endforeach ;?>
			</ul>
			<div class="clear"></div>
		</div>


		<div id="langTabContent">
		<!-- Tabs content blocks -->

		<?php foreach(Settings::get_languages() as $language) :?>
			
			<div class="tabcontent">
			
				<!-- Title -->
				<dl>
					<dt>
						<label for="site_title_<?=$language['lang']?>"><?=lang('ionize_label_site_title')?></label>
					</dt>
					<dd>
						<input name="site_title_<?=$language['lang']?>" id="site_title_<?=$language['lang']?>" class="inputtext w360" type="text" value="<?=Settings::get('site_title', $language['lang']) ?>"/>
					</dd>
				</dl>

				<dl>
					<dt>
						<label for="meta_description_<?=$language['lang']?>"><?=lang('ionize_label_meta_description')?></label>
					</dt>
					<dd>
						<textarea name="meta_description_<?=$language['lang']?>" id="meta_description_<?=$language['lang']?>" class="w360 h60"><?=Settings::get('meta_description', $language['lang']) ?></textarea>
					</dd>
				</dl>

				<dl>
					<dt>
						<label for="meta_keywords_<?=$language['lang']?>"><?=lang('ionize_label_meta_keywords')?></label>
					</dt>
					<dd>
						<textarea name="meta_keywords_<?=$language['lang']?>" id="meta_keywords_<?=$language['lang']?>" class="w360 h60"><?=Settings::get('meta_keywords', $language['lang']) ?></textarea>
					</dd>
				</dl>

			</div>

		<?php endforeach ;?>
		</div>
	</fieldset>

</div> <!-- /maincolumn -->

</form>

<script type="text/javascript">
	
	/**
	 * Panel toolbox
	 *
	 */
	MUI.initToolbox('setting_toolbox');

	/**
	 * Options Accordion
	 *
	 */
	MUI.initAccordion('.toggler', 'div.element');

	/**
	 * Init help tips on label
	 * see init-content.js
	 *
	 */
	MUI.initLabelHelpLinks('#settingsForm');

	/** 
	 * Tabs init
	 *
	 */
	new TabSwapper({tabsContainer: 'langTab', sectionsContainer: 'langTabContent', selectedClass: 'selected', deselectedClass: '', tabs: 'li', clickers: 'li a', sections: 'div.tabcontent', cookieName: 'categoryTab<?= $UNIQ ?>' });

</script>