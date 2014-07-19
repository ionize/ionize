<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 0.9.5
 */

// ------------------------------------------------------------------------

/**
 * Ionize Extend Field Model
 *
 * @package		Ionize
 * @subpackage	Models
 * @category	Extend Field
 * @author		Ionize Dev Team
 *
 */
class Extend_field_model extends Base_model
{
	public static $parents = array(
		'page',
		'article',
		'media'
	);

	private static $_CONTEXT_TABLE = 'extend_field_context';
	private static $_TYPE_TABLE = 'extend_field_type';

	/**
	 * Constructor
	 *
	 * @access	public
	 */
	public function __construct()
	{
		parent::__construct();

		// Extend Fields definition tables
		$this->set_table('extend_field');
		$this->set_pk_name('id_extend_field');
		$this->set_lang_table('extend_field_lang');
		
		// Extend Fields Instances table
		$this->instances_table =	'extend_fields';

		self::$ci->load->model('page_model', '', TRUE);
		self::$ci->load->model('article_model', '', TRUE);
	}


	// ------------------------------------------------------------------------


	public function get_types()
	{
		$query = $this->{$this->db_group}->get(self::$_TYPE_TABLE);
		$result = $query->result_array();

		return $result;
	}


	// ------------------------------------------------------------------------


	public function get_parents()
	{
		$parents = self::$parents;

		// Add parents found in extend table
		$this->{$this->db_group}->select('parent');
		$this->{$this->db_group}->distinct();
		$query = $this->{$this->db_group}->get($this->get_table());

		$result = $query->result_array();

		foreach($result as $row)
		{
			if ( ! in_array($row['parent'], $parents))
				$parents[] = $row['parent'];
		}

		return $parents;
	}


	// ------------------------------------------------------------------------


	/**
	 * @param array $where
	 * @param null  $lang
	 *
	 * @return array
	 */
	public function get_list($where = array(), $lang = NULL)
	{
		$where['order_by'] = 'ordering ASC';

		$this->{$this->db_group}->select(
			$this->get_table() . '.*,'
			. $this->get_lang_table() . '.label'
		);

		$this->{$this->db_group}->join(
			$this->get_lang_table(),
			$this->get_lang_table() . '.' . $this->get_pk_name() . ' = ' . $this->get_table() . '.' . $this->get_pk_name()
			. ' AND ' . $this->get_lang_table() . '.lang = \'' . Settings::get_lang('default') . '\'',
			'left'
		);

		$this->_join_to_extend_types();

		$list = parent::get_list($where);

		// Add languages definition on each field
		foreach($list as &$field)
		{
			$field['lang_definition'] = $this->get_lang(array('id_extend_field'=>$field['id_extend_field']));
		}

		return $list;
	}


	// ------------------------------------------------------------------------


	/**
	 * @param array $where
	 * @param null  $lang
	 *
	 * @return array
	 */
	public function get_lang_list($where = array(), $lang = NULL)
	{
		$this->_join_to_extend_types();

		if ( ! isset($where['order_by']))
			$where['order_by'] = 'ordering ASC';

		$list = parent::get_lang_list($where, $lang);

		return $list;
	}


	// ------------------------------------------------------------------------


	/**
	 * @param $id_extend_field
	 *
	 * @return string
	 */
	public function get_label($id_extend_field)
	{
		if($id_extend_field != '')
		{
			$this->{$this->db_group}->select($this->get_lang_table() . '.label');
			$this->{$this->db_group}->from($this->get_table());
			$this->{$this->db_group}->join(
				$this->get_lang_table(),
				$this->get_table() . '.' . $this->get_pk_name() . ' = ' . $this->get_lang_table() . '.' . $this->get_pk_name(),
				'inner'
			);
			$this->{$this->db_group}->where($this->get_lang_table() . '.lang', Settings::get_lang('default'));
			$this->{$this->db_group}->where($this->get_table() . '.' . $this->pk_name, $id_extend_field);
			
			$label = $this->{$this->db_group}->get();
			$label = $label->row_array();
			
			return (!empty($label['label'])) ? $label['label'] : '';
		}
		return 'Need a "$id_extend_field"';
	}


