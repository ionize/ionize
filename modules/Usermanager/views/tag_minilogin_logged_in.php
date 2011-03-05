<div class="usermanager_minilogin_logged_in">
	<ion:usermanager request="user" attr="is_editor" from_user_field="1">
		<span><a href="<ion:usermanager request='global' attr='admin_url' />"><ion:translation term="module_usermanager_link_admin_interface" /></a></span>
		<span>&nbsp;&nbsp;|&nbsp;&nbsp;</span>
	</ion:usermanager>
	<span><a href="javascript:void(0);" onclick="javascript:document.minilogin.submit();"><ion:translation term="module_usermanager_link_logout" /></a></span>
	<span>&nbsp;&nbsp;|&nbsp;&nbsp;</span>
	<span><ion:translation term="module_usermanager_text_logged_in_as" /> <a href="<ion:usermanager request='global' attr='profile_url' />"><ion:usermanager request="user" attr="firstname" from_user_field="1" /> <ion:usermanager request="user" attr="screen_name" from_user_field="1" /></a></span>
	<form name="minilogin" action="<ion:usermanager request='global' attr='url' />" method="post"><input type="hidden" name="form_name" value="logout" /></form>
</div>
