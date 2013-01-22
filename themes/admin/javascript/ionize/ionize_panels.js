ION.append({

	/**
	 *
	 * @param options    Options object
	 *
	 * @usage
	 *
	 * ION.splitPanel({
	 *      'urlMain': 'URL to the main panel content',
	 *      'urlOptions': 'Url to the option panel content',
	 *      'title': 'Title of the main panel',
	 *
	 * });
	 *
	 */
	splitPanel: function(options)
	{
		if ($('mainPanel'))
		{
			// Collapse / Expanded status from cookie
			var isCollapsed = false;
			var opened = Cookie.read('sidecolumn');

			if (typeOf(opened) != 'null' && opened == 'false')
				isCollapsed = true;

			MUI.Content.update({
				element: 'mainPanel',
				title: options.title,
				clear:true,
				loadMethod:'control',
				controls:[
					{
						control:'MUI.Column',
						container: 'mainPanel',
						id: 'splitPanel_mainColumn',
						placement: 'main',
						sortable: false,
						panels:[
							{
								control:'MUI.Panel',
								id: 'splitPanel_mainPanel',
								container: 'splitPanel_mainColumn',
								header: false,
								content: {
									url: options.urlMain
									/*
									onLoaded: function(){
										//$('splitPanel_mainColumn').setStyle('width', 'inherit');
									}
									*/
								}
							}
						]
					},
					{
						control:'MUI.Column',
						container: 'mainPanel',
						id: 'splitPanel_sideColumn',
						placement: 'right',
						sortable: false,
						isCollapsed: isCollapsed,
						width: 330,
						resizeLimit: [330, 400],
						panels:[
							{
								control:'MUI.Panel',
								header: false,
								id: 'splitPanel_sidePanel',
								cssClass: 'panelAlt',
								padding:typeOf(options.paddingOptions != 'null') ? options.paddingOptions : 8,
								content: {
									url: options.urlOptions,
									onLoaded: function(){
										$('splitPanel_sidePanel').setStyle('width', 'inherit');
									}
								},
								container: 'splitPanel_sideColumn'
							}
						]
					}
				]
			});
		}
	}
});