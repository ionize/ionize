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

		// Check / correct the URL
		$data['url'] = $this->get_unique_url($type, $id_entity, $lang, $data['url']);
		
		// Update the entity URL (page, article)
		$this->update_entity_url($type, $id_entity, $lang, $data['url']);
		
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
			$return = 1;
		}
		else if ( 
			(! empty($db_url) && $data['url'] != $db_url['path'] )
			OR (! empty($db_url) && ($data['path_ids'] != $db_url['path_ids'] OR $data['full_path_ids'] != $db_url['full_path_ids']))
		)
		{
			$element['path'] = $data['url'];
			$element['path_ids'] = $data['path_ids'];
			$element['full_path_ids'] = $data['full_path_ids'];
			$return = $this->update($where, $element);
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
	
	
	/**
	 * Return one entity based of its URL
	 * 
	 * Important : If one page and one article have the same URL, the page is returned 
	 *
	 * @return	Mixed	Array of the entity or NULL if no entity found
	 *
	 * @TODO : 	Check what happens when one article has the same URL
	 *			than one page !
	 *
	 */
	function get_by_url($url, $lang = NULL)
	{
		$url = trim($url, '/');

		$where = array(
			'path' => $url,
			'active' => 1
		);

		if ( is_null($lang))
			$lang = Settings::get_lang('current');

		$where['lang'] = $lang;
		
		$this->{$this->db_group}->where($where);
		$query = $this->{$this->db_group}->get($this->table);

		if ($query->num_rows() > 0)
		{
			$result = $query->result_array();
			
			if (count($result >1))
			{
				foreach($result as $row)
				{
					if ($row['type'] == 'page')
						return $row;
				}
			}
			
			return array_pop($result);
		}
		
		return NULL;
	}
	
	
	/**
	 * Returns list of URLs
	 *
	 * @param	String		Entity type. 'article, 'page'
	 * @param	Int			Entity ID
	 * @param	String		Lang code. 'all' for all languages
	 * @param	Boolean		Only active URLs. 1 default
	 *
	 */
	function get_collection($type, $id_entity, $lang = 'all', $active = TRUE)
	{
		$where = array(
			'type' => $type,
			'id_entity' => $id_entity,
			'active' => ($active) ? 1 : 0
		);
		
		if ($lang != 'all')
			$where['lang'] = $lang;
		
		$this->{$this->db_group}->where($where);
		$query = $this->{$this->db_group}->get($this->table);
		
		if ($query->num_rows() > 0)
			return $query->result_array();
		
		return array();
	}

	function get_entity_urls($type='page', $id_entity = NULL)
	{
		$urls = array();

		if ( ! is_null($id_entity))
		{
			$urls = $this->get_list(
				array(
					'type' => $type,
					'id_entity' => $id_entity,
					'active' => 1
				)
			);
		}

		return $urls;
	}

	/**
	 * Update the entity lang table with one new URL
	 *
	 * @param	String		Entity type. 'article, 'page'
	 * @param	Int			Entity ID
	 * @param	String		Lang code
	 * @param	String		URL
	 * 
	 */
	function update_entity_url($type, $id_entity, $lang, $url)
	{
		$table = $type . '_lang';

		// If the table exists and has the URL field
		if (
			$this->{$this->db_group}->table_exists($table)
			&& $this->has_field('url', $table)
		)
		{
			// Get only the last URL part
			$url = array_pop(explode('/', $url));
			
			$this->{$this->db_group}->where(
				array(
					'id_'.$type => $id_entity,
					'lang' => $lang
				)
			);
			$this->{$this->db_group}->update($table, array('url' => $url));
		}
	}
	
	
	function delete_empty_urls()
	{
		$this->{$this->db_group}->where(array('path' => ''));
		return $this->{$this->db_group}->delete('url');
	}
	
	
	function delete($type, $id_entity)
	{
		$where = array(
			'type' => $type,
			'id_entity' => $id_entity
		);

		$this->{$this->db_group}->where($where);
		return $this->{$this->db_group}->delete('url');
	}


	/**
	 * Deletes URLs which refers to no content
	 *
	 * @return int	Number of affected rows
	 *
	 */
	function clean_table()
	{
		$sql = "
			delete u from url u
			left join page p on p.id_page = u.id_entity and u.type='page'
			left join article a on a.id_article = u.id_entity and u.type = 'article'
			where
				p.id_page is null
				and a.id_article is null;
		";

		$this->{$this->db_group}->query($sql);

		// Returned : Number of deleted media rows
		$nb_affected_rows = (int) $this->{$this->db_group}->affected_rows();

		return $nb_affected_rows;
	}


	/**
	 * Return TRUE if one URL already exists (for another entity_id with the same type)
	 *
	 * @param	String		Entity type. 'article, 'page'
	 * @param	Int			Entity ID to exclude
	 * @param	String		URL
	 * @param	String		Lang code. 'all' for all languages (default)
	 *
	 * @return	boolean		TRUE if another entity URL exists
	 *
	 */
	function is_existing_url($type, $id_entity, $url, $lang='all')
	{
		$urls = $this->get_existing_urls($type, $id_entity, $url, $lang);

		return ( ! empty($urls));
	}


	public function get_existing_urls($type, $id_entity, $url, $lang='all')
	{
		$urls = array();

		// Get all the corresponding URL
		$where = array('path' => $url);
		if ($lang != 'all')	$where['lang'] = $lang;

		$this->{$this->db_group}->where($where);
		$query = $this->{$this->db_group}->get($this->table);

		if ($query->num_rows() > 0)
		{
			$urls = $query->result_array();

			foreach($urls as $key => $url)
			{
				if ($url['type'] == $type && $url['id_entity'] == $id_entity)
				{
					unset($urls[$key]);
				}
			}
		}
		return $urls;
	}
	
	
	/**
	 * Return one unique URL
	 *
	 * @param	String		Entity type. 'article, 'page'
	 * @param	Int			Entity ID
	 * @param	String		Lang code. 'all' for all languages
	 * @param	String		URL
	 *
	 */
	function get_unique_url($type, $id_entity, $lang, $url, $id = 1)
	{
		$this->clean_table();

		$existing_urls = $this->get_existing_urls($type, $id_entity, $url, $lang);

		if ( ! empty($existing_urls))
		{
			if ($id > 1 OR (substr($url, -2, count($url) -2) && intval(substr($url, -1)) != 0 ))
				$url = substr($url, 0, -2);

			$url = $url . '-' . $id;

			return $this->get_unique_url($type, $id_entity, $lang, $url, $id + 1);
		}

		return $url;
	}
	
}

