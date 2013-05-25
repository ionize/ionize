<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize
 * User Controller
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 0.9.0
 */

class User extends My_Admin
{
	public $current_role = NULL;


	/**
	 * Constructor
	 *
	 */
	function __construct()
	{
		parent::__construct();
		
		$this->load->model('user_model');
		$this->load->model('role_model');

		// Current connected user level
		$this->current_role = User()->get_role();
	}


	// ------------------------------------------------------------------------


	/**
	 * Default
	 *
	 */
	function index($page=1, $nb=30)
	{
		$this->template['users_count_all'] = $this->user_model->count_all();

		$roles = $this->role_model->get_list();
		$this->template['roles'] = array_filter($roles, array($this, '_filter_roles'));

		$this->output('user/index');
	}


	// ------------------------------------------------------------------------


	function get_list($page=1)
	{
		// Nb and Minimum
		$nb = ($this->input->post('nb')) ? $this->input->post('nb') : '50';
		if ($nb < 25) $nb = 25;

		$page = $page - 1;
		$offset = $page * $nb;

		// Send the filter elements to the view
		$this->template['filter'] = array();

		// Like conditions
		$like = array();
		foreach(array('username', 'screen_name', 'email') as $key)
		{
			if( $this->input->post($key))
			{
				$like[$key] = $this->input->post($key);
				$this->template['filter'][$key] = $like[$key];
			}
		}

		// Where
		$where = array();
		if( $this->input->post('id_role'))
		{
			$this->template['filter']['id_role'] = $this->input->post('id_role');
			$where['user.id_role'] = $this->input->post('id_role');
		}

		// Order by last registered
		if( $this->input->post('registered'))
		{
			$where['order_by'] = 'join_date DESC';
		}

		$where = array_merge(
			$where,
			array(
				'limit' => $nb,
				'offset' => $offset,
				'like' => $like,
				'role_level <= ' => $this->current_role['role_level']
			)
		);

		// Get user list filtered on levels <= current_user level
		$this->template['users'] = $this->user_model->get_list_with_role($where);

		// Pagination
		$this->template['current_page'] = $page + 1;
		$this->template['nb'] = $nb;

		$this->template['users_count'] = $this->user_model->count($where);
		$this->template['users_pages'] = ceil($this->template['users_count'] / $nb);

		// XHR answer
		$this->output('user/list');
	}


	// ------------------------------------------------------------------------


	/**
	 * Creation Form
	 *
	 */
	public function create()
	{
		$this->template['user'] = $this->user_model->feed_blank_template();

		// Get roles list filtered on level <= current_user level
		$roles = $this->role_model->get_list();
		$this->template['roles'] = array_filter($roles, array($this, '_filter_roles'));

		$this->output('user/user');
	}

	// ------------------------------------------------------------------------


	/**
	 * Edit one user
	 *
	 */
	public function edit()
	{
		$id_user = $this->input->post('id_user');

		$this->template['user'] = $this->user_model->get($id_user);

		// Panel from which the user is edited
		$this->template['from'] = $this->input->post('from');

		// Get roles, filtered on level <= $current_role level
		$roles = $this->role_model->get_list();
		$this->template['roles'] = array_filter($roles, array($this, '_filter_roles'));

		$this->output('user/user');
	}


	// ------------------------------------------------------------------------


	/**
	 * Save
	 *
	 */
	public function save()
	{
		if ($this->input->post('email'))
		{
			$id_user = $this->input->post('id_user');
			$post = $this->input->post();

			$post = array_merge(
				$post,
				array(
					'join_date' => $id_user ? $this->input->post('join_date') : date('Y-m-d H:i:s'),
					'salt' => $id_user ? $this->input->post('salt') : User()->get_salt(),
				)
			);

			// Existing
			if ($id_user != FALSE)
			{
				if (($this->input->post('password') != '' && $this->input->post('password2') != '') &&
					($this->input->post('password') == $this->input->post('password2'))	)
				{
					$post['password'] = User()->encrypt($this->input->post('password'), $post);
				}
				else
				{
					unset($post['password'], $post['password2']);
				}
			}
			// New
			else
			{
				$post['password'] = User()->encrypt($this->input->post('password'), $post);
			}

			// Save
			$this->user_model->save($post);

			// Reload user list
			if ( ! empty($post['from']) && $post['from'] == 'dashboard')
			{
				$this->_reload_dashboard();
			}
			else
			{
				$this->_reload_user_list();
			}

			// Success message
			$this->success(lang('ionize_message_user_saved'));
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Delete
	 *
	 */
	public function delete()
	{
		$id_user = $this->input->post('id_user');
		$current_user_id = User()->getId();

		if($id_user != $current_user_id)
		{
			$affected_rows = $this->user_model->delete($id_user);

			if ($affected_rows > 0)
			{
				// Update role list panel
				$this->_reload_user_list();

				$this->success(lang('ionize_message_user_deleted'));
			}
			else
			{
				$this->error(lang('ionize_message_user_not_deleted'));
			}
		}
		else
		{
			$this->error(lang('ionize_message_user_cannot_delete_yourself'));
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Return the current user or NULL if not logged in.
	 * Used by Ionize.User() JS object to get the current user
	 *
	 */
	function get_current_user()
	{
		$user = User()->get_user();

		if ( $user !== FALSE)
		{
			$user['role'] = User()->get_role();

			// Removes the password, even it is encoded
			if (isset($user['password'])) unset($user['password']);
			if (isset($user['salt'])) unset($user['salt']);

			// Returns the current user as JSON object
			if ($this->is_xhr())
			{
				echo json_encode($user);
				exit();
			}
			else
			{
				return $user;
			}
		}
		return NULL;
	}


	// ------------------------------------------------------------------------


	public function get_rules()
	{
		$rules = Authority::get_rules_array();

		if ($this->is_xhr())
		{
			$data = array(
				'rules' => $rules
			);
			$this->xhr_output($data);
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Must be called by XHR
	 * Called by User Edition form Validation
	 *
	 * Returns 1 if true, 0 if false
	 *
	 */
	function check_email_exists()
	{
		$id_user = $this->input->post('id_user');
		$email = $this->input->post('email');

		$exists = $this->user_model->user_with_same_email_exists($email, $id_user);

		$this->xhr_output($exists);
	}


	// ------------------------------------------------------------------------


	/**
	 * Roles filter callback function
	 *
	 */
	public function _filter_roles($row)
	{
		return ($row['role_level'] <= $this->current_role['role_level']) ? true : false;
	}


	// ------------------------------------------------------------------------


	private function _reload_user_list()
	{
		// Save options : as callback
		$this->callback[] = array(
			'fn' => 'ION.HTML',
			'args' => array(
				'user/get_list',
				'',
				array(
					'update'=> 'userList'
				)
			)
		);
	}


	// ------------------------------------------------------------------------


	private function _reload_dashboard()
	{
		$this->update = array(
			array(
				'element' => 'mainPanel',
				'url' => admin_url() . 'dashboard'
			)
		);
	}
}
