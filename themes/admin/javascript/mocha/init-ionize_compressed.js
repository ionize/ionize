
var initializeColumns=function(){windowOptions=MUI.Windows.windowOptions;windowOptions.cornerRadius=0;MUI.Window.implement({options:windowOptions});new MUI.Column({id:'sideColumn',placement:'left',sortable:false,width:280,resizeLimit:[222,600]});new MUI.Column({id:'mainColumn',placement:'main',sortable:false,resizeLimit:[100,500],evalScripts:true});new MUI.Panel({id:'structurePanel',title:'',loadMethod:'xhr',contentURL:admin_url+'core/get_structure',column:'sideColumn',panelBackground:'#f2f2f2',padding:{top:15,right:0,bottom:8,left:15},headerToolbox:true,headerToolboxURL:admin_url+'core/get/toolboxes/structure_toolbox',headerToolboxOnload:function(){$('toggleHeaderButton').addEvent('click',function(e)
{e.stop();var cn='desktopHeader';var el=$(cn);var opened='true';if(Cookie.read(cn))
{opened=(Cookie.read(cn));}
if(opened=='false')
{Cookie.write(cn,'true');el.show();}
else
{Cookie.write(cn,'false');el.hide();}
window.fireEvent('resize');});var dh=$('desktopHeader');var opened=(Cookie.read('desktopHeader'));if(opened=='false'){dh.hide();}
else{dh.show();}
window.fireEvent('resize');}});new MUI.Panel({id:'mainPanel',title:Lang.get('ionize_title_welcome'),loadMethod:'xhr',contentURL:admin_url+'dashboard',padding:{top:15,right:15,bottom:8,left:15},addClass:'pad-maincolumn',column:'mainColumn',collapsible:false,panelBackground:'#fff',headerToolbox:true,headerToolboxURL:admin_url+'core/get/toolboxes/empty_toolbox'});MUI.myChain.callChain();}
var initializeWindows=function(){MUI.hideSpinner=function()
{if($('spinner'))$('spinner').hide();}
MUI.showSpinner=function()
{if($('spinner'))$('spinner').show();}
MUI.notification=function(type,message)
{new MUI.Window({loadMethod:'html',closeAfter:2500,type:'notification',addClass:'notification ',content:'<div class="'+type+'">'+message+'</div>',width:350,height:50,y:1,padding:{top:15,right:12,bottom:10,left:12},shadowBlur:5,bodyBgColor:[250,250,250],contentBgColor:'#e5e5e5'});}
MUI.addConfirmation=function(id,button,callback,msg,options)
{$(button).addEvent('click',function(e)
{var e=new Event(e).stop();MUI.confirmation(id,callback,msg,options);});}
MUI.confirmation=function(id,callback,msg,wOptions)
{wButtons=MUI._getConfirmationButtons(id,callback);var wMsg=(Lang.get(msg))?Lang.get(msg):msg;var wMessage=new Element('div',{'class':'message'}).set('text',wMsg);var wContent=new Element('div').adopt(wMessage,wButtons);var options={id:'w'+id,content:wContent,title:Lang.get('ionize_modal_confirmation_title'),addClass:'confirmation',draggable:true,y:150,padding:{top:15,right:15,bottom:8,left:15}}
if(wOptions){$extend(options,wOptions);}
new MUI.Modal(options);}
MUI.error=function(msg,wOptions)
{var options=MUI._getModalOptions('error',msg);new MUI.Modal(options);}
MUI.alert=function(msg,wOptions)
{var options=MUI._getModalOptions('alert',msg);new MUI.Modal(options);}
MUI.information=function(msg,wOptions)
{var options=MUI._getModalOptions('information',msg);new MUI.Modal(options);}
MUI.formWindow=function(id,form,title,wUrl,wOptions)
{wUrl=MUI.cleanUrl(wUrl);var options={id:'w'+id,title:(Lang.get(title)==null)?title:Lang.get(title),loadMethod:'xhr',contentURL:admin_url+wUrl,onContentLoaded:function(c)
{var formUrl=$(form).getProperty('action')+'/true';MUI.setFormSubmit(form,('bSave'+id),formUrl);if(bCancel=$('bCancel'+id))
{bCancel.addEvent('click',function(e)
{var e=new Event(e).stop();MUI.closeWindow($('w'+id));});}
if(bSave=$('bSave'+id))
{bSave.addEvent('click',function(e)
{var e=new Event(e).stop();MUI.closeWindow($('w'+id));});}
if(wOptions.resize==true)
{var s=$('w'+id+'_content').getSize();$('w'+id).retrieve('instance').resize({height:s.y+10,width:s.x,centered:true});}},y:80,padding:{top:12,right:12,bottom:10,left:12},maximizable:false,contentBgColor:'#fff'};if(wOptions){$extend(options,wOptions);}
new MUI.Window(options);}
MUI.dataWindow=function(id,title,wUrl,wOptions)
{wUrl=MUI.cleanUrl(wUrl);var options={id:'w'+id,title:(Lang.get(title)==null)?title:Lang.get(title),loadMethod:'xhr',contentURL:admin_url+wUrl,evalResponse:true,width:310,height:130,y:80,padding:{top:12,right:12,bottom:10,left:12},maximizable:false,contentBgColor:'#fff'};if(wOptions){$extend(options,wOptions);}
return new MUI.Window(options);}
MUI._getModalOptions=function(type,msg)
{var wMsg=(Lang.get(msg))?Lang.get(msg):msg;var btnOk=new Element('button',{'class':'button yes right mr35'}).set('text',Lang.get('ionize_button_ok'));var button=new Element('div',{'class':'buttons'}).adopt(btnOk);var wMessage=new Element('div',{'class':'message'}).set('text',wMsg);var wContent=new Element('div').adopt(wMessage,button);var id=new Date().getTime();var options={id:'w'+id,content:wContent,title:Lang.get('ionize_modal_'+type+'_title'),addClass:type,draggable:true,y:150,padding:{top:15,right:15,bottom:8,left:15}}
btnOk.addEvent('click',function()
{MUI.closeWindow($('w'+id));}.bind(this));return options;}
MUI._getConfirmationButtons=function(id,callback)
{var btnYes=new Element('button',{'class':'button yes right mr35'}).set('text',Lang.get('ionize_button_confirm'));var btnNo=new Element('button',{'class':'button no '}).set('text',Lang.get('ionize_button_cancel'));btnNo.addEvent('click',function()
{MUI.closeWindow($('w'+id));}.bind(this));btnYes.addEvent('click',function()
{if((callback+'').indexOf('/')>-1&&(callback+'').indexOf('/')<6)
{MUI.sendForm(callback);}
else
{callback();}
MUI.closeWindow($('w'+id));}.bind(this));return new Element('div',{'class':'buttons'}).adopt(btnYes,btnNo)}
MUI.myChain.callChain();}
var initializeMenu=function(){var default_padding={top:12,right:15,bottom:8,left:15};if($('dashboardLink')){$('dashboardLink').addEvent('click',function(e){new Event(e).stop();MUI.updateContent({element:$('mainPanel'),title:Lang.get('ionize_title_welcome'),url:admin_url+'dashboard'});});}
$('logoAnchor').addEvent('click',function(e){$('dashboardLink').fireEvent('click',e);});if($('menuLink')){$('menuLink').addEvent('click',function(e){new Event(e).stop();MUI.updateContent({element:$('mainPanel'),title:Lang.get('ionize_title_menu'),url:admin_url+'menu'});});}
if($('newPageLink')){$('newPageLink').addEvent('click',function(e){new Event(e).stop();MUI.updateContent({element:$('mainPanel'),title:Lang.get('ionize_title_new_page'),url:admin_url+'page/create/0'});});}
if($('articlesLink')){$('articlesLink').addEvent('click',function(e){new Event(e).stop();MUI.updateContent({element:$('mainPanel'),title:Lang.get('ionize_title_articles'),url:admin_url+'article/list_articles'});});}
if($('translationLink')){$('translationLink').addEvent('click',function(e){new Event(e).stop();MUI.updateContent({element:$('mainPanel'),title:Lang.get('ionize_title_translation'),url:admin_url+'translation/'});});}
if($('mediaManagerLink')){$('mediaManagerLink').addEvent('click',function(e){new Event(e).stop();MUI.updateContent({element:$('mainPanel'),title:Lang.get('ionize_menu_media_manager'),url:admin_url+'media/get_media_manager',padding:{top:0,left:0,right:0}});});}
if($('extendfieldsLink')){$('extendfieldsLink').addEvent('click',function(e){new Event(e).stop();MUI.updateContent({element:$('mainPanel'),title:Lang.get('ionize_menu_extend_fields'),url:admin_url+'extend_field/index'});});}
if($('modulesLink')){$('modulesLink').addEvent('click',function(e){new Event(e).stop();MUI.updateContent({element:$('mainPanel'),title:Lang.get('ionize_title_modules'),url:admin_url+'modules/'});});}
if($('themesLink')){$('themesLink').addEvent('click',function(e){new Event(e).stop();MUI.updateContent({element:$('mainPanel'),title:Lang.get('ionize_title_theme'),url:admin_url+'setting/themes/'});});}
if($('ionizeSettingLink')){$('ionizeSettingLink').addEvent('click',function(e){new Event(e).stop();MUI.updateContent({element:$('mainPanel'),title:Lang.get('ionize_menu_ionize_settings'),url:admin_url+'setting/ionize'});});}
if($('settingLink')){$('settingLink').addEvent('click',function(e){new Event(e).stop();MUI.updateContent({element:$('mainPanel'),title:Lang.get('ionize_menu_site_settings_global'),url:admin_url+'setting'});});}
if($('technicalSettingLink')){$('technicalSettingLink').addEvent('click',function(e){new Event(e).stop();MUI.updateContent({element:$('mainPanel'),title:Lang.get('ionize_menu_site_settings_technical'),url:admin_url+'setting/technical'});});}
if($('languagesLink')){$('languagesLink').addEvent('click',function(e){new Event(e).stop();MUI.updateContent({element:$('mainPanel'),title:Lang.get('ionize_menu_languages'),url:admin_url+'lang'});});}
if($('usersLink')){$('usersLink').addEvent('click',function(e){new Event(e).stop();MUI.updateContent({element:$('mainPanel'),title:Lang.get('ionize_menu_users'),url:admin_url+'users'});});}
MUI.aboutWindow=function(){new MUI.Modal({id:'about',title:'MUI',contentURL:admin_url+'desktop/get/about',type:'modal2',width:360,height:210,y:200,padding:{top:70,right:12,bottom:10,left:22},scrollbars:false});}
if($('aboutLink')){$('aboutLink').addEvent('click',function(e){new Event(e).stop();MUI.aboutWindow();});}
$$('a.returnFalse').each(function(el){el.addEvent('click',function(e){new Event(e).stop();});});MUI.myChain.callChain();}
var initializeForms=function()
{MUI.getFormObject=function(url,data)
{if(!data){data='';}
url=MUI.cleanUrl(url);return{url:admin_url+url,method:'post',loadMethod:'xhr',data:data,onRequest:function()
{MUI.showSpinner();},onFailure:function(xhr)
{MUI.hideSpinner();MUI.notification('error',xhr.responseJSON);},onSuccess:function(responseJSON,responseText)
{MUI.hideSpinner();if(responseJSON&&responseJSON.update)
{MUI.updateElements(responseJSON.update);}
if(responseJSON&&responseJSON.callback)
{callbacks=new Array();if($type(responseJSON.callback)=='array'){callbacks=responseJSON.callback;}
else{callbacks.push(responseJSON.callback)}
callbacks.each(function(item,idx)
{var cb=(item.fn).split(".");var func=null;var obj=null;if(cb.length>1){obj=window[cb[0]];func=obj[cb[1]];}
else{func=window[cb];}
func.delay(100,obj,item.args);});}
if(responseJSON&&responseJSON.message_type)
{MUI.notification.delay(50,MUI,new Array(responseJSON.message_type,responseJSON.message));}}};}
MUI.sendForm=function(url)
{new Request.JSON(MUI.getFormObject(url)).send();}
MUI.sendData=function(url,data)
{new Request.JSON(MUI.getFormObject(url,data)).send();}
MUI.setFormSubmit=function(form,button,url,confirm)
{if($(button)&&($type(confirm)=='object'))
{var func=function()
{MUI.showSpinner();var options=MUI.getFormObject(url,$(form));var r=new Request.JSON(options);r.send();};$(button).addEvent('click',function(e)
{new Event(e).stop();MUI.confirmation('conf'+button.id,func,confirm.message);});}
else if($(button))
{$(button).addEvent('click',function(e)
{new Event(e).stop();MUI.showSpinner();if(typeof tinyMCE!="undefined")
tinyMCE.triggerSave();if(typeof CKEDITOR!="undefined")
{for(instance in CKEDITOR.instances)
CKEDITOR.instances[instance].updateElement();}
var options=MUI.getFormObject(url,$(form));var r=new Request.JSON(options);r.send();});}}
MUI.addFormSaveEvent=function(button)
{if($(button))
{$(document).removeEvents('keydown');$(document).addEvent('keydown',function(event)
{if((event.control||event.meta)&&event.key=='s')
{event.stop();if($(button))
{$(button).fireEvent('click',event);}}});}}
MUI.myChain.callChain();}
var initializeContent=function()
{MUI.initToolbox=function(toolbox_url,onContentLoaded)
{if(!$('mainPanel_headerToolbox')){this.panelHeaderToolboxEl=new Element('div',{'id':'mainPanel_headerToolbox','class':'panel-header-toolbox'}).inject($('mainPanel_header'));}
if(toolbox_url)
{cb='';if(onContentLoaded)
{cb=onContentLoaded;}
MUI.updateContent({'element':$('mainPanel'),'childElement':$('mainPanel_headerToolbox'),'loadMethod':'xhr','url':admin_url+'core/get/toolboxes/'+toolbox_url});}
else
{$('mainPanel_headerToolbox').empty();}};MUI.initModuleToolbox=function(module,toolbox_url)
{if(!$('mainPanel_headerToolbox')){this.panelHeaderToolboxEl=new Element('div',{'id':'mainPanel_headerToolbox','class':'panel-header-toolbox'}).inject($('mainPanel_header'));}
if(toolbox_url)
{MUI.updateContent({'element':$('mainPanel'),'childElement':$('mainPanel_headerToolbox'),'loadMethod':'xhr','url':admin_url+'module/'+module+'/'+module+'/get/admin/toolboxes/'+toolbox_url});}
else
{$('mainPanel_headerToolbox').empty();}};MUI.initAccordion=function(togglers,elements)
{var acc=new Fx.Accordion(togglers,elements,{display:0,opacity:false,alwaysHide:true,initialDisplayFx:false,onActive:function(toggler,element){toggler.addClass('expand');},onBackground:function(toggler,element){toggler.removeClass('expand');}});};MUI.initSideColumn=function()
{var maincolumn=$('maincolumn');var element=$('sidecolumn');var button=$('sidecolumnSwitcher');if(button)
{button.addEvent('click',function(e)
{var e=new Event(e).stop();if(this.retrieve('status')=='close')
{element.removeClass('close');maincolumn.addClass('with-side');this.set('value',Lang.get('ionize_label_hide_options'));this.store('status','open');Cookie.write('sidecolumn','open');}
else
{element.addClass('close');maincolumn.removeClass('with-side');this.set('value',Lang.get('ionize_label_show_options'));this.store('status','close');Cookie.write('sidecolumn','close');}});var pos=Cookie.read('sidecolumn');if(pos!=null&&pos=='close')
{element.addClass('close');maincolumn.removeClass('with-side');button.set('value',Lang.get('ionize_label_show_options'));button.store('status','close');}
else
{element.removeClass('close');maincolumn.addClass('with-side');button.store('status','open');button.set('value',Lang.get('ionize_label_hide_options'));}}};MUI.updateElements=function(elements)
{$each(elements,function(options,key)
{MUI.updateElement(options);});};MUI.updateElement=function(options)
{options.url=admin_url+MUI.cleanUrl(options.url);if(!MUI.Windows.instances.get(options.element)&&!MUI.Panels.instances.get(options.element))
{new Request.HTML({'url':options.url,'update':$(options.element)}).send()}
else
{options.element=$(options.element);MUI.updateContent(options);}};MUI.cleanUrl=function(url)
{url=url.replace(admin_url,'');url=url.replace(admin_url.replace(Lang.get('current')+'/',''),'');return url;};MUI.initLabelHelpLinks=function(element)
{if(show_help_tips=='1')
{$$(element+' label').each(function(el,id)
{if(el.getProperty('title'))
{el.addClass('help');}});new Tips(element+' .help',{'className':'tooltip'});}};MUI.onContentLoaded=function()
{};MUI.myChain.callChain();}