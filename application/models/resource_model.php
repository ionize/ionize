<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://ionizecms.com/doc-license
 * @link		http://ionizecms.com
 * @since		Version 1.0.0
 */

// ------------------------------------------------------------------------

/**
 * Ionize Resource Model
 *
 * @package		Ionize
 * @subpackage	Models
 * @category	ACL
 * @author		Ionize Dev Team
 *
 */

class resource_model extends Base_model
{
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


	public function get_tree()
	{
		$resources = $this->get_list();

		$tree = $this->_build_resources_tree($resources);

		return $tree;
	}


	// ------------------------------------------------------------------------


	protected function _build_resources_tree(array &$elements, $id_parent = 0, $level=0)
	{
		$branch = array();

		foreach ($elements as $key=>$element)
		{
			// $resource_details = explode('/', $resource['resource_key']);
			if ($element['id_parent'] == $id_parent)
			{
				$element['level'] = $level;
				$children = $this->_build_resources_tree($elements, $element['id_resource'], $level+1);

				if ($children) {
					$element['children'] = $children;
				}
				$branch[] = $element;
			}
		}

		return $branch;
	}


}
