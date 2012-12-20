<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://ionizecms.com/doc-license
 * @link		http://ionizecms.com
 * @since		Version 0.9.0
 */

// ------------------------------------------------------------------------

/**
 * Ionize Users Controller
 * Manage Users and Groups
 *
 * @package		Ionize
 * @subpackage	Controllers
 * @category	Users management
 * @author		Ionize Dev Team
 *
 */

class Users extends MY_admin 
{

	var $current_user_level = -100;

	/**
	 * Constructor
	 *
	 */
	public function __construct()
	{
		parent::__construct();

		// Users model
		$this->load->model('users_model', '', true);

		// Current connected user level
		$user = $this->connect->get_current_user();
		$this->current_user_level = $user['group']['level'];
	}


	// ------------------------------------------------------------------------


	/**
	 * Shows existing users and groups
	 *
	 * @param	int		page to display
	 * @param	int		number of users per page
	 *
	 */
	function index($page=1, $nb=30)
	{
		// 
		$this->template['users_count_all'] = $this->users_model->count_all();
		
		// Get groups list filtered on level <= current_user level
		$this->template['groups'] = $this->connect->model->get_groups(array('order_by'=>'level DESC', 'level <=' => $this->current_user_level));

		// Send the current user's level to the view
		$this->template['current_user_level'] = $this->current_user_level;

		$this->output('user/index');
	}


	// ------------------------------------------------------------------------


	function users_list($page=1)
	{
		$nb = ($this->input->post('nb')) ? $this->input->post('nb') : '50';
		
		// Minimum
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
		foreach(array('slug') as $key)
		{
			if( $this->input->post($key))
			{
				$where[$key] = $this->input->post($key);
				$this->template['filter'][$key] = $where[$key];
			}
		}
		
		// Order by last registered
		if( $this->input->post('registered'))
		{
			$where['order_by'] = 'join_date DESC';
		}
		

		// Get user list filtered on levels <= current_user level
		$this->template['users'] = $this->connect->model->get_users(array_merge($where, array('limit' => $nb, 'offset' => $offset, 'level <=' => $this->current_user_level, 'like' => $like)));

		// Pagination
		$this->template['current_page'] = $page + 1;
		$this->template['nb'] = $nb;
		$this->template['users_count'] = $this->connect->model->count_users(array_merge($where, array('level <=' => $this->current_user_level, 'like' => $like)));
		$this->template['users_pages'] = ceil($this->template['users_count'] / $nb);
		
		// XHR answer
    	$this->output('user/list');
	}
	
	
	// ------------------------------------------------------------------------


	/**
	 * Edit one user
	 *
	 */
	function edit($id)
	{
		$this->template['user'] = $this->connect->model->find_user(array('id_user' => $id));
		
		// Get groups list filtered on level <= current_user level
		$this->template['groups'] = array_filter($this->connect->model->get_groups(array('order_by'=>'level')), array($this, '_filter_groups'));
		
		$this->output('user/user');
	}


	// ------------------------------------------------------------------------


	function get_form()
	{
		$this->template['user'] = $this->users_model->feed_blank_template();

		// Get groups list filtered on level <= current_user level
		$this->template['groups'] = array_filter($this->connect->model->get_groups(array('order_by'=>'level')), array($this, '_filter_groups'));

		$this->output('user/user');
	}


	// ------------------------------------------------------------------------


	/**
	 * Update one user
	 *
	 */
	function update()
	{
		$id_user = $this->input->post('user_PK');
		
		if ($id_user !== FALSE)
		{
			// Update array
			$data = array(
						'id_group' =>	$this->input->post('id_group'),
						'username' =>		$this->input->post('username'),
						'screen_name' =>	$this->input->post('screen_name'),
						'email' =>			$this->input->post('email'),
						'join_date' =>			$this->input->post('join_date'),
						'salt' =>			$this->input->post('salt')
					);
			
			if ($this->_user_with_same_email_exists($this->input->post('email'), $id_user))
			{
				$this->error(lang('ionize_message_user_exists'));
			}
			
			if (($this->input->post('password') != '' && $this->input->post('password2') != '') &&
				($this->input->post('password') == $this->input->post('password2'))	)
			{
				$data['password'] = $this->connect->encrypt($this->input->post('password'), $data);
			}

			// Update the user
			$this->users_model->update($id_user, $data);
			
			// UI update panels
			$this->update[] = array(
				'element' => 'mainPanel',
				'url' => admin_url() . 'users'
			);
			
			// Success message
			$this->success(lang('ionize_message_user_updated'));
		}		
	}


	// ------------------------------------------------------------------------


