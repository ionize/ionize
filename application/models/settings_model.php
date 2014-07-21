<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize, creative CMS
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 0.9.0
 */

// ------------------------------------------------------------------------

/**
 * Ionize, creative CMS Settings Model
 *
 * @package		Ionize
 * @subpackage	Models
 * @category	Settings
 * @author		Ionize Dev Team
 */
class Settings_Model extends Base_model
{
	/**
	 *
	 */
	public function __construct()
	{
		parent::__construct();

		$this->set_table('setting');
		$this->set_pk_name('id_setting');
		
		$this->load->helper('path_helper');
	}


	// ------------------------------------------------------------------------


	/** 
	 * Get languages from LANG table
	 *
	 * @return	The lang array
	 */
	function get_languages()
	{
		return $this->{$this->db_group}->from('lang')->order_by('ordering', 'ASC')->get()->result_array();
	}


	// ------------------------------------------------------------------------


	/**
	 * Get one setting, from DB
	 *
	 * @param      $name
	 * @param null $lang
	 *
	 * @return mixed
	 */
	function get_setting($name, $lang=NULL)
	{
		$this->{$this->db_group}->where('name', $name);

		if ( ! is_null($lang))
			$this->{$this->db_group}->where('lang', $lang);

		$query = $this->{$this->db_group}->get($this->table);

		return $query->row_array();
	}


	// ------------------------------------------------------------------------


	public function get_setting_value($name, $lang=NULL)
	{
		$setting = $this->get_setting($name, $lang);

		if ( $setting && isset($setting['content']))
			return $setting['content'];

		return NULL;
	}


	// ------------------------------------------------------------------------


	/**
	 * Get the settings
	 * Don't retrieves the language depending settings
	 *
	 * @return	The settings array
	 */
	function get_settings()
	{
		$this->{$this->db_group}->where("(lang is null or lang='')");
		$query = $this->{$this->db_group}->get($this->table);

		return $query->result_array();
	}


	// ------------------------------------------------------------------------


	/**
	 * Get the language depending settings
	 * Each setting depending on the lang is stored in the setting table with 
	 * the field 'lang' feeded with the according language code
	 *
	 * @param	string		Lang code
	 *
	 * @return	The settings array
	 */
	function get_lang_settings($lang)
	{
		$this->{$this->db_group}->where('lang', $lang);
		$query = $this->{$this->db_group}->get($this->table);

		return $query->result_array();
	}


	// ------------------------------------------------------------------------


	/**
	 * Get the admin lang codes from the language folder
	 *
	 * @return	array	Array of lang code
	 *
	 */
	function get_admin_langs()
	{
		$path = set_realpath(APPPATH.'language/');
		$lang_dirs = array();

		if ($dirs = scandir($path))
		{
			foreach ($dirs as $dir)
			{
				if (is_dir($path.$dir))
				{
					$file_path = set_realpath($path.$dir).'admin_lang'.EXT;

					if (is_file($file_path))
						$lang_dirs[] = $dir;
				}
			}
		}

		return $lang_dirs;
	}


	// ------------------------------------------------------------------------


	function set_setting($name, $content, $lang=NULL)
	{
		// Check the setting
		$this->{$this->db_group}->from($this->table);
		$this->{$this->db_group}->where('name',$name);

		$where = array('name' => $name);
		$data = array(
			'name' => $name,
			'content' => $content
		);

		if ( ! is_null($lang))
		{
			$this->{$this->db_group}->where('lang', $lang);
			$where['lang'] = $lang;
			$data['lang'] = $lang;
		}

		if ($this->{$this->db_group}->count_all_results() > 0)
		{
			$this->{$this->db_group}->update($this->table, $data, $where);
		}
		else
		{
			$this->{$this->db_group}->insert($this->table, $data);
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * @param $data
	 *
	 */
	function save_setting($data)
	{
		// Check the setting
		$this->{$this->db_group}->from($this->table);
		$this->{$this->db_group}->where('name', $data['name']);
		
		// Check if the setting depends on lang code
		$where = '';
		if ( isset($data['lang']) )
		{
			$this->{$this->db_group}->where('lang', $data['lang']);
			$where =" and lang='".$data['lang']."'";
		}	
		
		if ($this->{$this->db_group}->count_all_results() > 0)
		{
			$this->{$this->db_group}->update($this->table, $data, "name = '".$data['name']."' ".$where);
		}
		else
		{
			$this->{$this->db_group}->insert($this->table, $data);
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Updates the media table
	 * Replaces the old path by the new one in columns "path" and "base_path"
	 * @param $old_path
	 * @param $new_path
	 */
	function update_media_path($old_path, $new_path)
	{
		// Example of query :
		// update media set path = replace(path, 'files/', 'files_new_folder/');
		if ($new_path)
		{
			
			// Update media table
			$sql = 	"UPDATE media set path = REPLACE(path, '" . $old_path . "/', '" . $new_path . "/'), base_path = REPLACE(base_path, '" . $old_path . "/', '" . $new_path . "/') ";
			$this->{$this->db_group}->query($sql);
			
			// Update articles table
			$sql = "UPDATE article_lang set content = REPLACE(content, '/".$old_path."/', '/" . $new_path . "/')";
			$this->{$this->db_group}->query($sql);
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Clean unused language depending settings -- Kochin
	 *
	 * When a language is deleted, language depending settings are left untouched.
	 * This function cleans up any unused language depending settings.
	 *
	 * @return	Int	number of rows deleted
	 */
	function clean_lang_settings()
	{
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
			$this->{$this->db_group}->where("NOT(lang IS NULL OR lang='')")
						->where_not_in('lang', $lang_codes);
			$nb_affected_rows = $this->{$this->db_group}->delete($this->table);
		}

		return $nb_affected_rows;
	}
}
