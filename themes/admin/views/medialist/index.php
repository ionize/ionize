<?php
/**
 * Media List
 *
 */
$thumb_size = (Settings::get('media_thumb_size') != '') ? Settings::get('media_thumb_size') : '120';
?>

<div id="mediaList" class="p10 bg-gray">
	<form id="medialistForm" name="medialistForm" action="medialist/save">
		<?php foreach($medias as $media) :?>

			<?php
				$id = $media['id_media'];
				$flag = '';
				if ( ! $media['has_source'])
					$flag = 'flag-broken';
				else if ($media['is_used'] && $media['alt_missing'])
					$flag = 'flag-incomplete';
				else if($media['is_used'] && ! $media['alt_missing'])
					$flag = 'flag-complete';

			?>
			<div data-id="<?php echo $media['id_media'] ?>" class="media mb10 card <?php echo $flag ?>" data-has-source="<?php echo $media['has_source'] ?>" data-alt-missing="<?php echo $media['alt_missing'] ?>" data-is-used="<?php echo $media['is_used'] ?>" >

				<input type="hidden" name="media_<?php echo $id; ?>[id_media]" value="<?php echo $id ?>" />

				<p class="icons-bar">
					<?php if(Authority::can('delete', 'admin/medialist')) :?>
						<a class="icon remove right" title="<?php echo lang('ionize_label_remove_media'); ?>" data-id="<?php echo $id; ?>"></a>
					<?php endif ;?>
					<a title="<?php echo $id ?> : <?php echo $media['path'] ?>" class="icon info right mr5"></a>
					<?php if ( ! empty($media['article_paths'])) :?>
						<a title="<?php echo $media['article_paths'] ?>" class="icon right article mr5"></a>
					<?php endif ;?>
					<?php if ( ! empty($media['page_paths'])) :?>
						<a title="<?php echo $media['page_paths'] ?>" class="icon right folder mr5"></a>
					<?php endif ;?>
				</p>

				<div class="displayer">

					<?php if ($media['type'] == 'picture') :?>
						<div class="picture">
							<div class="thumb" style="width:<?php echo $thumb_size; ?>px;height:<?php echo $thumb_size; ?>px; background-image:url(<?php echo admin_url(TRUE) . 'media/get_thumb/'.$media['id_media'].'/'.time() ; ?>);"></div>
						</div>
					<?php endif ;?>

					<?php if ($media['type'] == 'video') :?>
						<?php if($media['provider'] != '') :?>

							<iframe width="150" height="130" class="mt20" src="<?php echo $media['path'] ?>" frameborder="0"></iframe>

						<?php else :?>

							<div id="video<?php echo $id; ?>" class="mt20">
								<embed
									flashvars="file=<?php echo base_url().$media['path']?>&autostart=false"
									allowfullscreen="true"
									allowscriptaccess="always"
									id="video<?php echo $id; ?>"
									name="video<?php echo $id; ?>"
									src="<?php echo theme_url(); ?>flash/mediaplayer/player.swf"
									width="150"
									height="130"
								/>
							</div>

						<?php endif ;?>

					<?php endif ;?>

					<?php if ($media['type'] == 'music') :?>
						<div class="ui360 ui360-vis"><a class="sound" id="sound<?php echo $id ?>" href="<?php echo base_url().$media['path'] ?>" target="_blank"><?php echo $media['file_name'] ?></a></div>
					<?php endif ;?>

					<?php if ($media['type'] == 'file') :?>
						<div class="pt50"><?php echo $media['path'] ?></div>
					<?php endif ;?>
				</div>

				<div class="icon toggle-card panel-expand panel-expanded right" title=""></div>

				<div class="data">

					<dl class="small copyright p10">
						<dt></dt>
						<dd>
							<input id="copyright_<?php echo $id; ?>" placeholder="<?php echo lang('ionize_label_copyright'); ?>" name="media_<?php echo $id; ?>[copyright]" class="inputtext" type="text" value="<?php echo $media['copyright'] ?>"/>
						</dd>
					</dl>

					<div id="mediaTab<?php echo $id ?>" class="mainTabs" data-section="mediaTabContent<?php echo $id ?>">
						<ul class="tab-menu">
							<?php foreach(Settings::get_languages() as $language) :?>
								<li class="<?php if($language['def'] == '1') :?>dl<?php endif ;?>" data-lang="<?php echo $language['lang']; ?>"><a><?php echo ucfirst($language['lang']); ?></a></li>
							<?php endforeach ;?>
						</ul>
						<div class="clear"></div>
					</div>

					<div id="mediaTabContent<?php echo $media['id_media'] ?>" class="p10">

						<?php foreach(Settings::get_languages() as $language) :?>

							<?php $lang_code = $language['lang']; ?>

							<div class="tabcontent">
								<dl class="small">
									<dt>
										<label for="title_<?php echo $lang_code.$id; ?>"><?php echo lang('ionize_label_title'); ?></label>
									</dt>
									<dd>
										<input id="title_<?php echo $lang_code.$id; ?>" name="media_<?php echo $id; ?>[lang][<?php echo $lang_code; ?>][title]" class="inputtext" type="text" value="<?php echo $media['lang'][$lang_code]['title'] ?>"/>
									</dd>
								</dl>
								<dl class="small">
									<dt>
										<label for="alt_<?php echo $lang_code.$id; ?>"><?php echo lang('ionize_label_alt'); ?></label>
									</dt>
									<dd>
										<input id="alt_<?php echo $lang_code.$id; ?>" name="media_<?php echo $id; ?>[lang][<?php echo $lang_code; ?>][alt]" class="inputtext" type="text" value="<?php echo $media['lang'][$lang_code]['alt'] ?>"/>
									</dd>
								</dl>
								<dl class="small">
									<dt>
										<label for="description_<?php echo $lang_code.$id; ?>"><?php echo lang('ionize_label_description'); ?></label>
									</dt>
									<dd>
										<textarea id="description_<?php echo $lang_code.$id; ?>"  name="media_<?php echo $id; ?>[lang][<?php echo $lang_code; ?>][description]"><?php echo $media['lang'][$lang_code]['description'] ?></textarea>
									</dd>
								</dl>

							</div>
						<?php endforeach ;?>
						<div class="clearfix"></div>
					</div>

					<div>
						<a class="button green right saveMedia" data-id="<?php echo $id ?>">
							<?php echo lang('ionize_button_save') ?>
						</a>
					</div>

				</div>
				<div class="clearfix"></div>
			</div>

		<?php endforeach ?>
	</form>