	// ------------------------------------------------------------------------


	/**
	 * Return context's extend fields instances
	 * With definition set to null or default value if not set
	 *
	 * @param      $context
	 * @param      $id_context
	 * @param      $parent
	 * @param null $id_parent
	 *
	 * @return array
	 */
	public function get_context_instances_list($context, $id_context, $parent, $id_parent=NULL)
	{
		// Extend Field Definitions
		$this->{$this->db_group}->select($this->get_table() . '.*');

		// Extend Lang : Label
		$this->{$this->db_group}->select($this->get_lang_table() . '.label');

		$this->{$this->db_group}->join(
			$this->get_lang_table(),
			$this->get_lang_table() . '.' . $this->get_pk_name() . ' = ' . $this->get_table() . '.' . $this->get_pk_name()
			. ' AND ' . $this->get_lang_table() . '.lang = \'' . Settings::get_lang('default') . '\'',
			'left'
		);

		// Context join
		$this->{$this->db_group}->join(
			self::$_CONTEXT_TABLE,
			self::$_CONTEXT_TABLE . '.' . $this->get_pk_name() . ' = ' . $this->get_table() . '.' . $this->get_pk_name(),
			'inner'
		);
		$this->{$this->db_group}->where(array(
			self::$_CONTEXT_TABLE.'.context' => $context,
			self::$_CONTEXT_TABLE.'.id_context' => $id_context
		));

		// Add Extend Type info
		$this->_join_to_extend_types();

		$definitions = parent::get_list();

		// Get the definitions extend ids
		$id_definitions = array();
		foreach($definitions as $def)
			$id_definitions[] = $def['id_extend_field'];

		// Extend Fields instances
		$instances = array();
		if ($parent && $id_parent && ! empty($id_definitions))
		{
			$this->{$this->db_group}->where_in('id_extend_field', $id_definitions);
			$this->{$this->db_group}->where(array(
				'parent' => $parent,
				'id_parent' => $id_parent,
			));
			$instances = parent::get_list(NULL, $this->instances_table);
		}

		// Prepare before filling with data
		$langs = Settings::get_languages();
		$instance_fields = $this->{$this->db_group}->list_fields($this->instances_table);

		foreach($definitions as &$field)
		{
			// One not tranlated extend field...
			if ($field['translated'] != '1')
			{
				// fill the base data with empty values
				$field = array_merge(array_fill_keys($instance_fields, NULL), $field);

				foreach($instances as $row)
				{
					if($row['id_extend_field'] == $field['id_extend_field'])
						$field = array_merge($field , $row);
				}
			}
			else
			{
				foreach($langs as $language)
				{
					// Lang code
					$lang_code = $language['lang'];

					// Feed lang key with blank array
					$field['lang_data'][$lang_code] = array('content'=>NULL);

					// Feeding of template languages elements
					foreach($instances as $row)
					{
						if($row['id_extend_field'] == $field['id_extend_field'] && $row['lang'] == $lang_code)
							$field['lang_data'][$lang_code] = $row;
					}
				}
			}
		}

		return $definitions;
	}


	// ------------------------------------------------------------------------


