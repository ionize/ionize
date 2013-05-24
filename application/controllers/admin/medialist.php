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
	/**
	 * Constructor
	 *
	 */
	public function __construct()
	{
		parent::__construct();

		// Models
		$this->load->model('medialist_model');
		/*
		$this->load->model('media_model');
		$this->load->model('extend_field_model', '', TRUE);
		*/

	}


	public function index()
	{
		$medias = $this->medialist_model->get_list();
		$medias_lang = $this->medialist_model->get_all('media_lang');
		$media_lang_fields = $this->medialist_model->list_fields('media_lang');

		foreach($medias as &$media)
		{
			$media['alt_missing'] = FALSE;
			$media['is_used'] = TRUE;
			$media['has_source'] = (empty($media['provider'])) ? file_exists(DOCPATH.$media['path']) : TRUE;
			$media['lang'] = array();

			// Is linked to page or article
			if (empty($media['page_paths']) && empty($media['article_paths']))
				$media['is_used'] = FALSE;

			foreach(Settings::get_languages() as $lang)
			{
				$media['lang'][$lang['lang']] = array_fill_keys($media_lang_fields, '');
				$media['lang'][$lang['lang']]['id_media'] = $media['id_media'];
			}

			foreach($medias_lang as $media_lang)
			{
				if ($media_lang['id_media'] == $media['id_media'])
				{
					$media['lang'][$media_lang['lang']] = $media_lang;
				}
			}

			// Alt missing
			foreach(Settings::get_languages() as $lang)
			{
				// ALT missing
				if (empty($media['lang'][$lang['lang']]['alt']))
					$media['alt_missing'] = TRUE;
			}
		}

		$this->template['medias'] = $medias;

		$this->output('medialist/index');
	}


	public function save()
	{
		$post = $this->input->post();

		$this->medialist_model->save($post);

		// UI panel to update after saving
		$this->_reload_panel();

		// Answer send
		$this->success(lang('ionize_message_operation_ok'));
	}


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


	/**
	 * Removes not used media from the media tables.
	 *
	 */
	public function remove_broken_medias()
	{
		$medias = $this->medialist_model->get_list();




		// Check and correct page's views
		$nb_cleaned = $this->media_model->clean_table();

		$result['message'] = $nb_cleaned . lang('ionize_message_nb_media_cleaned');

		$this->xhr_output($result);
	}


	function _reload_panel()
	{
		$this->reload(
			'mainPanel',
			admin_url() . 'medialist',
			lang('ionize_menu_medialist')
		);
	}

}

