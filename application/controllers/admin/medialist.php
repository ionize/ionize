<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize
 * Medialist Controller
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 1.0.0
 */

class Medialist extends MY_admin
{
	private static $_NB_PER_PAGE = 20;

	/**
	 * Constructor
	 *
	 */
	public function __construct()
	{
		parent::__construct();

		// Models
		$this->load->model('medialist_model');
	}


	// ------------------------------------------------------------------------


	public function index()
	{
		$this->output('medialist/index');
	}


	// ------------------------------------------------------------------------


	/**
	 * Displays list of media in DB
	 *
	 * @param int $page
	 *
	 */
	public function get_list($page=1)
	{
		$nb = ($this->input->post('nb')) ? $this->input->post('nb') : self::$_NB_PER_PAGE;

		// Filter settings
		if ($nb < self::$_NB_PER_PAGE) $nb = self::$_NB_PER_PAGE;
		$page = $page - 1;
		$offset = $page * $nb;

		// Filter
		$filter = $this->input->post('filter');
		$filter = ! empty($filter) ? explode(',', $filter) : array();

		$medias = $this->medialist_model->get_list(
			array(
				'limit' => $nb,
				'offset' => $offset
			),
			$filter
		);
		$items_nb = $this->medialist_model->get_list(array(), $filter, FALSE);
		$items_nb = count($items_nb);

		$this->template['items'] = $medias;
		$this->template['current_page'] = $page + 1;
		$this->template['items_nb'] = $items_nb;
		$this->template['items_by_page'] = $nb;
		$this->template['nb_pages'] = ceil($items_nb / $nb);
		$this->template['filter'] = $filter;

		$this->output('medialist/list');
	}


	// ------------------------------------------------------------------------


	public function save()
	{
		$post = $this->input->post();

		$this->medialist_model->save($post);

		$this->callback[] = array(
			'fn' => 'ION.notification',
			'args' => array('success', lang('ionize_message_operation_ok'))
		);

		// UI panel to update after saving
		$this->_reload_medialist();
	}


	// ------------------------------------------------------------------------


	/**
	 * Removes one media from DB
	 * Does not delete the file !
	 *
	 */
	public function remove()
	{
		$id_media = $this->input->post('id_media');

		$this->medialist_model->remove($id_media);

		// UI panel update
		$this->callback[] = array(
			'fn' => 'ION.deleteDomElements',
			'args' => array(
				'div[data-id='.$id_media.']'
			)
		);

		// $this->_reload_panel();

		// Answer send
		$this->success(lang('ionize_message_operation_ok'));
	}


	// ------------------------------------------------------------------------


	/**
	 * Removes not used media from the media tables.
	 *
	public function remove_broken_medias()
	{
		$medias = $this->medialist_model->get_list();

		// Check and correct page's views
		$nb_cleaned = $this->media_model->clean_table();

		$result['message'] = $nb_cleaned . lang('ionize_message_nb_media_cleaned');

		$this->xhr_output($result);
	}
	 */


	// ------------------------------------------------------------------------


	function _reload_panel()
	{
		$this->reload(
			'mainPanel',
			admin_url(TRUE) . 'medialist',
			lang('ionize_menu_medialist')
		);
	}

	function _reload_medialist()
	{
		$filter = $this->input->post('filter');

		$this->callback[] =
			array(
				'fn' => 'ION.HTML',
				'args' => array(
					'medialist/get_list',
					array(
						'filter' => $filter
					),
					array(
						'update' => 'medialistContainer'
					)
				)
			);

		$this->response();
	}

}

