
var ION=new Hash({baseUrl:base_url,adminUrl:admin_url,trees:new Hash(),updateLinkInfo:function(options)
{var type=options.type;var id=options.id;var text=options.text;var dl=$('link_info');if(dl){dl.empty();}
if(type!='')
{var url=admin_url+type+'/edit/'+id;if(type=='article')
{url=admin_url+type+'/edit/0.'+id;}
var a=new Element('a').set('text',text);var dt=new Element('dt',{'class':'small'});var label=new Element('label').set('text',Lang.get('ionize_label_linkto'));dt.adopt(label);dt.inject(dl,'top');var dd=new Element('dd').inject(dl,'bottom');var span=new Element('span',{'class':'link-img '+type}).inject(label,'bottom');if(type=='external')
{a.setProperty('href',text);a.setProperty('target','_blank');}
else
{a.removeEvent('click').addEvent('click',function(e)
{e.stop();MUI.updateContent({'element':$('mainPanel'),'loadMethod':'xhr','url':url,'title':Lang.get('ionize_title_edit_'+type)});});}
a.inject(dd,'bottom');}},removeLink:function()
{$('link').set('text','').setProperty('value','').fireEvent('change');$('link_type').value='';$('link_id').value='';$('link_info').empty();},initTreeTitle:function(el)
{var edit=el.getElement('.edit');var add_page=el.getElement('.add_page');var id_menu=add_page.getProperty('rel');edit.addEvent('click',function(e)
{e.stop();MUI.updateContent({element:$('mainPanel'),title:Lang.get('ionize_title_menu'),url:admin_url+'menu'});});add_page.addEvent('click',function(e)
{e.stop();MUI.updateContent({element:$('mainPanel'),title:Lang.get('ionize_title_new_page'),loadMethod:'xhr',url:admin_url+'page/create/'+id_menu});});},updateTreeArticles:function(args)
{var title=(args.title!='')?args.title:args.url;var id=args.id_article;var icon=(args.indexed&&args.indexed=='1')?'file':'sticky';var old_icon=(args.indexed&&args.indexed=='1')?'sticky':'file';var span=new Element('span',{'class':'flag flag'+args.flag});$$('.tree .article'+id+'.title').each(function(el)
{el.empty();el.set('html',args.title).setProperty('title',title);span.clone().inject(el,'top');});$$('.tree .article'+id+' '+'.'+old_icon).removeClass(old_icon).addClass(icon);$('mainPanel_title').set('text',Lang.get('ionize_title_edit_article')+' : '+title);},updateTreeLinkIcon:function(args)
{$$('.tree .file').removeClass('filelink');$$('.tree .folder').removeClass('folderlink');},updateArticleOrder:function(args)
{var articleContainer=$('articleContainer'+args.id_page);var articleList=$('articleList'+args.id_page);var order=(args.order).split(',');order=order.reverse();for(var i=0;i<order.length;i++)
{var el=articleContainer.getElement('#article_'+args.id_page+'x'+order[i]);el.inject(articleContainer,'top');if(articleList)
{var el=articleList.getElement('li.article'+args.id_page+'x'+order[i]);el.inject(articleList,'top');}}},updateTreePage:function(args)
{var title=(args.title!='')?args.title:args.url;var id=args.id_page;var id_parent=args.id_parent;var status=(args.online=='1')?'online':'offline';var home_page=(args.home&&args.home=='1')?true:false;var element=$('page_'+id);var id_tree_parent=element.getParent('ul').getProperty('rel');var id_tree=args.menu.name+'Tree';var parent=(id_parent!='0')?$('page_'+id_parent):$(id_tree);var id_container=(id_parent!='0')?'pageContainer'+id_parent:id_tree;var el_link='.title.page'+id;$$(el_link).set('text',title);element.removeClass('offline').removeClass('online').addClass(status);if(!(container=$(id_container)))
{container=new Element('ul',{'id':'page_Container'+id_parent});if(articleContainer=$('articleContainer'+id_parent))
{container.inject(articleContainer,'before');}
else
{container.inject($('page_'+id_parent),'bottom');}
if(!(parent.hasClass('f-open'))){container.setStyle('display','none');}}
if(id_tree_parent!=id_parent)
{var childs=container.getChildren();container.adopt(element);var pNbLines=parent.getChildren('.tree-img').length;var eNbLines=element.getChildren('.tree-img').length;var treeline=new Element('div',{'class':'tree-img line'});var lis=element.getElements('li');lis.push(element);lis.each(function(li)
{for(var i=0;i<eNbLines-2;i++){(li.getFirst()).dispose();}
for(var i=0;i<pNbLines-1;i++){treeline.clone().inject(li,'top');}});element.setProperty('rel',id);}
if(home_page==true)
{$$('.folder').removeClass('home');element.getFirst('.folder').addClass('home');}},linkArticleToPage:function(id_article,id_page,id_page_origin)
{new Request.JSON({url:admin_url+'article/link_to_page',method:'post',loadMethod:'xhr',data:{'id_article':id_article,'id_page':id_page,'id_page_origin':id_page_origin},onRequest:function()
{MUI.showSpinner();},onFailure:function(xhr)
{MUI.hideSpinner();MUI.notification('error',xhr.responseJSON);},onSuccess:function(responseJSON,responseText)
{MUI.hideSpinner();ION.execCallbacks(responseJSON.callback);MUI.notification.delay(50,MUI,new Array(responseJSON.message_type,responseJSON.message));}}).send();},addPageToArticleParentListDOM:function(args)
{if($('parent_list'))
{var li=new Element('li',{'rel':args.id_page+'.'+args.id_article,'class':'parent_page'});li.adopt(new Element('a',{'class':'icon right unlink'}));var title=(args.title!='')?args.title:args.name;var aPage=new Element('a',{'class':'page'}).set('text',title).inject(li,'bottom');var span=new Element('span',{'class':'link-img page left'}).inject(aPage,'top');ION.addParentPageEvents(li);$('parent_list').adopt(li);}},addArticleToPageArticleListDOM:function(args)
{if($('articleList'+args.id_page))
{var status=(options.online=='1')?' online ':' offline ';var rel=args.id_page+'.'+args.id_article;var flat_rel=args.id_page+'x'+args.id_article;var title=(args.title!='')?args.title:args.url;var li=new Element('li',{'rel':rel,'class':'sortme article'+args.id_article+' article'+flat_rel+' '+status});var aUnlink=new Element('a',{'rel':rel,'class':'icon right unlink','title':Lang.get('ionize_label_unlink')});ION.initArticleUnlinkEvent(aUnlink);li.adopt(aUnlink);var aStatus=new Element('a',{'rel':rel,'class':'icon right pr5 status article'+args.id_article+' article'+flat_rel+' '+status,'alt':Lang.get('ionize_label_unlink')});ION.initArticleStatusEvent(aStatus);li.adopt(aStatus);var spanFlag=new Element('span',{'class':'right mr20 ml20'});var nbLang=0;$each(args.langs,function(value,key)
{nbLang++;if(value.content!='')
{spanFlag.adopt(new Element('img',{'class':'left pl5 pt3',src:theme_url+'images/world_flags/flag_'+value['lang']+'.gif'}));}});spanFlag.setStyles({'width':25*nbLang+'px','height':'16px'});li.adopt(spanFlag);var spanType=new Element('span',{'class':'right ml20'}).set('html',args.types);ION.initArticleTypeEvent(spanType.getFirst('select'));li.adopt(spanType);var spanView=new Element('span',{'class':'right'}).set('html',args.views);ION.initArticleViewEvent(spanView.getFirst('select'));li.adopt(spanView);li.adopt(new Element('a',{'class':'icon left drag pr5'}));var a=new Element('a',{'class':'left pl5 article'+flat_rel+' '+status,'rel':rel}).set('text',title);var span=new Element('span',{'class':'flag flag'+args.flag});span.inject(a,'top');ION.makeLinkDraggable(a,'article');li.adopt(a);li.getElements('.title').addEvent('click',function(e){e.stop();MUI.updateContent({'element':$('mainPanel'),'loadMethod':'xhr','url':admin_url+'article/edit/'+args.id_article,'title':Lang.get('ionize_title_edit_article')+' : '+title});});$('articleList'+args.id_page).adopt(li);var sortables=$('articleList'+args.id_page).retrieve('sortables');sortables.addItems(li);}},updateSimpleItem:function(args)
{if($(args.type+'List'))
{var item=$(args.type+'List').getFirst('[rel='+args.rel+']');if(item)
{item.getFirst('.title').set('text',args.name);}
else
{ION.addSimpleItemToList(args);}}},addSimpleItemToList:function(args)
{if($(args.type+'List'))
{var title=args.name;var li=new Element('li',{'rel':args.rel,'class':'sortme '+args.type+args.rel});var aDelete=new Element('a',{'class':'icon delete right','rel':args.rel});ION.initItemDeleteEvent(aDelete,args.type);li.adopt(aDelete);li.adopt(new Element('a',{'class':'icon left drag pr5'}));var a=new Element('a',{'class':'title left pl5 '+args.type+args.rel,'rel':args.rel}).set('text',title);a.addEvent('click',function()
{MUI.formWindow(args.type+args.rel,args.type+'Form'+args.rel,Lang.get('ionize_title_'+args.type+'_edit'),args.type+'/edit/'+this.getProperty('rel'));});li.adopt(a);$(args.type+'List').adopt(li);var sortables=$(args.type+'List').retrieve('sortables');sortables.addItems(li);}},addParentPageEvents:function(item)
{var rel=(item.getProperty('rel')).split(".");var id_page=rel[0];var id_article=rel[1];var flat_rel=id_page+'x'+id_article;var edit_url=admin_url+'article/edit_context/'+id_page+'/'+id_article;var unlink_url=admin_url+'article/unlink/'+id_page+'/'+id_article;var titleInput=$('title_'+Lang.get('default')).value;var urlInput=$('url_'+Lang.get('default')).value;var articleTitle=(titleInput!='')?titleInput:urlInput;var a=item.getElement('a.page');a.addEvent('click',function(e){e.stop();MUI.updateContent({'element':$('mainPanel'),'loadMethod':'xhr','url':admin_url+'page/edit/'+id_page,'title':Lang.get('ionize_title_edit_page')});});var del=item.getElement('a.unlink');del.addEvent('click',function(e){e.stop();MUI.confirmation('confDelete'+id_page+id_article,unlink_url,Lang.get('ionize_confirm_article_page_unlink'));});},initArticleUnlinkEvent:function(item)
{var rel=(item.getProperty('rel')).split(".");var id_page=rel[0];var id_article=rel[1];var flat_rel=id_page+'x'+id_article;var url=admin_url+'article/unlink/'+id_page+'/'+id_article;item.removeEvents('click');item.addEvent('click',function(e)
{e.stop();MUI.confirmation('confDelete'+flat_rel,url,Lang.get('ionize_confirm_article_page_unlink'));});},initArticleStatusEvent:function(item)
{var rel=(item.getProperty('rel')).split(".");var id_page=rel[0];var id_article=rel[1];var url=admin_url+'article/switch_online/'+id_page+'/'+id_article;item.removeEvents('click');item.addEvent('click',function(e)
{e.stop();ION.switchArticleStatus(id_page,id_article);});},initArticleViewEvent:function(item)
{var rel=item.getAttribute('rel').split(".");if(item.value!='0'&&item.value!=''){item.addClass('a');}
item.removeEvents('change');item.addEvents({'change':function(e)
{e.stop();var url=admin_url+'article/save_context';this.removeClass('a');if(this.value!='0'&&this.value!=''){this.addClass('a');}
var data={'id_page':rel[0],'id_article':rel[1],'view':this.value};MUI.sendData(url,data);}});},initArticleTypeEvent:function(item)
{var rel=item.getAttribute('rel').split(".");if(item.value!='0'&&item.value!=''){item.addClass('a');}
item.removeEvents('change');item.addEvents({'change':function(e)
{e.stop();var url=admin_url+'article/save_context';this.removeClass('a');if(this.value!='0'&&this.value!=''){this.addClass('a');}
var data={'id_page':rel[0],'id_article':rel[1],'id_type':this.value};MUI.sendData(url,data);}});},initPageStatusEvent:function(item)
{var rel=(item.getProperty('rel')).split(".");var id_page=rel[0];var url=admin_url+'page/switch_online/'+id_page;item.removeEvents('click');item.addEvent('click',function(e)
{e.stop();ION.switchPageStatus(id_page);});},initItemDeleteEvent:function(item,type)
{var id=item.getProperty('rel');if(id)
{var callback=ION.itemDeleteConfirm.pass([type,id]);item.addEvent('click',function(e)
{e.stop();MUI.confirmation('del'+type+id,callback,Lang.get('ionize_confirm_element_delete'));});}},itemDeleteConfirm:function(type,id,parent,id_parent)
{MUI.showSpinner();var url=admin_url+type+'/delete/'+id;if(parent&&id_parent)
{url+='/'+parent+'/'+id_parent}
var xhr=new Request.JSON({url:url,method:'post',onSuccess:function(responseJSON,responseText)
{if(responseJSON.id)
{$$('.'+type+responseJSON.id).each(function(item,idx){item.dispose();});if(responseJSON.update!=null&&responseJSON.update!=''){MUI.updateElements(responseJSON.update);}
if($('id_'+type)&&$('id_'+type).value==id)
{MUI.updateContent({'element':$('mainPanel'),'loadMethod':'xhr','url':admin_url+'dashboard','title':Lang.get('ionize_title_welcome')});MUI.initToolbox();}}
MUI.notification(responseJSON.message_type,responseJSON.message);MUI.hideSpinner();}}).send();},makeLinkDraggable:function(el,type)
{el.makeCloneDraggable({droppables:['.droppable','.folder'],snap:10,opacity:0.8,classe:'ondrag',onSnap:function(el){el.addClass('move');},onDrop:function(element,droppable,event)
{if(!droppable){}
else
{if(droppable.id=='link')
{var rel=(element.getProperty('rel')).split(".");var id=rel[0];if(rel.length>1){id=rel[1];}
if($('element').value==type&&$('id_'+type).value==id)
{MUI.notification('error',Lang.get('ionize_message_no_circular_link'));}
else
{var check=(type=='article')?'file':'folder';var nbOccurences=($$('.tree .'+check+'[class*='+type+id+']')).length;if(nbOccurences>1)
{MUI.notification('error',Lang.get('ionize_message_target_link_not_unique'));}
else
{$('link_type').value=type;$('link_id').value=id;$('link').set('text',element.get('text')).setProperty('value',element.get('text'));ION.updateLinkInfo({'type':type,'id':id,'text':element.get('text')});droppable.fireEvent('change');}}}
if(droppable.id=='new_parent')
{if(type=='page')
{ION.linkArticleToPage($('id_article').value,element.getProperty('rel'),'0');}
else
{MUI.notification('error',Lang.get('ionize_message_drop_only_page'));}}
if(droppable.id=='new_article')
{var rel=(element.getProperty('rel')).split(".");var id_page_origin=rel[0];var id_article=rel[1];if(type=='article')
{ION.linkArticleToPage(id_article,$('id_page').value,id_page_origin);}
else
{MUI.notification('error',Lang.get('ionize_message_drop_only_article'));}}
if(droppable.hasClass('folder'))
{droppable.removeProperty('style');if(type=='article')
{var rel=(element.getProperty('rel')).split(".");var id_page_origin=rel[0];var id_article=rel[1];var id_page=droppable.getProperty('rel');ION.linkArticleToPage(id_article,id_page,id_page_origin);}
else
{MUI.notification('error',Lang.get('ionize_message_drop_only_article'));}}
droppable.removeClass('focus');}},onEnter:function(el,droppable)
{droppable.addClass('focus');},onLeave:function(el,droppable)
{droppable.removeClass('focus');}});},switchArticleStatus:function(id_page,id_article)
{MUI.showSpinner();var xhr=new Request.JSON({url:this.adminUrl+'article/switch_online/'+id_page+'/'+id_article,method:'post',onSuccess:function(responseJSON,responseText)
{if(responseJSON.message_type=='success')
{var args={'id_page':id_page,'id_article':id_article,'status':responseJSON.status}
ION.updateArticleStatus(args);if(responseJSON.update!=null&&responseJSON.update!='')
{MUI.updateElements(responseJSON.update);}}
MUI.notification.delay(50,this,new Array(responseJSON.message_type,responseJSON.message));MUI.hideSpinner();}.bind(this)}).send();},switchPageStatus:function(id_page)
{MUI.showSpinner();var xhr=new Request.JSON({url:this.adminUrl+'page/switch_online/'+id_page,method:'post',onSuccess:function(responseJSON,responseText)
{if(responseJSON.message_type=='success')
{var args={'id_page':id_page,'status':responseJSON.status}
ION.updatePageStatus(args);}
MUI.notification.delay(50,this,new Array(responseJSON.message_type,responseJSON.message));MUI.hideSpinner();}.bind(this)}).send();},updateArticleStatus:function(args)
{var elements=$$('.article'+args.id_page+'x'+args.id_article);if(args.status==1){elements.removeClass('offline').addClass('online');}
else
{elements.removeClass('online').addClass('offline');}},updatePageStatus:function(args)
{var elements=$$('.page'+args.id_page);var inputs=$$('.online'+args.id_page);inputs.each(function(item,idx)
{item.setProperty('value',args.status);});if(args.status==1){elements.removeClass('offline').addClass('online');}
else
{elements.removeClass('online').addClass('offline');}},unlinkArticleFromPageDOM:function(args)
{$$('li[rel='+args.id_page+'.'+args.id_article+']').each(function(item,idx){item.dispose();});},initDroppable:function()
{$$('.droppable').each(function(item,idx)
{new ION.Droppable(item);});},addTranslationTerm:function(parent)
{parent=$(parent);var childs=parent.getChildren('ul');var nb=childs.length+1;var clone=$('termModel').clone();var toggler=clone.getElement('.toggler');toggler.setProperty('rel',nb);var input=clone.getElement('input');input.setProperty('name','key_'+nb);var translation=clone.getElement('.translation');translation.setProperty('id','el_'+nb);var labels=clone.getElements('label');labels.each(function(label,idx)
{label.setProperty('for',label.getProperty('for')+nb);});var textareas=clone.getElements('textarea');textareas.each(function(textarea,idx)
{textarea.setProperty('name',textarea.getProperty('name')+nb);});clone.inject($('block'),'top').setStyle('display','block');input.focus();ION.initListToggler(toggler,translation);},initListToggler:function(toggler,child)
{toggler.fx=new Fx.Slide(child,{mode:'vertical',duration:200});toggler.fx.hide();toggler.addEvent('click',function()
{this.fx.toggle();this.toggleClass('expand');this.getParent('ul').toggleClass('highlight');});},initCorrectUrl:function(src,target)
{var src=$(src);var target=$(target);if(src&&target)
{src.addEvent('keyup',function(e)
{var text=ION.correctUrl(this.value);target.setProperty('value',text);});}},correctUrl:function(text)
{var text=text.toLowerCase();text=text.replace(/ /g,'-');text=text.replace(/\//g,'');text=text.replace(/\\/g,'');text=text.replace(/\(/g,'');text=text.replace(/\)/g,'');text=text.replace(/,/g,'');text=text.replace(/\./g,'');text=text.replace(/;/g,'');text=text.replace(/!/g,'');text=text.replace(/\?/g,'');text=text.replace(/%/g,'');text=text.replace(/$/g,'');text=text.replace(/"/g,'');text=text.replace(/'/g,'');text=text.replace(/&/g,'-');text=text.replace(/:/g,'-');text=text.replace(/\*/g,'');text=text.replace(/à/g,'a');text=text.replace(/ä/g,'a');text=text.replace(/â/g,'a');text=text.replace(/é/g,'e');text=text.replace(/è/g,'e');text=text.replace(/ë/g,'e');text=text.replace(/ê/g,'e');text=text.replace(/ï/g,'i');text=text.replace(/î/g,'i');text=text.replace(/ì/g,'i');text=text.replace(/ô/g,'o');text=text.replace(/ö/g,'o');text=text.replace(/ò/g,'o');text=text.replace(/ü/g,'u');text=text.replace(/û/g,'u');text=text.replace(/ù/g,'u');text=text.replace(/µ/g,'u');text=text.replace(/»/g,'');text=text.replace(/«/g,'');return text;},clearFormInput:function(args)
{$$('#'+args.form+' .inputtext').each(function(item,idx)
{item.setProperty('value','');item.set('text','');});$$('#'+args.form+' .inputcheckbox').each(function(item,idx)
{item.removeProperty('checked');});},execCallbacks:function(callback)
{if(callback)
{callbacks=new Array();if($type(callback)=='array'){callbacks=callback;}
else{callbacks.push(callback)}
callbacks.each(function(item,idx)
{var cb=(item.fn).split(".");var func=null;var obj=null;if(cb.length>1){obj=window[cb[0]];func=obj[cb[1]];}
else{func=window[cb];}
func.delay(100,obj,item.args);});}}});ION.options={mainpanel:'mainPanel',baseUrl:base_url,adminUrl:admin_url};ION.Tree=new Class({Implements:[Events,Options],options:ION.options,initialize:function(element,options)
{this.setOptions(options);this.element=element;var options=this.options;this.mainpanel=$(options.mainPanel);this.pageItemManagers=new Array();var opened=new Array();if(Cookie.read('tree'))opened=(Cookie.read('tree')).split(',');var folders=$$('#'+element+' li.folder');folders.each(function(folder,idx)
{var folderContents=folder.getChildren('ul');var homeClass=(folder.hasClass('home'))?' home':'';var folderImage=new Element('div',{'class':'tree-img drag folder'+homeClass}).inject(folder,'top');var addclass='';if(idx==0){addclass='first';}
else{if(!folder.getNext()){addclass='last';}}
var image=new Element('div',{'class':'tree-img plus '+addclass});image.addEvent('click',this.openclose).inject(folder,'top');if(opened.contains(folder.id))
{folder.addClass('f-open');image.removeClass('plus').addClass('minus');}
else
{folderContents.each(function(el){el.setStyle('display','none');});}
folderContents.each(function(element){var docs=element.getChildren('li.doc').extend(element.getChildren('li.sticky'));docs.each(function(el){if(el==docs.getLast()&&!el.getNext()){new Element('div',{'class':'tree-img line last'}).inject(el.getElement('span'),'before');}
else{new Element('div',{'class':'tree-img line node'}).inject(el.getElement('span'),'before');}});});this.addEditLink(folder,'page');this.addPageActionLinks(folder);ION.makeLinkDraggable(folder.getLast('span').getElement('a'),'page');}.bind(this));$$('#'+element+' li').each(function(node,idx)
{node.getParents('li').each(function(parent){if(parent.getNext()||!parent.hasClass('last')){new Element('div',{'class':'tree-img line'}).inject(node,'top');}
else{new Element('div',{'class':'tree-img line empty'}).inject(node,'top');}});var typeClass=(node.hasClass('doc'))?'file':'sticky';if(node.hasClass('file'))
{var link=node.getElement('span');new Element('div',{'class':'tree-img drag '+typeClass}).inject(link,'before');this.addEditLink(node,'article');ION.makeLinkDraggable(node.getLast('span').getElement('a'),'article');}
this.addMouseOver(node);}.bind(this));$$('#'+element+' li span.action').setStyle('display','none');this.pageItemManagers[element]=new ION.PageManager({container:element});$$('#'+element+' .pageContainer').each(function(item,idx){this.pageItemManagers[item.id]=new ION.PageManager({container:item.id});}.bind(this));$$('#'+element+' .articleContainer').each(function(item,idx){item.store('articleManager',new ION.ArticleManager({container:item.id,id_parent:item.getProperty('rel')}));}.bind(this));},openclose:function(evt)
{evt.stop();el=evt.target;var folder=el.getParent();var folderContents=folder.getChildren('ul');var folderIcon=el.getNext('.folder');if(folder.hasClass('f-open')){el.addClass('plus').removeClass('minus');folderIcon.removeClass('open');folderContents.each(function(ul){ul.setStyle('display','none');});folder.removeClass('f-open');ION.treeDelFromCookie(folder.getProperty('id'));}
else{el.addClass('minus').removeClass('plus');folderIcon.addClass('open');folderContents.each(function(ul){ul.setStyle('display','block');});folder.addClass('f-open');ION.treeAddToCookie(folder.getProperty('id'));}},addEditLink:function(el,type)
{var a=el.getLast('span').getElement('a');var rel=(a.getProperty('rel')).split(".");var id=rel[0];if(rel.length>1){id=rel[1];}
var p=$(this.options.mainpanel);a.addEvent('click',function(e)
{e.stop();MUI.updateContent({'element':p,'loadMethod':'xhr','url':admin_url+type+'/edit/'+a.getProperty('rel'),'title':Lang.get('ionize_title_edit_'+type)});});},addPageActionLinks:function(el)
{var a=el.getElement('a.addArticle');var id=a.rel;var p=$(this.options.mainpanel);a.addEvent('click',function(e)
{e.stop();MUI.updateContent({'element':p,'loadMethod':'xhr','url':admin_url+'article/create/'+id,'title':Lang.get('ionize_title_create_article')});});a=el.getElement('a.status');ION.initPageStatusEvent(el.getElement('a.status'));},addArticleActionLinks:function(el)
{ION.initArticleStatusEvent(el.getElement('a.status'));ION.initArticleUnlinkEvent(el.getElement('a.unlink'));},addMouseOver:function(node)
{node.addEvent('mouseover',function(ev){ev.stopPropagation();ev.stop();this.addClass('highlight');this.getParent().getParent().getChildren('.action').setStyle('display','none');this.getChildren('.action').setStyle('display','block');});node.addEvent('mouseout',function(ev){this.removeClass('highlight');});node.addEvent('mouseleave',function(e)
{this.getChildren('.action').setStyle('display','none');});},insertTreeArticle:function(options)
{var title=(options.title!='')?options.title:options.url;var page=$('page_'+options.id_page);var id=options.id_article;var id_page=options.id_page;var rel=id_page+'.'+id;var flat_rel=id_page+'x'+id;var status=(options.online=='1')?' online ':' offline ';var li=new Element('li',{'id':'article_'+flat_rel,'class':'file doc'+status+' article'+id+' article'+flat_rel,'rel':rel});var action=new Element('span',{'class':'action','styles':{'display':'none'}});var icon=new Element('span',{'class':'icon'});var link=new Element('span');var a=new Element('a',{'id':'al'+id,'class':'title '+status+' article'+id+' article'+flat_rel,'rel':rel,title:title}).set('text',title);var treeline=new Element('div',{'class':'tree-img'});var iconOnline=icon.clone().adopt(new Element('a',{'class':'status '+status+' article'+flat_rel,'rel':rel}));var iconUnlink=icon.clone().adopt(new Element('a',{'class':'unlink','rel':rel}));action.adopt(iconOnline,iconUnlink);this.addArticleActionLinks(action);ION.makeLinkDraggable(a,'article');var span=new Element('span',{'class':'flag flag'+options.flag});span.inject(a,'top');link.adopt(a);li.adopt(action,link);this.addEditLink(li,'article');var icon=treeline.clone().addClass('file drag');icon.inject(li,'top');var parent=$('page_'+id_page);var treeLines=$$('#page_'+id_page+' > .tree-img');var nodeLine=treeline.clone();nodeLine.addClass('line').addClass('node');if(container=$('articleContainer'+id_page))
{var lis=container.getChildren('li');if(options.ordering>lis.length)nodeLine.removeClass('node').addClass('last');nodeLine.inject(li,'top');for(var i=0;i<treeLines.length-1;i++){treeline.clone().inject(li,'top');}
if(options.ordering=='1')li.inject(container,'top');else li.inject(lis[options.ordering-2],'after');if(nodeBefore=li.getPrevious())
{nodeTree=nodeBefore.getChildren('.tree-img');nodeTree[nodeTree.length-2].removeClass('last').addClass('node');}}
else
{nodeLine.addClass('last');nodeLine.inject(li,'top');for(var i=0;i<treeLines.length-1;i++){treeline.clone().inject(li,'top');}
container=new Element('ul',{'id':'articleContainer'+id_page});container.adopt(li);container.inject(page,'bottom');container.store('articleManager',new ION.ArticleManager({container:'articleContainer'+id_page,id_parent:id_page}));if(!(parent.hasClass('f-open'))){container.setStyle('display','none');}}
var sortables=container.retrieve('sortables');sortables.addItems(li);this.addMouseOver(li);},insertTreePage:function(options)
{var title=(options.title!='')?options.title:options.url;var menu=$(options.menu.name+'Tree');var id=options.id_page;var id_parent=options.id_parent;var status=(options.online=='1')?' online ':' offline ';var home_page=(options.home&&options.home=='1')?true:false;var containerName=(id_parent!='0')?'pageContainer'+id_parent:options.menu.name+'Tree';var li=new Element('li',{'id':'page_'+id,'class':'folder page'+status+' page'+id,'rel':id});var action=new Element('span',{'class':'action','styles':{'display':'none'}});var icon=new Element('span',{'class':'icon'});var link=new Element('span');var a=new Element('a',{'id':'pl'+id,'class':status+' page'+id,'rel':id,title:title}).set('text',title);var treeline=new Element('div',{'class':'tree-img'});var iconOnline=icon.clone().adopt(new Element('a',{'class':'status '+status+' page'+id,'rel':id}));var iconArticle=icon.clone().adopt(new Element('a',{'class':'addArticle article','rel':id}));action.adopt(iconOnline,iconArticle);this.addPageActionLinks(action);link.adopt(a);li.adopt(action,link);this.addEditLink(li,'page');var icon=treeline.clone().addClass('folder').addClass('drag');if(home_page==true)
{$$('.folder.home').removeClass('home');icon.addClass('home');}
icon.inject(li,'top');var pm=treeline.clone().addClass('plus').addEvent('click',this.openclose.bind(this)).inject(li,'top');var parent=$('page_'+id_parent);var treeLines=$$('#page_'+id_parent+' > .tree-img');ION.makeLinkDraggable(a,'page');if(container=$(containerName))
{var lis=container.getChildren('li');for(var i=0;i<treeLines.length-1;i++){treeline.clone().inject(li,'top');}
li.inject(container,'bottom');if(nb=li.getPrevious()&&!container.getElement('articleContainer'+id_parent))
{nb=li.getPrevious()
nodeTree=nb.getChildren('.tree-img');nodeTree[nodeTree.length-2].removeClass('last').addClass('node');}}
else
{for(var i=0;i<treeLines.length-1;i++){treeline.clone().inject(li,'top');}
container=new Element('ul',{'id':containerName});container.adopt(li);container.inject(parent.getLast('span'),'after');if(!(parent.hasClass('f-open'))){container.setStyle('display','none');}
this.pageItemManagers[containerName]=new ION.PageManager({container:containerName});}
var sortables=container.retrieve('sortables');sortables.addItems(li);this.addMouseOver(li);}});ION.Droppable=new Class({Implements:[Events,Options],options:ION.options,initialize:function(element,options)
{this.setOptions(options);var options=this.options;if(element.hasClass('nofocus')==false)
{element.addEvents({'change':function(e)
{var alt=this.getProperty('alt');var value=this.getProperty('value');var text=this.get('text');if(value=='')
{this.addClass('empty').set('text',alt).setProperty('value',alt);}
else
{this.removeClass('empty');}},'click':function(e)
{var alt=this.getProperty('alt');var value=this.getProperty('value');if(value==alt)
{this.removeClass('empty').set('text','').setProperty('value','');}},'blur':function(e)
{this.fireEvent('change');}});element.fireEvent('change');}
else
{if(element.hasClass('empty')==true)
{var alt=element.getProperty('alt');element.set('text',alt).setProperty('value',alt);}
element.addEvents({'focus':function(e)
{this.blur();}});}}});ION.ItemManager=new Class({Implements:[Events,Options],options:ION.options,initialize:function(options)
{this.setOptions(options);this.container=$(this.options.container);this.baseUrl=this.options.baseUrl;this.adminUrl=this.options.adminUrl;this.element=this.options.element;if(options.parent_element&&options.id_parent&&options.parent_element!='')
{this.parent_element=options.parent_element;this.id_parent=options.id_parent;}
this.initDeleteEvent();},initDeleteEvent:function()
{var type=this.element;$$('#'+this.options.container+' .delete').each(function(item)
{ION.initItemDeleteEvent(item,type);});},makeSortable:function()
{if(this.container)
{var list=this.options.list;if(!list)list=this.options.container;this.sortables=new Sortables(list,{constrain:true,revert:true,handle:'.drag',referer:this,clone:true,opacity:0.5,onComplete:function(item)
{item.removeProperty('style');var serialized=this.serialize(0,function(element,index)
{var rel=(element.getProperty('rel')).split(".");var id=rel[0];if(rel.length>1){id=rel[1];}
return id;});this.options.referer.sortItemList(serialized);}});this.container.store('sortables',this.sortables);this.container.store('sortableOrder',this.sortables.serialize(0,function(element,index)
{var rel=(element.getProperty('rel')).split(".");var id=rel[0];if(rel.length>1){id=rel[1];}
return id;}.bind(this)));}},sortItemList:function(serialized)
{var sortableOrder=this.container.retrieve('sortableOrder');if(sortableOrder.toString()!=serialized.toString())
{this.container.store('sortableOrder',serialized);var url=this.adminUrl+this.element+'/save_ordering';if(this.parent_element&&this.id_parent)
{url+='/'+this.parent_element+'/'+this.id_parent}
var myAjax=new Request.JSON({url:url,method:'post',data:'order='+serialized,onSuccess:function(responseJSON,responseText)
{MUI.hideSpinner();if(responseJSON.update!=null&&responseJSON.update!='')
{MUI.updateElements(responseJSON.update);}
ION.execCallbacks(responseJSON.callback);if(responseJSON&&responseJSON.message_type)
{MUI.notification.delay(50,MUI,new Array(responseJSON.message_type,responseJSON.message));}},onFailure:this.failure.bind(this)}).post();}},failure:function(xhr)
{MUI.notification('error',xhr.responseText);MUI.hideSpinner();}});ION.ArticleManager=new Class({Extends:ION.ItemManager,initialize:function(options)
{this.parent({'element':'article','container':options.container,'parent_element':'page','id_parent':options.id_parent});this.initStatusEvents();this.initUnlinkEvents();this.makeSortable();},initStatusEvents:function()
{$$('#'+this.options.container+' .status').each(function(item,idx)
{ION.initArticleStatusEvent(item);});},initUnlinkEvents:function()
{$$('#'+this.options.container+' .unlink').each(function(item,idx)
{ION.initArticleUnlinkEvent(item);});}});ION.PageManager=new Class({Extends:ION.ItemManager,initialize:function(options)
{this.parent({'element':'page','container':options.container});this.makeSortable();},initStatusEvents:function()
{$$('#'+this.options.container+' .status').each(function(item,idx)
{ION.initPageStatusEvent(item);});}});var IonizeMediaManager=new Class({Implements:Options,options:{parent:false,idParent:false,mode:'',musicArray:Array('mp3'),pictureArray:Array('jpg','gif','png','jpeg'),videoArray:Array('flv','fv4'),fileArray:Array(),thumbSize:120},initialize:function(options)
{this.setOptions(options);this.baseUrl=this.options.baseUrl;this.adminUrl=this.options.adminUrl;this.themeUrl=theme_url;this.idParent=options.idParent;this.parent=options.parent;this.containers=new Hash({'picture':options.pictureContainer,'music':options.musicContainer,'video':options.videoContainer,'file':options.fileContainer});this.mode=options.mode;var self=this;$$(options.fileButton).each(function(item)
{item.addEvent('click',function(e)
{var e=new Event(e).stop();self.toggleFileManager();});});if($('filemanagerWindow'))
{var self=this;filemanager.removeEvents('complete');filemanager.setOptions({'onComplete':self.addMedia.bind(self)});}},addMedia:function(url)
{var extension=(url.substr(url.lastIndexOf('.')+1)).toLowerCase();var type=false;if(this.options.pictureArray.contains(extension)){type='picture';}
if(this.options.musicArray.contains(extension)){type='music';}
if(this.options.videoArray.contains(extension)){type='video';}
if(this.options.fileArray.contains(extension)){type='file';}
if(type==false)
{MUI.notification('error',Lang.get('ionize_message_media_not_authorized'));}
else
{var path=url.replace(/\//g,"~");var xhr=new Request.JSON({'url':this.adminUrl+'media/add_media/'+type+'/'+this.parent+'/'+this.idParent,'method':'post','data':'path='+path,'onSuccess':this.successAddMedia.bind(this),'onFailure':this.failure.bind(this)}).send();}},successAddMedia:function(responseJSON,responseText)
{MUI.notification(responseJSON.message_type,responseJSON.message);if(responseJSON.type)
{this.loadMediaList(responseJSON.type);}},loadMediaList:function(type)
{if(this.idParent)
{var myAjax=new Request.JSON({'url':this.adminUrl+'media/get_media_list/'+type+'/'+this.parent+'/'+this.idParent,'method':'get','onFailure':this.failure.bind(this),'onComplete':this.completeLoadMediaList.bind(this)}).send();}},completeLoadMediaList:function(responseJSON,responseText)
{MUI.hideSpinner();var container=$(this.containers.get(responseJSON.type));if(responseJSON&&responseJSON.content)
{container.set('html',responseJSON.content);sortableMedia=new Sortables(container,{revert:true,handle:'.drag',referer:this,clone:true,opacity:0.5,onComplete:function()
{var serialized=this.serialize(0,function(element,index)
{return element.getProperty('id').replace(responseJSON.type+'_','');});this.options.referer.sortItemList(responseJSON.type,serialized);}});container.store('sortableOrder',sortableMedia.serialize(0,function(element,index)
{return element.getProperty('id').replace(responseJSON.type+'_','');}));new Tips('#'+this.containers.get(responseJSON.type)+' .help',{'className':'tooltip'});}
else
{container.set('html',responseJSON.message);}},sortItemList:function(type,serialized)
{var container=$(this.containers.get(type))
var sortableOrder=container.retrieve('sortableOrder');if(sortableOrder.toString()!=serialized.toString())
{container.store('sortableOrder',serialized);var myAjax=new Request.JSON({url:this.adminUrl+'media/save_ordering/'+this.parent+'/'+this.idParent,method:'post',data:'order='+serialized,onSuccess:function(responseJSON,responseText)
{MUI.hideSpinner();MUI.notification(responseJSON.message_type,responseJSON.message);}}).post();}},failure:function(xhr)
{MUI.notification('error',xhr.responseText);MUI.hideSpinner();},detachMedia:function(type,id)
{MUI.showSpinner();var xhr=new Request.JSON({url:this.adminUrl+'media/detach_media/'+type+'/'+this.parent+'/'+this.idParent+'/'+id,method:'post',onSuccess:this.disposeMedia.bind(this),onFailure:this.failure.bind(this)}).send();},detachMediaByType:function(type)
{MUI.showSpinner();var xhr=new Request.JSON({url:this.adminUrl+'media/detach_media_by_type/'+this.parent+'/'+this.idParent+'/'+type,method:'post',onSuccess:function(responseJSON,responseText)
{$(this.containers.get(type)).empty();MUI.notification(responseJSON.message_type,responseJSON.message);MUI.hideSpinner();}.bind(this),onFailure:this.failure.bind(this)}).send();},disposeMedia:function(responseJSON,responseText)
{var el=responseJSON.type+'_'+responseJSON.id;if(responseJSON.id&&$(el))
{$(el).dispose();MUI.notification('success',responseJSON.message);}
else
{MUI.notification('error',responseJSON.message);}
MUI.hideSpinner();},initThumbs:function(id_picture)
{MUI.showSpinner();var myAjax=new Request.JSON({url:this.adminUrl+'media/init_thumbs/'+id_picture,method:'post',onSuccess:function(responseJSON,responseText)
{MUI.notification(responseJSON.message_type,responseJSON.message);if(responseJSON.message_type=='success')
{this.loadMediaList('picture');}}.bind(this)}).send();},initThumbsForParent:function()
{MUI.showSpinner();var myAjax=new Request.JSON({url:this.adminUrl+'media/init_thumbs_for_parent/'+this.parent+'/'+this.idParent,method:'get',onSuccess:function(responseJSON,responseText)
{MUI.notification(responseJSON.message_type,responseJSON.message);if(responseJSON.message_type=='success')
{this.loadMediaList('picture');}}.bind(this)}).send();},toggleFileManager:function()
{if(!this.idParent||this.idParent=='')
{MUI.notification('error',Lang.get('ionize_message_please_save_first'));}
else
{switch(this.mode)
{case'filemanager':mcImageManager.init({remove_script_host:false,iframe:false});mcImageManager.open('fileManagerForm','hiddenFile',false,this.addMedia.bind(this));break;case'ezfilemanager':var url=this.themeUrl+'javascript/tinymce/jscripts/tiny_mce/plugins/ezfilemanager/ezfilemanager.php?type=file&sa=1';var xPos=(window.screen.availWidth/2)-(w/2);var yPos=60;var config='width=750, height=450, left='+xPos+', top='+yPos+', toolbar=no, menubar=no, scrollbars=yes, resizable=yes, location=no, directories=no, status=no';var w=window.open(url,'filemanager',config);w.focus();break;case'kcfinder':var url=this.themeUrl+'javascript/kcfinder/browse.php?type=pictures&lng='+Lang['current'];+'&noselect=1&opener=custom';var xPos=(window.screen.availWidth/2)-(750/2);var yPos=window.screen.availHeight/4;var config='width=750, height=450, left='+xPos+', top='+yPos+', toolbar=no, menubar=no, scrollbars=yes, resizable=yes, location=no, directories=no, status=no';window.KCFinder={};window.KCFinder.media=this;window.KCFinder.callBack=function(url){window.KCFinder.media.addMedia(url);window.KCFinder=null;};var w=window.open(url,'kcfinder',config);w.focus();break;case'mootools-filemanager':if($('filemanagerWindow'))
{var inst=$('filemanagerWindow').retrieve('instance');if(inst.isMinimized)
{inst.restore();}
else
{$('filemanagerWindow').shake();}
return;}
var self=this;var baseUrl=this.baseUrl;var adminUrl=this.adminUrl;var themeUrl=this.themeUrl;var xhr=new Request.JSON({url:this.adminUrl+'media/get_tokken',method:'post',onSuccess:function(responseJSON,responseText)
{if(responseJSON&&responseJSON.tokken!='')
{filemanager=new FileManager({baseURL:baseUrl,url:adminUrl+'media/filemanager',assetBasePath:themeUrl+'javascript/mootools-filemanager/Assets',language:Lang.get('current'),selectable:true,hideOnClick:true,thumbSize:self.options.thumbSize,'onComplete':self.addMedia.bind(self),'uploadAuthData':responseJSON.tokken});filemanager.show();}
else
{MUI.notification('error',Lang.get('ionize_session_expired'));}}},self).send();break;default:MUI.notification('error','No mode set for mediaManager');}}}});ION.extend({reload:function(args)
{window.top.location=this.baseUrl+args.url;},openFilemanager:function(callback)
{var self=this;var xhr=new Request.JSON({url:this.adminUrl+'media/get_tokken',method:'post',onSuccess:function(responseJSON,responseText)
{if(responseJSON&&responseJSON.tokken!='')
{filemanager=new FileManager({baseURL:baseUrl,url:adminUrl+'media/filemanager',assetBasePath:baseUrl+'themes/admin/javascript/mootools-filemanager/Assets',language:Lang.get('current'),selectable:true,hideOnClick:true,onComplete:complete,'uploadAuthData':responseJSON.tokken});filemanager.show();}
else
{MUI.notification('error',Lang.get('ionize_session_expired'));}}},self).send();},treeAddToCookie:function(value)
{var opened=Array();if(Cookie.read('tree'))
opened=(Cookie.read('tree')).split(',');if(!opened.contains(value))
{opened.push(value);Cookie.write('tree',opened.join(','));}},treeDelFromCookie:function(value)
{var opened=Array();if(Cookie.read('tree'))
opened=(Cookie.read('tree')).split(',');if(opened.contains(value))
{opened.erase(value);Cookie.write('tree',opened.join(','));}},setOpenTabToCookie:function(group,id,prefix)
{if(prefix==null){prefix='';}
Cookie.write('tab',group+','+id+','+prefix);},diplayCookieTab:function()
{if(Cookie.read('tab'))
{tab=(Cookie.read('tab')).split(',');if(tab[0]&&tab[1])
{$('tab-'+tab[1]).fireEvent('click',this);}}},displayBlock:function(group,id,prefix)
{if(prefix==null)
{prefix='';}
if($('block'+prefix+'-'+id))
{$$(group).setStyle('display','none');$('block'+prefix+'-'+id).setStyle('display','block');element=$('tab'+prefix+'-'+id);element.getParent('ul').getChildren('li').each(function(el){el.removeClass('active');});element.addClass('active');}},displayLangBlock:function(group,lang,prefix)
{if(prefix==null)
{prefix='';}
if($('block'+prefix+'-'+lang))
{$$(group).setStyle('display','none');$('block'+prefix+'-'+lang).setStyle('display','block');element=$('tab'+prefix+'-'+lang);element.getParent('ul').getChildren('li').each(function(el){el.removeClass('active');});element.addClass('active');$('main-title').set('text',$('title_'+lang).value);}},editAreaSave:function(id,content)
{MUI.showSpinner();var id=id.replace('edit_','');var data='view='+$('view_'+id).value+'&path='+$('path_'+id).value+'&content='+content;new Request.JSON({url:admin_url+'setting/save_view',data:data,onSuccess:function(responseJSON,responseText)
{MUI.hideSpinner();MUI.notification(responseJSON.message_type,responseJSON.message);},onFailure:function(xhr)
{MUI.hideSpinner();MUI.notification('error',xhr.responseJSON);}}).send();},generateKey:function(size)
{var vowels='aeiouyAEIOUY';var consonants='bcdfghjklmnpqrstvwxzBCDFGHJKLMNPQRSTVWXZ1234567890@#$!()';var key='';var alt=Date.time()%2;for(var i=0;i<size;i++){if(alt==1){key+=consonants[(Number.rand()%(consonants.length))];alt=0;}else{key+=vowels[(Number.rand()%(vowels.length))];alt=1;}}
return key;},clearField:function(field)
{if($(field))
{$(field).value='';$(field).focus();}}});Number.extend({rand:function(min,max){var argc=arguments.length;if(argc===0){min=0;max=2147483647;}else if(argc===1){throw new Error('Warning: rand() expects exactly 2 parameters, 1 given');}
return Math.floor(Math.random()*(max-min+1))+min;}});Date.extend({time:function()
{return Math.floor(new Date().getTime()/1000);}});