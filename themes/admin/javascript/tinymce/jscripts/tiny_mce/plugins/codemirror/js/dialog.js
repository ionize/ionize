tinyMCEPopup.requireLangPack();

var CodeDialog = {
	init : function() 
	{
		var f = document.forms[0];
	
		f.code.value = tinyMCEPopup.editor.getContent();
		
		// MirrorFrame in /themes/admin/javascript/codemirror/codemirror.TinyMCE.js
		var mirrorFrame = new MirrorFrame(CodeMirror.replace(f.code), {
			height: "400px",
			width: "95%",
			content: f.code.value,
			tabMode: 'shift',
			parserfile: ['parsexml.js', 'parsecss.js', 'tokenizejavascript.js', 'parsejavascript.js', 'parsehtmlmixed.js', 'tokenizephp.js', 'parsephp.js', 'parsephphtmlmixed.js'],
			stylesheet: [
				'../../../../../codemirror/css/basic.css',
				'../../../../../codemirror/css/xmlcolors.css',
				'../../../../../codemirror/css/jscolors.css',
				'../../../../../codemirror/css/csscolors.css',
				'../../../../../codemirror/css/phpcolors.css'
			],
			path: '../../../../../codemirror/js/',
			reindentOnLoad: true, 
//			continuousScanning: 500,
			lineNumbers: true
		});
	},
	
	insert : function() {
		tinyMCEPopup.editor.setContent(codemirror.getCode());
		tinyMCEPopup.close();
	}
};

tinyMCEPopup.onInit.add(CodeDialog.init, CodeDialog);


