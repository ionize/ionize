ION.Login = new Class({

	Implements: [Options, Events],

	initialize: function()
	{},

	getInlineLoginWindow: function()
	{
		var xhr = typeOf(arguments[0]) != 'null' ? arguments[0] : null,
			divContent = new Element('div'),
			divButtons = new Element('div', {'class':'buttons'}).inject(divContent),
			btnLogin = new Element('a', {'class':'button yes right'}).set('text', Lang.get('ionize_login')).inject(divButtons)
			;

		var p = new Element('p', {'class':'mb5 lite', html:Lang.get('ionize_session_expired')}).inject(divContent),
			input_user = new Element('input', {'class':'inputtext w100p mb5', name:'username', placeholder:Lang.get('ionize_login_name'), type: 'text'}).inject(divContent),
			input_pass = new Element('input', {'class':'inputtext w100p mb5', name:'password', placeholder:Lang.get('ionize_login_password'), type: 'password'}).inject(divContent)
			;

		btnLogin.addEvent('click', function()
		{
			ION.JSON(
				ION.adminUrl + 'auth/xhr_login',
				{
					username:input_user.value,
					password:input_pass.value
				},
				{
					onSuccess: function(response)
					{
						var w = MUI.get('wLogin');

						if (response.message_type == 'error')
						{
							w.el.windowEl.shake();
						}
						else
						{
							w.close();
							// if (xhr != null) xhr.send();
						}
					}
				}
			)
		});

		new MUI.Modal({
			id: 'wLogin',
			content: divContent,
			title: Lang.get('ionize_label_denied_action_401'),
			// cssClass: type,
			draggable: true,
			y: 150,
			padding: { top: 15, right: 15, bottom: 8, left: 15 }
		});
	},

	getLoginWindow: function()
	{
		// @todo
	}
});


