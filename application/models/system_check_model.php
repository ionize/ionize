<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
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
		parent::__construct();
	}


	// ------------------------------------------------------------------------


	public function get_todos()
	{
		$settings = Settings::get_settings();

		log_message('app', print_r($settings, TRUE));
	}


	// ------------------------------------------------------------------------


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


	// ------------------------------------------------------------------------


	/**
	 * Recursively processes all wrong pages levels
	 *
	 * @param      $pages			Pages to process, by ref.
	 * @param bool $correct			Correct wrong level ? Default to FALSE
	 * @param int  $id_parent
	 * @param int  $level
	 * @param int  $nb_wrong
	 *
	 * @return int					Number of corrected pages
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
					$this->{$this->db_group}->where('id_page', $child['id_page']);
					$this->{$this->db_group}->update('page', array('level' => $level));
				}
			}

			$nb_wrong = $this->process_wrong_page_level($pages, $correct, $child['id_page'], $sub_level, $nb_wrong);
		}
		return $nb_wrong;
	}


	// ------------------------------------------------------------------------


	/**
	 * @return int
	 */
	public function check_article_context()
	{
		$nb = 0;

        $sql = '
			select id_article, main_parent
			from page_article
			where main_parent = 0
			group by id_article
			having COUNT(id_page) = 1 AND
			main_parent = 0
		';
		
		$query = $this->{$this->db_group}->query($sql);
		
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
				$this->{$this->db_group}->set('main_parent', '1');
				$this->{$this->db_group}->where('id_article in (' . implode(',', $articles_id) . ')');
				$nb = $this->{$this->db_group}->update('page_article');
			}
		} 
		
		// Removes non existing articles from context
		$sql = '
			select distinct id_article 
			from page_article
		';
		
		$query = $this->{$this->db_group}->query($sql);
		
		if ($query->num_rows() > 0)
		{
			$context_articles = $query->result_array();

			// Get all articles ID
			$sql = '
				select distinct id_article 
				from article
			';

			$query = $this->{$this->db_group}->query($sql);

			if ($query->num_rows() > 0)
			{
				$id_articles = array();
				$articles = $query->result_array();
				foreach($articles as $article)
					$id_articles[] = $article['id_article'];
					
				foreach($context_articles as $article)
				{
					if ( ! in_array($article['id_article'], $id_articles))
					{
						$this->{$this->db_group}->where('id_article', $article['id_article'] );
						$this->{$this->db_group}->delete('page_article');
						$nb++; 
					
					}
				}
			}				
		}
		return $nb;
	}
}