	/**
	 * Get the current extend fields and their values for one parent element
	 * Used by backend, as all the languages data are also get
	 *
	 * @param	string		Field & Field Definition parent name
	 * @param	null		Field instance parent ID
	 * @param	null		Field Definition parent ID
	 *
	 * @return 	array
	 *
	 */
	public function get_element_extend_fields($parent, $id_parent=NULL, $id_field_parent=NULL)
	{
		// Definitions
		$where = array('parent' => $parent);
		if ($id_field_parent) $where['id_parent'] = $id_field_parent;
		$definitions = $this->get_list($where);

		// Fields Instances
		$fields = array();
		$get_fields = FALSE;

		if ( ! $id_field_parent && $id_parent)
		{
			// Get fields instances directly linked to parent
			$this->{$this->db_group}->where(
				array(
					'extend_field.parent' => $parent,
					'extend_fields.id_parent' => $id_parent
				)
			);
			$get_fields = TRUE;
		}
		else if ($id_field_parent && $id_parent)
		{

			// Get fields linked to the field definition
			$this->{$this->db_group}->where(
				array(
					'extend_field.parent' => $parent,
					'extend_field.id_parent' => $id_field_parent,
					'extend_fields.id_parent' => $id_parent
				)
			);
			$get_fields = TRUE;
		}

		if ($get_fields)
		{
			$this->{$this->db_group}->join(
				$this->instances_table,
				$this->instances_table.'.id_'.$this->table.' = ' .$this->table.'.id_'.$this->table,
				'inner'
			);

			$query = $this->{$this->db_group}->get($this->get_table());
			if ( $query->num_rows() > 0) $fields = $query->result_array();
		}

		// Languages
		$langs = Settings::get_languages();

		// Instances table columns
		$fields_columns = $this->{$this->db_group}->list_fields($this->instances_table);

		foreach($definitions as $k => &$extend_field)
		{
			// One not tranlated extend field...
			if ($extend_field['translated'] != '1')
			{
				// fill the base data with empty values
				$extend_field = array_merge(array_fill_keys($fields_columns, NULL), $extend_field);
			
				foreach($fields as $row)
				{
					if($row['id_extend_field'] == $extend_field['id_extend_field'])
					{
						$extend_field = array_merge($extend_field , $row);
					}
				}
			}
			else
			{
				$extend_field['lang_data'] = array();

				foreach($langs as $language)
				{
					// Lang code
					$lang_code = $language['lang'];
					
					// Feed lang key with blank array
					$extend_field['lang_data'][$lang_code] = array('content'=>NULL);

					// Feeding of template languages elements
					foreach($fields as $row)
					{
						if($row['id_extend_field'] == $extend_field['id_extend_field'] && $row['lang'] == $lang_code)
						{
							$extend_field['lang_data'][$lang_code] = $row;
						}
					}
				}
			}
		}

		return $definitions;
	}


	// ------------------------------------------------------------------------


	/**
	 * Return one extend field definition + value for one given parent
	 *
	 * @param $id_extend
	 * @param $parent
	 * @param $id_parent
	 * @param $lang
	 *
	 * @return array
	 */
	public function get_element_extend_field($id_extend, $parent, $id_parent, $lang=NULL)
	{
		$result = array();

		$where = array(
			$this->get_table().'.'.$this->get_pk_name() => $id_extend,
			'extend_field.parent' => $parent,
			$this->instances_table.'.id_parent' => $id_parent
		);

		if ($lang)
			$where[$this->instances_table.'.lang'] = $lang;

		$this->{$this->db_group}->select(
			$this->get_table().'.*,'
			.$this->instances_table.'.id_parent,'
			.$this->instances_table.'.lang,'
			.$this->instances_table.'.content'
		);

		$this->{$this->db_group}->where($where);

		$this->{$this->db_group}->join(
			$this->instances_table,
			$this->instances_table.'.id_'.$this->table.' = ' .$this->table.'.id_'.$this->table,
			'left'
		);

		$query = $this->{$this->db_group}->get($this->get_table());

		if ( $query->num_rows() > 0)
			$result = $query->row_array();

		return $result;
	}


	// ------------------------------------------------------------------------