ION.UserManager = new Class({

	Implements: [Options, Events],

	/**
	 * Array of roles
	 */
	roles: [],

	/**
	 * List Container
	 * HTMLDomElement
	 */
	listContainer: null,

	/**
	 * List restrictions
	 * Object
	 */
	listRestrictions: {},

	/**
	 * List of fields to hide in list
	 */
	hideInList: [],

	/**
	 * Singleton
	 *
	 * @returns {*}
	 */
	initialize: function()
	{
		var self = this,
			options = typeOf(arguments[0]) != 'null' ? arguments[0] : {};

		this.url = ION.adminUrl + 'user/';

		// Container for User List
		if (options.listContainer) this.listContainer = options.listContainer;

		// Roles
		ION.JSON(
			ION.adminUrl + 'role/get_list2',
			{},
			{
				onSuccess: function(json)
				{
					self.roles = json;
					self.fireEvent('onLoad');
				}
			}
		);

		if (Object.getLength(options) > 0)
			this.init(options);
	},

	init: function(options)
	{
		this.listContainer = options.listContainer ?  options.listContainer : null;
		this.listRestrictions = options.listRestrictions ?  options.listRestrictions : {};
		this.hideInList = options.hideInList ?  options.hideInList : [];

	},

	setListContainer: function(container)
	{
		this.listContainer = container;
	},

	setListRestrictions: function(where)
	{
		this.listRestrictions = where;
	},

	/**
	 *
	 * @param options	{
	 * 						post: {key:value}
	 * 					}
	 *
	 */
	create: function()
	{
		var options = typeOf(arguments[0]) != 'null' ? arguments[0] : {};

		// Blank user
		var user = {
			id_user: ''
		};

		this.edit(user, options);
	},


	/**
	 *
	 * @param user
	 * @param options	{
	 * 						post: {
	 * 							id_role: xx
	 * 						},
	 * 						onSuccess: function(user) {}
	 * 					}
	 */
	edit: function(user)
	{
		var self = this,
			id = user.id_user,
			options = typeOf(arguments[1]) != 'null' ? arguments[1] : {},
			autoPost = options.autoPost ? options.autoPost : [],
			post = typeOf(options.post) != 'null' ? options.post : {},
			subtitle = []
		;

		if (autoPost.contains('id_role'))
			post.id_role = user.id_role;

		if (post.id_role)
		{
			var role = this.getRoleFromId(post.id_role);
			subtitle.push({key:Lang.get('ionize_label_role'), value:role.role_name});
		}

		new ION.Window(
		{
			type: 'form',
			form: {},
			width: 500,
			height: 440,
			id: 'wUser' + id,
			title: {
				text: id == '' ? Lang.get('ionize_title_add_user') : Lang.get('ionize_title_user_edit'),
				'class': 'user'
			},
			subtitle: subtitle,
			onDraw: function(w)
			{
				// FORM
				var form = w.getForm();

				// Form Options
				options = Object.merge(options, {
					notify: true
				});

				// Get Form content (without form)
				var content = self._getUserForm(user, options, post);

				form.adopt(content);

				// Must be done here, after DOM injection
				ION.initAccordion('.toggler-user' + id, 'div.element-user' + id);
				ION.initFormAutoGrow(form);

				// Add Validator
				self._addUserFormValidator(form, user);

				// Save
				var saveButton = w.getSaveButton();

				// Save Form
				saveButton.removeEvents().addEvent('click', function(e)
				{
					e.stop();

					var validator = form.retrieve('validator');

					if (validator && ! validator.validate())
					{
						new ION.Notify(form, {type:'error'}).show('ionize_message_form_validation_please_correct');
					}
					else
					{
						// Transform Form to JSON
						var data = w.getForm().toJSON();

						ION.JSON(
							self.url + 'save',
							data,
							{
								onSuccess: function(json)
								{
									if (options.onSuccess)
										options.onSuccess(json);
									else
										self.getList(options);

									w.close();
								}
							}
						)
					}
				});
			}
		});
	},


	/**
	 * Returns the current user's Edition Form
	 * Used by the current user, to edit his own data from the backend
	 *
	 */
	getCurrentUserEditForm: function(container)
	{
		var self = this,
			user = ION.User.getUser(),
			options = typeOf(arguments[1]) != 'null' ? arguments[1] : {},
			form = new Element('form', {'class':'p20'}).inject(container),
			userContent = this._getUserForm(user, {autoPost:['id_role']}).inject(form),
			ff_btn_save = new ION.FormField({container: userContent, 'class':'small'})
		;

		new ION.Button({
			container: ff_btn_save.getContainer(),
			title: Lang.get('ionize_button_save'),
			'class': 'green',
			onClick: function()
			{
				var validator = form.retrieve('validator');

				if (validator && ! validator.validate())
				{
					new ION.Notify(form, {type:'error'}).show('ionize_message_form_validation_please_correct');
				}
				else
				{
					ION.JSON(
						self.url + 'save',
						form,
						{
							onSuccess: function(json)
							{
								ION.User.getLoggedUser({
									onUserLoaded: function()
									{
										if (options.onSuccess)
											options.onSuccess(json);
									}
								});
							}
						}
					)
				}
			}
		});

		ION.initAccordion('.toggler-user' + user.id_user, 'div.element-user' + user.id_user);

		// Add Validator
		self._addUserFormValidator(form, user);
	},


	delete: function(user)
	{
		var self = this,
			options = typeOf(arguments[1]) != 'null' ? arguments[1] :{},
			id_user = typeOf(user) == 'object' ? user.id_user : user;

		ION.confirmation(
			'wDeleteUser',
			function()
			{
				ION.JSON(
					self.url + 'delete',
					{id_user:id_user},
					{
						onSuccess: function()
						{
							if (options.onSuccess)
								options.onSuccess();
							else
								self.getList();
						}
					}
				);
			},
			Lang.get('ionize_confirm_user_delete')
		);
	},


	editFromEmail: function(options)
	{
		var self = this,
			post = typeOf(options.post) != 'null' ? options.post : {}
		;

		new ION.Window(
		{
			type: 'form',
			form: {},
			width: 500,
			height: 220,
			id: 'wUserFromEmail',
			title: {
				text: Lang.get('ionize_title_add_user'),
				'class': 'user'
			},
			subtitle: {html:Lang.get('ionize_label_add_user_from_email')},
			onDraw: function(w)
			{
				// FORM
				var form = w.getForm();

				// Form Content
				var ff_email = new ION.FormField({container: form, 'class':'small', label: {text: Lang.get('ionize_label_email')}}),
					input_email = new Element('input', {'class':'inputtext required w95p', name:'email', type: 'text', value:''}).inject(ff_email.getContainer()),

					// Firstname
					ff_firstname = new ION.FormField({container: form, 'class':'small', label: {text: Lang.get('ionize_label_firstname')}}),
					input_firstname = new Element('input', {'class':'inputtext w95p', disabled:'disabled', name:'firstname', type: 'text', value:''}).inject(ff_firstname.getContainer()),

					// Lastname
					ff_lastname = new ION.FormField({container: form, 'class':'small', label: {text: Lang.get('ionize_label_lastname')}}),
					input_lastname = new Element('input', {'class':'inputtext w95p', disabled:'disabled', name:'lastname', type: 'text', value:''}).inject(ff_lastname.getContainer())
				;

				// Hidden Fields
				var input_id = new Element('input', {name:'id_user', type: 'hidden', value:''}).inject(form, 'top');

				Object.each(post, function(value, key)
				{
					new Element('input', {name:key, type: 'hidden', value:value}).inject(form, 'top');
				});


				new Autocompleter.Request.HTML(
					input_email,
					self.url + 'search_email',
					{
						'postVar': 'search',
						'indicatorClass': 'autocompleter-loading',
						minLength: 2,
						maxChoices: 10,
						zIndex: 200000,
						relative: true,
						'injectChoice': function(choice)
						{
							input_id.set('value', '');

							var text = choice.getFirst();
							var value = text.innerHTML;
							choice.inputValue = value;
							text.set('html', this.markQueryValue(value));
							this.addChoiceEvents(choice);
						},
						onSelect: function(selection, item, value, input)
						{
							input_id.set('value', item.getProperty('data-id'));
							input_firstname.set('value', item.getProperty('data-firstname'));
							input_lastname.set('value', item.getProperty('data-lastname'));
						}
					}
				);

				// Save
				var saveButton = w.getSaveButton();

				// Save Form
				saveButton.removeEvents().addEvent('click', function(e)
				{
					e.stop();

					var validator = form.retrieve('validator');

					if (validator && ! validator.validate())
					{
						new ION.Notify(form, {type:'error'}).show('ionize_message_form_validation_please_correct');
					}
					else
					{
						if (input_id.value !='')
						{
							// Transform Form to JSON
							var data = w.getForm().toJSON();

							ION.JSON(
								self.url + 'save',
								data,
								{
									onSuccess: function()
									{
										self.getList(options);
										w.close();
									}
								}
							);
						}
					}
				});
			}
		});
	},

	/**
	 *
	 * @param options	{
	 * 						container: HTMLDomElement,
	 * 						onSuccess: function(),
	 *						page: int,
	 *						filter: {}
	 * 					}
	 */
	getList: function()
	{
		var self = this,
			options = typeOf(arguments[0]) != 'null' ? arguments[0] : {},
			filter = typeOf(options.filter) != 'null' ? options.filter : null,
			onSuccess = typeOf(options.onSuccess) == 'function' ? options.onSuccess : null,
			post = {
				page: typeOf(options.page) != 'null' ? options.page : 1,
				filter: filter,
				where: this.listRestrictions
			}
		;

		ION.JSON(
			ION.adminUrl + 'user/get_pagination_list',
			post,
			{
				onSuccess: function(json)
				{
					if (onSuccess)
						onSuccess(json);
					else if (self.listContainer != null)
					{

						self._displayUserList(json, options);
					}
				}
			}
		);
	},

	/**
	 *
	 * @param json
	 * @param options	{
	 * 						container: HTMLDomElement,
	 *						page: int,
	 *						filter: {},
	 *						where: {},
	 *						onDelete: function(user)
 	 *						onEdit: function(user)
	 * 					}
	 */
	_displayUserList: function(json, options)
	{
		var self = this,
			options = typeOf(options) != 'null' ? options : {}
		;

		if (this.listContainer)
		{
			this.listContainer.empty();

			var columns = [
				// ID
				{key: 'id_user', 'class': 'lite edit'}
			];

			// Edit icon
			if ( ! this.hideInList.contains('edit_icon'))
			{
				columns.push({
					onCellDraw:function(td)
					{
						td.empty();
						var a = new Element('a', {'class':'icon edit'}).inject(td);
					}
				});
			}

			// Write Email icon
			if ( ! this.hideInList.contains('email_icon'))
			{
				columns.push({
					onCellDraw:function(td, item)
					{
						td.empty();
						var a = new Element('a', {'class':'icon mail'}).inject(td);
						a.addEvent('click', function()
						{
							location.href="mailto:" + item.email;
						});
					}
				});
			}

			// Login
			columns.push({key: 'username', label:'Login','class': 'edit'});

			// Email
			columns.push({key: 'email', label:'Email','class': 'edit'});

			// Firstname
			if ( ! this.hideInList.contains('firstname'))
				columns.push({key: 'firstname', label: Lang.get('ionize_label_firstname'),'class': 'edit'});

			// Lastname
			if ( ! this.hideInList.contains('lastname'))
				columns.push({key: 'lastname', label: Lang.get('ionize_label_lastname'),'class': 'edit'});

			// Role name
			if ( ! this.hideInList.contains('role_name'))
				columns.push({key: 'role_name', label: Lang.get('ionize_label_role_name'),'class': 'edit'});

			// Join Date
			if ( ! this.hideInList.contains('join_date'))
				columns.push({key: 'join_date', label: Lang.get('ionize_label_join_date'),'class': 'edit'});

			// Delete icon
			if ( ! this.hideInList.contains('delete_icon'))
			{
				columns.push({
					onCellDraw:function(td, item)
					{
						td.empty();
						var a = new Element('a', {'class':'icon delete'}).inject(td);
						a.addEvent('click', function()
						{
							var delOptions = {};

							if (options.onDelete)
								delOptions.onSuccess = options.onDelete;

							self.delete(item, delOptions);
						})
					}
				});
			}

			// Table
			new ION.TableList({
				container: this.listContainer,
				items: json.items,
				buildIfEmpty: true,
				sortable: true,
				columns: columns,
				onItemDraw: function(tr, item)
				{
					// Edit
					tr.getElements('.edit').addEvent('click', function()
					{
						self.edit(item, options);
					});
				},
				filter: {
					keys : ['username','email', 'screen_name', 'firstname', 'lastname'],
					filters: json.filter,
					onFilter: function(filter)
					{
						options.filter = filter;
						self.getList(options);
					}
				},
				pagination: {
					nb: json.nb,						// Nb total (results or results filtered)
					nb_by_page: json.nb_by_page,		// Nb results by page
					current_page: json.page,			// Current page number
					onClick: function(page_nb)
					{
						options.page = page_nb;
						self.getList(options);
					}
				}
			});
		}
	},

	/**
	 *
	 * @param user
	 * @param post		{key:value}
	 *
	 * @returns {HTMLElement}
	 *
	 */
	_getUserForm: function(user, options)
	{
		var self = this,
			container = new Element('div'),
			id = user.id_user,
			post = typeOf(arguments[2]) != 'null' ? arguments[2] : {},
			autoPost = options.autoPost ? options.autoPost : []
		;

		if (autoPost.contains('id_role') && ! post.id_role)
			post.id_role = user.id_role;

		// Fields
		var input_id = new Element('input', {name:'id_user', type: 'hidden', value:user.id_user}).inject(container),

			// UserName
			ff_username = new ION.FormField({container: container, 'class':'small', label: {text: Lang.get('ionize_label_username')}}),
			input_username = new Element('input', {'class':'inputtext required w95p usernameUnique', name:'username', type: 'text', value:user.username}).inject(ff_username.getContainer()),

			// Email
			ff_email = new ION.FormField({container: container, 'class':'small', label: {text: Lang.get('ionize_label_email')}}),
			input_email = new Element('input', {'class':'inputtext required w95p emailUnique', name:'email', type: 'text', value:user.email}).inject(ff_email.getContainer()),

			// Firstname
			ff_firstname = new ION.FormField({container: container, 'class':'small', label: {text: Lang.get('ionize_label_firstname')}}),
			input_firstname = new Element('input', {'class':'inputtext w95p', name:'firstname', type: 'text', value:user.firstname}).inject(ff_firstname.getContainer()),

			// Lastname
			ff_lastname = new ION.FormField({container: container, 'class':'small', label: {text: Lang.get('ionize_label_lastname')}}),
			input_lastname = new Element('input', {'class':'inputtext w95p', name:'lastname', type: 'text', value:user.lastname}).inject(ff_lastname.getContainer())
		;

		// Role :
		if ( ! post.id_role)
		{
			var ff_role = new ION.FormField({container: container, 'class':'small', label: {text: Lang.get('ionize_label_role')}})

			this.select = new ION.Form.Select({
				name:'id_role',
				container: ff_role.getContainer(),
				data: this.roles,
				key: 'id_role',
				label: 'role_name',
				fireOnInit: true,
				firstValue: '',
				firstLabel: Lang.get('ionize_select_company'),
				selected: user.id_role
			});
		}

		// Password
		var div_pass = new Element('div').inject(container),
			ff_pass = new ION.FormField({container: div_pass, 'class':'small', label: {text: Lang.get('ionize_label_password')}}),
			input_pass = new Element('input', {id:'userPassword' + user.id_user, 'class':'inputtext w95p', name:'password', type: 'password', value:''}).inject(ff_pass.getContainer()),

			ff_pass_conf = new ION.FormField({container: div_pass, 'class':'small', label: {text: Lang.get('ionize_label_password2')}}),
			// input_pass_conf = new Element('input', {'class':"inputtext w95p validate-match matchInput:'userPassword" + user.id_user + "'", name:'password2', type: 'password', value:''}).inject(ff_pass_conf.getContainer())
			input_pass_conf = new Element('input', {'class':"inputtext w95p matchPassword", name:'password2', type: 'password', value:''}).inject(ff_pass_conf.getContainer())
		;

		// Existing user
		if (id != '')
		{
			var h3_pass = new Element('h3', {'class':'mt20 toggler toggler-user' + id, html:Lang.get('ionize_title_change_password')}).inject(div_pass, 'before');
			div_pass.addClass('element element-user' + id);

			new Element('p', {'class':'lite', html:Lang.get('ionize_help_password_change')}).inject(div_pass, 'top');
		}
		else
		{
			input_pass.addClass('required');
			input_pass_conf.addClass('required');
		}

		// Notify the user
		if (options.notify)
		{
			var h3_notify = new Element('h3', {'class':'toggler toggler-user' + id, html:Lang.get('ionize_title_notify_user_account_updated')}).inject(container);
			var div_notify = new Element('div', {'class':'element element-user' + id}).inject(container),
				p_notify = new Element('p', {'class':'lite', html:Lang.get('ionize_help_notify_user_account_updated')}).inject(div_notify),
				input_notify = new Element('textarea', {name:'message', 'class':'autogrow'}).inject(div_notify)
			;

			if (id == '') h3_notify.addClass('mt20');
		}

		// Post fields
		Object.each(post, function(value, key)
		{
			new Element('input', {name:key, type: 'hidden', value:value}).inject(container, 'top');
		});

		return container;
	},

	/**
	 * Adds Company Validator to the Company form
	 *
	 * @param form
	 * @param company
	 */
	_addUserFormValidator: function(form, user)
	{
		// Validator
		if (! form.retrieve('validator'))
		{
			form.store(
				'validator',
				new Form.Validator.Inline(form, {
					evaluateFieldsOnBlur: false,
					evaluateFieldsOnChange: false,
					errorPrefix: '',
					onElementValidate: function(isValid, field, className, warn){
						var validator = this.getValidator(className);
						if (!isValid && validator.getError(field)){
							console.log(validator.getError(field));
						}
					},
					showError: function(element) {
						element.show();
					}
				})
			);
		}

		var validator = form.retrieve('validator');

		validator.add(
			'emailUnique',
			{
				errorMsg: Lang.get('ionize_message_email_already_registered'),
				test: function(element)
				{
					if (element.value.length > 0)
					{
						var req = new Request({
							url: ION.adminUrl + 'user/check_email_exists',
							async: true,
							data: {
								email: element.value,
								id_user: user.id_user
							}
						}).send();

						return (req.response.text != '1')
					}
				}
			}
		);
		validator.add(
			'usernameUnique',
			{
				errorMsg: Lang.get('ionize_message_username_already_registered'),
				test: function(element)
				{
					if (element.value.length > 0)
					{
						var req = new Request({
							url: ION.adminUrl + 'user/check_username_exists',
							async: true,
							data: {
								username: element.value,
								id_user: user.id_user
							}
						}).send();

						return (req.response.text != '1')
					}
				}
			}
		);
		validator.add(
			'matchPassword',
			{
				errorMsg: Lang.get('ionize_message_password_not_match'),
				test: function(element)
				{
					if (element.value.length > 0)
					{
						var passInput = $('userPassword' + user.id_user);
						return element.value == passInput.value;
					}
					else
					{
						return true;
					}
				}
			}
		);
	},

	/**
	 * Returns one role from id, or null if no role is found
	 *
	 * @param id_role
	 * @returns {*}
	 */
	getRoleFromId: function(id_role)
	{
		var role = null;

		Object.each(this.roles, function(r)
		{
			if (r.id_role == id_role)
				role = r;
		});

		return role;
	}
});


