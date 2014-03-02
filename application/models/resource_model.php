<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 1.0.0
 */

// ------------------------------------------------------------------------

/**
 * Ionize Resource Model
 *
 * @package		Ionize
 * @subpackage	Models
 * @category	Authorization
 * @author		Ionize Dev Team
 *
 */
class resource_model extends Base_model
{
	/**
	 * Rule table
	 * @var string
	 */
	static $RULE_TABLE = 'rule';

	/**
	 * Role table
	 * @var string
	 */
	static $ROLE_TABLE = 'role';


	// ------------------------------------------------------------------------


	/**
	 * Constructor
	 *
	 * @access	public
	 */
	public function __construct()
	{
		parent::__construct();

		$this->table =		'resource';
		$this->pk_name = 	'id_resource';
	}


	// ------------------------------------------------------------------------


	public function create($type, $element, $actions = array(),$id_element = NULL, $parent = NULL, $title = NULL, $description = NULL)
	{
		return $this->save($type, $element, $actions ,$id_element, $parent, $title, $description);
	}


	// ------------------------------------------------------------------------


	/**
	 * @param       $type
	 * @param array $element
	 * @param array $actions
	 * @param null  $id_element
	 * @param null  $parent
	 * @param null  $title
	 * @param null  $description
	 *
	 * @return int
	 */
	public function save($type, $element, $actions = array(), $id_element = NULL, $parent = NULL, $title = NULL, $description = NULL)
	{
		$id_parent = NULL;

		if ( is_null($actions)) $actions = array();

		// Find out $id_parent
		if ( ! is_null($parent) )
		{
			if (is_string($parent) && intval($parent) !== $parent)
				$id_parent = $this->_save_resource($type, $parent, NULL, array('title' => ucfirst($parent)));
			else
				$id_parent = $parent;
		}

		$data = array(
			'id_parent' => $id_parent,
			'title' => $title,
			'description' => $description
		);

		$element = ! is_null($id_element) ? $element . '/' . $id_element : $element;

		$id_resource = $this->_save_resource($type, $element, $actions, $data);

		return $id_resource;
	}


	// ------------------------------------------------------------------------


	/**
	 * Creates one resource if it doesn't exist, else updates it.
	 *
	 *
	 * @param       $type
	 * @param       $element		String. Can contain the element ID (eg. 'element/3')
	 * @param array $actions
	 * @param array $data
	 *
	 * @return int|null
	 */
	private function _save_resource($type, $element, $actions = array(), $data=array())
	{
		$id_resource = NULL;

		// Correct NULL actions
		if (is_null($actions)) $actions = array();

		$resource = $type . '/' . $element;

		if ( is_null($actions)) $actions = array();

		// Find out $id_parent
		$db_resource = $this->get_row_array(array('resource' => $resource));

		$data = array_merge(
			array(
				'resource' => $resource,
				'actions' => implode(',', $actions)
			),
			$data
		);

		// Get ID resource
		if ( ! empty($db_resource))
		{
			$data['id_resource'] = $db_resource['id_resource'];
		}

		$id_resource = parent::save($data);

		return $id_resource;
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns the resource tree
	 * From DB resources (Admin)
	 *
	 * @param array $where
	 *
	 * @return array
	 */
	public function get_tree($where = array())
	{
		$resources = $this->get_list($where);

		$tree = $this->build_resources_tree($resources);

		return $tree;
	}


	// ------------------------------------------------------------------------


	/**
	 * @param        $element
	 * @param        $id_element
	 * @param array  $actions
	 * @param string $type
	 *
	 * @return array
	 */
	public function get_element_roles_resources($element, $id_element, $actions=array(), $type='frontend')
	{
		$data = array();
		$where = NULL;

		switch($type)
		{
			case 'frontend' :
				$where = array(
					'role_level < 1000',
					'role_level >= 100',
				);
				break;

			case 'backend' :
				$where = array(
					'role_level >= 1000',
					'role_level <=' . User()->get('role_level')
				);
				break;
		}

		$where['order_by'] = 'role_level DESC';

		$roles= $this->get_list($where, self::$ROLE_TABLE);

		$resource = $this->get_element_resource($element, $id_element, $actions, $type);
		$resource_actions = $resource['actions'];

		foreach($roles as $role)
		{
			$resource['actions'] = $resource_actions;

			// No actions needed for super-admin
			if ($role['role_code'] == 'super-admin')
				$resource['actions'] = '';

			$resource['title'] = $role['role_name'];

			$rules = $this->get_list(
				array(
					'id_role' => $role['id_role'],
					'resource' => $resource['id_resource']
				),
				self::$RULE_TABLE
			);
			$data[$role['id_role']] = array(
				'resources' => array($resource),
				'rules' => $rules
			);
		}

		return $data;
	}


	// ------------------------------------------------------------------------


	/**
	 * @param        $element
	 * @param        $id_element
	 * @param array  $actions
	 * @param string $type
	 *
	 * @return array
	 */
	public function get_element_resource($element, $id_element, $actions=array(), $type='frontend')
	{
		if ($actions == NULL) $actions = array();

		$resource = $type . '/' . $element . '/' . $id_element;

		$data = array
		(
			'id_resource' => $resource,
			'id_parent' => '',
			'resource' => $resource,
			'actions' => ! empty($actions) ? implode(',', $actions) : '',
			'title' => '',
			'description' => '',
//			'level' => 0,
		);

		return $data;
	}


	// ------------------------------------------------------------------------


	/**
	 * Builds the resource tree
	 *
	 * @param array $elements
	 * @param null  $id_parent
	 * @param int   $level
	 *
	 * @return array
	 */
	public function build_resources_tree(array &$elements, $id_parent = NULL, $level=0)
	{
		$branch = array();

		foreach ($elements as $element)
		{
			// $id_parent can be a string,
			if ($element['id_parent'] == $id_parent)
			{
				$element['level'] = $level;
				$children = $this->build_resources_tree($elements, $element['id_resource'], $level+1);

				if ($children) {
					$element['children'] = $children;
				}
				$branch[] = $element;
			}
		}

		return $branch;
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns all the available resources,
	 * DB + Modules, merged in one array.
	 *
	 * @return array
	 *
	 */
	public function get_all_resources()
	{
		$resources = $this->get_list();

		$modules_resources = Modules()->get_resources();

		return array_merge($resources, $modules_resources);
	}


	// ------------------------------------------------------------------------


	/**
	 * Return TRUE if the resource has at least one rule,
	 * independently from any role,
	 * FALSE if the resource has no rule.
	 *
	 * @param $resource
	 *
	 * @return bool
	 */
	public function has_rule($resource)
	{
		$result = $this->get_list(array('resource' => $resource), static::$RULE_TABLE);

		return ( ! empty($result));
	}
}
