<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize, creative CMS
 * Category Controller
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 0.9.0
 */

class Category extends MY_admin
{
	/**
	 * Constructor
	 *
	 */
	public function __construct()
	{
		parent::__construct();

		$this->load->model('category_model', '', TRUE);
	}


	// ------------------------------------------------------------------------


	/**
	 * Displays the categories panel
	 *
	 */
	public function index()
	{
		$this->output('category/index');
	}


	// ------------------------------------------------------------------------


	/**
	 * Prints out the category form
	 *
	 * @param	string	parent. Element from which we edit the categories list
	 * @param	string	parent ID
	 *
	 */
	public function get_form($parent = FALSE, $id_parent = FALSE)
	{
		$this->category_model->feed_blank_template($this->template);
		$this->category_model->feed_blank_lang_template($this->template);
		
		// Pass the parent informations to the template
		$this->template['parent'] = $parent;
		$this->template['id_parent'] = $id_parent;
		
		$this->template['categories'] = $this->category_model->get_list(array('order_by'=>'ordering ASC'));

		$this->output('category/category');
	}


	// ------------------------------------------------------------------------

	
	/**
	 * Return categories list
	 *
	 */
	public function get_list()
	{
		// Categories list
		$this->template['categories'] = $this->category_model->get_list(array('order_by'=>'ordering ASC'));

		$this->output('category/list');
	}



	// ------------------------------------------------------------------------	

	
	/**
	 * Get the select box of categories
	 *
	 * @param	bool|string		parent type. Can be 'article', 'page', etc.
	 * @param	bool|string		parent ID.
	 *
	 */
	public function get_select($parent = FALSE, $id_parent = FALSE)
	{
		// Get data formed to feed the category select box
		$categories = $this->category_model->get_categories_select();

		// Get the current categories for the element
		$current_categories = FALSE;
		
		if ($parent && $id_parent)
			$current_categories = $this->category_model->get_joined_items_keys('category', $parent, $id_parent);

		// Outputs the categories form dropdown
		$this->template['categories'] =	form_dropdown('categories[]', $categories, $current_categories, 'class="select w100p" multiple="multiple"');
		$this->output('category/select');

		// $this->xhr_output(form_dropdown('categories[]', $categories, $current_categories, 'class="select w100p" multiple="multiple"'));
	}

	
	// ------------------------------------------------------------------------


	/** 
	 * Edit one category
	 *
	 * @param	int		Category ID
	 * @param	string	parent. Element from which we edit the categories list
	 * @param	string	parent ID
	 *
	 */
	public function edit($id, $parent = FALSE, $id_parent = FALSE)
	{

		$this->category_model->feed_template($id, $this->template);
		$this->category_model->feed_lang_template($id, $this->template);

		// Pass the parent informations to the template
		$this->template['parent'] = $parent;
		$this->template['id_parent'] = $id_parent;
		
		$this->template['categories'] = $this->category_model->get_list(array('order_by'=>'ordering ASC'));

		$this->output('category/category');
	}

	
	// ------------------------------------------------------------------------


