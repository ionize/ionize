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
 * Ionize Lang Model
 *
 * @package		Ionize
 * @subpackage	Models
 * @category	Lang
 * @author		Ionize Dev Team
 *
 */

class Lang_model extends Base_model 
{

	/**
	 * Model Constructor
	 *
	 * @access	public
	 */
	public function __construct()
	{
		parent::__construct();

		$this->set_table('lang');
		$this->set_pk_name('lang');
	}


	// ------------------------------------------------------------------------


	/**
	 * Insert the $from lang data to the $to lang data in the given tables
	 *
	 * @param	String / Array		Name of the table in which copy the data (without the "_lang" suffix
	 *								or siple array of table names
	 *								ex : array('page', 'article')
	 * @param	Array				Array of fields to copy
	 *								ex : array(lang, id, url)
	 *									 "id" will be replaced by the correct plang table ID.
	 									 So, if you set "page" as table name to copy, "idi" will become "id_page", which is correct
	 * @param	String				Lang code from which copy the data
	 * @param	String				Lang code to copy to.
	 * @param	Boolean				Force the destination lang data to be erased by the origin lang data
	 */
	public function insert_lang_data($tables, $fields, $from, $to, $erase = FALSE)
	{
		if ( ! is_array($tables))
		{
			$tables = array($tables);
		}
	
		$fields = ',' . implode(',', $fields);
		
		/*
		 * Example query : Copy english data to german data in page_lang table
		 *	 
		 *	insert into page_lang (lang, id_page, url, title)
		 *	(
		 *		select 'de', p2.id_page, p2.url, p2.title from page_lang p2 
		 *		where p2.lang='en'
		 *		and 
		 *		p2.id_page not in
		 *		(
		 *			SELECT DISTINCT id_page
		 *			FROM `page_lang`
		 *			WHERE lang = 'de'
		 *		)
		 *	);
		 *
		 */
		foreach ($tables as $table)
		{
			// Force copy from $from lang
			if ($erase == TRUE)
			{
				$this->db->where('lang', $to);
				$this->db->delete($table . '_lang');
			}

			// Copy...
			$sql = "insert into " . $table . "_lang (lang, id_" . $table . $fields .")
					(
						select '" . $to ."', id_" . $table. $fields . " from " . $table . "_lang 
						where lang='". $from ."'
						and 
						id_" . $table . " not in
						(
							SELECT DISTINCT id_" . $table . "
							FROM " . $table . "_lang
							WHERE lang = '". $to ."'
						)
					)";
			$this->db->query($sql);
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Cleans lang tables from data of not defined lang
	 *
	 * When you delete a lang in Ionize, it doesn't delete the lang tables content for this language (human error is possible...)
	 * So to "restore" the language data, just create again the lang code and that's it : The content is available again.
	 * 
	 * Now, if you really want to delete the lang AND all content linked to it, use this function to clean lang data tables.
	 * 
	 * @param	String / Array		Table name or array of table names to clean, without "lang" suffixe.
	 * @return	Int					Number of affecte rows
	 *								
	 */
	public function clean_lang_tables($tables)
	{
		if ( ! is_array($tables))
		{
			$tables = array($tables);
		}
		
		// Get the existing languages
		$languages = Settings::get_languages();
		
		$lang_codes = array();
		
		foreach ($languages as $lang)
		{
			$lang_codes[] = $lang['lang'];
		}
		
		// Execute the cleaning request
		$nb_affected_rows = 0;
		if ( ! empty($lang_codes))
		{
			foreach ($tables as $table)
			{
				$this->db->where_not_in('lang', $lang_codes);
				$nb_affected_rows += $this->db->delete($table . '_lang');
			}
		}
		
		return $nb_affected_rows;
	}


	// ------------------------------------------------------------------------


	/**
	 * Updates the content lang tables after one language code update
	 *
	 * @param	String / Array	Table name or Array of table names
	 * @param	String			Old lang code
	 * @param	String			New lang code
	 *
	 */
	public function update_lang_tables($tables, $from, $to)
	{
		if ( ! is_array($tables))
		{
			$tables = array($tables);
		}

		foreach ($tables as $table)
		{
			$this->db->where('lang', $from);
			$this->db->update($table . '_lang', array('lang' => $to));
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Copy the content of the whole site, page or articles from one language to another
	 *
	 */
	function copy_lang_content($from, $to, $table, $id)
	{
		// Data (all languages)
		$this->db->where('id_'.$table, $id);
		
		$query = $this->db->get($table.'_lang');
		
		$data = array();
		if ( $query->num_rows() > 0 )
			$data = $query->result_array();
		
		$query->free_result();

		// Fields
		$fields = $this->field_data($table.'_lang');

		// Compare destination lang data and source lang data
		$src = array_values(array_filter($data, create_function('$row','return $row["lang"] == "'. $from .'";')));
		$src = ( !empty($src[0])) ? $src[0] : array();
		
		$dest = array_values(array_filter($data, create_function('$row','return $row["lang"] == "'. $to .'";')));
		$dest = ( !empty($dest[0])) ? $dest[0] : array();

		// Only update if source and destination aren't empty
		if ( ! empty($src) && ! empty($dest))
		{
			// Limit set array to empty fields
			$dest = array_filter($dest, create_function('$row','return $row == "";'));	
			
			// Get only the destination empty fields
			$dest = array_diff_assoc($dest, $src);
			
			// Fill the destination empty fields with the source data
			$dest = array_intersect_key($src, $dest);

			// Update
			if ( ! empty($dest))
			{
				$this->db->where(array('id_'.$table => $id, 'lang' => $to));
			
				return $this->db->update($table.'_lang', $dest);
			}
			return 0;
		}
	}
}
/* End of file lang_model.php */
/* Location: ./application/models/lang_model.php */