<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Country_model extends Base_model
{
	/**
	 * Constructor
	 *
	 * @access	public
	 */
	public function __construct()
	{
		parent::__construct();

		$this->table =		'country';
		$this->pk_name 	=	'id_country';
		$this->lang_table = 'country_lang';
	}

	function get_list($lang=NULL)
	{
		$data = array();

		if (is_null($lang))	$lang = Settings::get_lang('current');

		$sql = "
			select
			country.id_country,
			country.iso2 as country_iso2,
			ifnull(country_lang.country_name, country.name) as country_name
			from country
				left join country_lang on country_lang.id_country = country.id_country
					and country_lang.lang='".$lang."'
			where continent is not null
			and continent != ''
			order by country_name ASC
		";

		$query = $this->db->query($sql);

		if ( $query->num_rows() > 0)
			$data = $query->result_array();

		return $data;

	}


	// ------------------------------------------------------------------------


	public function get_list_from_ids($ids, $lang=NULL)
	{
		$data = array();

		if (is_null($lang))	$lang = Settings::get_lang('default');

		$sql = "
			select
			country.id_country,
			ifnull(country_lang.country_name, country.name) as country_name
			from country
				left join country_lang on country_lang.id_country = country.id_country
					and country_lang.lang='".$lang."'
			where continent is not null
			and continent != ''
			and country.id_country in (".implode(',', $ids).")
			order by country_name ASC
		";

		$query = $this->db->query($sql);

		if ( $query->num_rows() > 0)
			$data = $query->result_array();

		return $data;
	}


	// ------------------------------------------------------------------------


	function get_select($lang=NULL)
	{
		$data = array();

		$items = $this->get_list($lang);

		foreach($items as $item)
		{
			$data[$item['id_country']] = $item['country_name'];
		}

		return $data;
	}


	// ------------------------------------------------------------------------


	public function get_code_select()
	{
		$data = array();

		$countries = $this->get_countries();

		foreach($countries as $country)
		{
			$data[$country['country_iso2']] = $country['country_name'];
		}

		return $data;
	}


	// ------------------------------------------------------------------------


	public function get_countries()
	{
		$results = array();

		$this->db->select('cl.*, c.default');
		$this->db->from('country_lang cl');
		$this->db->where('cl.lang', Settings::get_lang() );
		$this->db->where('c.num <>', '0');
		$this->db->join('country c', 'c.id_country = cl.id_country', 'left');

		$this->db->order_by( 'c.default', 'desc' );
		$this->db->order_by( 'cl.country_name', 'asc' );

		$query = $this->db->get();

		if ( $query->num_rows() > 0)
			$results = $query->result_array();

		return $results;
	}
}