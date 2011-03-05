<div class="usermanager_minilogin">
	<ion:usermanager request="user" attr="is_logged_in" is_like="0">
		<form action="<ion:usermanager request='global' attr='url' />" method="post">
			<span><a href="<ion:usermanager request='global' attr='register_url' />"><ion:translation term="module_usermanager_link_register" /></a></span>
			<span>&nbsp;&nbsp;|&nbsp;&nbsp;</span>
			<span><a href="<ion:usermanager request='global' attr='login_url' />"><ion:translation term="module_usermanager_link_login" /></a></span>
			<span>&nbsp;&nbsp;&nbsp;&nbsp;<ion:usermanager request="global" attr="login_field_label" />&nbsp;<input class="usermanager_input_text" name="<ion:usermanager request='global' attr='login_field_name' />" value="<ion:usermanager request='user' attr='username' from_post_data='minilogin' />" /></span>
			<span>&nbsp;&nbsp;&nbsp;&nbsp;<ion:translation term="module_usermanager_field_password" />&nbsp;<input class="usermanager_input_password" name="password" type="password" /></span>
			<input type="hidden" name="form_name" value="minilogin" />
		</form>
		<ion:usermanager request="form" attr="has_errors" form_name="minilogin">
			<div class="usermanager_error"><ion:usermanager request="form" attr="error_string" /></div>
		</ion:usermanager>
	</ion:usermanager>

	<ion:usermanager request="user" attr="is_logged_in" is_like="1">
		<ion:usermanager request="user" attr="is_editor" from_user_field="1">
			<span><a href="<ion:usermanager request='global' attr='admin_url' />"><ion:translation term="module_usermanager_link_admin_interface" /></a></span>
			<span>&nbsp;&nbsp;|&nbsp;&nbsp;</span>
		</ion:usermanager>
		<span><a href="javascript:void(0);" onclick="javascript:document.minilogin.submit();"><ion:translation term="module_usermanager_link_logout" /></a></span>
		<span>&nbsp;&nbsp;|&nbsp;&nbsp;</span>
		<span><ion:translation term="module_usermanager_text_logged_in_as" /> <a href="<ion:usermanager request='global' attr='profile_url' />"><ion:usermanager request="user" attr="firstname" from_user_field="1" /> <ion:usermanager request="user" attr="screen_name" from_user_field="1" /></a></span>
		<form name="minilogin" action="<ion:usermanager request='global' attr='url' />" method="post"><input type="hidden" name="form_name" value="logout" /></form>
	</ion:usermanager>

	<script type="text/javascript">
		<!--
			// Some browsers can't submit a form without
			// a submit button. This little script
			// helps those browsers.
			function addInputSubmitEvent(form, input) {
				input.onkeydown = function(e) {
					e = e || window.event;
					if (e.keyCode == 13) {
						form.submit();
						return false;
					}
				};
			}

			window.addEvents({
				domready: function() {
					var forms = document.getElementsByTagName('form');

					for (var i=0;i < forms.length;i++) {
						var inputs = forms[i].getElementsByTagName('input');

						for (var j=0;j < inputs.length;j++)
							addInputSubmitEvent(forms[i], inputs[j]);
					}
				}
			});
		-->
	</script>
</div>
