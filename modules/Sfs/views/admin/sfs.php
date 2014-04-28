<?php


?>

<div id="maincolumn">

	<h2 class="main sfs"><?php echo config_item('name'); ?></h2>

	<div class="main subtitle">

		<!-- About this module -->
		<p class="lite">
			<?php echo config_item('description'); ?>
		</p>

	</div>
	
	<!-- Tabs -->
	<div id="sfsTab" class="mainTabs">
		
		<ul class="tab-menu">
			
			<li><a><?php echo lang('module_sfs_settings'); ?></a></li>
			<li><a><?php echo lang('module_sfs_howto'); ?></a></li>

		</ul>
		
		<div class="clear"></div>
	
	</div>

	<div id="sfsTabContent">
	
		<!-- Settings Form tab content -->
		<div class="tabcontent">
		
			<form id="sfsSettingsForm" name="sfsSettingsForm" method="post">

				<!-- Forms registered events -->
				<dl>
					<dt><label for="module_sfs_events" title="<?php echo lang('module_sfs_events_help') ?>"><?php echo lang('module_sfs_events'); ?></label></dt>
					<dd>
						<input class="inputtext w240" type="text" name="events" id="module_sfs_events" value="<?php echo config_item('events'); ?>" />
					</dd>
				</dl>

				<!-- Track spam -->
				<dl>
					<dt><label for="module_sfs_track" title="<?php echo lang('module_sfs_track_help') ?>"><?php echo lang('module_sfs_track'); ?></label></dt>
					<dd>
						<input class="checkbox" type="checkbox" name="track" id="module_sfs_track" <?php if (config_item('track')) :?>checked="checked"<?php endif;?> value="true"/>
					</dd>
				</dl>

				<div id="sfsTrack">
					<!-- API Key -->
					<dl>
						<dt><label for="module_sfs_api_key" title="<?php echo lang('module_sfs_api_key_help') ?>"><?php echo lang('module_sfs_label_api_key'); ?></label></dt>
						<dd>
							<input class="inputtext w240" type="text" name="api_key" id="module_sfs_api_key" value="<?php echo config_item('api_key'); ?>" />
						</dd>
					</dl>

					<!-- Evidence input name -->
					<dl>
						<dt><label for="module_sfs_evidence_input" title="<?php echo lang('module_sfs_evidence_input_help') ?>"><?php echo lang('module_sfs_evidence_input'); ?></label></dt>
						<dd>
							<input class="inputtext w240" type="text" name="evidence_input" id="module_sfs_evidence_input" value="<?php echo config_item('evidence_input'); ?>" />
						</dd>
					</dl>

					<!-- Username input name -->
					<dl>
						<dt><label for="module_sfs_username_input" title="<?php echo lang('module_sfs_username_input_help') ?>"><?php echo lang('module_sfs_username_input'); ?></label></dt>
						<dd>
							<input class="inputtext w240" type="text" name="username_input" id="module_sfs_username_input" value="<?php echo config_item('username_input'); ?>" />
						</dd>
					</dl>
				</div>

				<!-- Submit button  -->
				<dl class="mt10">
					<dt>&#160;</dt>
					<dd>
						<a id="btnTestSettings" class="button mr10">Test settings</a>
						<input id="submit_config" type="submit" class="submit" value="<?php echo lang('ionize_button_save_settings'); ?>" />
					</dd>
				</dl>
		
			</form>

			<div id="testSettingsResult"></div>

		</div>

		<!-- How to -->
		<div class="tabcontent">

			<div class="p10">
				<?php echo lang('module_sfs_howto_text_1') ?>
				<?php echo lang('module_sfs_howto_text_2') ?>
<pre>
<code>$result = Event::fire('Form.yourform.check', $post);

if ( empty($result) OR $result == TRUE)
{
	//... OK
}
else
{
	//... Not OK
}</code></pre>
			</div>
		</div>
	</div>
	
</div>

<script type="text/javascript">

// Init the panel toolbox is mandatory !!!
ION.initToolbox('empty_toolbox');


// Tabs
var sfsTab = new TabSwapper({
	tabsContainer: 'sfsTab',
	sectionsContainer: 'sfsTabContent',
	selectedClass: 'selected',
	deselectedClass: '',
	tabs: 'li',
	clickers: 'li a',
	sections: 'div.tabcontent',
	cookieName: 'sfsTab'
});

// Send Form (XHR)
ION.setFormSubmit(
	'sfsSettingsForm',				// ID of the form to send
	'submit_config',				// ID of the submit button to put the send action on
	'module/sfs/sfs/save_config' 	// URL of the controller's method which process data
);


$('btnTestSettings').addEvent('click', function(e)
{
	ION.HTML(
		'module/sfs/sfs/test',
		$('sfsSettingsForm'),
		{
			'update' : $('testSettingsResult')
		}
	);
});


toggleTrack = function()
{
	var track = $('module_sfs_track').getProperty('checked');

	if (track)
	{
		$('sfsTrack').show();
	}
	else
	{
		$('sfsTrack').hide();
	}
}
toggleTrack();

$('module_sfs_track').addEvent('change', function()
{
	toggleTrack();
});

</script>
