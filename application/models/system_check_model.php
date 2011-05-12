<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://ionizecms.com/doc-license
 * @link		http://ionizecms.com
 * @since		Version 0.9.7
 */

// ------------------------------------------------------------------------

/**
 * Ionize System Check Model
 *
 * @package		Ionize
 * @subpackage	Models
 * @category	System
 * @author		Ionize Dev Team
 *
 */

class System_check_model extends Base_model 
{

	/**
	 * Constructor
	 *
	 * @access	public
	 */
	public function __construct()
	{
		// Call the Model constructor
		parent::Model();
	}
	
	
	/**
	 * Checks the pages levels.
	 * @access	public
	 * @param	boolean		Correct wrong level ? Default to FALSE
	 * @return 	int			Number of wrong levels
	 *						If "correct" is set to TRUE, returns the number of corrected levels.
	 *
	 */ 
	public function check_page_level($correct = FALSE)
	{
		$pages = $this->get_list(FALSE, 'page');
		
		if ( ! empty($pages))
		{
			return $this->process_wrong_page_level($pages, $correct);
		}
		
		return 0;
	}
	
	
	
	/**
	 * Recursively processes all wrong pages levels
	 * @access	private
	 * @param	array		Pages to process, by ref.
	 * @param	boolean		Correct wrong level ? Default to FALSE
	 * @return	int			Number of corrected pages
	 *
	 */
	private function process_wrong_page_level(&$pages, $correct=FALSE, $id_parent=0, $level=0, $nb_wrong=0 )
	{
		$children = array_values(array_filter($pages, create_function('$row','return $row["id_parent"] == "'. $id_parent .'";')));
		$sub_level = $level + 1;
		
		foreach ($children as $child)
		{
			if ($child['level'] != $level)
			{
				$nb_wrong++;
				if ($correct == TRUE)
				{
					$this->db->where('id_page', $child['id_page']);
					$this->db->update('page', array('level' => $level));
				}
			}

			$nb_wrong = $this->process_wrong_page_level($pages, $correct, $child['id_page'], $sub_level, $nb_wrong);
		}
		return $nb_wrong;
	}
	
	
	public function check_article_context()
	{
		$sql = '
			select id_article 
			from page_article
			where main_parent = 0
			group by id_article
			having COUNT(id_page) = 1
		';
		
		$query = $this->db->query($sql);
		
		if ($query->num_rows() > 0)
		{
			$articles = $query->result_array();
			
			$articles_id = array();
			foreach($articles as $article)
			{
				$articles_id[] = $article['id_article'];
			}
			
			if ( ! empty($articles_id))
			{
				$this->db->set('main_parent', '1');
				$this->db->where('id_article in (' . implode(',', $articles_id) . ')');
				return $this->db->update('page_article');
			}
		} 
		return 0;
	}
	
	
	
}
/* End of file system_check_model.php */
/* Location: ./application/models/system_check_model.php */