	/**
	 * Saves one category
	 *
	 */
	public function save()
	{
		if( $this->input->post('name') != '' ) {

			// If no ID (means new one) and this item name already exists in DB : No save
			if ($this->input->post('id_category') == '' && $this->category_model->exists(array('name'=>url_title($this->input->post('name')))))
			{
				$this->error(lang('ionize_message_category_name_exists'));			
			}
			else
			{
				$this->_prepare_data();

				// Event
				$event_data = array(
					'base' => $this->data,
					'lang' => $this->lang_data,
				);
				$event_received = Event::fire('Category.save.before', $event_data);
				$event_received = array_pop($event_received);
				if ( ! empty($event_received['base']) && !empty($event_received['lang']))
				{
					$this->data = $event_received['base'];
					$this->lang_data = $event_received['lang'];
				}

				// Save data
				$this->id = $this->category_model->save($this->data, $this->lang_data);

				// Event
				$event_data = array(
					'base' => $this->data,
					'lang' => $this->lang_data,
				);
				Event::fire('Category.save.success', $event_data);

				// JSON Update array : If parent is defined in form, the categories selectbox of the parent will be updated
				if ($this->input->post('parent') != '')
				{
					$this->update[] = array(
						'element' => 'categories',
						'url' => 'category/get_select/'.$this->input->post('parent').'/'.$this->input->post('id_parent')
					);
				}
				// We suppose we are in the categories manager
				else
				{
					$this->callback = array(
						array(
							'fn' => 'ION.HTML',
							'args' => array('category/get_list', '', array('update' => 'categoriesContainer'))
						),
						array(
							'fn' => 'ION.clearFormInput',
							'args' => array('form' => 'newCategoryForm')
						)
					);
				}

				$this->success(lang('ionize_message_category_saved'));
			}
		}
		else
		{
			Event::fire('Category.save.error');

			$this->error(lang('ionize_message_category_not_saved'));			
		}
	}

		
	// ------------------------------------------------------------------------


	/**
	 * Deletes one category
	 *
	 * @param	int 	Category ID
	 * @param	string 	Parent table name. optional
	 * @param	int 	Parent ID. Optional
	 */
	public function delete($id, $parent = FALSE, $id_parent = FALSE)
	{
		if ($id && $id != '')
		{
			if ($this->category_model->delete($id) > 0)
			{
				// Delete join between parent and the deleted category
				if ( $parent !== FALSE && $id_parent !== FALSE )
					$this->category_model->delete_joined_key('category', $id, $parent, $id_parent);
				
				// Delete lang data
				$this->category_model->delete(array('id_category' => $id), 'category_lang');

				if ($parent != FALSE)
				{
					$parent_url = ($parent && $id_parent) ? '/' . $parent . '/' . $id_parent : '';
				
					$this->update[] = array(
						'element' => 'categories',
						'url' =>  admin_url() . 'category/get_select' . $parent_url
					);
				}

				// Remove deleted items from DOM
				$this->callback[] = array(
					'fn' => 'ION.deleteDomElements',
					'args' => array('.category' . $id)
				);

				// Answer prepare
				$this->id = $id;
				
				// Send answer				
				$this->success(lang('ionize_message_category_deleted'));
			}
			else
			{
				$this->error(lang('ionize_message_category_not_deleted'));
			}
		}
	}


	// ------------------------------------------------------------------------


	/** 
	 * Saves categories ordering
	 * 
	 */
	public function save_ordering($parent = FALSE, $id_parent = FALSE)
	{
		$order = $this->input->post('order');
		
		if( $order != FALSE )
		{
			// Saves the new ordering
			$this->category_model->save_ordering($order);

			if ($parent != FALSE)
			{
				// Update Array for JSON
				$this->update[] = array(
					'element' => 'categories',
					'url' =>  admin_url() . 'category/get_select/' . $parent . '/' . $id_parent
				);
			}

			// Answer
			$this->success(lang('ionize_message_operation_ok'));
		}
		else 
		{
			$this->error(lang('ionize_message_operation_nok'));
		}
	}

	
	// ------------------------------------------------------------------------


	/** 
	 * Prepare data before saving
	 *
	 */
	private function _prepare_data($xhr = FALSE)
	{
		// Standard fields
		$fields = $this->db->list_fields('category');
		
		// Set the data to the posted value.
		foreach ($fields as $field)
			$this->data[$field] = $this->input->post($field);

		// Some safe !
		$this->data['name'] = url_title($this->data['name']);

		// Lang data
		$this->lang_data = array();

		$fields = $this->db->list_fields('category_lang');

		foreach(Settings::get_languages() as $language)
		{
			foreach ($fields as $field)
			{
				if ( $this->input->post($field.'_'.$language['lang']) !== FALSE)
				{
					$this->lang_data[$language['lang']][$field] = $this->input->post($field.'_'.$language['lang']);
				}
			}
		}
	}
}
