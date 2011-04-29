/*
 ---
 name: MUI-Controls

 script: mui-controls.js

 description: Root MochaUI Controls - Loads/Configures all MochaUI controls.

 copyright: (c) 2011 Contributors in (/AUTHORS.txt).

 license: MIT-style license in (/MIT-LICENSE.txt).

 note:
 This documentation is taken directly from the javascript source files. It is built using Natural Docs.
 ...
 */

Object.append(MUI.controls, {

	'accordion':{'samples':['root'],data:['json','html'],'description':'Accordion','isFormControl':false,'css':['{theme}css/accordion.css']},
	'column':{'samples':['root'],data:['json'],'description':'Column','isFormControl':false,'childNode':'panels','childType':'MUI.Panel','css':['{theme}css/desktop.css']},
	'desktop':{'samples':['root'],data:['json'],'description':'Desktop','isFormControl':false,'childNode':'content','childType':'MUI.DesktopColumns','dependsOn':['MUI.Dock','MUI.Taskbar'],'css':['{theme}css/desktop.css']},
	'desktopcolumns':{'hide':true,'loadOnly':true,'description':'used to map columns in MUI.Desktop content section','childNode':'columns','childType':'MUI.Column','js':[],'css':[]},
	'modal':{'samples':['root'],data:['json'],'description':'Modal','isFormControl':true,'css':['{theme}css/desktop.css'],location:'window'},
	'panel':{'samples':['root'],data:['json'],'description':'Panel','isFormControl':true,'childNode':'content','childType':'MUI.Panel','css':['{theme}css/desktop.css']},
	'taskbar':{'samples':['root'],data:['json'],'description':'Taskbar','isFormControl':false,'css':['{theme}css/taskbar.css']},
	'toolbar':{'samples':['root'],data:['json'],'description':'Toolbar','isFormControl':false,'css':['{theme}css/toolbar.css']},
	'dock':{'samples':['root'],data:['json'],'description':'Toolbar Dock','isFormControl':false,'childNode':'docked','dependsOn':['MUI.DockHtml'],'css':['{theme}css/toolbar.css']},
	'dockhtml':{'samples':['root'],data:['json'],'description':'Toolbar HTML Block','isFormControl':false,'css':['{theme}css/toolbar.css']},
	'menu':{'samples':['root'],data:['json'],'description':'Toolbar Menu','isFormControl':false,'css':['{theme}css/menu.css']},
	'spinner':{'samples':['root'],data:['json'],'description':'Toolbar Spinner','isFormControl':false,'css':['{theme}css/toolbar.css']},
	'window':{'samples':['root'],data:['json'],'description':'Modal','isFormControl':false,'childNode':'content','css':['{theme}css/window.css']}

});
