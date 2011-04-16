<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://ionizecms.com/doc-license
 * @link		http://ionizecms.com
 * @since		Version 0.9.5
 */

// ------------------------------------------------------------------------

/**
 * Ionize #module_name module's Model
 *
 */


class Comments_model extends Base_model
{

	var $content = "";
	var $author = "";
	var $email = "";
	
	/**
	 * Model Constructor
	 *
	 * @access	public
	 */
	public function __construct()
	{
		parent::__construct();

		$this->set_table('article_comment');
		$this->set_pk_name('id_article_comment');
	}


	// ------------------------------------------------------------------------


	/**
	 * Saving one blog comment 
	 *
	 * @param		id		article id
	 * @returns		int		created id
	 */
	public function insert_comment($id_article)
	{
		
		// Retrieve data
		$email 		= $this->input->post("email");
		$content 	= nl2br($this->input->post("content"));
		$author 	= $this->input->post("author");
		
		// Checking data
		if (empty($email)||empty($author)||empty($content))
			return false; 
		
		// Defining record
		$data = array( 
						"content"	=>	$content,
						"author"	=>	$author,
						"email"		=>	$email,
						"created"	=>	date('Y-m-d H:i:s'),
						"id_article"=>	$id_article 
					);
					
					
		// Saving record
		$this->db->insert( $this->table, $data );
		
		// Returns created id 
		//return $this->db->insert_id();
		return true;
	}
	
	public function get_comments($id_article)
	{
		$this->db->where( "id_article = $id_article" );
		$query = $this->db->get( $this->table);
		
		return $query->result_array();
	}
	
	/**
	 * Updating article / comments infos
	 *
	 * @param		id		article id
	 * @returns		
	 */
	public function update_article($id_article)
	{
		
		// "comments_allow" is a checkbox, will be defined in POST array if checked
		if ($this->input->post("comments_allow")) 
			$comment_allow = "1";
		else
			$comment_allow = "0";
			
		 		
		// Defining record
		$data = array( 
					"comment_allow"=>$comment_allow 
				);
										
			// Updating record
			$this->db->update( "article", $data, "id_article = $id_article" );
		
		return $comment_allow;
		
	}
}