	public function get_extend_link_list($id_extend, $parent, $id_parent, $lang=NULL, $where=array(), $link_lang=NULL)
	{
		$data = $prepared_data = $values = array();

		if ( ! $lang) $lang = NULL;

		$extend = $this->get_element_extend_field($id_extend, $parent, $id_parent, $lang);

		$lang =  ! $link_lang ? Settings::get_lang('default') : $link_lang;

		if ( ! empty($extend))
		{
			$values = strlen($extend['content']) > 0 ? explode(',', $extend['content']) : array();

			$types = array();

			if ( ! empty($values))
			{
				// Try to find entities (pages, articles)
				foreach($values as $val)
				{
					$arr = explode(':', $val);

					if ( ! empty($arr[1]))
					{
						if ( ! isset($types[$arr[0]])) $types[$arr[0]] = array();
						$types[$arr[0]][] = $arr[1];
					}
				}
			}

			$sql = '';

			if ( ! empty($types['article']))
			{
				$in_types = "'" . implode("','", $types['article']) . "'";

				$sql = "
					select
						'article' as type,
						page_article.id_page as id_parent,
						article.id_article as id_entity,
						page_lang.title as parent_title,
						article_lang.title,
						article_lang.subtitle,
						article_lang.content,
						page_article.online,
						page_article.link_type,
						page_article.link_id,
						url.path as entity_url,
						page_article.link_type as target_type,
						IF (page_article.link_type = 'page', page_lang_target.online, article_lang_target.online) as target_online,
						url_target.path as target_url,
						COALESCE(article_lang_target.title, page_lang_target.title) as target_title,
						COALESCE(article_lang_target.subtitle, page_lang_target.subtitle) as target_subtitle,
						article_lang_target.content as target_content

					from article
						join article_lang on article_lang.id_article = article.id_article and lang='".$lang."'
						join page_article on
						(
							page_article.id_article = article.id_article
							and concat(page_article.id_page, '.', page_article.id_article) in (".$in_types.")
						)
						left join page_lang
							on page_lang.id_page = page_article.id_page and page_lang.lang = '".$lang."'
						left join url
							on url.type='article' and url.id_entity = page_article.id_article and url.active=1 and url.lang ='".$lang."'
						left join url as url_target on
						(
							url_target.type = page_article.link_type
							and url_target.id_entity = (if(LOCATE('.', page_article.link_id)>0, SUBSTRING(page_article.link_id, LOCATE('.', page_article.link_id)+1), page_article.link_id))
							and url_target.active=1
							and url_target.lang ='".$lang."'
						)
						left join page_lang as page_lang_target
							on page_lang_target.id_page = url_target.id_entity and url_target.type='page' and page_lang_target.lang ='".$lang."'
						left join article_lang as article_lang_target
							on article_lang_target.id_article = url_target.id_entity and url_target.type='article' and article_lang_target.lang='".$lang."'
				";
			}

			if ( ! empty($types['page']))
			{
				$in_types = implode(",", $types['page']);

				if ( ! empty($sql))
				{
					$sql .= " union ";
				}

				$sql .= "
					select
						'page' as type,
						NULL as id_parent,
						page.id_page as id_entity,
						NULL as parent_title,
						page_lang.title,
						page_lang.subtitle,
						NULL as content,
						page_lang.online,
						page.link_type,
						page.link_id,
						url.path as entity_url,
						page.link_type as target_type,
						IF (page.link_type = 'page', page_lang_target.online, article_lang_target.online) as target_online,
						url_target.path as target_url,
						COALESCE(article_lang_target.title, page_lang_target.title) as target_title,
						COALESCE(article_lang_target.subtitle, page_lang_target.subtitle) as target_subtitle,
						article_lang_target.content as target_content
					from page
						left join page_lang on page_lang.id_page = page.id_page and lang='".$lang."'
						left join url on url.type = 'page' and url.id_entity = page.id_page and url.active=1 and url.lang ='".$lang."'
						left join url as url_target on
						(
							url_target.type = page.link_type
							and url_target.id_entity = (if(LOCATE('.', page.link_id)>0, SUBSTRING(page.link_id, LOCATE('.', page.link_id)+1), page.link_id))
							and url_target.active=1 and url_target.lang ='".$lang."'
						)
						left join page_lang as page_lang_target
							on page_lang_target.id_page = url_target.id_entity and url_target.type='page' and page_lang_target.lang ='".$lang."'
						left join article_lang as article_lang_target
							on article_lang_target.id_article = url_target.id_entity and url_target.type='article' and article_lang_target.lang='".$lang."'

					where
						page.id_page in (".$in_types.")
				";
			}

			if ( ! empty($sql))
			{
				$query = $this->{$this->db_group}->query($sql);

				if ( $query->num_rows() > 0) $data = $query->result_array();
				$query->free_result();
			}


			// Get full pages and articles
			$get_enhanced_data = self::$ci->uri->segment(1) != config_item('admin_url');

			if ($get_enhanced_data)
			{
				$page_ids = $article_ids = array();
				$entity_data = array(
					'page' => array(),
					'article' => array()
				);

				foreach($data as $row)
				{
					if ($row['type'] == 'page') $page_ids[] = $row['id_entity'];
					if ($row['type'] == 'article') $article_ids[] = $row['id_entity'];
				}

				if ( ! empty($page_ids)) $entity_data['page'] = self::$ci->page_model->get_lang_list(array('where_in' => array('page.id_page' => $page_ids)), $lang);
				if ( ! empty($article_ids))	$entity_data['article'] = self::$ci->article_model->get_lang_list(array('where_in' => array('article.id_article' => $article_ids)), $lang);
			}


			// Reorder data
			foreach($values as $val)
			{
				$arr = explode(':', $val);
				$type = $arr[0];
				$rel = $arr[1];
				$ids = explode('.', $rel);
				$id_page = $ids[0];
				$id_article = !empty($ids[1]) ? $ids[1] : NULL;

				foreach($data as $row)
				{
					if (
						($row['type'] == $type && $row['type'] == 'page' && $row['id_entity'] == $id_page)
						OR ($row['type'] == $type && $row['type'] == 'article' && $row['id_parent'] == $id_page && $row['id_entity'] == $id_article)
					)
					{
						$row['rel'] = $rel;
						$row['extend_value'] = $val;

						if ($get_enhanced_data)
						{
							foreach($entity_data[$row['type']] as $e_data)
							{
								if (
									($row['type'] == 'page' && $e_data['id_page'] == $row['id_entity'])
									OR ($row['type'] == 'article' && $e_data['id_page'] == $row['id_parent'] && $e_data['id_article'] == $row['id_entity'])
								)
								{
									$row['data'] = $e_data;
								}
							}
						}

						$prepared_data[] = $row;
						break;
					}
				}
			}
		}

		return $prepared_data;
	}