	/**
	 * Saves one new user
	 *
	 */
	function save()
	{
		if (($this->input->post('username') && $this->input->post('password') && $this->input->post('email') ) && 
			($this->input->post('password') == $this->input->post('password2'))	
		)
		{
			if ($this->_user_with_same_email_exists($this->input->post('email')))
			{
				$this->error(lang('ionize_message_user_exists'));
			}
			
			// Insert array
			$data = array(
						'id_group' =>		$this->input->post('id_group'),
						'username' =>		$this->input->post('username'),
						'screen_name' =>	$this->input->post('screen_name'),
						'password' =>		$this->input->post('password'),
						'email' =>			$this->input->post('email'),
						'join_date' =>		date('Y-m-d H:i:s'),
						'salt' =>			$this->connect->get_salt()
					);
			
			$data['password'] = $this->connect->encrypt($data['password'], $data);
			
			
			// Save new user only if it not exists
			if ( ! $this->users_model->exists(array('username' => $data['username'])))
			{
				// DB insertion
				$id = $this->users_model->insert($data);

				// UI update panels
				$this->update[] = array(
					'element' => 'mainPanel',
					'url' => admin_url() . 'users'
				);
				
				// JSON answer
				$this->success(lang('ionize_message_user_saved'));
			}
			else
			{
				$this->error(lang('ionize_message_user_exists'));
			}
		}
		else
		{
			$this->error(lang('ionize_message_user_not_saved'));
		}
	}
	

	// ------------------------------------------------------------------------

	
	/**
	 * Deletes one user
	 *
	 */
	function delete($id)
	{
		$current_user = $this->connect->get_current_user();

		if ($current_user['id_user'] != $id)
		{
			$affected_rows = $this->users_model->delete($id);
	
			if ($affected_rows > 0)
			{
				$this->id = $id;
<<<<<<< HEAD

				// UI update panels
				$this->update[] = array(
					'element' => 'mainPanel',
					'url' => admin_url() . 'users'
				);

=======
				
				/** Remove deleted items from DOM **/
				$this->callback[] = array(
					'fn' => 'ION.deleteDomElements',
					'args' => array('.users' . $id)
				);
				
				/** Send answer **/
>>>>>>> 37ae275c480b6d3e0b24d07a92920ce8f2b8b12e
				$this->success(lang('ionize_message_user_deleted'));
				$this->response();
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
	 * Export the users list
	 *
	 */
	function export($format = NULL)
	{
		$format = ( ! is_null($format)) ? $format : $this->input->post('format');
	
		// Load download helper
		$this->load->helper('download');
		
		// Get users
		$users = $this->users_model->get_list();
		
		// If users, get the format
		if (!empty($users))
		{
			// Export in in asked format
			switch($format)
			{
				case 'csv': $this->_export_csv($users);
			}
			
			$this->success(lang('ionize_message_users_exported'));

		}
		else
		{
			$this->error(lang('ionize_message_users_not_exported'));		
		}

	}


	// ------------------------------------------------------------------------
	
	/**
	 * Checks if another user has the same email
	 *
	 * @param	Current user ID
	 * @param	Email to find
	 *
	 * @return	TRUE if another user is found, FALSE if not
	 */
	private function _user_with_same_email_exists($email, $id_user = NULL)
	{
		$user = $this->connect->model->find_user(array('email' => $email));
		
		if ( ! is_null($id_user))
		{
			if ( ! empty($user) && $user['id_user'] != $id_user)
				return TRUE;
		}
		else
		{
			if ( ! empty($user))
				return TRUE;
		}
		return FALSE;
	}
	

	// ------------------------------------------------------------------------

	
	/**
	 * Export the email list to CSV
	 *
	 */
	private function _export_csv($users)
	{
		$data= array();

		// Add columns names to file header
		$data[] = implode(';', array_keys($users[0]));

		// Add users to data table
		foreach($users as $user)
		{
			$data[] = implode(';', $user);
		}
		
		// Add new line 
		$data = implode("\r\n", $data);

		// File name
		$name = Settings::get('theme').'_users.csv';

		// Send the file to the user
		force_download($name, $data);
	}


	// ------------------------------------------------------------------------


	/**
	 * Users filter callback function
	 *
	 */
	function _filter_users($row)
	{
		return ($row['group']['level'] <= $this->current_user_level) ? true : false; 
	}


	// ------------------------------------------------------------------------


	/**
	 * Groups filter callback function
	 *
	 */
	function _filter_groups($row)
	{
		return ($row['level'] <= $this->current_user_level) ? true : false; 
	}
}


/* End of file users.php */
/* Location: ./application/controllers/admin/users.php */