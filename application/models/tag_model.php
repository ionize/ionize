<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize, creative CMS
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://ionizecms.com/doc-license
 * @link		http://ionizecms.com
 * @since		Version 0.9.0
 */

// ------------------------------------------------------------------------

/**
 * Ionize, creative CMS Page Model
 *
 * @package		Ionize
 * @subpackage	Models
 * @category	Tags
 * @author		Ionize Dev Team
 *
 */

class Tag_model extends Base_model 
{

	/**
	 * Model Constructor
	 *
	 * @access	public
	 */
	public function __construct()
	{
		parent::__construct();

		$this->table =		'tag';
		$this->pk_name = 	'id_tag';
	}


	// ------------------------------------------------------------------------


	/**
	 * Save tags linked to a parent element
	 * 
	 * @param	string		Tags as string (coma separated or ; separated, depending on what the user inputs...)
	 * @param	string		Parent type. Can be 'article, 'page', etc.
	 * @param	int			Parent ID
	 *
	 */
	function save_tags($tags, $parent, $id_parent)
	{
		// Join table name
		$join_table = $parent.'_'.$this->table;
		
		// Array of tags	
		$tag_array = array();
		
		// First clean input tags
		if ($tags)
		{
			// Prepare tags string before insertion
			$tags = str_replace(', ', ',', $tags);
			$tags = str_replace('; ', ',', $tags);
			$tags = str_replace(';', ',', $tags);
			$tags = str_replace('"', '', $tags);
			$tags = str_replace("'", '', $tags);
			$tags = str_replace("/", '', $tags);
			$tags = str_replace("\\", '', $tags);
			$tags = str_replace("(", '', $tags);
			$tags = str_replace(")", '', $tags);
			$tags = str_replace("\"", '', $tags);
			$tags = str_replace("&", '-', $tags);
			$tags = str_replace(":", '-', $tags);
			$tags = str_replace("*", '', $tags);
	
			$tags = explode(',', $tags);
			
			foreach ($tags as $tag)
			{
				$tag_array[] = trim($tag);
				// $tag_array[] = strtolower($tag);
				// $tag_array[] = preg_replace("/[^a-z0-9s]/", "", strtolower($tag));
			}
		}
				
		// Delete the already saved tags from parent join table
		$this->{$this->db_group}->where('id_'.$parent, $id_parent);
		$query = $this->{$this->db_group}->delete($join_table);
		
		// Update tag table
		foreach	($tag_array as $tag)
		{
			$this->{$this->db_group}->where('tag', $tag);
			$query = $this->{$this->db_group}->get($this->table, 1);
			
			if ($query->num_rows == 1)
			{
				$row = $query->row_array();
				$id_tag = $row['id_tag'];
			}
			else
			{
				$query = $this->{$this->db_group}->insert($this->table, array('tag'=>$tag) );
				$id_tag = $this->{$this->db_group}->insert_id();
			}
			
			// Update the parent join table
			$tag_data = array(
							'id_'.$parent 	=> $id_parent,
							'id_tag'	=> $id_tag
						);
		
			$query = $this->{$this->db_group}->insert($join_table, $tag_data);
		}
		
		// Clean up the tag table : Remove unused tags
		// ...
	}


	// ------------------------------------------------------------------------


	/** Get the tag cloud datas
	 *  @param	$lang		Code lang. Submitted to get tag on translated articles only
	 *	@param	$id_page	If submitted, get only the tags from articles in this page
	 */
	function tag_cloud($lang, $id_page=false)
	{
		$built = array();
		
		$sql = 	' SELECT t.tag, COUNT(t2.id_tag) as qty '
				.'FROM (article_tags t)'
				.' INNER JOIN article_tag t2  ON t2.id_tag = t.id_tag '
				.' INNER JOIN article_lang t3 ON t3.id_article = t2.id_article '
				.' INNER JOIN article t4 ON t4.id_article = t3.id_article '
				.' WHERE t3.lang = \''.$lang.'\' '
				.' AND t3.title is not null '
				.' AND t3.title != \'\'' ;

		if ($id_page)
		{
			$sql .= ' AND t4.id_page = \''.$id_page.'\'' ;
		}
		
		$sql .=' GROUP BY t.id_tag';

		$query = $this->{$this->db_group}->query($sql);
		
//		echo($this->{$this->db_group}->last_query());
		
		$built = array();
		
		if ($query->num_rows > 0)
		{
			$result = $query->result_array();
			
			foreach ($result as $row)
			{
				$built[$row['tag']] = $row['qty'];
			}
		}
		
		$query->free_result();

		return $built;
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns all tags as string or as simple array
	 *
	 * @param	string	Type of wished return. Can be 'array' or 'string'
	 *
	 */
	function get_tags($return = 'array')
	{
		$built = array();
		$string = '';

		// DB query
		$this->{$this->db_group}->select('tag');
		$query = $this->{$this->db_group}->get($this->table);
		
		if ($query->num_rows() > 0)
		{
			$result = $query->result();
			
			foreach ($result as $tag)
			{
				$built[] = $tag->tag;
			}
			
			if ($return == 'string')
			{
				foreach ($built as $tag)
				{
					$string .= $tag.', ';
				}
					
				$string = substr($string, 0, -2);
			}
		}
		
		$query->free_result();
		
		return ($return == 'array') ? $built : $string;
		
	}


	// ------------------------------------------------------------------------


	/**
	 * Gets tags from parent element
	 *
	 * @param	string	Parent type
	 * @param	int		Parent ID
	 * @param	string	Type of wished return. Can be 'array' or 'string'
	 *
	 * @return 	string or array	Tags string, values separated by coma
	 *
	 */	
	function get_tags_from_parent($parent, $id_parent, $return = 'array')
	{
		$built = array();
		$string = '';
		
		// Join table
		$join_table = $parent.'_'.$this->table;
		
		$this->{$this->db_group}->select('tag');
		$this->{$this->db_group}->where('id_'.$parent, $id_parent);
		$this->{$this->db_group}->join($this->table, $this->table.'.'.$this->pk_name.' = '. $join_table.'.'.$this->pk_name, 'inner');
		$this->{$this->db_group}->order_by($join_table.'.id_tag');
		
		$query = $this->{$this->db_group}->get($join_table);
		
		if ($query->num_rows() > 0)
		{
			$result = $query->result();
			
			foreach ($result as $tag)
			{
				$built[] = $tag->tag;
			}
			
			if ($return == 'string')
			{
				foreach ($built as $tag)
				{
					$string .= $tag.', ';
				}
					
				$string = substr($string, 0, -2);
			}
		}
		
		$query->free_result();
		
		return ($return == 'array') ? $built : $string;
	}
}


/* End of file tag_model.php */
/* Location: ./application/models/tag_model.php */