	// ------------------------------------------------------------------------


	/**
	 * Saves one parent's extend fields data
	 * All extend fields values are saved by this method
	 *
	 * @param string		$parent		Parent type
	 * @param int			$id_parent	Current parent element ID. Can be the page ID, the article ID...
	 * @param array			$post		$_POST data array
	 * @param bool			$by_id 		If set to TRUE, each form input has the ID of the extend set. (old good way)
	 *									If false, the passed array contains extends names as keys
	 *
	 */
	public function save_data($parent, $id_parent, $post, $by_id = true)
	{
		// Get all extends fields with this element OR kind of parent
		$extend_fields = (!empty($post['id_element_definition'])) ? $this->get_list(array('id_element_definition' => $post['id_element_definition'])) : $this->get_list(array('parent' => $parent));

		foreach ($extend_fields as $extend_field)
		{
			$id_extend = $extend_field[$this->get_pk_name()];

			// Link between extend_field and the current parent
			$where = array(
				$this->get_pk_name() => $id_extend,
				'id_parent' => $id_parent,
				'parent' => $parent
			);
			
			// Checkboxes : first clear values from DB as the var isn't in $_POST if no value is checked
			// Todo :
			// Furthermore, make sure that if all checkbox values are unchecked, we do not fallback to the
			// default values, we do that by storing the special `-` value in the database. 
			// $langs = Settings::get_languages();

			if ($extend_field['html_element_type'] == 'checkbox')
			{
				if ($this->exists($where, $this->instances_table))
				{
					$this->{$this->db_group}->where($where);
					$this->{$this->db_group}->update($this->instances_table, array('content' => ''));
				}
				else
				{
					$cb_data = array_merge($where, array('content' => ''));
					$this->insert($cb_data, $this->instances_table);
				}
			}

			// Get the value from _POST values and feed the data array
			if ($by_id)
			{
				foreach ($post as $k => $value)
				{
					if (substr($k, 0, 2) == 'cf')
					{
						// id of the extend field
						$key = explode('_', $k);

						if (isset($key[1]) && $key[1] == $id_extend)
						{
							// if language code is set, use it in the query
							$lang=NULL;

							if (isset($key[2]))
								$lang = $key[2];

							// Save Extend field data
							$this->save_extend_field_value($id_extend, $parent, $id_parent, $value, $lang);

							// Save in other field
						// @deprecated
							/*
						 * @deprecated
						 *
							if ( ! empty($extend_field['copy_in']))
							{
								$this->copy_extend_value_to_field($extend_field, $parent, $id_parent, $value, $lang);
							}
							*/
						}
					}
				}
			}
			else
			{
				// Check the post
				foreach ($post as $name => $value)
				{
					if ($extend_field['name'] == $name)
					{
						// Lang array ?
						if (is_array($value))
						{
							foreach($value as $lang => $lang_val)
							{
								$this->save_extend_field_value($id_extend, $parent, $id_parent, $lang_val, $lang);
							}
						}
						else
						{
							$this->save_extend_field_value($id_extend, $parent, $id_parent, $value);
						}
					}
				}
			}
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Adds one value to one multiple values extend fields
	 * Values are coma separated in DB
	 *
	 * @param $id_extend
	 * @param $parent
	 * @param $id_parent
	 * @param $value
	 * @param $lang
	 *
	 * @return bool
	 */
	public function add_value_to_extend_field($id_extend, $parent, $id_parent, $value, $lang=NULL)
	{
		if( ! $id_extend)
		{
			log_message('error', print_r(get_class($this) . '->add_value_to_extend_field() : $id_extend is NULL', TRUE));
		}
		else
		{
			$content = array();

			$data = $this->get_element_extend_field($id_extend, $parent, $id_parent, $lang);

			// Check if $id_media already linked
			if ( ! empty($data))
			{
				$content = explode(',', $data['content']);

				if (in_array($value, $content))
					return FALSE;
			}

			$content[] = $value;

			$this->save_extend_field_value($id_extend, $parent, $id_parent, $content, $lang);

			return TRUE;
		}
	}


	// ------------------------------------------------------------------------


	public function delete_extend_field($id_extend_field)
	{
		// Begin transaction
		$this->{$this->db_group}->trans_start();

		// Definition
		parent::delete(array('id_extend_field'=>$id_extend_field), 'extend_field');

		// Lang
		parent::delete(array('id_extend_field'=>$id_extend_field), 'extend_field_lang');

		// Instances
		$this->delete_extend_fields($id_extend_field);

		// Context
		parent::delete(array('id_extend_field'=>$id_extend_field), 'extend_field_context');

		// Transaction complete
		$this->{$this->db_group}->trans_complete();

		return $this->{$this->db_group}->trans_status();
	}


	// ------------------------------------------------------------------------


	/**
	 * Removes one value from one multiple values extend field
	 * Values are coma separated in DB
	 *
	 * @param $id_extend
	 * @param $parent
	 * @param $id_parent
	 * @param $value
	 * @param $lang
	 */
	public function delete_value_from_extend_field($id_extend, $parent, $id_parent, $value, $lang=NULL)
	{
		$data = $this->get_element_extend_field($id_extend, $parent, $id_parent, $lang);

		// Check if $id_media already linked
		if ( ! empty($data))
		{
			$content = explode(',', $data['content']);

			foreach($content as $key => $existing_value)
			{
				if ($existing_value == $value)
					unset($content[$key]);
			}

			$this->save_extend_field_value($id_extend, $parent, $id_parent, $content, $lang);
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Save one extend field value
	 *
	 * @param      $id_extend
	 * @param      $parent
	 * @param      $id_parent
	 * @param      $value
	 * @param null $lang
	 */
	public function save_extend_field_value($id_extend, $parent, $id_parent, $value, $lang = NULL)
	{
		// Extend field definition
		$this->_join_to_extend_types();
		$extend_field = $this->get(array($this->get_pk_name() => $id_extend));

		if ( ! $lang) $lang = NULL;

		// Array ?
		if (is_array($value)) $value = trim(implode(',', $value), ',');

		// Date or Datetime
		if (in_array($extend_field['html_element_type'], array('date', 'datetime'))) $value = str_replace('.', '-', $value);

		// Select, Checkbox, Multi Select values : Clean white spaces
		if (in_array($extend_field['html_element_type'], array('select', 'checkbox', 'radio', 'select-multiple')))
		{
			$value = preg_replace('/\s*,\s*/', ',', $value);
		}

		$data = array(
			$this->get_pk_name() => $id_extend,
			'parent' => $parent,
			'id_parent' => $id_parent,
			'content' => $value,
		);
		$where = array(
			$this->get_pk_name() => $id_extend,
			'parent' => $parent,
			'id_parent' => $id_parent
		);

		if ( ! is_null($lang))
			$where['lang'] = $lang;

		// Update
		if( $this->exists($where, $this->instances_table))
		{
			$this->{$this->db_group}->where($where);
			$this->{$this->db_group}->update($this->instances_table, $data);
		}
		// Insert
		else
		{
			if ( ! is_null($lang)) $data['lang'] = $lang;
			$this->{$this->db_group}->insert($this->instances_table, $data);
		}
	}


	// ------------------------------------------------------------------------


	public function copy_extend_value_to_field($extend_field, $parent, $id_parent, $value, $lang = NULL)
	{
		$field = $extend_field['copy_in'];
		$dest = explode('.', $field);

		if ( !empty($dest[0]) && ! empty($dest[1]))
		{
			$field_pk = ! empty($extend_field['copy_in_pk']) ? $extend_field['copy_in_pk'] : $this->get_pk_name($dest[0]);

			if ( $field_pk)
			{
				$where = array($field_pk => $id_parent);
				$data = array($dest[1] => $value);

				if( ! is_null($lang))
				{
					$where['lang'] = $lang;
					if ($this->exists($where, $dest[0]))
					{
						$this->update($where, $data);
					}
					else
					{
						$data = array_merge($data, $where);
						$this->insert($data, $dest[0]);
					}
				}
				else
				{
					$this->update($where, $data, $dest[0]);
				}
			}
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Delete all the extend fields elements corresponding to a extend field definition
	 * Can be very dangerous !
	 *
	 * @param $id
	 *
	 * @return mixed
	 */
	public function delete_extend_fields($id)
	{
		$this->{$this->db_group}->where('id_'.$this->table, $id);
		
		return $this->{$this->db_group}->delete($this->instances_table);
	}


	// ------------------------------------------------------------------------


	public function get_context_list($context, $id_context = NULL, $parent = NULL, $id_parent = NULL, $id_extend_field_type=NULL)
	{
		$where = array(
			'order_by' => 'ordering ASC'
		);

		if ( ! empty($parent)) $where['parent'] = $parent;
		if ( ! empty($id_parent)) $where['id_parent'] = $id_parent;

		// Extend Lang : Label
		$this->{$this->db_group}->select($this->get_lang_table() . '.label');

		$this->{$this->db_group}->join(
			$this->get_lang_table(),
			$this->get_lang_table() . '.' . $this->get_pk_name() . ' = ' . $this->get_table() . '.' . $this->get_pk_name()
			. ' AND ' . $this->get_lang_table() . '.lang = \'' . Settings::get_lang('default') . '\'',
			'left'
		);

		// Add Extend Type info
		$this->_join_to_extend_types($id_extend_field_type);

		// Context
		$this->{$this->db_group}->select(
			self::$_CONTEXT_TABLE . '.context,'
			.self::$_CONTEXT_TABLE . '.id_context'
		);

		$this->_join_to_context($context, $id_context);

		// Extend Definition List
		$list = parent::get_list($where);

		return $list;
	}


	// ------------------------------------------------------------------------


	public function link_to_context($id_extend_field, $context, $id_context=NULL)
	{
		$where = array(
			'id_extend_field' => $id_extend_field,
			'context' => $context,
		);

		if ( ! is_null($id_context))
			$where['id_context'] = $id_context;

		if ( ! $this->exists($where, self::$_CONTEXT_TABLE))
		{
			$this->insert($where, self::$_CONTEXT_TABLE);
		}
	}


	// ------------------------------------------------------------------------


	public function unlink_from_context($id_extend_field, $context, $id_context=NULL)
	{
		$where = array(
			'id_extend_field' => $id_extend_field,
			'context' => $context,
		);

		if ( ! is_null($id_context))
			$where['id_context'] = $id_context;

		if ( $this->exists($where, self::$_CONTEXT_TABLE))
		{
			parrent::delete($where, self::$_CONTEXT_TABLE);
		}
	}


	// ------------------------------------------------------------------------


	public function check_context_existence($name, $context, $id_context, $id_extend = NULL)
	{
		$sql = "
			select e.id_extend_field
			from extend_field e
				join extend_field_context c on c.id_extend_field = e.id_extend_field
			where
				e.name = '".$name."'
				and c.context='".$context."'
				and c.id_context = ".$id_context."
		";

		if ( ! is_null($id_extend))
		{
			$sql .= "
				and e.id_extend_field <> ".$id_extend."
			";
		}

		$query = $this->{$this->db_group}->query($sql);

		return ($query->num_rows() > 0);
	}


	// ------------------------------------------------------------------------


	public function _join_to_extend_types($id_extend_type = NULL)
	{
		// Join to types
		$this->{$this->db_group}->select(
			self::$_TYPE_TABLE . '.type_name,'
			.self::$_TYPE_TABLE . '.active,'
			.self::$_TYPE_TABLE . '.display,'
			.self::$_TYPE_TABLE . '.validate,'
			.self::$_TYPE_TABLE . '.html_element,'
			.self::$_TYPE_TABLE . '.html_element_type,'
			.self::$_TYPE_TABLE . '.html_element_class,'
			.self::$_TYPE_TABLE . '.html_element_pattern'
		);

		$this->{$this->db_group}->join(
			self::$_TYPE_TABLE,
			self::$_TYPE_TABLE . '.id_extend_field_type = ' . $this->get_table() . '.type',
			'inner'
		);

		if ( ! is_null($id_extend_type))
			$this->{$this->db_group}->where('id_extend_field_type', $id_extend_type);
	}


	// ------------------------------------------------------------------------


	public function _join_to_context($context, $id_context=NULL)
	{
		$this->{$this->db_group}->join(
			self::$_CONTEXT_TABLE,
			self::$_CONTEXT_TABLE . '.' . $this->get_pk_name() . ' = ' . $this->get_table() . '.' . $this->get_pk_name(),
			'inner'
		);

		$this->{$this->db_group}->where(self::$_CONTEXT_TABLE . '.context', $context);

		if ( ! is_null($id_context))
			$this->{$this->db_group}->where(self::$_CONTEXT_TABLE . '.id_context', $id_context);
	}
}
