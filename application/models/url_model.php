<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://ionizecms.com/doc-license
 * @link		http://ionizecms.com
 * @since		Version 0.9.8
 */

// ------------------------------------------------------------------------

/**
 * Ionize Url Model
 *
 * @package		Ionize
 * @subpackage	Models
 * @category	Admin settings
 * @author		Ionize Dev Team
 *
 */

class Url_model extends Base_model 
{

	/**
	 * Model Constructor
	 *
	 * @access	public
	 */
	public function __construct()
	{
		parent::__construct();

		$this->set_table('url');
		$this->set_pk_name('id_url');
	}

	
	/**
	 * Saves one URL
	 * 
	 * @param	String		'page', 'article', etc.
	 * @param	String		lang code
	 * @param	int			ID of the entity.
	 * @param	Array		Array of URL paths
	 *							array(
	 *								'url' => 			'/path/to/the/element',
	 *								'path_ids' =>		'/1/8/12',
	 *								'full_path_ids' =>	'/1/8/3/12'
	 *							)
	 *
	 * @return	int			Number of inserted / updated URL;
	 *
	 */
	public function save_url($type, $lang, $id_entity, $data)
	{
		$return = 0;
		
		$where = array(
			'type' => $type,
			'lang' => $lang,
			'id_entity' => $id_entity,
			'active' => '1'
		);
		
		$element = array(
			'id_entity' => $id_entity,
			'type' => $type,
			'lang' => $lang,
			'active' => '1',
			'canonical' => '1'
		);
		
		// Get the potential existing URL
		$db_url = $this->get($where);

		// The URL already exists
		if ( ! empty($db_url) && 
			 ( time() - strtotime($db_url['creation_date'])) > 3600 &&
			 $data['url'] != $db_url['path'] )
		{
			// Set the old link as inactive
			$element['active'] = '0';
			$this->update($where, $element);
			$nb = $this->db->affected_rows();
			
			// Insert the new link
			$element['active'] = '1';
			$element['path'] = $data['url'];
			$element['path_ids'] = $data['path_ids'];
			$element['full_path_ids'] = $data['full_path_ids'];
			$element['creation_date'] = date('Y-m-d H:i:s');
			$this->insert($element);
		}
		else if ( 
			(! empty($db_url) && $data['url'] != $db_url['path'] )
			OR (! empty($db_url) && ($data['path_ids'] != $db_url['path_ids'] OR $data['full_path_ids'] != $db_url['full_path_ids']))
		)
		{
			$element['path'] = $data['url'];
			$element['path_ids'] = $data['path_ids'];
			$element['full_path_ids'] = $data['full_path_ids'];
			$this->update($where, $element);
		}
		else if (empty($db_url))
		{
			$element['path'] = $data['url'];
			$element['path_ids'] = $data['path_ids'];
			$element['full_path_ids'] = $data['full_path_ids'];
			$element['creation_date'] = date('Y-m-d H:i:s');
			
			$this->insert($element);
			$return = 1;
		}

		return $return;
	}
	
	
	function delete_empty_urls()
	{
		$this->delete(array('path' => ''));
	}
}