</div>
<script type="text/javascript">

	ION.initToolbox('medialist_toolbox');

	// Tabs
	$$('#mediaList .mainTabs').each(function(el)
	{
		new TabSwapper({
			tabsContainer: el.id,
			sectionsContainer: el.getProperty('data-section'),
			selectedClass: 'selected',
			deselectedClass: '',
			tabs: 'li',
			clickers: 'li a',
			sections: 'div.tabcontent'
		});
	});
	$$('#mediaList .data').each(function(el)
	{
		el.slide('hide').removeClass('open');
	});

	$$('#mediaList .toggle-card').each(function(el)
	{
		el.addEvent('click', function(e)
		{
			e.stop();
			if (el.hasClass('panel-expanded'))
			{
				el.getParent('div').getElement('.data').slide('in');
				el.removeClass('panel-expand').removeClass('panel-expanded');
				el.addClass('panel-collapse').addClass('panel-collapsed');
			}
			else
			{
				el.getParent('div').getElement('.data').slide('out');
				el.addClass('panel-expand').addClass('panel-expanded');
				el.removeClass('panel-collapse').removeClass('panel-collapsed');
			}
		});
	});


	$$('.saveMedia').each(function(btn)
	{
		btn.hide();
		btn.addEvent('click', function(e){
			e.stop();
			var id = e.target.getProperty('data-id');
		})
	});

	$$('#mediaList .remove').each(function(item)
	{
		var id = item.getProperty('data-id');
		ION.initRequestEvent(
			item,
			'medialist/remove',
			{'id_media': id},
			{}
		);
	});

	$$('#mediaList .sound').each(function(item)
	{
		soundManager.createSound({
			id: item.getProperty('id'),
			url: item.getProperty('href'),
			autoLoad: true
		});
	});
	threeSixtyPlayer.init();

</script>