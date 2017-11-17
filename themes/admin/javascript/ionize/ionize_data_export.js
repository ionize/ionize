/**
 *  DataExport
 *
 *	Options :
 *
 */

ION.DataExport = new Class({

	Implements: [Events, Options],

	options: {},

	mimes: {
		'xls' : 'data:text/xls;charset=utf-8,'
	},


	initialize: function (options) {},


	/**
	 *
	 * @param title
	 * @param definition		[
	 * 								{
	 * 									key:	field of the data array
	 * 									type:	'String', 'Number', 'Date'
	 * 									title:	Title of the Columns
	 * 								}
	 * 								...
	 * 							]
	 *
	 * @param data
	 */
	getXls: function(title, definition, data)
	{
		var XLS = '<?xml version="1.0"?><ss:Workbook xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"><ss:Worksheet ss:Name="'+title+'"><ss:Table>';

		XLS += '<ss:Row>';

		// Header
		definition.each(function(def)
		{
			XLS += '<ss:Cell><ss:Data ss:Type="' + def.type + '">' + def.title + '</ss:Data></ss:Cell>';
		});

		XLS += '</ss:Row>';

		for (var i = 0; i < data.length; i++)
		{
			XLS += '<ss:Row>';

			definition.each(function(def)
			{
				var val = data[i][def.key];
				if (val == null) val = '';
				XLS += '<ss:Cell><ss:Data ss:Type="' + def.type + '">' + val + '</ss:Data></ss:Cell>';
			});

			XLS += '</ss:Row>';
		}

		XLS += '</ss:Table></ss:Worksheet></ss:Workbook>';

		return XLS;
	},


	download: function(title, definition, data)
	{
		var format = typeOf(arguments[3]) != 'null' ? arguments[3] : 'xls';

		switch(format)
		{
			case 'xls':
				data = this.getXls(title, definition, data);
				break;

			default:
				throw 'ION.DataExport : Error : Format not implemented';
		}

		if (data != null)
			this._download(title, data, 'xls');
		else
			throw 'ION.DataExport : Data is empty';
	},


	_download: function(title, data, extension)
	{
		var title = title.replace(/ /g,"_"),
			isWebkit = 'WebkitAppearance' in document.documentElement.style,
			isChrome = /Chrome/.test(navigator.userAgent) && /Google Inc/.test(navigator.vendor),
			isSafari = /Safari/.test(navigator.userAgent) && /Apple Computer/.test(navigator.vendor);

		// Initialize file format you want csv or xls
		var uri = this.mimes[extension] + encodeURIComponent(data);

		// this trick will generate a temp <a /> tag
		var link = document.createElement("a");
		link.href = uri;
		link.target = "_self";

		// set the visibility hidden so it will not effect on your web-layout
		link.style = "visibility:hidden";
		link.download = title + "." + extension;

		if ( isWebkit)
		{
			link.click();
		}
		else
		{
			// this part will append the anchor tag and remove it after automatic click
			document.body.appendChild(link);
			link.click();
			document.body.removeChild(link);
		}
	}
});