/**
 * User logged in .
 * Singleton
 *
 * @type {Class}
 */

ION.UserClass = new Class({

	Implements: [Options, Class.Singleton, Events, Class.Binds],

	user: null,

	/**
	 * Singleton
	 *
	 * @returns {*}
	 */
	initialize: function()
	{
		return this.check() || this.init();
	},

	init:function()
	{
		this.user = null;
		this.getLoggedUser();
		return this;
	},

	getLoggedUser: function()
	{
		var self = this;
		new Request.JSON(
		{
			url: ION.adminUrl + 'user/get_current_user',
			method: 'post',
			loadMethod: 'xhr',
			async: true,
			onFailure: function()
			{
				return null;
			},
			onSuccess: function(json)
			{
				self.user = json;
				self.fireEvent('onUserLoaded');
			}
		}).send();
	},

	// @todo : write
	isLoggedIn: function()
	{

	},

	getUser: function()
	{
		return this.user;
	},

	getRole: function()
	{
		if (typeOf(this.user.role) != 'null')
			return this.user.role;
		return null;
	},


	is: function(role_codes)
	{
		if (typeOf(role_codes) == 'string')
			role_codes = [role_codes];

		var role = this.getRole();

		if (role != null)
		{
			if (role_codes.contains(role.role_code))
				return true;
		}

		return false;
	},

	is_not: function(role_codes)
	{
		if (typeOf(role_codes) == 'string')
			role_codes = [role_codes];

		var role = this.getRole();

		if (role != null)
		{
			if (role_codes.contains(role.role_code))
				return false;
		}

		return true;
	},

	getGroupField: function(field)
	{
		if (typeOf(this.user) != 'null')
		{
			if (typeOf(this.user.group[field]) != 'null')
				return this.user.group[field];
		}

		return null;
	},

	get: function(key)
	{
		return (typeOf(this.user[key]) != 'null') ? this.user[key] : null;
	},

	getGroupLevel:function()
	{
		return this.getGroupField('level');
	},

	getName: function()
	{
		return this.get('screen_name');
	},

	getCode: function()
	{
		return this.get('username');
	}
});







